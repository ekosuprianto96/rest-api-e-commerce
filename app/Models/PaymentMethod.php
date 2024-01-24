<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kodePay) {
            // Menghasilkan nilai primary key dalam format "PD001"
            $lastPrimaryKey = PaymentMethod::max('kode_payment');
            if ($lastPrimaryKey) {
                $number = (int)substr($lastPrimaryKey, 3) + 1;
            } else {
                $number = 1;
            }
            $kodePay->kode_payment = 'PAY'. str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

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
