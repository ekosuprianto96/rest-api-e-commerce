<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

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
            $detailProduk->kode_produk = 'PD' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function toko()
    {
        return $this->belongsTo(DetailToko::class, 'kode_toko', 'kode_toko');
    }
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori');
    }

    public function getHargaFixed()
    {
        if ($this->potongan_persen > 0) {
            $persen = (float) $this->potongan_persen / 100;
            $harga_fixed = (float) ($this->harga * $persen);
            $harga_fixed = (float) ($this->harga - $harga_fixed);
        } else if ($this->potongan_harga > 0) {
            $harga_fixed = (float) ($this->harga - $this->potongan_harga);
        } else {
            $harga_fixed = $this->harga;
        }

        return $harga_fixed;
    }
    public function getHargaDiskon()
    {
        if ($this->potongan_harga > 0) {
            $potongan = (float) ($this->harga - $this->potongan_harga);
            $this->harga_fixed = number_format($potongan, 0, 0, '.');
            $this['potongan'] = number_format($this->potongan_harga, 0, 0, '.');
        }

        if ($this->potongan_persen > 0) {
            $potongan = (float) $this->harga * ($this->potongan_persen  / 100);
            $this->harga_fixed = (float) $this->harga - $potongan;
            $this['potongan'] = number_format($potongan, 0, 0, '.');
            $this->harga_fixed = number_format($this->harga_fixed, 0, 0, '.');
        }

        return array(
            'harga_real' => number_format($this->harga, 0, 0, '.'),
            'harga_fixed' => ($this->harga_fixed > 0 ? $this->harga_fixed : number_format($this->harga, 0, 0, '.')),
            'harga_diskon' => ($this['potongan'] > 0 ? $this['potongan'] : 0)
        );
    }

    public function waktuProses()
    {
        $waktuProses = WaktuProsesOrder::where('kode', $this->waktu_proses)->first();
        return $waktuProses->nama;
    }

    public function form()
    {
        return $this->hasMany(ListFormProduk::class, 'kode_produk', 'kode_produk');
    }
    public function order()
    {
        return $this->hasMany(DetailOrder::class, 'kode_produk', 'kode_produk');
    }
    public function akses()
    {
        return $this->hasMany(AksesDownload::class, 'kode_produk', 'kode_produk');
    }
    public function images()
    {
        return $this->hasMany(ImageProduk::class, 'kode_produk', 'kode_produk');
    }

    public static function getProdukTerlaris()
    {
        $produkTerlaris = Cache::tags('produkTerlaris')->rememberForever('produkTerlaris', function () {
            return Produk::select('produk.*', DB::raw('SUM(detail_orders.quantity) as total_penjualan'))
                ->leftJoin('detail_orders', 'detail_orders.kode_produk', 'produk.kode_produk')
                ->where([
                    'produk.an' => 1,
                    'produk.status_confirm' => 1
                ])
                ->groupBy('produk.kode_produk')
                ->orderBy('total_penjualan')
                ->take(20)->get();
        });
        if (@count($produkTerlaris) > 0) {
            foreach ($produkTerlaris as $pr) {
                $produk = Produk::where('kode_produk', $pr->kode_produk)->first();
                $pr->detail_harga = $produk->getHargaDiskon($produk);
            }
        }
        return $produkTerlaris;
    }

    public static function getProdukSerupa(Produk $produk)
    {
        $produkSerupa = Cache::tags('produkSerupa')->rememberForever('produkSerupa', function () use ($produk) {
            return Produk::whereRaw("kode_kategori = '" . $produk->kategori->kode_kategori . "' AND (kode_produk != '" . $produk->kode_produk . "') ")
                ->where([
                    'an' => 1,
                    'status_confirm' => 1
                ])
                ->take(20)->get();
        });

        if (@count($produkSerupa) > 0) {
            foreach ($produkSerupa as $pr) {
                $produk = Produk::where('kode_produk', $pr->kode_produk)->first();
                $pr->detail_harga = $produk->getHargaDiskon($produk);
            }
        }
        return $produkSerupa;
    }

    public static function getProdukRekomendasi()
    {
        $produkRekom = Cache::tags('produk')->rememberForever('produkRekom', function () {
            return Produk::select('produk.*', DB::raw('SUM(detail_orders.quantity) as total_penjualan'))
                ->leftJoin('detail_orders', 'detail_orders.kode_produk', 'produk.kode_produk')
                ->where([
                    'produk.an' => 1,
                    'produk.status_confirm' => 1
                ])
                ->groupBy('produk.kode_produk')
                ->orderBy('total_penjualan')
                ->take(20)->get();
        });

        if (@count($produkRekom) > 0) {
            foreach ($produkRekom as $pr) {
                $produk = Produk::where('kode_produk', $pr->kode_produk)->first();
                $pr->detail_harga = $produk->getHargaDiskon($produk);
            }
        }
        return $produkRekom;
    }

    public function hargaReal() {
        return $this->harga;
    }

    public function totalDiskon() {
        if ($this->potongan_harga > 0) {
            return $this->potongan_harga;
        }

        if ($this->potongan_persen > 0) {
            $potongan = (float) $this->harga * ($this->potongan_persen  / 100);
            return $potongan;
        }

        return 0;
    }
    // public function cart() {
    //     return $this->belongsToMany(Cart::class, 'cart_produk', 'kode_produk', 'kode_cart');
    // }
}
