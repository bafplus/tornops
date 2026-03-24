<?php

namespace App\Http\Controllers;

class TravelController extends Controller
{
    public function index()
    {
        return view('travel.index');
    }
}