<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Modules\Trip\Models\StuffassignModel;

class DriverLocation extends BaseController
{
    protected $stuffassignModel;

    public function __construct()
    {
        $this->stuffassignModel = new StuffassignModel();
    }

    // Display the map
    public function index($trip_id = null)
    {
        if ($trip_id) {
            $drivers = $this->stuffassignModel
                ->where('trip_id', $trip_id)
                ->where('latitude IS NOT NULL')
                ->where('longitude IS NOT NULL')
                ->findAll();
        } else {
            $drivers = $this->stuffassignModel
                ->where('latitude IS NOT NULL')
                ->where('longitude IS NOT NULL')
                ->findAll();
        }

        //return view('admin/driver_map', ['drivers' => $drivers]);
         return view('template/admin/driver_map', $drivers);
    }

    // Return live JSON data for all drivers
    public function getDriverLocations()
    {
        $drivers = $this->stuffassignModel
            ->select('stuffassigns.id, stuffassigns.trip_id, stuffassigns.employee_id, stuffassigns.latitude, stuffassigns.longitude, stuffassigns.status, stuffassigns.updated_at')
            ->where('latitude IS NOT NULL')
            ->where('longitude IS NOT NULL')
            ->findAll();

        return $this->response->setJSON($drivers);
    }
}
