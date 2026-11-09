<?php

namespace App\Http\Controllers;

use App\Models\Bus;

class BusController extends Controller
{
    public function display()
    {
        $amlv_bus = Bus::get();
        //Bus::all();
        //Bus::get();
        //Product::orderBy('bus_number', 'ASC')->get();
        //Bus::where('seating_capacity', 'Accesories')->get();
        //Bus::where('seating_capacity', '>', 50)->get();
        //Bus::limit(3)->get();
        //Bus::where('seating_capacity', '>', 50)->orderBy('bus_number', 'ASC')->get();
        return view('bus', compact('amlv_bus'));
    }
}

