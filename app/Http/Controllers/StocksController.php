<?php

namespace App\Http\Controllers;

class StocksController extends Controller
{
    public function index()
    {
        return view('stocks.index');
    }
}