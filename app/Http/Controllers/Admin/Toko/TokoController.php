<?php

namespace App\Http\Controllers\Admin\Toko;

use App\Models\DetailToko;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\API\Handle\ErrorController;

class TokoController extends Controller
{
    public function index(Request $request) {

        $where = '1=1 ';
        if($request->nama_toko) {
            $where .= "and nama_toko like '%".$request->nama_toko."%' ";
        }
        // dd($request->all());
        if(isset($request->status)) {
            $where .= "and status_toko = '".$request->status."' ";
        }

        $toko = DetailToko::selectRaw('detail_toko.*')->whereRaw($where)->paginate(20);

        return view('admin.toko.index', compact('toko'));
    }

    public function konfirmasi() {

        $toko = DetailToko::where('status_toko', 'PENDING')->latest()->paginate(20);

        return view('admin.toko.konfirmasi', compact('toko'));
    }

    public function konfirmasi_toko(Request $request, $kode_toko) {

        try {
            $toko = DetailToko::findOrFail($kode_toko);

            if(empty($toko)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya Toko Tidak Ditemukan');

                return redirect()->back();
            }

            if($toko->status_toko == 'APPROVED') {

                Alert::error('Mohon Maaf', 'Maaf, Toko Sudah Terkonfirmasi.');

                return redirect()->back();
            }   

            $toko->status_toko = 'APPROVED';
            $toko->save();

            Alert::success('Sukses!', 'Konfirmasi Toko '.$toko->nama_toko.' Berhasil.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function reject(Request $request, $kode_toko) {

        try {
            $toko = DetailToko::findOrFail($kode_toko);

            if(empty($toko)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya Toko Tidak Ditemukan');

                return redirect()->back();
            }

            if($toko->status_toko == 'REJECT') {

                Alert::error('Mohon Maaf', 'Maaf, Toko Sudah Direject.');

                return redirect()->back();
            }   

            if($request->batal) {
                $toko->status_toko = 'PENDING'; 
            }else {
                $toko->status_toko = 'REJECT';
            }
            $toko->save();

            Alert::success('Sukses!', 'Toko '.$toko->nama_toko.' Berhasil Direject.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function batal_konfirmasi(Request $request, $kode_toko) {

        try {
            $toko = DetailToko::findOrFail($kode_toko);

            if(empty($toko)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya Toko Tidak Ditemukan');

                return redirect()->back();
            }

            if($toko->status_toko == 'PENDING' || $toko->status_toko == 'REJECT') {

                Alert::error('Mohon Maaf', 'Maaf, Toko Belum Dikonfirmasi.');

                return redirect()->back();
            }   

            $toko->status_user = 0;
            $toko->save();

            Alert::success('Sukses!', 'Konfirmasi Toko '.$toko->nama_toko.' Berhasil Dibatalkan.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function view_toko($kode_toko) {
        
        $toko = DetailToko::findOrFail($kode_toko);

        if(!isset($toko)) {
            Alert::warning('Tidak Ditemukan!', 'Maaf, Toko Tidak Ditemukan');

            return redirect()->back();
        }

        return view('admin.toko.view', compact('toko'));
    }
}
