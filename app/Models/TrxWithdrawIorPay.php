<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxWithdrawIorPay extends Model
{
    use HasFactory;
    protected $table = 'trx_withdraw_ior_pay';

    public function iorPay() {
        return $this->belongsTo(IorPay::class, 'kode_pay', 'kode_pay');
    }
    public function bank() {
        return $this->belongsTo(PaymentMethod::class, 'bank_tujuan', 'kode_payment');
    }

}
