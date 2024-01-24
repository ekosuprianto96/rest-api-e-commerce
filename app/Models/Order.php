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

    public function get_pendapatan_toko() {
        $total_pendapatan = 0;
        foreach($this->where('status_order', 'SUCCESS')->get() as $item) {
            $biaya = (intval($item->biaya_platform) / 100) * intval($item->total_biaya);
            // dd(intval($biaya));
            $total_pendapatan += $biaya;
        }

        return $total_pendapatan;
    }
    public function get_biaya_platform() {
        $biaya_platform = ($this->biaya_platform / 100) * $this->total_biaya;

        return $biaya_platform;
    }
}
