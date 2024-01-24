<?php

namespace App\Http\Controllers\Admin\Transaksi\Withdraw;

use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Http\Request;
use App\Models\TrxWithdrawIorPay;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TransaksiWithdrawController extends Controller
{
    public function index()
    {
        return view('admin.transaksi.withdraw.index');
    }

    public function data_withdraw(Request $request)
    {
        try {
            $where = '1=1';

            if (isset($request->type_pembayaran)) {
                $where .= " and payment_methods.type = '" . $request->type_pembayaran . "'";
            }
            if (isset($request->tanggal_mulai)) {
                $where .= " and DATE_FORMAT(trx_withdraw_ior_pay.created_at, '%Y-%m-%d') >= '$request->tanggal_mulai'";
            }
            if (isset($request->tanggal_akhir)) {
                $where .= " and DATE_FORMAT(trx_withdraw_ior_pay.created_at, '%Y-%m-%d') <= '$request->tanggal_akhir'";
            }

            // dd($where);
            $trx_withdraw = TrxWithdrawIorPay::whereRaw($where)
                ->join('payment_methods', 'trx_withdraw_ior_pay.bank_tujuan', 'payment_methods.kode_payment')
                ->orderBy('trx_withdraw_ior_pay.created_at', 'desc')->get();
            // dd($trx_withdraw);
            $data = DataTables::of($trx_withdraw)
                ->addColumn('no_order', function ($list) {
                    return $list->no_trx;
                })
                ->addColumn('nama_user', function ($list) {
                    return $list->iorPay->user->full_name;
                })
                ->addColumn('type_pembayaran', function ($list) {
                    return $list->bank->payment_name;
                })
                ->addColumn('total_withdraw', function ($list) {
                    return 'Rp. ' . number_format($list->total_withdraw, 0);
                })
                ->addColumn('biaya_admin', function ($list) {
                    return 'Rp. ' . number_format($list->biaya_adm);
                })
                ->addColumn('status_withdraw', function ($list) {
                    $status = '-';
                    if ($list->status_withdraw == 0) {
                        $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                    } else {
                        $status = '<span class="badge badge-sm badge-success">SUCCESS</span>';
                    }

                    return $status;
                })
                ->addColumn('tanggal', function ($list) {
                    return $list->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($list) {
                    return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="' . route('admin.transaksi.detail', $list->no_trx) . '" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                })->rawColumns(['nomor_order', 'nama_user', 'type_pembayaran', 'biaya_admin', 'total_withdraw', 'status_withdraw', 'tanggal', 'action'])
                ->make(true);

            return $data;
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function detailWithdraw(Request $request)
    {
        try {
            $trxwithdraw = TrxWithdrawIorPay::with(['bank'])->where([
                'no_trx' => $request->notrx,
                'status_withdraw' => 0
            ])->first();

            if (empty($trxwithdraw)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Transkasi tidak ditemukan',
                    'detail' => null
                ]);
            }

            $trxwithdraw->total_withdraw = number_format($trxwithdraw->total_withdraw, 0);
            $trxwithdraw->biaya_admin = number_format($trxwithdraw->biaya_admin, 0);
            $trxwithdraw->tanggal = $trxwithdraw->created_at->format('Y-m-d');
            $trxwithdraw->nama_user = $trxwithdraw->iorpay->user->full_name;

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get transaksi withdraw',
                'detail' => $trxwithdraw
            ], 200);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
