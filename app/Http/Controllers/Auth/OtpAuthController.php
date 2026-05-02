<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpAuthController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

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

    public function showTelegramForm(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => (string) $request->query('redirect')]);
        }

        return view('auth.telegram-login');
    }

    public function sendCode(Request $request)
    {
        $validated = $request->validate([
            'identity' => ['required', 'string'],
        ]);

        $identity = trim($validated['identity']);

        $user = User::query()
            ->where('email', $identity)
            ->orWhere('phone', $identity)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'identity' => 'Пользователь не найден.',
            ]);
        }

        if (! $user->telegram_chat_id) {
            $token = Str::random(32);
            $user->update([
                'telegram_link_token' => $token,
                'telegram_link_token_expires_at' => now()->addMinutes(30),
            ]);

            $botUsername = config('services.telegram.bot_username');
            $link = $botUsername
                ? "https://t.me/{$botUsername}?start={$token}"
                : "Откройте бота и отправьте /start {$token}";

            return back()->withErrors([
                'identity' => "Сначала привяжите Telegram: {$link}",
            ]);
        }

        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $cacheKey = 'otp_code_'.($user->phone ?: $user->id);

        Cache::put($cacheKey, [
            'code' => $code,
            'user_id' => $user->id,
        ], now()->addMinutes(5));

        $this->telegramService->sendOtp($user->telegram_chat_id, $code);

        session([
            'otp_cache_key' => $cacheKey,
        ]);

        return back()
            ->with('status', 'Код отправлен в Telegram.')
            ->with('status_type', 'info');
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:4'],
        ]);

        $payload = Cache::get(session('otp_cache_key'));

        if (! $payload) {
            throw ValidationException::withMessages([
                'code' => 'Код устарел. Запросите новый.',
            ]);
        }

        if (($payload['code'] ?? null) !== $validated['code']) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код.',
            ]);
        }

        Auth::loginUsingId($payload['user_id'], remember: true);
        Cache::forget(session('otp_cache_key'));

        session()->forget(['otp_cache_key']);

        return $this->redirectByRole(Auth::user())
            ->with('status', 'Успешно авторизировались.')
            ->with('status_type', 'success');
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
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
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

    public function telegramAutoRegister()
    {
        $token = Str::random(32);
        $suffix = Str::lower(Str::random(8));

        $user = User::query()->create([
            'name' => 'Telegram User '.$suffix,
            'email' => "tg_{$suffix}@dyab.local",
            'password' => Str::random(24),
            'role' => 'client',
            'telegram_link_token' => $token,
            'telegram_link_token_expires_at' => now()->addMinutes(30),
        ]);
        $user->syncRoles(['client']);

        $botUsername = config('services.telegram.bot_username');
        $link = $botUsername
            ? "https://t.me/{$botUsername}?start={$token}"
            : "Откройте бота и отправьте /start {$token}";

        return redirect()->route('otp.telegram.form')
            ->with('status', "Аккаунт создан. Подтвердите вход в Telegram: {$link}")
            ->with('status_type', 'info');
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
