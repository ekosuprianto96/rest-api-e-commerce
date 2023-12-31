<?php

namespace App\Http\Controllers\Admin\IorPay;

use App\Models\IorPay;
use App\Models\TrxIorPay;
use Illuminate\Http\Request;
use App\Models\NotifikasiAdmin;
use App\Models\TransaksiAccount;
use App\Models\TrxWithdrawIorPay;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class IorpayController extends Controller
{
    public function permintaan_topup() {
        NotifikasiAdmin::where([
            'type' => 'konfirmasi-topup',
            'status_read' => 0
        ])->update([
            'status_read' => 1
        ]);
        return view('admin.iorpay.konfirmasi_topup');
    }

    public function konfirmasi(Request $request) {
        try {
            // ambil semua transaksi topup dengan metode pembayaran manual yang belum di konfirmasi
            $trx_topup = TrxIorPay::where([
                'status_trx' => 0,
                'jenis_pembayaran' => 'manual',
                'no_trx' => $request['no_trx']
            ])->first();
            
            if(empty($trx_topup)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon Maaf, Transaksi Topup Tidak Ditemukan.',
                    'detail' => []
                ]);
            }

            // check status trx
            if($trx_topup->status_trx != 0) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Transaksi Topup Sudah Dikonfirmasi Dengan Status '.$trx_topup->status_trx.'.',
                    'detail' => []
                ]);
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

                $param_trx_account = [
                    'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                    'uuid_user' => $trx_topup->uuid_user,
                    'type_payment' => 'manual',
                    'method' => $trx_topup->jenis_pembayaran,
                    'jns_payment' => 'DEBIT',
                    'biaya_trx' => $trx_topup->total_fixed,
                    'total' => $trx_topup->total_fixed,
                    'no_refrensi' => $trx_topup->no_trx,
                    'kode_unique' => $trx_topup->kode_unique,
                    'keterangan' => 'Topup Saldo'
                ];

                TransaksiAccount::create($param_trx_account);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Konfirmasi Pembayaran Topup User '.$iorpay_user->user->full_name.'.',
                'detail' => $iorpay_user
            ]);
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function konfirmasi_topup_data(Request $request) {
        try {

            $trx_topup = TrxIorPay::with(['iorpay'])->whereRaw("status_trx = '0' and jenis_pembayaran = 'manual'")->latest()->get();

            $data = DataTables::of($trx_topup)
                    ->addColumn('no_trx', function($list) {
                        return $list->no_trx;
                    })
                    ->addColumn('user', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('total_fixed', function($list) {
                        return 'Rp. '.number_format($list->total_fixed, 0);
                    })
                    ->addColumn('total_trx', function($list) {
                        return 'Rp. '.number_format($list->total_trx, 0);
                    })
                    ->addColumn('biaya_adm', function($list) {
                        return 'Rp. '.number_format($list->biaya_adm, 0);
                    })
                    ->addColumn('kode_unique', function($list) {
                        return ($list->kode_unique ? $list->kode_unique : '-');
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.iorpay.detail', $list->no_trx).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                    <a href="javascript:void(0)" onclick="konfirmasi_topup('."'".$list->no_trx."'".')" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Konfirmasi</a>
                                </div>';
                    })->rawColumns(['no_trx', 'user', 'total_fixed', 'total_trx', 'biaya_adm', 'kode_unique', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function permintaan_withdraw() {
        $trx_withdraw = TrxWithdrawIorPay::where([
            'status_withdraw' => 0,
        ])->paginate(20);

        NotifikasiAdmin::where([
            'type' => 'permintaan-withdraw',
            'status_read' => 0
        ])->update([
            'status_read' => 1
        ]);

        return view('admin.iorpay.konfirmasi_withdraw', compact('trx_withdraw'));
    }

    public function konfirmasi_withdraw(Request $request, $no_trx) {
        try {
            DB::beginTransaction();
            $trx_withdraw = TrxWithdrawIorPay::where('no_trx', $no_trx)->first();

            if(empty($trx_withdraw)) {
                Alert::error('Maaf!', 'Sepertinya No Transaksi Tidak Ditemukan.');

                return redirect()->back();
            }

            if($trx_withdraw->status_withdraw == 1) {
                Alert::error('Maaf!', 'Transaksi Sudah Dikonfirmasi dengan status SUCCESS');

                return redirect()->back();
            }

            if($trx_withdraw->status_withdraw == 2) {
                Alert::error('Maaf!', 'Transaksi Sudah Dikonfirmasi dengan status REJECT, Silahkan Batal Reject Terlebih Dahulu.');

                return redirect()->back();
            }

            $trx_account = new TransaksiAccount();
            if($trx_withdraw->total_withdraw > $trx_account->get_saldo('manual', $request['account'])) {
                Alert::error('Maaf!', 'Saldo Bank Yang Di Pilih Tidak Mencukupi, Silahkan Pilih Bank Lain.');

                return redirect()->back();
            }

            $trx_withdraw->status_withdraw = 1;
            $trx_withdraw->save();

            $trx_pay = TrxIorPay::where('no_trx', $trx_withdraw->no_trx_pay)->first();
            $trx_pay->status_trx = 'SUCCESS';
            $trx_pay->save();

            $pay = IorPay::where('kode_pay', $trx_withdraw->kode_pay)->first();
            $pay->saldo = ($pay->saldo - $trx_withdraw->total_withdraw);
            $pay->save();

            $param_trx_account = [
                'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                'uuid_user' => $trx_pay->uuid_user,
                'type_payment' => 'manual',
                'method' => $request['account'],
                'jns_payment' => 'CREDIT',
                'biaya_trx' => $trx_pay->total_fixed,
                'total' => $trx_pay->total_fixed,
                'no_refrensi' => $trx_pay->no_trx,
                'keterangan' => 'Penarikan Saldo'
            ];

            TransaksiAccount::create($param_trx_account);

            Alert::success('Sukses!', 'Transaksi Berhasil Dikonfirmasi.');
            DB::commit();
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
