<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MsBanner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function getBanner() {
        $banner = MsBanner::where('an', 1)->get();

        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get banners.',
            'detail' => $banner
        ], 200);
    }
}
