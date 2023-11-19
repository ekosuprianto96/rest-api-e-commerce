<?php

namespace App\Http\Controllers\Admin\Transaksi\Topup;

use App\Models\TrxIorPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class TransaksiTopupController extends Controller
{
    public function index() {
        return view('admin.transaksi.topup.index');
    }

    public function data_topup(Request $request) {
        try {
            $where = '1=1';

            if(isset($request->type_pembayaran)) {
                $where .= " and type_pembayaran = '".$request->type_pembayaran."'";
            }
            if(isset($request->tanggal_mulai)) {
                $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') >= '$request->tanggal_mulai'";
            }
            if(isset($request->tanggal_akhir)) {
                $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') <= '$request->tanggal_akhir'";
            }
            
            // dd($where);
            $trx_topup = TrxIorPay::whereRaw($where)->latest()->get();
            // dd($trx_withdraw);
            $data = DataTables::of($trx_topup)
                    ->addColumn('no_order', function($list) {
                        return $list->no_trx;
                    })
                    ->addColumn('nama_user', function($list) {
                        return $list->iorPay->user->full_name;
                    })
                    ->addColumn('type_pembayaran', function($list) {
                        return $list->type_pembayaran;
                    })
                    ->addColumn('total_withdraw', function($list) {
                        return 'Rp. '.number_format($list->total_withdraw, 0);
                    })
                    ->addColumn('biaya_admin', function($list) {
                        return 'Rp. '.number_format($list->biaya_adm);
                    })
                    ->addColumn('status_withdraw', function($list) {

                        if($list->status_withdraw == 'PENDING') {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }else if($list->status_withdraw == 'SUCCESS') {
                            $status = '<span class="badge badge-sm badge-success">SUCCESS</span>';
                        }else if($list->status_withdraw == 'CANCEL') {
                            $status = '<span class="badge badge-sm badge-danger">CANCEL</span>';
                        }else if($list->status_withdraw == '0') {
                            $status = '<span class="badge badge-sm badge-warning">Belum Dikonfirmasi</span>';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.transaksi.detail', $list->no_trx).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                    })->rawColumns(['nomor_order', 'nama_user', 'type_pembayaran', 'biaya_admin', 'total_withdraw', 'status_withdraw', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
