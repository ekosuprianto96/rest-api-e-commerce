<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $guarded = ['kode_produk', 'id'];
    public $primaryKey = 'kode_produk';
    protected $keyType = 'string';
    public $incrementing = false;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detailProduk) {
            // Menghasilkan nilai primary key dalam format "PD001"
            $lastPrimaryKey = Produk::max('kode_produk');
            if ($lastPrimaryKey) {
                $number = (int)substr($lastPrimaryKey, 2) + 1;
            } else {
                $number = 1;
            }
            $detailProduk->kode_produk = 'PD'. str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function toko() {
        return $this->belongsTo(DetailToko::class, 'kode_toko', 'kode_toko');
    }
    public function kategori() {
        return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori');
    }

    public function getHargaDiskon(Produk $produk) {
        if($produk->potongan_harga > 0) {
            $potongan = (float) ($produk->harga - $produk->potongan_harga);
            $produk->harga_fixed = number_format($potongan, 0);
            $produk['potongan'] = number_format($produk->potongan_harga, 0);
        }

        if($produk->potongan_persen > 0) {
            $potongan = (float) $produk->harga * ($produk->potongan_persen  / 100);
            $produk->harga_fixed = (float) $produk->harga - $potongan;
            $produk['potongan'] = number_format($potongan, 0);
            $produk->harga_fixed = number_format($produk->harga_fixed, 0);
        }

        return array(
            'harga_real' => number_format($produk->harga, 0),
            'harga_fixed' => ($produk->harga_fixed > 0 ? $produk->harga_fixed : number_format($produk->harga, 0)),
            'harga_diskon' => ($produk['potongan'] > 0 ? $produk['potongan'] : 0));
    }

    public function form() {
        return $this->hasMany(ListFormProduk::class, 'kode_produk', 'kode_produk');
    }
    public function order() {
        return $this->hasMany(DetailOrder::class, 'kode_produk', 'kode_produk');
    }
    public function akses() {
        return $this->hasMany(AksesDownload::class, 'kode_produk', 'kode_produk');
    }
    // public function cart() {
    //     return $this->belongsToMany(Cart::class, 'cart_produk', 'kode_produk', 'kode_cart');
    // }
}
