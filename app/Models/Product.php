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
        'secondary_image',
        'color',
        'size',
        'is_new_collection',
        'is_limited_edition',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_new_collection' => 'bool',
        'is_limited_edition' => 'bool',
    ];

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'entity_id')
            ->where('entity_type', self::class);
    }
}
