<?php

namespace App\Models;

use App\Models\User;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'wishlists';
    protected $guarded = ['id'];


    public function produk() {
        return $this->hasMany(Produk::class, 'kode_produk', 'kode_produk');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
