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

    public function getHargaFixed() {
        if($this->potongan_persen > 0) {
            $persen = (float) $this->potongan_persen / 100;
            $harga_fixed = (float) ($this->harga * $persen);
            $harga_fixed = (float) ($this->harga - $harga_fixed);
        }else if($this->potongan_harga > 0) {
            $harga_fixed = (float) ($this->harga - $this->potongan_harga);
        }else {
            $harga_fixed = $this->harga;
        }

        return $harga_fixed;
    }
    public function getHargaDiskon() {
        if($this->potongan_harga > 0) {
            $potongan = (float) ($this->harga - $this->potongan_harga);
            $this->harga_fixed = number_format($potongan, 0);
            $this['potongan'] = number_format($this->potongan_harga, 0);
        }

        if($this->potongan_persen > 0) {
            $potongan = (float) $this->harga * ($this->potongan_persen  / 100);
            $this->harga_fixed = (float) $this->harga - $potongan;
            $this['potongan'] = number_format($potongan, 0);
            $this->harga_fixed = number_format($this->harga_fixed, 0);
        }

        return array(
            'harga_real' => number_format($this->harga, 0),
            'harga_fixed' => ($this->harga_fixed > 0 ? $this->harga_fixed : number_format($this->harga, 0)),
            'harga_diskon' => ($this['potongan'] > 0 ? $this['potongan'] : 0));
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
