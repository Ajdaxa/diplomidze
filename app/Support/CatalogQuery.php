<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class CatalogQuery
{
    /** @return array<string, mixed> */
    public static function filtersFromRequest(Request $request): array
    {
        return [
            'cat' => $request->string('cat')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'new',
            'gender' => $request->string('gender')->toString(),
            'size' => strtoupper($request->string('size')->toString()),
            'q' => trim($request->string('q')->toString()),
            'in_stock' => $request->boolean('in_stock'),
            'new' => $request->boolean('new'),
            'limited' => $request->boolean('limited'),
            'price_min' => $request->filled('price_min') ? (float) $request->input('price_min') : null,
            'price_max' => $request->filled('price_max') ? (float) $request->input('price_max') : null,
        ];
    }

    public static function apply(Builder $query, array $filters): Builder
    {
        if ($filters['cat'] !== '') {
            $cat = $filters['cat'];
            $query->where(function (Builder $q) use ($cat): void {
                $q->whereHas('categoryModel', fn (Builder $c) => $c->where('slug', $cat))
                    ->orWhere('category', $cat);
            });
        }

        if ($filters['gender'] !== '') {
            $query->where('gender', $filters['gender']);
        }

        if ($filters['in_stock']) {
            $query->where('stock', '>', 0);
        }

        if ($filters['new']) {
            $query->where('is_new_collection', true);
        }

        if ($filters['limited']) {
            $query->where('is_limited_edition', true);
        }

        if ($filters['price_min'] !== null) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if ($filters['price_max'] !== null) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if ($filters['size'] !== '') {
            $size = $filters['size'];
            $query->where(function (Builder $q) use ($size): void {
                $q->where('size', $size)
                    ->orWhereJsonContains('available_sizes', $size);
            });
        }

        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('sku', 'like', $term);
            });
        }

        return match ($filters['sort']) {
            'price-asc' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_new_collection')
                ->orderByDesc('is_limited_edition')
                ->latest('updated_at'),
        };
    }

    public static function base(): Builder
    {
        return Product::query()
            ->where('is_active', true)
            ->with('categoryModel');
    }
}
