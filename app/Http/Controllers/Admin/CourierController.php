<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = User::role('courier')->latest()->paginate(20);

        return view('admin.couriers.index', compact('couriers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'courier_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $courier = User::query()->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'courier',
            'courier_commission_percent' => $validated['courier_commission_percent'] ?? 10,
        ]);

        $courier->syncRoles(['courier']);

        return back()->with('status', 'Курьер создан.');
    }

    public function update(Request $request, User $courier)
    {
        abort_unless($courier->hasRole('courier'), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($courier->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($courier->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'courier_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $courier->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => ! empty($validated['password']) ? Hash::make($validated['password']) : $courier->password,
            'role' => 'courier',
            'courier_commission_percent' => $validated['courier_commission_percent'] ?? $courier->courier_commission_percent ?? 10,
        ]);
        $courier->syncRoles(['courier']);

        return back()->with('status', 'Курьер обновлен.');
    }

    public function destroy(User $courier)
    {
        abort_unless($courier->hasRole('courier'), 404);

        $courier->delete();

        return back()->with('status', 'Курьер удален.');
    }
}
