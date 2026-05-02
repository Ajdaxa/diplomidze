<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Promocode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(total_price) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $promocodeUsage = Promocode::query()
            ->select('code', DB::raw('usage_count as total'))
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        $activeCouriersCount = User::role('courier')->whereNotNull('telegram_chat_id')->count();

        return view('admin.dashboard', compact('sales', 'promocodeUsage', 'activeCouriersCount'));
    }
}
