<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promocode extends Model
{
    use HasFactory;

    public const PURPOSE_STANDARD = 'standard';

    public const PURPOSE_REFERRAL = 'referral';

    public const PURPOSE_LOYALTY = 'loyalty';

    protected $fillable = [
        'code',
        'purpose',
        'user_id',
        'type',
        'value',
        'max_discount',
        'min_order_total',
        'usage_limit',
        'usage_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'bool',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purposeLabel(): string
    {
        return match ($this->purpose) {
            self::PURPOSE_REFERRAL => 'Реферальный',
            self::PURPOSE_LOYALTY => 'Лояльность',
            default => 'Обычный',
        };
    }
}
