<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    use HasFactory;
    protected $table = 'detail_orders';
    protected $guarded = ['id'];

    public function order() {
        return $this->belongsTo(Order::class, 'no_order', 'no_order');
    }

    public function produk() {
        return $this->hasOne(Produk::class, 'kode_produk', 'kode_produk');
    }

    public function toko() {
        return $this->belongsTo(DetailToko::class, 'kode_toko', 'kode_toko');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
