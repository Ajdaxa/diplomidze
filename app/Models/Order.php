<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'courier_id',
        'total_price',
        'status',
        'address',
        'promocode_id',
        'yookassa_payment_id',
        'paid_at',
    ];

    protected $casts = [
        'address' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class);
    }
}
