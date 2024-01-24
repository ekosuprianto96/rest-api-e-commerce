<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\IorPay;
use App\Models\TrxIorPay;
use App\Models\Pendapatan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\NotifikasiAdmin;
use App\Models\TransaksiAccount;
use App\Events\NotificationAdmin;
use App\Models\TrxWithdrawIorPay;
use Illuminate\Support\Facades\DB;
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
    public function detail_pay()
    {
    }

    public function refresh(Request $request)
    {
        $pay = IorPay::where([
            'uuid_user' => $request['uuid_user'],
            'kode_pay' => $request['kode_pay']
        ])->first();

        $saldo = number_format($pay->saldo, 0);
        return response()->json([
            'status' => true,
            'message' => 'Refresh Success',
            'detail' => $saldo
        ]);
    }

    public function top_up(Request $request)
    {
        try {
            DB::beginTransaction();
            $trx_pay = new TrxIorPay();
            $trx_pay->no_trx = 'TRX-' . rand(100000000, 999999999);
            $trx_pay->kode_pay = Auth::user()->iorPay->kode_pay;
            $trx_pay->uuid_user = Auth::user()->uuid;
            $trx_pay->type_pay = 'DEBIT';
            $trx_pay->jenis_pembayaran = $request['type_payment'];
            $trx_pay->total_fixed = intval($request['total_topup']);

            if ($request['type_payment'] == 'manual') {
                $kode_unique = rand(111, 999);
                $trx_pay->method = $request['method'];
                $trx_pay->total_trx = (float) intval($request['total_topup']) + $kode_unique;
                $kode_unique = substr($trx_pay->total_trx, -3);
                $trx_pay->kode_unique = $kode_unique;

                $param_trx_account = [
                    'no_transaksi' => 'AC-' . rand(100000000, 999999999),
                    'uuid_user' => Auth::user()->uuid,
                    'type_payment' => $request['type_payment'],
                    'method' => $request['method'],
                    'jns_payment' => 'DEBIT',
                    'biaya_trx' => $trx_pay->total_trx,
                    'total' => $trx_pay->total_trx,
                    'no_refrensi' => $trx_pay->no_trx,
                    'keterangan' => 'Topup Saldo'
                ];

                TransaksiAccount::create($param_trx_account);
            } else if ($request['type_payment'] == 'gateway') {
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

            if ($trx_pay->save()) {
                $notification_admin = array(
                    'uuid' => Str::uuid(32),
                    'type' => 'konfirmasi-topup',
                    'target' => 'konfirmasi-topup',
                    'value' => $trx_pay,
                    'status_read' => 0
                );

                NotifikasiAdmin::create($notification_admin);
                event(new NotificationAdmin($notification_admin));
                DB::commit();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Topup Berhasil, Silahkan Menunggu Konfirmasi Dari Admin Selama 1 x 24 jam.',
                    'no_trx' => $trx_pay->no_trx
                ], 200);
            }
        } catch (\Exception $err) {
            DB::rollBack();
            return ErrorController::getResponseError($err);
        }
    }

    public function getIorPay(Request $request)
    {
        try {
            $pay = IorPay::where([
                'kode_pay' => $request['kode_pay'],
                'uuid_user' => $request['uuid_user']
            ])->first();

            $pay->total_saldo = number_format($pay->saldo, 0);

            if (empty($pay)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Sepertinya iorPay anda bermasalah, silahkan hubungi customer service untuk masalah ini.',
                    'detail' => []
                ], 402);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get iorPay',
                'detail' => $pay
            ], 200);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function get_trx(Request $request)
    {
        try {
            if ($request['no_trx']) {
                $trx = TrxIorPay::with(['iorpay', 'payment', 'trxWithdraw'])->where([
                    'no_trx' => $request['no_trx'],
                    'uuid_user' => $request['uuid_user']
                ])->first();

                $trx->total_trx = number_format($trx->total_trx, 0);
                $trx->total_fixed = number_format($trx->total_fixed, 0);
            } else {
                $trx = TrxIorPay::with(['iorpay', 'payment', 'trxWithdraw'])->where('uuid_user', Auth::user()->uuid)->latest()->get();
                foreach ($trx as $key => $tr) {
                    $tr->tanggal_trx = $tr->created_at->format('d/m/Y');
                    $tr->total_fixed = number_format($tr->total_fixed, 0);
                    $tr->total_trx = number_format($tr->total_trx, 0);
                }
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Get Transaksi Iorpay.',
                'detail' => $trx
            ], 200);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function withdraw(Request $request)
    {
        if ($request->method == 'transfer') {
            $request->validate([
                'bank' => 'required|string',
                'total_withdraw' => 'required|numeric|min:10000|max:' . floatval(Auth::user()->iorPay->saldo),
                'norek' => 'required',
                'method' => 'required',
                'nama_pemilik' => 'required|string'
            ]);
        } else {
            $request->validate([
                'nama_wallet' => 'required|string',
                'total_withdraw' => 'required|numeric|min:10000|max:' . floatval(Auth::user()->iorPay->saldo),
                'id_wallet' => 'required',
                'method' => 'required',
                'nama_pemilik' => 'required|string'
            ]);
        }

        try {

            $pay = IorPay::where([
                'uuid_user' => $request['uuid_user'],
                'kode_pay' => $request['kode_pay']
            ])->first();

            if (empty($pay)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Sepertinya iorPay anda bermasalah, silahkan hubungi customer service untuk masalah ini.',
                    'detail' => []
                ], 402);
            }

            $no_trx_pay = 'TRX-' . rand(100000000, 999999999);
            $trx_withdraw = new TrxWithdrawIorPay();
            $trx_withdraw->no_trx = 'WD-' . rand(100000000, 999999999);
            $trx_withdraw->kode_pay = $pay->kode_pay;
            $trx_withdraw->total_withdraw = intval($request['total_withdraw']);
            $trx_withdraw->nama_pemilik = $request['nama_pemilik'];
            $trx_withdraw->keterangan = 'Transaksi Withdraw';
            $trx_withdraw->no_trx_pay = $no_trx_pay;

            if ($request->method == 'transfer') {
                $trx_withdraw->norek_tujuan = $request['norek'];
                $trx_withdraw->bank_tujuan = $request['bank'];
            } else {
                $trx_withdraw->nomor_wallet = $request['id_wallet'];
                $trx_withdraw->nama_wallet = $request['nama_wallet'];
            }
            $trx_withdraw->type_pembayaran = $request->method;

            $trx_withdraw->save();

            $notification_admin = array(
                'uuid' => Str::uuid(32),
                'type' => 'permintaan-withdraw',
                'target' => 'permintaan-withdraw',
                'value' => $trx_withdraw,
                'status_read' => 0
            );

            NotifikasiAdmin::create($notification_admin);
            event(new NotificationAdmin($notification_admin));

            $trx_pay = new TrxIorPay();
            $trx_pay->no_trx = $no_trx_pay;
            $trx_pay->kode_pay = $pay->kode_pay;
            $trx_pay->uuid_user = Auth::user()->uuid;
            $trx_pay->type_pay = 'CREDIT';
            $trx_pay->jenis_pembayaran = ($request['method'] == 'transfer' ? 'manual' : 'wallet');
            $trx_pay->total_trx = $trx_withdraw->total_withdraw;
            $trx_pay->total_fixed = $trx_withdraw->total_withdraw;
            $trx_pay->keterangan = 'Penarikan Saldo';
            $trx_pay->status_trx = 'PENDING';
            $trx_pay->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Transaksi Berhasil.',
                'detail' => $trx_withdraw
            ], 200);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function detailWithdraw(Request $request)
    {
        try {
            $trx = TrxWithdrawIorPay::with(['bank'])->where([
                'no_trx' => $request->no_trx
            ])->first();

            $trx->total_withdraw = number_format($trx->total_withdraw, 0);

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get detail',
                'detail' => $trx
            ]);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
