<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'clothes' => 'Одежда',
        'accessories' => 'Аксессуары',
        'shoes' => 'Обувь',
        'sportswear' => 'Спорт',
        'home' => 'Дом',
    ];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'price',
        'stock',
        'image',
        'secondary_image',
        'color',
        'size',
        'available_sizes',
        'display_colors',
        'is_new_collection',
        'is_limited_edition',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_new_collection' => 'bool',
        'is_limited_edition' => 'bool',
        'available_sizes' => 'array',
        'display_colors' => 'array',
    ];

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'entity_id')
            ->where('entity_type', self::class);
    }

    /** @return list<string> */
    public function sizesList(): array
    {
        $sizes = $this->available_sizes;

        if (is_array($sizes) && $sizes !== []) {
            return array_values(array_map('strval', $sizes));
        }

        if (! empty($this->size)) {
            return [strtoupper((string) $this->size)];
        }

        return ['XS', 'S', 'M', 'L', 'XL'];
    }

    /** @return list<string> */
    public function colorsForCard(): array
    {
        if (is_array($this->display_colors) && $this->display_colors !== []) {
            return array_values(array_map('strval', $this->display_colors));
        }

        if (! empty($this->color)) {
            return [(string) $this->color];
        }

        return ['#000000', '#d4d4d4', '#a3a3a3'];
    }
}
