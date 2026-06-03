<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Review;
use App\Models\User;
use App\Support\OrderStatus;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $sales = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(total_price) as total')
            ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $promocodeUsage = Promocode::query()
            ->select('code', DB::raw('usage_count as total'))
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        $activeCouriersCount = User::role('courier')->count();

        $todayOrders = Order::query()->where('created_at', '>=', $today)->count();
        $todayRevenue = (float) Order::query()
            ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
            ->where('paid_at', '>=', $today)
            ->sum('total_price');

        $weekRevenue = (float) Order::query()
            ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
            ->where('paid_at', '>=', now()->subDays(7))
            ->sum('total_price');

        $pendingOrders = Order::query()->where('status', 'pending')->count();
        $paidAwaitingCourier = Order::query()->where('status', 'paid')->count();

        $paidOrdersCount = Order::query()
            ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
            ->count();

        $avgOrderValue = $paidOrdersCount > 0
            ? (float) Order::query()
                ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
                ->avg('total_price')
            : 0.0;

        $lowStockProducts = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->limit(8)
            ->get(['id', 'name', 'stock', 'sku']);

        $outOfStockCount = Product::query()->where('is_active', true)->where('stock', '<', 1)->count();

        $pendingReviews = Review::query()->where('status', Review::STATUS_PENDING)->count();

        $ordersByStatus = collect(OrderStatus::LABELS)->map(function (string $label, string $status) {
            return [
                'status' => $status,
                'label' => $label,
                'count' => Order::query()->where('status', $status)->count(),
            ];
        })->values();

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'sales',
            'promocodeUsage',
            'activeCouriersCount',
            'todayOrders',
            'todayRevenue',
            'weekRevenue',
            'pendingOrders',
            'paidAwaitingCourier',
            'avgOrderValue',
            'lowStockProducts',
            'outOfStockCount',
            'pendingReviews',
            'ordersByStatus',
            'recentOrders',
        ));
    }
}
