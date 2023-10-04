<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\IorPay;
use App\Models\TrxIorPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;

class IorPayController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = 'SB-Mid-server-vQYtgExR76-NLFZqSYo-C5MX';
        Config::$clientKey = 'SB-Mid-client-TKuIPz4RRzS3pxRC';
        Config::$isProduction = false;
        Config::$is3ds = false;
    }
    public function detail_pay() {

    }

    public function refresh(Request $request) {
        $pay = IorPay::where([
            'uuid_user' => $request['uuid_user'],
            'kode_pay' => $request['kode_pay']
        ])->first();
        
        $saldo = $pay->saldo_formatted;
        return response()->json([
            'status' => true,
            'message' => 'Refresh Success',
            'detail' => $saldo
        ]);
    }

    public function top_up(Request $request) {
        try {

            $trx_pay = new TrxIorPay();
            $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
            $trx_pay->kode_pay = Auth::user()->iorPay->kode_pay;
            $trx_pay->uuid_user = Auth::user()->uuid;
            $trx_pay->type_pay = 'DEBIT';
            $trx_pay->jenis_pembayaran = $request['type_payment'];
            $trx_pay->total_fixed = intval($request['total_topup']);
            
            if($request['type_payment'] == 'manual') {
                $kode_unique = rand(111, 999);
                $trx_pay->method = $request['method'];
                $trx_pay->total_trx = (float) intval($request['total_topup']) + $kode_unique;
                $kode_unique = substr($trx_pay->total_trx, -3);
                $trx_pay->kode_unique = $kode_unique;
            }else if($request['type_payment'] == 'gateway') {
                $items = array();
                $trx_pay->total_trx = intval($request['total_topup']);
                array_push($items, [
                    'id' => $trx_pay->no_trx,
                    'price' => (intval($request['total_topup'])),
                    'quantity' => 1,
                    'name' => 'Topup Saldo'
                ]);

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $trx_pay->no_trx,
                        'gross_amount' => intval($request['total_topup'])
                    )
                );

                $billing_address = array(
                    'first_name'   => Auth::user()->full_name,
                    'address'      => Auth::user()->alamat,
                    'city'         => Auth::user()->alamat,
                    'phone'        => Auth::user()->no_hape,
                    'country_code' => 'IDN'
                );
    
                $params['customer_details'] = $billing_address;
                $params['item_details'] = $items;

                $snapToken = Snap::getSnapToken($params);
                $trx_pay->snap_token = $snapToken;
            }

            $trx_pay->keterangan = 'Topup Saldo';
            $trx_pay->biaya_adm = 0;
            
            if($trx_pay->save()) {
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Topup Berhasil, Silahkan Menunggu Konfirmasi Dari Admin Selama 1 x 24 jam.',
                    'no_trx' => $trx_pay->no_trx
                ], 200);
            }

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function get_trx(Request $request) {
        try {
            if($request['no_trx']) {
                $trx = TrxIorPay::with(['iorpay', 'payment'])->where([
                    'no_trx' => $request['no_trx'],
                    'uuid_user' => $request['uuid_user']
                ])->first();

                $trx->total_trx = number_format($trx->total_trx, 0);
                $trx->total_fixed = number_format($trx->total_fixed, 0);
            }else {
                $trx = TrxIorPay::with(['iorpay', 'payment'])->where('uuid_user', Auth::user()->uuid)->latest()->get();
                foreach($trx as $key => $tr) {
                    $tr->tanggal_trx = $tr->created_at->format('d/m/Y');
                    $tr->total_fixed = number_format($tr->total_fixed, 0);
                }
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Get Transaksi Iorpay.',
                'detail' => $trx
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
