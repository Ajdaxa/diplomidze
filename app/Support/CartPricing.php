<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

final class CartPricing
{
    /** @return list<array{product_id: int, size: string, quantity: int, key: string}> */
    public static function parseSessionLines(array $raw): array
    {
        $lines = [];
        foreach ($raw as $key => $qty) {
            if (! is_string($key) || ! str_contains($key, '|')) {
                continue;
            }
            [$id, $size] = explode('|', $key, 2);
            $lines[] = [
                'product_id' => (int) $id,
                'size' => $size,
                'quantity' => max(1, (int) $qty),
                'key' => $key,
            ];
        }

        return $lines;
    }

    /**
     * @param  list<array{product_id: int, size: string, quantity: int, key?: string}>  $lines
     * @return Collection<int, array{
     *     product: Product,
     *     size: string,
     *     quantity: int,
     *     key: string|null,
     *     unit_original: float,
     *     unit_price: float,
     *     line_original_total: float,
     *     line_total: float,
     *     product_discount: float
     * }>
     */
    public static function buildItems(array $lines): Collection
    {
        $productIds = collect($lines)->pluck('product_id')->unique()->filter();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        return collect($lines)
            ->map(function (array $line) use ($products) {
                $product = $products->get($line['product_id']);
                if (! $product) {
                    return null;
                }

                $qty = (int) $line['quantity'];
                $unitOriginal = (float) $product->price;
                $unitPrice = $product->saleUnitPrice();
                $lineOriginal = round($unitOriginal * $qty, 2);
                $lineTotal = round($unitPrice * $qty, 2);

                return [
                    'product' => $product,
                    'size' => $line['size'],
                    'quantity' => $qty,
                    'key' => $line['key'] ?? null,
                    'unit_original' => $unitOriginal,
                    'unit_price' => $unitPrice,
                    'line_original_total' => $lineOriginal,
                    'line_total' => $lineTotal,
                    'product_discount' => max(0.0, $lineOriginal - $lineTotal),
                ];
            })
            ->filter()
            ->values();
    }

    public static function buildFromSession(array $raw): Collection
    {
        return self::buildItems(self::parseSessionLines($raw));
    }

    /**
     * @return array{
     *     items: Collection,
     *     catalog_subtotal: float,
     *     product_discount: float,
     *     subtotal: float,
     *     promocode_discount: float,
     *     total: float,
     *     promocode_valid: bool,
     *     promocode_code: string|null,
     *     promocode_message: string|null
     * }
     */
    public static function summarize(Collection $items, ?string $promocodeInput = null): array
    {
        $catalogSubtotal = (float) $items->sum('line_original_total');
        $subtotal = (float) $items->sum('line_total');
        $productDiscount = max(0.0, $catalogSubtotal - $subtotal);

        $promoPreview = PromocodePricing::preview($subtotal, $promocodeInput);
        $promocodeDiscount = (float) $promoPreview['discount'];

        return [
            'items' => $items,
            'catalog_subtotal' => round($catalogSubtotal, 2),
            'product_discount' => round($productDiscount, 2),
            'subtotal' => round($subtotal, 2),
            'promocode_discount' => round($promocodeDiscount, 2),
            'total' => round(max(0.0, $subtotal - $promocodeDiscount), 2),
            'promocode_valid' => (bool) $promoPreview['promocode_valid'],
            'promocode_code' => $promoPreview['promocode_code'],
            'promocode_message' => $promoPreview['message'],
        ];
    }
}
