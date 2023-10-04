<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $guarded = ['id'];
    public $primaryKey = 'no_order';
    protected $keyType = 'string';
    public $incrementing = false;

    public function detail() {
        return $this->hasMany(DetailOrder::class, 'no_order', 'no_order');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function payment() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method', 'kode_payment');
    }
}
