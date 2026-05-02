<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load([
            'clientOrders' => function ($q) {
                $q->latest()->with(['items.product', 'promocode', 'courier']);
            },
        ]);

        return view('profile.show', compact('user'));
    }
}
