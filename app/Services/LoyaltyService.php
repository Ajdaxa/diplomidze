<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class LoyaltyService
{
    public const POINTS_PER_RUBLE = 0.05;

    /** Максимум списания баллами от суммы заказа после промокода. */
    public const MAX_ORDER_PERCENT = 0.30;

    public function ensureReferralCode(User $user): string
    {
        if (filled($user->referral_code)) {
            return (string) $user->referral_code;
        }

        $code = 'DAB-'.strtoupper(Str::random(6));
        while (User::query()->where('referral_code', $code)->exists()) {
            $code = 'DAB-'.strtoupper(Str::random(6));
        }

        $user->update(['referral_code' => $code]);

        return $code;
    }

    public function attachReferrer(User $user, ?string $referralCode): void
    {
        if ($user->referred_by_user_id || ! filled($referralCode)) {
            return;
        }

        $referrer = User::query()
            ->where('referral_code', strtoupper(trim($referralCode)))
            ->where('id', '!=', $user->id)
            ->first();

        if (! $referrer) {
            return;
        }

        $user->update(['referred_by_user_id' => $referrer->id]);
    }

    public function maxSpendablePoints(User $user, float $subtotalAfterPromo): int
    {
        if ($subtotalAfterPromo < 1 || (int) $user->loyalty_points < 1) {
            return 0;
        }

        $capByPercent = (int) floor($subtotalAfterPromo * self::MAX_ORDER_PERCENT);

        return max(0, min((int) $user->loyalty_points, $capByPercent, (int) floor($subtotalAfterPromo)));
    }

    public function pointsDiscount(int $pointsToUse, float $subtotal): float
    {
        if ($pointsToUse < 1 || $subtotal < 1) {
            return 0.0;
        }

        return min((float) $pointsToUse, $subtotal);
    }

    public function awardForPaidOrder(Order $order): void
    {
        if (! Cache::add('loyalty_awarded_order_'.$order->id, true, now()->addYear())) {
            return;
        }

        $order->loadMissing('user');
        $user = $order->user;
        if (! $user) {
            return;
        }

        $earned = (int) floor((float) $order->total_price * self::POINTS_PER_RUBLE);
        if ($earned > 0) {
            $user->increment('loyalty_points', $earned);
        }

    }

    public function redeemPoints(User $user, int $points): void
    {
        if ($points < 1) {
            return;
        }

        $user->decrement('loyalty_points', min($points, (int) $user->loyalty_points));
    }
}
