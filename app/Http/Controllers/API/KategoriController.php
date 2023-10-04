<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function show() {
        try {

            $kategori = Kategori::all()->where('an', 1);
            foreach($kategori as $kt) {
                $kt->image = asset($kt->image);
            }
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get produk',
                'detail' => $kategori
            ], 200);
            
        }catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
