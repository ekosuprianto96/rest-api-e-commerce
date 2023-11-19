<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\ReportOrder;
use App\Http\Controllers\Controller;
use App\Models\DetailToko;
use App\Models\Order;
use App\Models\Pendapatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(ReportOrder $chart) {

        $pendapatan = new Order();
        // dd($pendapatan->get_pendapatan_toko());
        return view('dashboard', [
            'chart' => $chart->build(), 
            'total_pendapatan' => Pendapatan::where('status', 'SUCCESS')->sum('pendapatan'),
            'total_toko' => DetailToko::all()->count()
        ]);
    }
}
