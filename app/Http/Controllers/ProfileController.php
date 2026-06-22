<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CourierStatsService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly CourierStatsService $courierStatsService,
    ) {
    }

    public function show()
    {
        $user = Auth::user();
        $user->load([
            'clientOrders' => function ($q) {
                $q->latest()->with(['items.product', 'promocode', 'courier']);
            },
        ]);

        $courierStats = $user->isCourier()
            ? $this->courierStatsService->forUser($user)
            : null;

        return view('profile.show', compact('user', 'courierStats'));
    }
}
