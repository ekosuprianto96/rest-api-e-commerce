<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxIorPay extends Model
{
    use HasFactory;
    protected $table = 'trx_ior_pays';

    public function iorpay()
    {
        return $this->belongsTo(IorPay::class, 'kode_pay', 'kode_pay');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class, 'method', 'kode_payment');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function trxWithdraw()
    {
        return $this->hasOne(TrxWithdrawIorPay::class, 'no_trx_pay', 'no_trx');
    }

    public function order()
    {
    }
}
