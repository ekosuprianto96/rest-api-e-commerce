<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatan extends Model
{
    use HasFactory;
    protected $table = 'pendapatan';
    protected $guarded = ['id'];

    public function payment() {
        return $this->belongsTo(PaymentMethod::class, 'account', 'kode_payment');
    }
}
