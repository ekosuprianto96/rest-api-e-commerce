<?php

namespace App\Models;

use App\Models\IorPay;
use App\Models\TrxIorPay;
use App\Models\SaldoRefaund;
use Illuminate\Http\Request;
use App\Models\ClearingSaldo;
use App\Models\PesananProduk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiKomisiReferal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailOrder extends Model
{
    use HasFactory;
    protected $table = 'detail_orders';
    protected $guarded = ['id'];

    public function order() {
        return $this->belongsTo(Order::class, 'no_order', 'no_order');
    }

    public function produk() {
        return $this->hasOne(Produk::class, 'kode_produk', 'kode_produk');
    }

    public function toko() {
        return $this->belongsTo(DetailToko::class, 'kode_toko', 'kode_toko');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function waktu_proses() {
        return $this->hasOne(WaktuProsesOrder::class, 'order_id', 'id');
    }

    public function file_pesanan() {
        return $this->belongsTo(PesananProduk::class, 'no_order', 'no_order');
    }

    public function getPayment() {
        if(isset($this->order->payment)) {
            return $this->order->payment->payment_name;
        }

        if($this->order->type_payment == 'linggaPay') {
            return 'Lingga Pay';
        }

        return 'Bank Transfer';
    }

    public function updateStatusOrder(Request $request) {
        try {

            DB::beginTransaction();

            if($request->status_order == '1') {
                $this->status_order = 'PENDING';
                $this->save();
            }else if($request->status_order == '2') {
                $this->status_order = 'PROCCESS';
                $this->save();
            }else if($request->status_order == '3') {

                if($request->type_data_order === 'file') {
                    if(empty($request->file_order)) {
                        Alert::warning('Maaf!', 'Data file untuk customer belum di upload');

                        return redirect()->back();
                    }

                    if ($request->hasFile('file_order')) {
                        $file = $request->file('file_order');
                        $ext = $file->getClientOriginalExtension();
                        $newname = date('Ymd') . rand(1000, 9999) . $this->uuid_user . '.' . $ext;
                        $file->move(public_path('assets/users/' . $this->user->username), $newname);
        
                        $param = [
                            'no_order' => $this->no_order,
                            'uuid_user' => $this->uuid_user,
                            'kode_toko' => $this->kode_toko,
                            'kode_produk' => $this->kode_produk,
                            'file' => $newname
                        ];
        
                        $status = PesananProduk::create($param);
                    }
                }else {
                    if(empty($request->text_order) || $request->text_order == '') {
                        Alert::warning('Maaf!', 'Data text untuk customer belum di kirim');

                        return redirect()->back();
                    }

                    $param = [
                        'no_order' => $this->no_order,
                        'uuid_user' => $this->uuid_user,
                        'kode_toko' => $this->kode_toko,
                        'kode_produk' => $this->kode_produk,
                        'text' => nl2br($request['text'])
                    ];
    
                    $status = PesananProduk::create($param);
                }

                $paramTransaksiAffiliasi = [
                    'kode_produk' => $this->kode_produk,
                    'uuid_user' => Auth::user()->uuid,
                    'type_payment' => $request->typePayment,
                    'total_komisi' => $this->potongan_referal
                ];
                $assignTransaksiAffiliasi = (new TransaksiKomisiReferal())->addTransaksiAffiliasi($paramTransaksiAffiliasi);
                if(!$assignTransaksiAffiliasi['status']) {
                    return [
                        'status' => false,
                        'message' => $assignTransaksiAffiliasi['message'],
                        'detail' => $assignTransaksiAffiliasi['detail']
                    ];
                }

                SaldoRefaund::addSaldo($this->produk->toko->kode_toko, $this->potongan_referal);

                ClearingSaldo::create([
                    'kode_toko' => $this->produk->toko->kode_toko,
                    'saldo' => $this->potongan_referal,
                    'tanggal_insert' => now()->format('Y-m-d'),
                    'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                ]);

                // Update status komisi affiliasi jika ada
                $transaksiKomisi = TransaksiKomisiReferal::where([
                    'no_order' => $this->no_order,
                    'id_order' => $this->id
                ])->first();

                if(isset($transaksiKomisi)) {
                    $transaksiKomisi->status_pembayaran = 'SUCCESS';
                    $transaksiKomisi->save();

                    // Update Transaksi linggaPay
                    $trxIorPay = TrxIorPay::where([
                        'kode_pay' => $transaksiKomisi->kode_pay,
                        'no_trx' => $transaksiKomisi->no_trx
                    ])->first();

                    $trxIorPay->status_trx = 'SUCCESS';
                    $trxIorPay->save();

                    // Tanbah saldo linggaPay yang share link
                    $linggaPay = IorPay::where('kode_pay', $transaksiKomisi->iorPay->kode_pay)->first();
                    $linggaPay->saldo += $transaksiKomisi->total_komisi;
                    $linggaPay->save();
                }

                $this->status_order = 'SUCCESS';
                $this->save();
            }else if($request->status_order == '4') {
                if($this->order->status_order === 'SUCCESS') {
                    $total_biaya = $this->total_biaya;

                    // ambil linggaPay customer
                    $linggaPay = IorPay::where('uuid_user', $this->uuid_user)->first();

                    if(isset($linggaPay)) {

                        $trx_pay = new TrxIorPay();
                        $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                        $trx_pay->kode_pay = $linggaPay->kode_pay;
                        $trx_pay->uuid_user = $this->uuid_user;
                        $trx_pay->type_pay = 'DEBIT';
                        $trx_pay->jenis_pembayaran = 'linggaPay';
                        $trx_pay->total_trx = $total_biaya;
                        $trx_pay->total_fixed = $total_biaya;
                        $trx_pay->keterangan = 'Pengembalian Dana';
                        $trx_pay->status_trx = 'SUCCESS';
                        $trx_pay->save();

                        $linggaPay->saldo += $total_biaya;
                        $linggaPay->save();

                        // Update status komisi affiliasi jika ada
                        $transaksiKomisi = TransaksiKomisiReferal::where([
                            'no_order' => $this->no_order,
                            'id_order' => $this->id
                        ])->first();

                        if(isset($transaksiKomisi)) {
                            $transaksiKomisi->status_pembayaran = 'CANCEL';
                            $transaksiKomisi->save();
                        }
                    }
                }

                $this->is_cancel = 'toko';
                $this->status_order = 'CANCEL';
                $this->save();

                DB::commit();
                return [
                    'status' => true,
                    'message' => 'ok',
                    'is_cancel' => true,
                    'detail' => 1
                ];
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'ok',
                'detail' => 1
            ];
        }catch(\Exception $err) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Terjadi keslahan system pada saat update status order',
                'detail' => $err->getMessage().'-'.$err->getLine()
            ];
        }
    }
}
