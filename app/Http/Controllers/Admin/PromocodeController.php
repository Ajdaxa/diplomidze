<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
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
            'purpose' => ['nullable', 'in:standard,referral,loyalty'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:1'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Promocode::query()->create($validated + [
            'purpose' => $validated['purpose'] ?? Promocode::PURPOSE_STANDARD,
            'is_active' => true,
        ]);

        return back()->with('status', 'Промокод создан.');
    }

    public function update(Request $request, Promocode $promocode)
    {
        $validated = $request->validate([
            'value' => ['required', 'numeric', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $promocode->update([
            'value' => $validated['value'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Промокод обновлен.');
    }

    public function destroy(Promocode $promocode)
    {
        $promocode->delete();

        return back()->with('status', 'Промокод удален.');
    }
}
