<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaldoRefaund extends Model
{
    use HasFactory;
    protected $table = 'saldo_refaund';
    protected $guarded = ['id'];
    public $primaryKey = 'kode_payment';
    protected $keyType = 'string';
    protected $icrementing = false;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saldo) {
            $saldo->kode_payment = Str::uuid(32);
        });
    }

    public function toko() {
        return $this->belongsTo(DetailToko::class, 'kode_toko', 'kode_toko');
    }

    public function getSaldo($kode_toko) {
        $saldo = parent::where('kode_toko', $kode_toko)->first();
        return $saldo;
    }

    static function addSaldo($kode_toko, $total) {
        $saldo = parent::where('kode_toko', $kode_toko)->first();
        $saldo->total_refaund += (float) $total;
        $saldo->save();
    }

    static function kurangiSaldo($kode_toko, $total) {
        $saldo = parent::where('kode_toko', $kode_toko)->first();
        $saldo->total_refaund -= (float) $total;
        $saldo->save();
    }
}
