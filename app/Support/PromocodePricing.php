<?php

namespace App\Support;

use App\Models\Promocode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class PromocodePricing
{
    public static function findActiveByCode(string $code): ?Promocode
    {
        $trimmed = trim($code);
        if ($trimmed === '') {
            return null;
        }

        return Promocode::query()
            ->whereRaw('LOWER(code) = ?', [Str::lower($trimmed)])
            ->where('is_active', true)
            ->first();
    }

    public static function redeemable(Promocode $promocode, ?User $user = null): bool
    {
        if ($promocode->expires_at && $promocode->expires_at->isPast()) {
            return false;
        }

        if ($promocode->usage_limit !== null && (int) $promocode->usage_limit > 0
            && (int) $promocode->usage_count >= (int) $promocode->usage_limit) {
            return false;
        }

        $user ??= Auth::user();

        if ($promocode->user_id && $user && (int) $promocode->user_id !== (int) $user->id) {
            return false;
        }

        if ($promocode->user_id && ! $user) {
            return false;
        }

        return true;
    }

    public static function meetsMinimumOrder(Promocode $promocode, float $subtotal): bool
    {
        if ($promocode->min_order_total === null) {
            return true;
        }

        return $subtotal >= (float) $promocode->min_order_total;
    }

    public static function discountAmount(float $subtotal, Promocode $promocode): float
    {
        if ($promocode->type === 'percent') {
            $discount = $subtotal * ((float) $promocode->value / 100);
        } else {
            $discount = (float) $promocode->value;
        }

        if ($promocode->max_discount) {
            $discount = min($discount, (float) $promocode->max_discount);
        }

        return min($discount, $subtotal);
    }

    /**
     * @return array{subtotal: float, discount: float, total: float, promocode_valid: bool, promocode_code: string|null, message: string|null}
     */
    public static function preview(float $subtotal, ?string $codeInput): array
    {
        $base = [
            'subtotal' => $subtotal,
            'discount' => 0.0,
            'total' => $subtotal,
            'promocode_valid' => false,
            'promocode_code' => null,
            'message' => null,
        ];

        $trimmed = trim((string) $codeInput);
        if ($trimmed === '') {
            return $base;
        }

        $promo = self::findActiveByCode($trimmed);
        if (! $promo) {
            return $base + [
                'message' => 'Промокод не найден.',
            ];
        }

        if (! self::redeemable($promo)) {
            return $base + [
                'message' => 'Промокод истёк или недоступен.',
            ];
        }

        if (! self::meetsMinimumOrder($promo, $subtotal)) {
            return $base + [
                'message' => 'Минимальная сумма заказа для промокода: '.number_format((float) $promo->min_order_total, 0, '.', ' ').' ₽.',
            ];
        }

        $discount = self::discountAmount($subtotal, $promo);
        $total = max(0.0, $subtotal - $discount);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'promocode_valid' => true,
            'promocode_code' => $promo->code,
            'message' => null,
        ];
    }
}
