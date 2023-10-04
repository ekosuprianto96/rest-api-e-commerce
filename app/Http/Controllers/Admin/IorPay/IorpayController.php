<?php

namespace App\Http\Controllers\Admin\IorPay;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\IorPay;
use App\Models\TrxIorPay;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IorpayController extends Controller
{
    public function permintaan_topup() {
        $trx_topup = TrxIorPay::with(['iorpay'])->whereRaw("status_trx = '0' and jenis_pembayaran = 'manual'")->latest()->paginate(50);

        return view('admin.iorpay.konfirmasi_topup', compact('trx_topup'));
    }

    public function konfirmasi(Request $request, $no_trx) {
        try {
            // ambil semua transaksi topup dengan metode pembayaran manual yang belum di konfirmasi
            $trx_topup = TrxIorPay::where([
                'status_trx' => 0,
                'jenis_pembayaran' => 'manual',
                'no_trx' => $no_trx
            ])->first();
            
            if(empty($trx_topup)) {
                Alert::error('Terjadi Kesalahan', 'Transaksi Topup Tidak Ditemukan.');
                return redirect()->back();
            }

            // check status trx
            if($trx_topup->status_trx != 0) {
                Alert::error('Terjadi Kesalahan', 'Transaksi Topup Sudah Dikonfirmasi Dengan Status '.$trx_topup->status_trx.'.');
                return redirect()->back();
            }

            // Lakukan penambahan saldo ke iorpay user
            $iorpay_user = IorPay::where([
                'kode_pay' => $trx_topup->kode_pay,
                'uuid_user' => $trx_topup->uuid_user,
                'status_pay' => 1
            ])->first();
            
            // cek biaya admin
            $biaya_admin = intval($trx_topup->biaya_adm);
            $total_saldo = (float) $trx_topup->total_fixed - $biaya_admin;
            $iorpay_user->saldo += $total_saldo;
            
            if($iorpay_user->save()) {
                $trx_topup->status_trx = 'SUCCESS';
                $trx_topup->save();
            }

            Alert::success('Sukses!', 'Berhasil Konfirmasi Pembayaran Topup User '.$iorpay_user->user->full_name.'.');
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
