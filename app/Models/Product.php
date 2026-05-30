<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'sku',
        'category_id',
        'category',
        'description',
        'composition',
        'price',
        'stock',
        'image',
        'secondary_image',
        'color',
        'gender',
        'size',
        'available_sizes',
        'is_new_collection',
        'is_limited_edition',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_new_collection' => 'bool',
        'is_limited_edition' => 'bool',
        'available_sizes' => 'array',
    ];

    protected static function booted(): void
    {
        static::created(function (Product $product) {
            if (filled($product->sku)) {
                return;
            }
            $product->updateQuietly(['sku' => self::generateSku($product)]);
        });
    }

    /** Артикул: DAB-{код категории}-{id}. */
    public static function generateSku(Product $product): string
    {
        $slug = $product->category;
        if ($product->relationLoaded('categoryModel') && $product->categoryModel) {
            $slug = $product->categoryModel->slug;
        } elseif ($product->category_id && ! $product->relationLoaded('categoryModel')) {
            $slug = $product->categoryModel()->value('slug') ?? $slug;
        }

        $letters = strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $slug));
        $code = substr($letters !== '' ? $letters : 'GEN', 0, 3);
        $code = str_pad($code, 3, 'X');

        return sprintf('DAB-%s-%06d', $code, (int) $product->id);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'entity_id')
            ->where('entity_type', self::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_products')->withTimestamps();
    }

    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /** Slug категории для фильтров на витрине (совместимость со старым полем category). */
    public function getCategorySlugAttribute(): string
    {
        if ($this->relationLoaded('categoryModel') && $this->categoryModel) {
            return (string) $this->categoryModel->slug;
        }

        return (string) ($this->attributes['category'] ?? 'clothes');
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
        if (! empty($this->color)) {
            return [(string) $this->color];
        }

        return ['#000000', '#d4d4d4', '#a3a3a3'];
    }
}
