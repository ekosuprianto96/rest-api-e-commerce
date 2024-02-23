<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailToko extends Model
{
    use HasFactory;
    protected $table = 'detail_toko';
    public $primaryKey = 'kode_toko';
    protected $keyType = 'string';
    protected $guarded = ['kode_toko'];
    public $incrementing = false;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detailToko) {
            // Menghasilkan nilai primary key dalam format "TK001"
            $lastPrimaryKey = DetailToko::max('kode_toko');
            if ($lastPrimaryKey) {
                $number = (int)substr($lastPrimaryKey, 2) + 1;
            } else {
                $number = 1;
            }
            $detailToko->kode_toko = 'TK' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kode_toko', 'kode_toko');
    }

    public function message()
    {
        return $this->hasMany(Message::class, 'uuid_user', 'uuid_user');
    }

    public function saldo()
    {
        return $this->hasOne(SaldoToko::class, 'kode_toko', 'kode_toko');
    }

    public function saldo_refaund()
    {
        return $this->hasOne(SaldoRefaund::class, 'kode_toko', 'kode_toko');
    }

    public function order()
    {
        return $this->hasMany(DetailOrder::class, 'kode_toko', 'kode_toko');
    }

    public function getProdukToko()
    {
        $produkToko = $this->produk()->where([
            'an' => 1,
            'status_confirm' => 1
        ])->get();
        if (@count($produkToko) > 0) {
            foreach ($produkToko as $pr) {
                $produk = Produk::where('kode_produk', $pr->kode_produk)->first();
                $pr->detail_harga = $produkToko->detail_harga = $produk->getHargaDiskon($produk);
            }
        }
        return $produkToko;
    }

    public function addSaldoToko() {
        
    }
}
