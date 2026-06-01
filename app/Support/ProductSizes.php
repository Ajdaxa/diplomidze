<?php

namespace App\Support;

use App\Models\Product;

final class ProductSizes
{
    /** @return list<string> */
    public static function displayGrid(Product $product): array
    {
        $slug = $product->category_slug;

        if (in_array($slug, ['shoes', 'obuv'], true)) {
            return ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        }

        if (in_array($slug, ['accessories', 'home', 'dom'], true)) {
            return ['ONE SIZE'];
        }

        return ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    }

    /** @return list<string> */
    public static function inStock(Product $product): array
    {
        if ((int) $product->stock < 1) {
            return [];
        }

        if (is_array($product->size_stock) && $product->size_stock !== []) {
            $sizes = [];
            foreach ($product->size_stock as $size => $qty) {
                if ((int) $qty > 0) {
                    $sizes[] = strtoupper((string) $size);
                }
            }

            return array_values(array_unique($sizes));
        }

        $listed = [];
        if (is_array($product->available_sizes) && $product->available_sizes !== []) {
            $listed = array_values(array_unique(array_map(
                fn ($s) => strtoupper((string) $s),
                $product->available_sizes
            )));
        }

        if ($listed !== []) {
            if ((int) $product->stock === 1 && count($listed) > 1) {
                if (filled($product->size)) {
                    return [strtoupper((string) $product->size)];
                }

                return [$listed[0]];
            }

            return $listed;
        }

        if (filled($product->size)) {
            return [strtoupper((string) $product->size)];
        }

        return [];
    }

    /** Для корзины и валидации — только размеры в наличии. */
    public static function selectable(Product $product): array
    {
        $inStock = self::inStock($product);

        return $inStock !== [] ? $inStock : [];
    }
}
