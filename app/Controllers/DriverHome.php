<?php

namespace App\Controllers;

use App\Libraries\Chart;
use Modules\Trip\Models\TripModel;
use Modules\Employee\Models\EmployeeModel;
use Modules\Trip\Models\StuffassignModel;
use App\Libraries\Rolepermission;

class DriverHome extends BaseController
{
    protected $tripModel;
    protected $employeeModel;
    protected $stuffassignModel;
    protected $rolepermissionLibrary;

    public function __construct()
    {
        $this->tripModel = new TripModel();
        $this->employeeModel = new EmployeeModel();
        $this->stuffassignModel = new StuffassignModel();  
        $this->rolepermissionLibrary = new Rolepermission();
    }

    public function index()
    {
        return redirect()->route('driver-home');
    }

    public function driver()
    {
        $charrLibrary = new Chart();
        $todaytrip = $charrLibrary->totalTripToday();
        $totalBooking = $charrLibrary->totalBooking();
        $totalMoney = $charrLibrary->totalMoney();
        $totalPassanger = $charrLibrary->totalPassanger();

        $data['trip'] = $this->tripModel->select('trips.id as tripid, trips.*, fleets.*, schedules.*, vehicles.*')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->orderBy('trips.id', 'desc')
            ->findAll();

        $employeeId = $this->session->get('employee_id');
        $data['get_employee_type'] = $employeeId ? $this->employeeModel->find($employeeId) : null;
        $data['employee_type'] = $data['get_employee_type']->employeetype_id ?? null;

        $current_date = date('Y-m-d');
        $data['driverAssignedListToday'] = [];

        if ($data['employee_type'] == 1 && $employeeId) {
            $data['driverAssignedListToday'] = $this->stuffassignModel
                ->select('stuffassigns.id as assign_id, stuffassigns.latitude, stuffassigns.longitude, stuffassigns.status, trips.id as trip_id, trips.trip_title, l1.name as pickup_location_name, l2.name as drop_location_name')
                ->join('trips', 'trips.id = stuffassigns.trip_id')
                ->join('locations l1', 'trips.pick_location_id = l1.id', 'left')
                ->join('locations l2', 'trips.drop_location_id = l2.id', 'left')
                ->where('employee_type', 1)
                ->where('employee_id', $employeeId)
                ->where("DATE(start_date) <=", $current_date)
                ->where("DATE(end_date) >=", $current_date)
                ->where('is_approved', 1)
                ->findAll();
        }

        $data['todaytrip'] = $todaytrip;
        $data['todaybooking'] = $totalBooking;
        $data['totalmoney'] = $totalMoney;
        $data['totalpassanger'] = $totalPassanger;

        $data['read_driver_report'] = $this->rolepermissionLibrary->read("driver_report");
        $data['create_driver_dashboard'] = $this->rolepermissionLibrary->create("driver_dashboard");
        $data['read_trip_list'] = $this->rolepermissionLibrary->read("trip_list");

        $data['module'] = lang("Localize.dashboard");
        $data['title']  = lang("Localize.dashboard");

        return view('template/admin/driver_welcome', $data);
    }

    // 🗺️ View all drivers’ locations
    public function driverLocations()
    {
        $data['driverLocations'] = $this->stuffassignModel
            ->select('stuffassigns.id, stuffassigns.employee_id, stuffassigns.latitude, stuffassigns.longitude, stuffassigns.status, employees.first_name, employees.last_name')
            ->join('employees', 'employees.id = stuffassigns.employee_id', 'left')
            ->where('stuffassigns.latitude IS NOT NULL')
            ->where('stuffassigns.longitude IS NOT NULL')
            ->findAll();

        return view('template/admin/driver_locations_map', $data);
    }

    // 🧩 API: return all drivers’ live locations as JSON (for auto-refresh)
public function fetchDriverLocations()
{
    $drivers = $this->stuffassignModel
        ->select('stuffassigns.id, stuffassigns.employee_id, stuffassigns.latitude, stuffassigns.longitude, stuffassigns.status, employees.first_name, employees.last_name')
        ->join('employees', 'employees.id = stuffassigns.employee_id', 'left')
        ->where('stuffassigns.latitude IS NOT NULL')
        ->where('stuffassigns.longitude IS NOT NULL')
        ->findAll();

    // Return only raw array (no wrapper object)
    return $this->response->setJSON($drivers);
}


    // 🚗 View single driver on map
    public function viewDriver($employee_id)
    {
        $driver = $this->stuffassignModel
            ->select('stuffassigns.*, employees.first_name, employees.last_name')
            ->join('employees', 'employees.id = stuffassigns.employee_id', 'left')
            ->where('stuffassigns.employee_id', $employee_id)
            ->orderBy('stuffassigns.updated_at', 'DESC')
            ->first();

        if (!$driver) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Driver not found");
        }

        $data['driver'] = $driver;
        return view('template/admin/single_driver_map', $data);
    }
}
