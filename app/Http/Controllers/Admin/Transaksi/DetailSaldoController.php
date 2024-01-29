<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Http\Request;
use App\Models\TransaksiAccount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class DetailSaldoController extends Controller
{
    public function index(Request $request) {
        $data = null;
        if(isset($request->payment)) {
            $data['transaksi'] = TransaksiAccount::where([
                'type_payment' => ($request->payment == 'gateway' ? 'gateway' : 'manual'),
                'method' => ($request->payment == 'gateway' ? ['bank_transfer'] : $request->payment)
            ])->latest()->paginate(100);

            $data['payment'] = $request->payment;
            $trx = new TransaksiAccount();
            if($request->payment == 'gateway') {
                $data['total'] = $trx->get_saldo();
            }else {
                $data['total'] = $trx->get_saldo('manual', $request->payment);
            }
        }
        return view('admin.transaksi.detail_saldo', compact('data'));
    }

    public function penarikan_dana(Request $request) {
        try {
            DB::beginTransaction();
            
            $transaksi = TransaksiAccount::where('type_payment', 'gateway')
                            ->update([
                                'type_payment' => 'manual',
                                'method' => $request->payment,
                                'keterangan' => 'Mutasi Saldo',
                                'created_at' => now()->format('Y-m-d H:i:s')
                            ]);
            if($transaksi) {
                DB::commit();
                return redirect()->back();
            }else {
                Alert::error('Error', 'Data Transaksi Tidak Ditemukan/Saldo 0');
                return redirect()->back();
            }

        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getError($err);
        }
    }
    
}
