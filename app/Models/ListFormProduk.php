<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListFormProduk extends Model
{
    use HasFactory;
    protected $table = 'list_form_produk';
    protected $guarded = ['id'];

    public function produk() {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode_produk');
    }
}
