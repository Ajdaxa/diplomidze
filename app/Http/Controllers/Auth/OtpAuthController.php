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

    public function showForm()
    {
        return view('auth.otp-login');
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

        return back()->with('status', 'Код отправлен в Telegram.');
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

        return redirect()->route('home');
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

        return redirect()->route('home');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('otp.form');
    }
}
