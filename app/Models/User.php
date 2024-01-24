<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'uuid';
    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'username',
        'full_name',
        'no_hape',
        'email',
        'alamat',
        'tgl_lahir',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function toko() {
        return $this->hasOne(DetailToko::class, 'uuid_user', 'uuid');
    }

    public function cart() {
        return $this->hasMany(Cart::class, 'uuid_user', 'uuid');
    }

    public function wishlist() {
        return $this->hasMany(Wishlist::class, 'uuid_user', 'uuid');
    }

    public function messages() {
        return $this->belongsToMany(Message::class, 'message_user', 'uuid_user', 'message_id', 'uuid');
    }

    public function detail_order() {
        return $this->hasMany(DetailOrder::class, 'uuid_user', 'uuid');
    }

    public function order() {
        return $this->hasMany(Order::class, 'uuid_user', 'uuid');
    }

    public function iorPay() {
        return $this->hasOne(IorPay::class, 'uuid_user', 'uuid');
    }
    public function trx_account() {
        return $this->hasMany(TransaksiAccount::class, 'uuid_user', 'uuid');
    }
    public function roles() {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id', 'uuid');
    }

    public function bannerUpload() {
        return $this->hasOne(MsBanner::class, 'uuid_user', 'uuid');
    }

    public function updateNotifikasi() {
        return $this->hasOne(NotifikasiPengguna::class, 'updateBy', 'uuid');
    }
    public function artikel() {
        return $this->hasMany(Artikel::class, 'created_by', 'uuid');
    }
    public function pemberitahuan() {
        return $this->hasMany(Pemberitahuan::class, 'uuid_user', 'uuid');
    }
}
