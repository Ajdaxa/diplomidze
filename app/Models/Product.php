<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'entity_id')
            ->where('entity_type', self::class);
    }
}
