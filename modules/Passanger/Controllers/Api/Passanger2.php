<?php

namespace Modules\Passanger\Controllers\Api;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\User\Models\UserDetailModel;
use Modules\Role\Models\RoleModel;
use Modules\Ticket\Models\TicketModel;
use Modules\Schedule\Models\ScheduleModel;
use Modules\Trip\Models\TripModel;
use Modules\Rating\Models\RatingModel;
use Modules\Passanger\Models\Socialsignin;
use Modules\Passanger\Models\RegistrationOtpModel;
use Modules\Website\Models\EmailModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use Firebase\JWT\JWT;
use Exception;
use App\Libraries\Tokenjwt;
use Modules\Trip\Models\FacilityModel;
use Modules\Trip\Models\SubtripModel;

class Passanger extends BaseController
{
    use ResponseTrait;
    protected $Viewpath;
    protected $userModel;
    protected $userDetailModel;
    protected $roleModel;
    protected $tokenJwt;
    protected $ticketModel;
    protected $scheduleModel;
    protected $tripModel;
    protected $ratingModel;
    protected $socialsigninModel;
    protected $facilitypModel;
    protected $subtripModel;
    protected $registrationOtpModel;


    public function __construct()
    {

        $this->Viewpath = "Modules\Passanger\Views";
        $this->userModel = new UserModel();
        $this->userDetailModel = new UserDetailModel();
        $this->roleModel = new RoleModel();
        $this->db = \Config\Database::connect();

        $this->tokenJwt = new Tokenjwt();
        $this->ticketModel = new TicketModel();

        $this->scheduleModel = new ScheduleModel();
        $this->tripModel = new TripModel();

        $this->ratingModel = new RatingModel();

        $this->socialsigninModel = new Socialsignin();
        $this->facilitypModel = new FacilityModel();
        $this->subtripModel = new SubtripModel();
        $this->registrationOtpModel = new RegistrationOtpModel();
    }

    private function passengerBaseQuery()
    {
        return $this->userModel
            ->select('users.id AS user_id, users.login_email, users.login_mobile, users.slug, users.status, user_details.first_name, user_details.last_name, user_details.id_number, user_details.id_type, user_details.address, user_details.country_id, user_details.city, user_details.zip_code, user_details.image');
    }

    private function fetchPassengerBySlug($slug)
    {
        return $this->passengerBaseQuery()
            ->join('user_details', 'user_details.user_id = users.id', 'left')
            ->where('users.slug', $slug)
            ->where('users.role_id', 3)
            ->first();
    }

    private function fetchPassengerById($userId)
    {
        return $this->passengerBaseQuery()
            ->join('user_details', 'user_details.user_id = users.id', 'left')
            ->where('users.id', $userId)
            ->where('users.role_id', 3)
            ->first();
    }

    private function formatPassengerResponse($passenger)
    {
        if (empty($passenger)) {
            return [];
        }

        $passenger = (object) $passenger;

        $payload = [
            'user_id'      => property_exists($passenger, 'user_id') ? (int) $passenger->user_id : null,
            'slug'         => property_exists($passenger, 'slug') ? $passenger->slug : null,
            'login_email'  => property_exists($passenger, 'login_email') ? $passenger->login_email : null,
            'login_mobile' => property_exists($passenger, 'login_mobile') ? $passenger->login_mobile : null,
            'status'       => property_exists($passenger, 'status') ? (int) $passenger->status : null,
            'first_name'   => property_exists($passenger, 'first_name') ? $passenger->first_name : null,
            'last_name'    => property_exists($passenger, 'last_name') ? $passenger->last_name : null,
            'id_number'    => property_exists($passenger, 'id_number') ? $passenger->id_number : null,
            'id_type'      => property_exists($passenger, 'id_type') ? $passenger->id_type : null,
            'address'      => property_exists($passenger, 'address') ? $passenger->address : null,
            'country_id'   => property_exists($passenger, 'country_id') ? $passenger->country_id : null,
            'city'         => property_exists($passenger, 'city') ? $passenger->city : null,
            'zip_code'     => property_exists($passenger, 'zip_code') ? $passenger->zip_code : null,
        ];

        $payload['image'] = (!empty($passenger->image))
            ? base_url('/public/' . $passenger->image)
            : null;

        return $payload;
    }

