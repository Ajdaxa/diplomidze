<?php

namespace App\Support;

use App\Models\Product;

final class ProductSizeStockGenerator
{
    /**
     * @return array{size_stock: array<string, int>|null, available_sizes: list<string>|null, size: string|null}
     */
    public static function forProduct(Product $product): array
    {
        $total = max(0, (int) $product->stock);
        $slug = $product->category_slug;

        if ($total === 0) {
            return [
                'size_stock' => null,
                'available_sizes' => null,
                'size' => $product->size,
            ];
        }

        if (in_array($slug, ['accessories', 'home', 'dom'], true)) {
            return [
                'size_stock' => ['ONE SIZE' => $total],
                'available_sizes' => ['ONE SIZE'],
                'size' => 'ONE SIZE',
            ];
        }

        if (in_array($slug, ['shoes', 'obuv'], true)) {
            $grid = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
            $weights = [
                '36' => 1, '37' => 2, '38' => 3, '39' => 5, '40' => 6,
                '41' => 6, '42' => 5, '43' => 4, '44' => 2, '45' => 1,
            ];

            return self::buildFromGrid($grid, $total, $weights);
        }

        $grid = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $weights = [
            'XXS' => 1, 'XS' => 3, 'S' => 5, 'M' => 7, 'L' => 6, 'XL' => 4, 'XXL' => 2,
        ];

        return self::buildFromGrid($grid, $total, $weights);
    }

    /**
     * @param  list<string>  $grid
     * @param  array<string, int>  $weights
     * @return array{size_stock: array<string, int>, available_sizes: list<string>, size: string}
     */
    private static function buildFromGrid(array $grid, int $total, array $weights): array
    {
        if ($total === 1) {
            $size = self::weightedPick($grid, $weights);

            return [
                'size_stock' => [$size => 1],
                'available_sizes' => [$size],
                'size' => $size,
            ];
        }

        $maxSizes = min(5, count($grid), $total);
        $minSizes = min(2, $maxSizes);
        $numSizes = random_int($minSizes, $maxSizes);

        $picked = self::weightedPickMany($grid, $weights, $numSizes);
        $distribution = self::splitStock($total, count($picked));

        $sizeStock = [];
        foreach ($picked as $i => $size) {
            $sizeStock[$size] = $distribution[$i];
        }

        $available = array_keys($sizeStock);
        $primary = $available[0];
        $maxQty = 0;
        foreach ($sizeStock as $size => $qty) {
            if ($qty > $maxQty) {
                $maxQty = $qty;
                $primary = $size;
            }
        }

        return [
            'size_stock' => $sizeStock,
            'available_sizes' => $available,
            'size' => $primary,
        ];
    }

    /** @param  list<string>  $grid @param  array<string, int>  $weights */
    private static function weightedPick(array $grid, array $weights): string
    {
        return self::weightedPickMany($grid, $weights, 1)[0];
    }

    /**
     * @param  list<string>  $grid
     * @param  array<string, int>  $weights
     * @return list<string>
     */
    private static function weightedPickMany(array $grid, array $weights, int $count): array
    {
        $pool = $grid;
        shuffle($pool);

        $scored = [];
        foreach ($pool as $size) {
            $scored[] = ['size' => $size, 'score' => ($weights[$size] ?? 1) * random_int(1, 100)];
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_values(array_map(
            fn ($row) => $row['size'],
            array_slice($scored, 0, $count)
        ));
    }

    /** @return list<int> */
    private static function splitStock(int $total, int $parts): array
    {
        if ($parts === 1) {
            return [$total];
        }

        $result = array_fill(0, $parts, 1);
        $remaining = $total - $parts;

        while ($remaining > 0) {
            $idx = random_int(0, $parts - 1);
            $result[$idx]++;
            $remaining--;
        }

        return $result;
    }
}
