<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $guarded = ['id'];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($cart) {
    //         // Menghasilkan nilai primary key dalam format "PD001"
    //         $lastPrimaryKey = Cart::max('kode_cart');
    //         if ($lastPrimaryKey) {
    //             $number = (int)substr($lastPrimaryKey, 2) + 1;
    //         } else {
    //             $number = 1;
    //         }
    //         $cart->kode_cart = 'CT'. str_pad($number, 4, '0', STR_PAD_LEFT);
    //     });
    // }

    public function produk() {
        return $this->hasMany(Produk::class, 'kode_produk', 'kode_produk');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
