<?php

namespace App\Http\Controllers\Frontend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($username)
    {
        return view('frontend.dashboard.index');
    }
    public function keranjang($username)
    {
        return view('frontend.dashboard.keranjang');
    }
}
