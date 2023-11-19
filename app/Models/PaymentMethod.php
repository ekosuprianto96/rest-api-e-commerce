<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public function pendapatan() {
        return $this->hasOne(Pendapatan::class, 'account', 'kode_payment');
    }

    public function trx_withdraw() {
        return $this->hasOne(TrxWithdrawIorPay::class, 'bank_tujuan', 'kode_payment');
    }

    public function trx_account() {
        return $this->hasMany(TransaksiAccount::class, 'method', 'kode_payment');
    }
}
