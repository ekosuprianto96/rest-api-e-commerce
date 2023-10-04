<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesDownload extends Model
{
    use HasFactory;
    protected $table = 'akses_downloads';
    protected $guarded = ['id'];

    public function produk() {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode_produk');
    }
}
