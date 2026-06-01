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
        'sale_percent',
        'stock',
        'image',
        'secondary_image',
        'gallery_images',
        'color',
        'gender',
        'size',
        'available_sizes',
        'size_stock',
        'is_new_collection',
        'is_limited_edition',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_new_collection' => 'bool',
        'is_limited_edition' => 'bool',
        'available_sizes' => 'array',
        'size_stock' => 'array',
        'gallery_images' => 'array',
        'sale_percent' => 'integer',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', Review::STATUS_APPROVED);
    }

    /** @return list<string> */
    public function galleryUrls(): array
    {
        $urls = [];
        foreach ([$this->image, $this->secondary_image] as $url) {
            if (filled($url) && ! in_array($url, $urls, true)) {
                $urls[] = (string) $url;
            }
        }

        if (is_array($this->gallery_images)) {
            foreach ($this->gallery_images as $url) {
                if (filled($url) && ! in_array($url, $urls, true)) {
                    $urls[] = (string) $url;
                }
            }
        }

        return $urls !== [] ? $urls : ['https://picsum.photos/1000/1333?random='.$this->id];
    }

    public function averageRating(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function hasSale(): bool
    {
        return $this->sale_percent !== null && (int) $this->sale_percent > 0;
    }

    public function saleUnitPrice(): float
    {
        $base = (float) $this->price;
        if (! $this->hasSale()) {
            return $base;
        }

        return round($base * (1 - ((int) $this->sale_percent / 100)), 2);
    }

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

    /** @return list<string> Размеры, которые можно положить в корзину. */
    public function sizesList(): array
    {
        return \App\Support\ProductSizes::selectable($this);
    }

    /** @return list<string> Сетка для отображения на карточке товара. */
    public function displaySizeGrid(): array
    {
        return \App\Support\ProductSizes::displayGrid($this);
    }

    /** @return list<string> */
    public function inStockSizes(): array
    {
        return \App\Support\ProductSizes::inStock($this);
    }

    public function isSizeAvailable(string $size): bool
    {
        return in_array(strtoupper(trim($size)), $this->inStockSizes(), true);
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
