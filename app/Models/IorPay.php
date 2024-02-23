<?php

namespace App\Models;

use App\Models\TrxIorPay;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function addSaldo($kodePay = null, $total = 0, $status = 'SUCCESS') {
        try {
            DB::beginTransaction();

            if(empty($kodePay)) {
                if($status == 'SUCCESS') {
                    $this->saldo += intval($total);
                    $this->save();
                }
    
                $trx_pay = new TrxIorPay();
                $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                $trx_pay->kode_pay = $this->kode_pay;
                $trx_pay->uuid_user = $this->user->uuid;
                $trx_pay->type_pay = 'DEBIT';
                $trx_pay->jenis_pembayaran = 'AFFILIASI';
                $trx_pay->total_trx = $total;
                $trx_pay->total_fixed = $total;
                $trx_pay->keterangan = 'Komisi Affiliasi';
                $trx_pay->status_trx = $status;
                $trx_pay->save();
                
                DB::commit();
                return [
                    'status' => true,
                    'message' => 'ok',
                    'detail' => $trx_pay->no_trx
                ];
            }
    
            if($status == 'SUCCESS') {
                $pay = $this->where('kode_pay', $kodePay)->first();
                $pay->saldo += intval($total);
                $this->save();
            }
    
            $trx_pay = new TrxIorPay();
            $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
            $trx_pay->kode_pay = $this->kode_pay;
            $trx_pay->uuid_user = $this->user->uuid;
            $trx_pay->type_pay = 'DEBIT';
            $trx_pay->jenis_pembayaran = 'AFFILIASI';
            $trx_pay->total_trx = $total;
            $trx_pay->total_fixed = $total;
            $trx_pay->keterangan = 'Komisi Affiliasi';
            $trx_pay->status_trx = $status;
            $trx_pay->save();
            
            DB::commit();
            return [
                'status' => true,
                'message' => 'ok',
                'detail' => $trx_pay->no_trx
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
}
