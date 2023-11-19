<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
