<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use App\Models\NotifikasiPengguna;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;

class SettingWebsiteController extends Controller
{
    public function getSettings()
    {
        $setttings = SettingWebsite::first();
        $notifikasi = NotifikasiPengguna::where('an', 1)->get();
        $artikel = KategoriArtikel::with(['artikel'])->get()->take(3);

        $setttings['notifikasi'] = $notifikasi;
        $setttings['articles'] = $artikel;
        $setttings['manifest'] = asset('assets/admin/js/manifest.json');

        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get settings.',
            'detail' => $setttings
        ], 200);
    }
}
