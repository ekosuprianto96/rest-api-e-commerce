<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DetailToko;
use App\Models\Saldo;
use App\Models\SaldoRefaund;
use App\Models\SaldoToko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterTokoController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'nama_toko' => 'required|unique:detail_toko|string|min:6|max:18',
            'alamat_toko' => 'required|string|min:6|max:100',
            'deskripsi_toko' => 'required|string|min:20|max:255',
            'perjanjian' => 'required'
        ]);

        try {
            $request = $request->input();
            $toko = new DetailToko();
            $toko->uuid_user = Auth::user()->uuid;
            $toko->nama_toko = $request['nama_toko'];
            $toko->alamat_toko = $request['alamat_toko'];
            $toko->deskripsi_toko = $request['deskripsi_toko'];
            $toko->status_toko = 'PENDING';

            if($toko->save()) {
                SaldoToko::create([
                    'kode_toko' => $toko->kode_toko
                ]);
                SaldoRefaund::create([
                    'kode_toko' => $toko->kode_toko
                ]);
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'detail' => $toko,
                    'message' => 'Yeah!, Kamu Berhasil Mendaftar Sebagai Seller, Silahkan Tunggu Konfirmasi Dari Admin 1 x 24 Jam.'
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'detail' => $toko,
                    'message' => 'Maaf!, Sepertinya Terjadi Kesalahan Saat Registrasi, Silahkan Coba Kembali.'
                ], 400);
            }
        }catch(\Exception $err) {
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => $err->getMessage().'-'.$err->getLine()
            ], 500);
        }
    }
}
