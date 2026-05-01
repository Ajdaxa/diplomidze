<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendPromoMessageJob;
use App\Http\Controllers\Controller;
use App\Models\Promocode;
use App\Models\User;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index()
    {
        $promocodes = Promocode::query()->latest()->paginate(20);

        return view('admin.promocodes.index', compact('promocodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promocodes,code'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:1'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Promocode::query()->create($validated + ['is_active' => true]);

        return back()->with('status', 'Промокод создан.');
    }

    public function broadcastPromo(Promocode $promocode)
    {
        $discountText = $promocode->type === 'percent'
            ? "{$promocode->value}%"
            : "{$promocode->value} ₽";

        User::query()
            ->whereNotNull('telegram_chat_id')
            ->chunk(100, function ($users) use ($promocode, $discountText) {
                foreach ($users as $user) {
                    SendPromoMessageJob::dispatch($user->id, $promocode->code, $discountText);
                }
            });

        return back()->with('status', 'Рассылка поставлена в очередь.');
    }
}
