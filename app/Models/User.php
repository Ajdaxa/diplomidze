<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'telegram_chat_id',
        'telegram_link_token',
        'telegram_link_token_expires_at',
        'role',
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
        'telegram_link_token_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function clientOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function courierOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_id');
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }
}
