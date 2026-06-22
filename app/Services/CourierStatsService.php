<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CourierStatsService
{
    public function forUser(User $user): array
    {
        $percent = $this->commissionPercent($user);

        return [
            'commission_percent' => $percent,
            'today' => $this->periodStats($user, now()->startOfDay(), $percent),
            'week' => $this->periodStats($user, now()->startOfDay()->subDays(6), $percent),
            'month' => $this->periodStats($user, now()->startOfMonth(), $percent),
            'all_time' => $this->periodStats($user, null, $percent),
            'recent_deliveries' => $this->recentDeliveries($user),
        ];
    }

    public function commissionPercent(User $user): float
    {
        return round((float) ($user->courier_commission_percent ?? 10), 2);
    }

    private function periodStats(User $user, ?Carbon $from, float $percent): array
    {
        $query = Order::query()
            ->where('courier_id', $user->id)
            ->where('status', 'delivered');

        if ($from) {
            $query->where('updated_at', '>=', $from);
        }

        $ordersTotal = (float) $query->sum('total_price');
        $count = (int) $query->count();

        return [
            'count' => $count,
            'orders_total' => $ordersTotal,
            'earnings' => round($ordersTotal * $percent / 100, 2),
        ];
    }

    private function recentDeliveries(User $user): Collection
    {
        return Order::query()
            ->where('courier_id', $user->id)
            ->where('status', 'delivered')
            ->with('user')
            ->latest('updated_at')
            ->limit(5)
            ->get();
    }
}
