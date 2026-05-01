<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promocode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
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
}