    private function sendRegistrationOtp(string $recipient, string $otp): bool
    {
        $emailModel = new EmailModel();
        $emailConfig = $emailModel->first();

        if (!$emailConfig) {
            log_message('error', 'OTP email configuration missing.');
            return false;
        }

        $email = \Config\Services::email();

        $config = [
            'userAgent'   => 'Enugu Smart Bus',
            'protocol'    => $emailConfig->protocol,
            'mailPath'    => '/usr/sbin/sendmail',
            'SMTPHost'    => $emailConfig->smtphost,
            'SMTPUser'    => $emailConfig->smtpuser,
            'SMTPPass'    => $emailConfig->smtppass,
            'SMTPPort'    => $emailConfig->smtpport,
            'SMTPTimeout' => 5,
            'SMTPKeepAlive' => false,
            'SMTPCrypto'  => $emailConfig->smtpcrypto,
            'wrapChars'   => 76,
            'mailType'    => 'html',
            'charset'     => 'UTF-8',
            'validate'    => false,
            'priority'    => 3,
            'CRLF'        => "\r\n",
            'newline'     => "\r\n",
            'BCCBatchMode' => false,
            'BCCBatchSize' => 200,
            'DSN'         => false,
        ];

        $email->initialize($config);

        $email->setTo($recipient);
        $email->setFrom($emailConfig->smtpuser, 'Enugu Smart Bus');
        $email->setSubject('Your Enugu Smart Bus verification code');

        $message = '<p>Hello,</p>'
            . '<p>Your Enugu Smart Bus verification code is <strong>' . esc($otp) . '</strong>.</p>'
            . '<p>This code expires in 10 minutes. If you did not request it, please ignore this message.</p>'
            . '<p>Thank you,<br/>Enugu Smart Bus Team</p>';

        $email->setMessage($message);

        if ($email->send(false)) {
            return true;
        }

        log_message('error', 'Failed to send OTP email: ' . print_r($email->printDebugger(['headers']), true));
        return false;
    }

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(24));
    }
    public function getPassangerdata($segment, $type)

    {
        $userdata = array();
        if ($type == "email") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_email', $segment)->findAll();
        }
        if ($type == "mobile") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_mobile', $segment)->findAll();
        }

        if (empty($userdetail)) {
            $data = [
                'message' => "No Data not found.",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            foreach ($userdetail as $key => $uservalue) {
                $userdata['user_id'] = $uservalue->user_id;
                $userdata['login_email'] = $uservalue->login_email;
                $userdata['login_mobile'] = $uservalue->login_mobile;
                $userdata['slug'] = $uservalue->slug;
                $userdata['status'] = $uservalue->status;
                $userdata['first_name'] = $uservalue->first_name;
                $userdata['last_name'] = $uservalue->last_name;
                $userdata['id_number'] = $uservalue->id_number;
                $userdata['id_type'] = $uservalue->id_type;
                $userdata['address'] = $uservalue->address;
                $userdata['country_id'] = $uservalue->country_id;
                $userdata['city'] = $uservalue->city;
                $userdata['zip_code'] = $uservalue->zip_code;
            }
            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $userdata,
            ];

            return $this->response->setJSON($data);
        }
    }

    public function getPassanger()
    {


        $segment    = $this->request->getVar('userid');
        $password = $this->request->getVar('password');
        $type = $this->request->getVar('type');




        if ($type == "email") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_email', $segment)->first();
        }
        if ($type == "mobile") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_mobile', $segment)->first();
        }

        if ($userdetail) {
            $pass = $userdetail->password;
            $verify_pass = password_verify($password, $pass);



            if ($verify_pass) {


                $token = $this->tokenJwt->generateToken($userdetail->slug);
                $passengerProfile = $this->fetchPassengerBySlug($userdetail->slug);
                $userPayload = $this->formatPassengerResponse($passengerProfile);



                $data = [
                    'status' => "success",
                    'response' => 200,
                    'token' => $token,
                    'data' => [
                        'token' => $token,
                        'user' => $userPayload,
                    ],
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'message' => "Password or User Name Not Match",
                    'status' => "fail",
                    'response' => 204,

                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'message' => "User Name Not Match",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }



    public function getPassangerinfo()

    {
        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();




        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            $passenger = $this->fetchPassengerBySlug($decoded->slug);

            if (empty($passenger)) {
                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'data' => "Passenger not found",
                ];
                return $this->response->setJSON($data);
            }

            $userdata = $this->formatPassengerResponse($passenger);

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $userdata,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
            ];
            return $this->response->setJSON($data);
        }
    }


    public function getTickets()
    {
        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();

        try {
            // check and validate user token
            $decoded = JWT::decode($token, $key, array("HS256"));
            $userdetail = $this->userModel->where('slug', $decoded->slug)->first();

            // get tickets
            $ticketlist = $this->ticketModel
                // select columns
                ->select('tickets.*')
                ->select('l1.name AS pick_location_name, l2.name AS drop_location_name')
                ->select('s1.name AS pick_stand_name, s2.name AS drop_stand_name')
                ->select('pd1.time AS pick_stand_time, pd2.time AS drop_stand_time')

                // join with locations
                ->join('locations l1', 'tickets.pick_location_id = l1.id', 'left')
                ->join('locations l2', 'tickets.drop_location_id = l2.id', 'left')
                ->join('pickdrops pd1', 'tickets.pick_stand_id = pd1.id', 'left')
                ->join('pickdrops pd2', 'tickets.drop_stand_id = pd2.id', 'left')
                ->join('stands s1', 'pd1.stand_id = s1.id', 'left')
                ->join('stands s2', 'pd2.stand_id = s2.id', 'left')

                // select rows
                ->where('passanger_id', $userdetail->id)
                ->orderBy('tickets.id', 'DESC')
                ->findAll();

            if (empty($ticketlist)) {
                // ticket list is empty
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'data' => "No ticket found",
                ];
                return $this->response->setJSON($data);
            }
            // var_dump($ticketlist);exit;
            foreach ($ticketlist as $key => $ticketvalue) {
                $gettripdata =  $this->tripModel
                ->select('trips.*, l_p.name AS pl_name, l_d.name AS dl_name, sc.start_time, sc.end_time')
                ->join('locations l_p', 'trips.pick_location_id = l_p.id', 'left')
                ->join('locations l_d', 'trips.drop_location_id = l_d.id', 'left')
                ->join('schedules sc', 'trips.schedule_id = sc.id', 'left')
                ->withDeleted()
                ->find($ticketvalue->trip_id);
  
                $travelartripdata = $this->subtripModel
                    ->select('subtrips.*, l_p.name AS pl_name, l_d.name AS dl_name')
                    ->join('locations l_p', 'subtrips.pick_location_id = l_p.id')
                    ->join('locations l_d', 'subtrips.drop_location_id = l_d.id')
                    ->withDeleted()
                    ->find($ticketvalue->subtrip_id);

                $tipdetil = $this->tripModel->where('id', $ticketvalue->trip_id)->first();

                $facility = "no ficility";

                if ($facilities = $tipdetil->facility) {
                    // facility exists
                    // explode comma separated facilities
                    $facilityArr = explode(",", $facilities);
                    $facilityNameArr = [];

                    foreach ($facilityArr as $facility) {
                        $facilityInfo = $this->facilitypModel->select('name')->withDeleted()->find($facility);
                        $facilityNameArr[] = $facilityInfo->name;
                    }

                    $facility = implode(", ", $facilityNameArr);
                }

                $scheduldetail = $this->scheduleModel->where('id', $tipdetil->schedule_id)->first();

                $reviewStatus = 0;
                $rating = $this->ratingModel->where('booking_id', $ticketvalue->booking_id)->first();

                // Journey date
                $journeyDay = date('Y-m-d', strtotime($ticketvalue->journeydata));
                $bookingDate = date('Y-m-d', strtotime($ticketvalue->created_at));

                if (!empty($rating)) {
                    $reviewStatus = 1;
                }

                $ticketdata[$key]['id'] = $ticketvalue->id;
                $ticketdata[$key]['booking_id'] = $ticketvalue->booking_id;
                $ticketdata[$key]['trip_id'] = $ticketvalue->trip_id;
                $ticketdata[$key]['subtrip_id'] = $ticketvalue->subtrip_id;
                $ticketdata[$key]['passanger_id'] = $ticketvalue->passanger_id;
                $ticketdata[$key]['pick_location_id'] = $ticketvalue->pick_location_id;
                $ticketdata[$key]['pick_location_name'] = $ticketvalue->pick_location_name;
                $ticketdata[$key]['drop_location_id'] = $ticketvalue->drop_location_id;
                $ticketdata[$key]['drop_location_name'] = $ticketvalue->drop_location_name;
                $ticketdata[$key]['pick_stand_id'] = $ticketvalue->pick_stand_id;
                $ticketdata[$key]['pick_stand_name'] = $ticketvalue->pick_stand_name;
                $ticketdata[$key]['pick_stand_time'] = $journeyDay . ' ' . $ticketvalue->pick_stand_time;
                $ticketdata[$key]['drop_stand_id'] = $ticketvalue->drop_stand_id;
                $ticketdata[$key]['drop_stand_name'] = $ticketvalue->drop_stand_name;
                $ticketdata[$key]['drop_stand_time'] = $journeyDay . ' ' . $ticketvalue->drop_stand_time;
                $ticketdata[$key]['price'] = $ticketvalue->price;
                $ticketdata[$key]['discount'] = $ticketvalue->discount;
                $ticketdata[$key]['totaltax'] = $ticketvalue->totaltax;
                $ticketdata[$key]['paidamount'] = $ticketvalue->paidamount;
                $ticketdata[$key]['offerer'] = $ticketvalue->offerer;
                $ticketdata[$key]['adult'] = $ticketvalue->adult;
                $ticketdata[$key]['chield'] = $ticketvalue->chield;
                $ticketdata[$key]['special'] = $ticketvalue->special;
                $ticketdata[$key]['seatnumber'] = $ticketvalue->seatnumber;
                $ticketdata[$key]['totalseat'] = $ticketvalue->totalseat;
                $ticketdata[$key]['journeydata'] = $ticketvalue->journeydata;
                $ticketdata[$key]['payment_status'] = $ticketvalue->payment_status;
                $ticketdata[$key]['vehicle_id'] = $ticketvalue->vehicle_id;
                $ticketdata[$key]['payment_detail'] = $ticketvalue->payment_detail;
                $ticketdata[$key]['startime'] = $scheduldetail->start_time;
                $ticketdata[$key]['endtime'] = $scheduldetail->end_time;
                $ticketdata[$key]['refund'] = $ticketvalue->refund;
                $ticketdata[$key]['cancel_status'] = $ticketvalue->cancel_status;
                $ticketdata[$key]['review_status'] = $reviewStatus;
                $ticketdata[$key]['booking_date'] = $bookingDate;
                $ticketdata[$key]['facility'] = $facility;

                
            if ($ticketvalue->paid_max_luggage_pcs == null || $ticketvalue->paid_max_luggage_pcs == '') {
               $ticketdata[$key]['paid_max_luggage_pcs'] = 0;
            }else{
                $ticketdata[$key]['paid_max_luggage_pcs'] = $ticketvalue->paid_max_luggage_pcs;
            }

            if ($ticketvalue->price_pcs == null || $ticketvalue->price_pcs == '') {
               $ticketdata[$key]['price_pcs'] = 0.00;
            }else{
                $ticketdata[$key]['price_pcs'] = $ticketvalue->price_pcs;
            }

            if ($ticketvalue->special_max_luggage_pcs == null || $ticketvalue->special_max_luggage_pcs == '') {
               $ticketdata[$key]['special_max_luggage_pcs'] = 0;
            }else{
                $ticketdata[$key]['special_max_luggage_pcs'] = $ticketvalue->special_max_luggage_pcs;
            }
            if ($ticketvalue->special_price_pcs == null || $ticketvalue->special_price_pcs == '') {
               $ticketdata[$key]['special_price_pcs'] = 0.00;
            }else{
                $ticketdata[$key]['special_price_pcs'] = $ticketvalue->special_price_pcs;
            }
          
           $ticketdata[$key]['from'] = $gettripdata->pl_name;
           $ticketdata[$key]['to'] = $gettripdata->dl_name;
           $ticketdata[$key]['trip_start_time'] = $gettripdata->start_time;
           $ticketdata[$key]['trip_end_time'] = $gettripdata->end_time;
           $ticketdata[$key]['travelerPick'] = $travelartripdata->pl_name;
           $ticketdata[$key]['travelerDrop'] = $travelartripdata->dl_name;

           $ticketdata[$key]['discount'] = (float)$ticketvalue->discount;
           $ticketdata[$key]['totaltax'] = (float)$ticketvalue->totaltax;
           $ticketdata[$key]['paidamount'] = (float)$ticketvalue->paidamount;
           $ticketdata[$key]['roundtrip_discount'] = (float)$ticketvalue->roundtrip_discount;
           $ticketdata[$key]['total_paid_luggage_price'] = round(((int)$ticketvalue->paid_max_luggage_pcs * (float)$ticketvalue->price_pcs), 2);
           $ticketdata[$key]['total_special_luggage_price'] = round(((int)$ticketvalue->special_max_luggage_pcs * (float)$ticketvalue->special_price_pcs), 2);

           $ticketdata[$key]['sub_total'] = round(((float)$ticketvalue->price + (float)$ticketdata[$key]['total_paid_luggage_price'] + (float)$ticketdata[$key]['total_special_luggage_price']), 2);
           $ticketdata[$key]['grand_total'] = round((float)$ticketdata[$key]['sub_total'] + (float)$ticketdata[$key]['totaltax'] - (float)$ticketdata[$key]['discount'], 2);
                
            }

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $ticketdata,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
                'error' => $ex->getMessage(),
            ];
            return $this->response->setJSON($data);
        }
    }

    public function passangerpicuplod()
    {
        $path = 'image/passenger';
        $image =  $this->request->getFile('image');

        $validation =     $this->validate([
            'image' => 'uploaded[image]|max_size[image,1024]',
        ]);
        if (!$validation) {

            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => $validation,
                'message' => "Max file size 1MB",
            ];

            return $this->response->setJSON($data);
        }


        if ($image->isValid() && !$image->hasMoved()) {
            $profilepic     = $this->imgaeCheck($image, $path);
        }

        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();





        try {
            $decoded = JWT::decode($token, $key, array("HS256"));


            $userdetailId = $this->usercheck($decoded->slug);

            $picupload = array(
                "id" => $userdetailId->id,
                "image" => $profilepic,

            );

            $success = $this->userDetailModel->save($picupload);

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $success,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
            ];
            return $this->response->setJSON($data);
        }
    }

    public function changePassengerinfo()
    {

        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();


        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            $userdetailId = $this->usercheck($decoded->slug);

            $validdata = array(
                // "id" => $userdetailId->id,
                "first_name" => $this->request->getVar('first_name'),
                "last_name" => $this->request->getVar('last_name'),
                "id_type" => $this->request->getVar('id_type'),
                // "country_id" => $this->request->getVar('country_id'),
            );
            $validationRules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'id_type' => 'permit_empty|string',
            ];
            $validation = \Config\Services::validation();

            if ($validation->setRules($validationRules)->run($validdata)) {

                $inputdata = array(
                    "id" => $userdetailId->id,
                    "first_name" => $this->request->getVar('first_name'),
                    "last_name" => $this->request->getVar('last_name'),
                    "id_type" => $this->request->getVar('id_type'),
                    "country_id" => $this->request->getVar('country_id'),
                    "id_number" => $this->request->getVar('id_number'),
                    "address" => $this->request->getVar('address'),
                    "city" => $this->request->getVar('city'),
                    "zip_code" => $this->request->getVar('zip_code'),

                );




                $success = $this->userDetailModel->save($inputdata);

                $data = [
                    'status' => "success",
                    'response' => 200,
                    'data' => $success,
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'message' => "data validation error",
                    'data' => $this->validation->listErrors(),
                ];
                return $this->response->setJSON($data);
            }
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
                'error' => $ex,
            ];
            return $this->response->setJSON($data);
        }
    }

    public function changePassword()
    {
        $password = $this->request->getVar('password');
        $repassword = $this->request->getVar('repassword');
        $oldpassword = $this->request->getVar('oldpassword');

        if ($password == $repassword) {

            $key = getenv('TOKEN_SECRET');
            $token = $this->tokenJwt->tokencheck();

            try {
                $decoded = JWT::decode($token, $key, array("HS256"));



                $userdetail = $this->userModel->where('slug', $decoded->slug)->first();

                $pass = $userdetail->password;
                $verify_pass = password_verify($oldpassword, $pass);

                if ($verify_pass) {
                    $newpassword = password_hash($password, PASSWORD_DEFAULT);
                    $passupdate = array(
                        "id" => $userdetail->id,
                        "password" => $newpassword,

                    );

                    $success = $this->userModel->save($passupdate);

                    $data = [
                        'status' => "success",
                        'response' => 200,
                        'data' => $success,
                    ];

                    return $this->response->setJSON($data);
                } else {
                    $data = [
                        'status' => "fail",
                        'response' => 201,
                        'data' => "old-password dosen't match",
                    ];

                    return $this->response->setJSON($data);
                }
            } catch (Exception $ex) {
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'data' => "token not valid",
                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "password dosen't match",
            ];
            return $this->response->setJSON($data);
        }
    }


    public function requestOtp()
    {
        $email = trim((string) $this->request->getVar('email'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Please provide a valid email address.',
            ]);
        }

        $existingUser = $this->userModel->where('login_email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 409,
                'message' => 'An account with this email already exists.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = Time::now()->addMinutes(10)->toDateTimeString();

        $payload = [
            'email' => $email,
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'verified' => 0,
            'verification_token' => null,
        ];

        $existingOtp = $this->registrationOtpModel->where('email', $email)->first();
        if ($existingOtp) {
            $this->registrationOtpModel->update($existingOtp['id'], $payload);
        } else {
            $this->registrationOtpModel->insert($payload);
        }

        if (!$this->sendRegistrationOtp($email, $otp)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 500,
                'message' => 'Unable to send verification code. Please contact support.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'response' => 200,
            'message' => 'Verification code sent successfully.',
        ]);
    }

    public function verifyOtp()
    {
        $email = trim((string) $this->request->getVar('email'));
        $otp = trim((string) $this->request->getVar('otp'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Please provide a valid email address.',
            ]);
        }

        if (empty($otp) || !preg_match('/^\d{6}$/', $otp)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Please provide the 6-digit verification code.',
            ]);
        }

        $record = $this->registrationOtpModel->where('email', $email)->first();
        if (!$record) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 404,
                'message' => 'No verification request found for this email. Please request a new code.',
            ]);
        }

        if (!empty($record['verified']) && !empty($record['verification_token'])) {
            return $this->response->setJSON([
                'status' => 'success',
                'response' => 200,
                'data' => [
                    'verification_token' => $record['verification_token'],
                ],
                'message' => 'Email already verified.',
            ]);
        }

        $attempts = isset($record['attempts']) ? (int) $record['attempts'] : 0;
        if ($attempts >= 5) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 429,
                'message' => 'Too many incorrect attempts. Please request a new code.',
            ]);
        }

        $expiry = !empty($record['expires_at'])
            ? Time::createFromFormat('Y-m-d H:i:s', $record['expires_at'])
            : null;

        if ($expiry instanceof Time && $expiry->isBefore(Time::now())) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 410,
                'message' => 'The verification code has expired. Please request a new one.',
            ]);
        }

        if (empty($record['otp_hash']) || !password_verify($otp, $record['otp_hash'])) {
            $this->registrationOtpModel->update($record['id'], [
                'attempts' => $attempts + 1,
            ]);

            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 401,
                'message' => 'The verification code you entered is incorrect.',
            ]);
        }

        $verificationToken = $this->generateVerificationToken();

        $this->registrationOtpModel->update($record['id'], [
            'verified' => 1,
            'attempts' => 0,
            'verification_token' => $verificationToken,
            'otp_hash' => null,
            'expires_at' => Time::now()->addMinutes(30)->toDateTimeString(),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'response' => 200,
            'data' => [
                'verification_token' => $verificationToken,
            ],
            'message' => 'Email verified successfully.',
        ]);
    }

    public function usercheck($slag)
    {

        $userdetail = $this->userModel->where('slug', $slag)->first();
        $userdetailid = $this->userDetailModel->where('user_id', $userdetail->id)->first();
        return $userdetailid;
    }

    public function imgaeCheck($image, $path)
    {
        $newName = $image->getRandomName();
        $path = $path;
        $image->move($path, $newName);
        return $path . '/' . $newName;
    }


    public function regUser()
    {
        $login_email = $this->request->getVar('login_email') ?? $this->request->getVar('email');
        $login_mobile = $this->request->getVar('login_mobile') ?? $this->request->getVar('phone');
        $verificationToken = $this->request->getVar('verification_token');

        if (is_string($login_email)) {
            $login_email = trim($login_email);
        }

        if (is_string($login_mobile)) {
            $login_mobile = trim($login_mobile);
        }

        if (empty($login_email)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Email address is required for registration.',
            ]);
        }

        if (empty($login_mobile)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Phone number is required for registration.',
            ]);
        }

        if (empty($verificationToken)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Verification token is missing. Please verify your email again.',
            ]);
        }

        $otpRecord = $this->registrationOtpModel->where('email', $login_email)->first();
        if (!$otpRecord || empty($otpRecord['verified']) || $otpRecord['verification_token'] !== $verificationToken) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 403,
                'message' => 'Email verification failed. Please restart the registration process.',
            ]);
        }

        if (strcasecmp($otpRecord['email'], $login_email) !== 0) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 403,
                'message' => 'The verification token does not match the provided email.',
            ]);
        }

        $verificationExpiry = !empty($otpRecord['expires_at'])
            ? Time::createFromFormat('Y-m-d H:i:s', $otpRecord['expires_at'])
            : null;

        if ($verificationExpiry instanceof Time && $verificationExpiry->isBefore(Time::now())) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 410,
                'message' => 'Your verification session has expired. Please verify your email again.',
            ]);
        }

        $inputPass = $this->request->getVar('password');
        $inputRepass = $this->request->getVar('repassword') ?? $inputPass;

        if ($inputPass !== $inputRepass) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'PIN entries do not match.',
            ]);
        }

        if (!preg_match('/^\d{4}$/', (string) $inputPass)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'PIN must be exactly 4 digits.',
            ]);
        }

        $photo = $this->request->getFile('profile_photo');
        if (!$photo || !$photo->isValid()) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Please provide a valid profile photo.',
            ]);
        }

        if ($photo->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Profile photo must not exceed 2MB.',
            ]);
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $photoExtension = strtolower($photo->getExtension());
        if (!in_array($photoExtension, $allowedExtensions, true)) {
            return $this->response->setJSON([
                'status' => 'fail',
                'response' => 422,
                'message' => 'Profile photo must be a JPG, JPEG, PNG, or WEBP file.',
            ]);
        }

        $password = password_hash($inputPass, PASSWORD_DEFAULT);

        $bytes = random_bytes(5);
        $slug = bin2hex($bytes);
        $role_id = 3;
        $status = 1;

        $country_id = $this->request->getVar('country_id');
        if (empty($country_id)) {
            $country_id = 154;
        }

        $userData = array(
            "login_email" => $login_email,
            "login_mobile" => $login_mobile,
            "password" => $password,
            "slug" => $slug,
            "role_id" => $role_id,
            "status" => $status,
        );

        $validuserData = array(
            "login_email" => $login_email,
            "login_mobile" => $login_mobile,
            "password" => $inputPass,
            "repassword" => $inputRepass,
            "slug" => $slug,
            "role_id" => $role_id,
            "status" => $status,
        );
        $validdata = array(
            "first_name" => $this->request->getVar('first_name'),
            "last_name" => $this->request->getVar('last_name'),
            "id_type" => $this->request->getVar('id_type') ?: null,
            "id_number" => $this->request->getVar('id_number') ?: null,
            "country_id" => $country_id,
        );

        if ($this->validation->run($validuserData, 'reguser') && $this->validation->run($validdata, 'userDetail')) {

            $profileImagePath = $this->imgaeCheck($photo, 'image/passenger');
            if (empty($profileImagePath)) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'response' => 500,
                    'message' => 'Failed to store profile photo. Please try again.',
                ]);
            }

            $this->db->transStart();

            // Try to insert the user data
            $userid = $this->userModel->insert($userData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'data' => "User insertion failed",
                ];
                return $this->response->setJSON($data);
            }
            // If user detail validation passes, insert user details
            $data = array(
                "user_id" => $userid,
                "first_name" => $this->request->getVar('first_name'),
                "last_name" => $this->request->getVar('last_name'),
                "id_type" => $this->request->getVar('id_type') ?: null,
                "country_id" => $country_id,
                "id_number" => $this->request->getVar('id_number') ?: null,
                "address" => $this->request->getVar('address') ?: null,
                "city" => $this->request->getVar('city') ?: null,
                "zip_code" => $this->request->getVar('zip_code') ?: null,
                "image" => $profileImagePath,
            );

            $this->userDetailModel->insert($data);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'data' => "User detail insertion failed",
                ];
                return $this->response->setJSON($data);
            } else {
                // If everything succeeds, commit the transaction
                $this->db->transCommit();

                if (!empty($otpRecord['id'])) {
                    $this->registrationOtpModel->delete($otpRecord['id']);
                }

                $passengerProfile = $this->fetchPassengerById($userid);
                $userPayload = $this->formatPassengerResponse($passengerProfile);
                $token = $this->tokenJwt->generateToken($slug);

                $data = [
                    'status' => "success",
                    'response' => 200,
                    'token' => $token,
                    'data' => [
                        'token' => $token,
                        'user' => $userPayload,
                    ],
                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'status' => "fail",
                'response' => 422,
                'error' => $this->validation->getErrors(),
                'data' => "User validation failed",
            ];
            return $this->response->setJSON($data);
        }
    }



    public function loginsocial()
    {


        $appid = $this->request->getVar('appid');
        $first_name = $this->request->getVar('first_name');
        $last_name = $this->request->getVar('last_name');
        $email = $this->request->getVar('email');

        $getAppid = $this->socialsigninModel->where('appid', $appid)->where('email', $email)->first();

        if (!empty($getAppid)) {



            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)
                ->where('login_email', $email)
                ->where('login_mobile', $appid)
                ->first();


            if ($userdetail) {

                $token = $this->tokenJwt->generateToken($userdetail->slug);
                $passengerProfile = $this->fetchPassengerBySlug($userdetail->slug);
                $userPayload = $this->formatPassengerResponse($passengerProfile);

                $data = [
                    'status' => "success",
                    'response' => 200,
                    'token' => $token,
                    'data' => [
                        'token' => $token,
                        'user' => $userPayload,
                    ],
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'message' => "User  Not Found",
                    'status' => "fail",
                    'response' => 204,

                ];
                return $this->response->setJSON($data);
            }
        } else {



            $socialdatavalidation = [
                'appid' => $appid,
                'email' => $email,
            ];

            $socialdata = [
                'appid' => $appid,
                'email' => $email,
                'other' => $this->request->getVar('other'),
            ];

            if ($this->validation->run($socialdatavalidation, 'socialsingup')) {



                $this->socialsigninModel->insert($socialdata);


                $inputPass = $appid;
                $password = password_hash($inputPass, PASSWORD_DEFAULT);

                $bytes = random_bytes(5);
                $slug = bin2hex($bytes);
                $role_id = 3;
                $status = 1;

                $userData = array(
                    "login_email" => $email,
                    "login_mobile" => $appid,
                    "password" => $password,
                    "slug" => $slug,
                    "role_id" => $role_id,
                    "status" => $status,
                );

                if ($this->validation->run($userData, 'user')) {

                    $userid = $this->userModel->insert($userData);

                    $validdata = array(
                        "user_id" => $userid,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "id_type" => "passport",
                        "id_number" => $appid ?: null,
                        "country_id" => 14,
                    );

                    if ($this->validation->run($validdata, 'userDetail')) {
                        $data = array(
                            "user_id" => $userid,
                            "first_name" => $first_name,
                            "last_name" => $last_name,
                            "id_type" => "passport",
                            "id_number" => $appid ?: null,
                            "country_id" => 14,

                        );

                        $this->userDetailModel->insert($data);


                        $userdetail = $this->fetchPassengerById($userid);


                        if ($userdetail) {

                            $token = $this->tokenJwt->generateToken($slug);
                            $userPayload = $this->formatPassengerResponse($userdetail);

                            $data = [
                                'status' => "success",
                                'response' => 200,
                                'token' => $token,
                                'data' => [
                                    'token' => $token,
                                    'user' => $userPayload,
                                ],
                            ];

                            return $this->response->setJSON($data);
                        } else {
                            $data = [
                                'message' => "User  Not Found",
                                'status' => "fail",
                                'response' => 204,

                            ];
                            return $this->response->setJSON($data);
                        }
                    }
                } else {
                    $data = [
                        'status' => "fail",
                        'response' => 404,
                        'error' => $this->validation->getErrors(),   //$validation->listErrors()
                        'data' => "Registration fail",
                    ];
                    return $this->response->setJSON($data);
                }
            } else {

                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'error' => $this->validation->getErrors(),   //$validation->listErrors()
                    'data' => "Registration fail",
                ];
                return $this->response->setJSON($data);
            }
        }
    }


    public function checkEmail()
    {
        $email = $this->request->getVar('login_email');

        $emailDetail = $this->userModel->where('login_email', $email)->first();

        if (empty($emailDetail)) {
            $data = [
                'message' => "No Email address found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "Email address found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }



    public function checkMobile()
    {
        $mobile = $this->request->getVar('login_mobile');

        $emailDetail = $this->userModel->where('login_mobile', $mobile)->first();

        if (empty($emailDetail)) {
            $data = [
                'message' => "No Mobile Number found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "Mobile Number found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }


    public function checkIdNumber()
    {
        $idnumber = $this->request->getVar('id_number');

        $idDetail = $this->userDetailModel->where('id_number', $idnumber)->first();

        if (empty($idDetail)) {
            $data = [
                'message' => "No Id Number found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "ID Number found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }
}
