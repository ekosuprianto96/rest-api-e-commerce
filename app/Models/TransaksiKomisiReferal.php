<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiKomisiReferal extends Model
{
    use HasFactory;

    protected $guarded= ['id'];

    public function trx_iorpay() {
        return $this->belongsTo(TrxIorPay::class, 'no_trx', 'no_trx');
    }

    public function iorpay() {
        return $this->belongsTo(IorPay::class, 'kode_pay', 'kode_pay');
    }

    public function produk() {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode_produk');
    }

    public function addTransaksiAffiliasi($param = []) {
        try {

            DB::beginTransaction();
            $detail = 0;

            // Get Produk
            $produk = Produk::where([
                'kode_produk' => $param['kode_produk']
            ])->first();

            $cart = Cart::where([
                'kode_produk' => $param['kode_produk'],
                'uuid_user' => $param['uuid_user'],
            ])->first();
           
            if(isset($cart) && isset($cart->referal)) {
                
                if(intval($produk->status_referal)) {

                    $pay_referal = IorPay::where('uuid_user', $cart->referal)->first();

                    $status = 'PENDING';
                    if($param['type_payment'] == 'linggaPay') {
                        if($produk->type_produk == 'AUTO') {
                            $status = 'SUCCESS';
                        }
                    }

                    $statusAddSaldo = $pay_referal->addSaldo(null, $param['total_komisi'], $status);
                    if(!$statusAddSaldo['status']) {
                        DB::rollBack();
                        return [
                            'status' => false,
                            'message' => $statusAddSaldo['message'],
                            'detail' => $statusAddSaldo['detail']
                        ];
                    }

                    $this->no_trx = $statusAddSaldo['detail'];
                    $this->kode_produk = $produk->kode_produk;
                    $this->kode_pay = $pay_referal->kode_pay;
                    $this->total_komisi = $param['total_komisi'];
                    $this->no_order = $param['no_order'];
                    $this->uuid_user = $cart->referal;
                    $this->id_order = $param['id_order'];
                    $this->status_pembayaran = $status;
                    $this->save();

                    $detail = 1;
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'ok',
                'detail' => $detail
            ];

        }catch(\Exception $err) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan system pada saat tambah saldo linggaPay',
                'detail' => $err->getMessage().'-'.$err->getLine()
            ];
        }
    }

    public function updateKomisi() {
        try {
            DB::beginTransaction();

            $pay_referal = IorPay::where('uuid_user', $this->iorpay->kode_pay)->first();


        }catch(\Exception $err) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Terjadi keslahan system',
                'detail' => $err->getMessage().'-'.$err->getLine()
            ];
        }
    }
}
