<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpAuthController extends Controller
{
    public function showForm(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => (string) $request->query('redirect')]);
        }

        return view('auth.otp-login');
    }

    public function showPasswordForm(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => (string) $request->query('redirect')]);
        }

        return view('auth.password-login');
    }

    public function showRegisterForm(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => (string) $request->query('redirect')]);
        }

        return view('auth.register');
    }

    public function loginWithPassword(Request $request)
    {
        $validated = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::query()
            ->where('email', trim($validated['identity']))
            ->orWhere('phone', trim($validated['identity']))
            ->first();

        if (! $user || ! $user->password || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Неверные данные для входа.',
            ]);
        }

        Auth::login($user, remember: true);

        return $this->redirectByRole($user)
            ->with('status', 'Успешно авторизировались.')
            ->with('status_type', 'success');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => isset($validated['phone']) ? trim((string) $validated['phone']) : null,
            'password' => $validated['password'],
            'role' => 'client',
        ]);
        $user->syncRoles(['client']);

        Auth::login($user, remember: true);

        return redirect()->intended(route('home'))
            ->with('status', 'Регистрация прошла успешно. Добро пожаловать!')
            ->with('status_type', 'success');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('otp.form')
            ->with('status', 'Вы вышли из аккаунта.')
            ->with('status_type', 'info');
    }

    private function redirectByRole(User $user)
    {
        if ($user->hasRole('courier')) {
            return redirect()->intended(route('courier.orders.index'));
        }

        if ($user->hasAnyRole(['admin', 'manager'])) {
            return redirect()->intended(route('admin.hub'));
        }

        return redirect()->intended(route('home'));
    }
}
