<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::role('manager')->latest()->paginate(20);

        return view('admin.managers.index', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $manager = User::query()->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'manager',
        ]);

        $manager->syncRoles(['manager']);

        return back()->with('status', 'Менеджер назначен.');
    }

    public function update(Request $request, User $manager)
    {
        abort_unless($manager->hasRole('manager'), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($manager->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($manager->id)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $manager->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => ! empty($validated['password']) ? Hash::make($validated['password']) : $manager->password,
            'role' => 'manager',
        ]);
        $manager->syncRoles(['manager']);

        return back()->with('status', 'Данные менеджера обновлены.');
    }

    public function destroy(User $manager)
    {
        abort_unless($manager->hasRole('manager'), 404);
        abort_if($manager->id === auth()->id(), 403, 'Нельзя удалить свой аккаунт.');

        $manager->delete();

        return back()->with('status', 'Менеджер удалён.');
    }
}
