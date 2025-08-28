<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = [
            [
                'name'    => 'KFC Pavilion Kuala Lumpur',
                'address' => '168, Jalan Bukit Bintang, 55100 Kuala Lumpur, Wilayah Persekutuan',
                'city'    => 'Kuala Lumpur',
                'state'   => 'WP Kuala Lumpur',
                'hours'   => '10:00 – 22:00',
            ],
            [
                'name'    => 'KFC Sunway Pyramid',
                'address' => '3, Jalan PJS 11/15, Bandar Sunway, 47500 Subang Jaya, Selangor',
                'city'    => 'Subang Jaya',
                'state'   => 'Selangor',
                'hours'   => '10:00 – 22:00',
            ],
            [
                'name'    => 'KFC Jalan Genting Klang',
                'address' => ' 2, Jalan 2/50C, Off, Jalan Genting Kelang, Taman Setapak Indah Jaya, 53300, Federal Territory of Kuala Lumpur',
                'city'    => 'Kuala Lumpur',
                'state'   => 'WP Kuala Lumpur',
                'hours'   => '10:00 – 22:00',
            ],
            // ... add more outlets
        ];

        // point to your view name
        return view('user.location', compact('locations'));
    }
}
