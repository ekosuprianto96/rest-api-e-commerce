<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IorPay extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getSaldoFormattedAttribute()
    {
        // Menggunakan helper number_format untuk mengatur format saldo
        return number_format($this->attributes['saldo'], 0);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kode) {
            // Menghasilkan nilai primary key dalam format "PD001"
            $lastPrimaryKey = IorPay::max('kode_pay');
            if ($lastPrimaryKey) {
                $number = (int)substr($lastPrimaryKey, 3) + 1;
            } else {
                $number = 1;
            }
            $kode->kode_pay = 'PAY'. str_pad($number, 4, '0', STR_PAD_LEFT);
        });

    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function trx() {
        return $this->hasMany(TrxIorPay::class, 'kode_pay', 'kode_pay');
    }

    public function trx_komisi() {
        return $this->hasMany(TransaksiKomisiReferal::class, 'kode_pay', 'kode_pay');
    }
}
