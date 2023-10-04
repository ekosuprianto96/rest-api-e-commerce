<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    protected $guarded = ['kode_kategori', 'id'];
    public $primaryKey = 'kode_kategori';
    protected $keyType = 'string';
    public $incrementing = false;


    public function produk() {
        return $this->hasMany(Produk::class, 'kode_kategori', 'kode_kategori');
    }
    
}
