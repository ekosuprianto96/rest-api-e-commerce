<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProduk extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'image_produk';

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode_produk');
    }
}
