<?php

namespace Modules\Paymethod\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Paymethod\Models\PaystackModel;
use Modules\Ticket\Models\TicketModel;
use Modules\Ticket\Models\PartialpaidModel;
use Modules\User\Models\UserModel;

class Paystack extends BaseController
{
    use ResponseTrait;

    protected $payStackModel;
    protected $ticketModel;
    protected $partialpaidModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->payStackModel = new PaystackModel();
        $this->ticketModel = new TicketModel();
        $this->partialpaidModel = new PartialpaidModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Initialize: POST JSON { email, amount, booking_id, cus_id }
     * amount must be in kobo (i.e. NGN * 100)
     */
    public function initialize()
    {
        $req = $this->request->getJSON(true);

        $email = $req['email'] ?? null;
        $amount = $req['amount'] ?? null; // already in kobo
        $bookingId = $req['booking_id'] ?? null;
        $cusId = $req['cus_id'] ?? null;
        $currency = $req['currency'] ?? 'NGN';

        if (!$email || !$amount || !$bookingId || !$cusId) {
            return $this->failValidationErrors('Missing required fields: email, amount, booking_id, cus_id');
        }

        // Hardcoded Paystack credentials (replace with your actual keys)
        $environment = env('PAYSTACK_ENVIRONMENT', '0');
        $keys = [
            'secret_key' => $environment === 'live' ? 'sk_live_abcdef1234567890' : 'sk_test_320b052781a8e3a6df876fc3a9e325f1e9c0cbec',
            'public_key' => $environment === 'live' ? 'pk_live_abcdef1234567890' : 'pk_test_1e6d7eee29170399ea8237bef435dcdc8feb2f7f',
        ];

        // Add metadata as JSON encoded string for Paystack
        $metadata = [
            'booking_id' => $bookingId,
            'cus_id' => $cusId
        ];
$callbackUrl = base_url('modules/api/v1/paymethods/paystack/callback?booking_id='.$bookingId.'&cus_id='.$cusId);
	//$callbackUrl = base_url("modules/api/v1/paymethods/paystack/callback?booking_id={$bookingId}&cus_id={$cusId}");
        $payload = [
            'email' => $email,
            'amount' => (int)$amount,
            'currency' => $currency,
            'metadata' => $metadata, // Pass as array, Paystack API will handle JSON encoding
            'callback_url'=> $callbackUrl
        ];

        // Call Paystack initialize endpoint
        $ch = curl_init("https://api.paystack.co/transaction/initialize");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$keys['secret_key']}",
            "Content-Type: application/json"
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $this->failServerError('Curl error: ' . $err);
        }

        $result = json_decode($resp, true);

        if (!isset($result['status']) || $result['status'] !== true) {
            return $this->fail('Paystack initialize failed: ' . ($result['message'] ?? 'Unknown'));
        }

        // Return authorization_url and reference to frontend
        return $this->respond([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Manual verify (GET): /modules/api/v1/paymethods/paystack/verify/{reference}
     */
    public function verify($reference = null)
    {
        if (!$reference) {
            return $this->failValidationErrors('Reference required');
        }

        $res = $this->verifyAndProcessTransaction($reference);
        if ($res['status'] === 'success') {
            return $this->respond($res);
        } else {
            return $this->fail($res['message'], 400);
        }
    }

    /**
     * Callback (redirect endpoint) — Paystack will redirect user here after payment
     * Example redirect url: /modules/api/v1/paymethods/paystack/callback?reference=...&tran_id=...&cus_id=...&callback_url=...
     */
    public function callback()
    {
        $reference = $this->request->getGet('reference');
        $bookingId = $this->request->getGet('booking_id');
        $cusId = $this->request->getGet('cus_id');
        $callbackUrl = $this->request->getGet('callback_url');
        $frontendUrl = "https://etransport.ng/booking-success";

        // Fallback redirect if callbackUrl not provided
     /*   if (!$callbackUrl) {
            $callbackUrl = base_url();
        }*/

           if (!$reference) {
            return redirect()->to($frontendUrl . "?status=failed&message=" . urlencode("Missing required params"));
        }
        



    $verifyUrl = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$paystackSecret = 'sk_test_320b052781a8e3a6df876fc3a9e325f1e9c0cbec';

if (!$paystackSecret) {
    log_message('error', 'Paystack secret key not set');
    return redirect()->to($frontendUrl . "?status=failed&message=" . urlencode("Server misconfiguration: missing secret key"));
}
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$paystackSecret}"
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($resp, true);

    log_message('debug', 'Paystack verify response: ' . $resp);

    // Guard: invalid JSON or empty
    if (!$result) {
        return redirect()->to($frontendUrl . "?status=failed&message=" . urlencode("Invalid response from Paystack"));
    }

    // Guard: status not true
    if (!isset($result['status']) || $result['status'] !== true) {
        $msg = $result['message'] ?? "Paystack verification failed";
        return redirect()->to($frontendUrl . "?status=failed&message=" . urlencode($msg));
    }

    // Guard: no data key
    if (!isset($result['data'])) {
        return redirect()->to($frontendUrl . "?status=failed&message=" . urlencode("No transaction data from Paystack"));
    }

    $paymentData = $result['data'];
    $amount      = $paymentData['amount'] ?? 0;
    $status      = $paymentData['status'] ?? 'failed';
if ($status === 'success') {
    // ✅ Update booking as paid
    $this->ticketModel->where('booking_id', $bookingId)->set([
        'payment_status' => 'paid',
        'pay_method_id' => '10',
        'updated_at'     => date('Y-m-d H:i:s')
    ])->update();

    $message = "Payment verified successfully";

    return redirect()->to(
        $frontendUrl . "?reference={$reference}&booking_id={$bookingId}&cus_id={$cusId}&status={$status}&message=" . urlencode($message)
    );
}
}

    /**
     * Webhook (Paystack posts events here). Paystack sends X-Paystack-Signature
     */
    public function webhook()
    {
        $input = @file_get_contents("php://input");
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

        // Hardcoded Paystack credentials (replace with your actual keys)
        $environment = env('PAYSTACK_ENVIRONMENT', '0');
        $keys = [
            'secret_key' => $environment === 'live' ? 'sk_live_abcdef1234567890' : 'sk_test_320b052781a8e3a6df876fc3a9e325f1e9c0cbec',
            'public_key' => $environment === 'live' ? 'pk_live_abcdef1234567890' : 'pk_test_1e6d7eee29170399ea8237bef435dcdc8feb2f7f',
        ];

        // Verify signature
        if ($signature !== hash_hmac('sha512', $input, $keys['secret_key'])) {
            return $this->fail('Invalid signature', 401);
        }

        $payload = json_decode($input, true);
        if (isset($payload['event']) && $payload['event'] === 'charge.success') {
            // Read metadata (Paystack returns metadata as object or string)
            $meta = $payload['data']['metadata'] ?? null;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            $ref = $payload['data']['reference'] ?? null;
            $bookingId = $meta['booking_id'] ?? null;
            $cusId = $meta['cus_id'] ?? null;

            $res = $this->verifyAndProcessTransaction($ref, $bookingId, $cusId, null, true);

            // Return response for webhook
            return $this->respond($res);
        }

        return $this->respond(['status' => 'ignored', 'message' => 'Unhandled event']);
    }

    /**
     * Shared verification & processing
     * If $isWebhook === true => don't send emails or redirect actions that expect user
     */
    private function verifyAndProcessTransaction($reference, $tranId = null, $cusId = null, $callbackUrl = null, $isWebhook = false)
    {
        // Minimal validation
        if (!$reference) {
            return ['status' => 'failed', 'message' => 'Missing reference'];
        }

        // Hardcoded Paystack credentials (replace with your actual keys)
        $environment = env('PAYSTACK_ENVIRONMENT', '0');
        $keys = [
            'secret_key' => $environment === 'live' ? 'sk_live_abcdef1234567890' : 'sk_test_320b052781a8e3a6df876fc3a9e325f1e9c0cbec',
            'public_key' => $environment === 'live' ? 'pk_live_abcdef1234567890' : 'pk_test_1e6d7eee29170399ea8237bef435dcdc8feb2f7f',
        ];

        // Verify with Paystack
        $ch = curl_init("https://api.paystack.co/transaction/verify/" . urlencode($reference));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$keys['secret_key']}"]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['status' => 'failed', 'message' => 'Curl error: ' . $err];
        }

        $result = json_decode($resp, true);
        if (!isset($result['status']) || $result['status'] !== true || ($result['data']['status'] ?? '') !== 'success') {
            return ['status' => 'failed', 'message' => 'Verification failed'];
        }

        // Now check booking/ticket
        if (!$tranId) {
            // Attempt to position booking id from metadata if not provided
            $meta = $result['data']['metadata'] ?? null;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            $tranId = $tranId ?? ($meta['booking_id'] ?? null);
            $cusId = $cusId ?? ($meta['cus_id'] ?? null);
        }

        if (!$tranId) {
            return ['status' => 'failed', 'message' => 'Missing booking id'];
        }

        $ticketInfo = $this->ticketModel->where('booking_id', $tranId)->first();
        if (!$ticketInfo) {
            return ['status' => 'failed', 'message' => 'Invalid booking'];
        }

        if ($ticketInfo->payment_status === 'paid') {
            return ['status' => 'failed', 'message' => 'Already paid'];
        }

        // Handle round trips / multiple tickets
        $ticketsToProcess = [$ticketInfo];
        if (!empty($ticketInfo->round_id)) {
            $roundTickets = $this->ticketModel
                ->where('round_id', $ticketInfo->round_id)
                ->where('cancel_status', 0)
                ->where('payment_status', 'unpaid')
                ->findAll();
            if (!empty($roundTickets)) {
                $ticketsToProcess = $roundTickets;
            }
        }

        // Process DB updates in transaction
        $this->db->transStart();

        foreach ($ticketsToProcess as $tk) {
            $updateTicketData = [
                "pay_type_id" => 3,
                "pay_method_id" => 999, // Paystack id in your system (adjust if different)
                "payment_status" => "paid",
                "payment_detail" => "Paystack Payment"
            ];
            $ok1 = $this->ticketModel->where('booking_id', $tk->booking_id)->set($updateTicketData)->update();

            if (!$ok1) {
                $this->db->transRollback();
                return ['status' => 'failed', 'message' => 'Ticket update failed'];
            }

            // Update partial paid table record if exists
            $updatePaymentData = [
                "paidamount" => $tk->paidamount,
                "pay_type_id" => 3,
                "pay_method_id" => 2,
                "payment_detail" => "Paystack Payment"
            ];
            $ok2 = $this->partialpaidModel->where('booking_id', $tk->booking_id)->set($updatePaymentData)->update();

            if (!$ok2) {
                $this->db->transRollback();
                return ['status' => 'failed', 'message' => 'Payment update failed'];
            }

            // Account transaction helper
            if (function_exists('accoutTranjection')) {
                accoutTranjection("income", "Ticket Booking (" . $tk->booking_id . ")", $tk->paidamount, $tk->passanger_id);
            }
        }

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return ['status' => 'failed', 'message' => 'DB transaction failed'];
        }

        // Send ticket email on normal callback only (not webhook)
        if (!$isWebhook && function_exists('sendTicket')) {
            $ticketmailLibrary = new \Ticketmail();
            $passenger_email = $this->userModel->select("login_email")->where('id', $ticketInfo->passanger_id)->first();
            if ($passenger_email) {
                $emaildata = $ticketmailLibrary->getticketEmailData($tranId);
                sendTicket($passenger_email->login_email, $emaildata);
            }
        }

        return ['status' => 'success', 'message' => 'Payment successful', 'booking_id' => $tranId];
    }
}