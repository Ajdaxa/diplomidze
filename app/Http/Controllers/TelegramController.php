<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    /**
     * Webhook Telegram Bot API: тело запроса — объект Update (JSON).
     * Поле message.text для личных чатов часто выглядит как "/start TOKEN" или "/start@YourBot TOKEN".
     *
     * Привязка идёт по полю users.telegram_link_token (не tg_link_token).
     */
    public function handle(Request $request)
    {
        Log::info('telegram.webhook.raw', $request->all());

        $message = $this->extractMessage($request);
        if (! is_array($message)) {
            Log::debug('telegram.webhook.no_message_block');

            return response()->json(['ok' => true]);
        }

        $text = trim((string) data_get($message, 'text', ''));
        $chatId = data_get($message, 'chat.id');

        if ($chatId === null || $chatId === '') {
            Log::warning('telegram.webhook.missing_chat_id');

            return response()->json(['ok' => true]);
        }

        $chatIdStr = (string) $chatId;

        // Допускаем /start, /start TOKEN, /start@BotName, /start@BotName TOKEN
        if (! preg_match('#^/start(?:@\w+)?(?:\s+(.*))?$#isu', $text, $m)) {
            return response()->json(['ok' => true]);
        }

        $payload = isset($m[1]) ? trim((string) $m[1]) : '';

        if ($payload === '') {
            $this->telegramService->replyWelcomeNeedSiteLink($chatIdStr);

            return response()->json(['ok' => true]);
        }

        $token = $payload;

        $user = User::query()
            ->where('telegram_link_token', $token)
            ->whereNotNull('telegram_link_token_expires_at')
            ->where('telegram_link_token_expires_at', '>', now())
            ->first();

        if (! $user) {
            Log::info('telegram.webhook.token_not_found_or_expired', [
                'token_prefix' => substr($token, 0, 6).'…',
            ]);
            $this->telegramService->sendMessage(
                $chatIdStr,
                'Ссылка привязки устарела или уже использована. Запросите новую на сайте Дəб (вход через Telegram).',
                'Telegram link invalid'
            );

            return response()->json(['ok' => true]);
        }

        $user->update([
            'telegram_chat_id' => $chatIdStr,
            'telegram_link_token' => null,
            'telegram_link_token_expires_at' => null,
        ]);

        $loginToken = Str::random(48);
        Cache::put('tg_login:'.$loginToken, $user->id, now()->addMinutes(20));

        $loginUrl = route('otp.telegram.complete', ['token' => $loginToken]);

        $this->telegramService->sendMessage(
            $chatIdStr,
            "✅ Telegram привязан.\n\nНажмите, чтобы войти на сайт:\n{$loginUrl}\n\nСсылка действует 20 минут.",
            'Account linked'
        );

        return response()->json(['ok' => true]);
    }

    /** @return array<string, mixed>|null */
    private function extractMessage(Request $request): ?array
    {
        foreach (['message', 'edited_message'] as $key) {
            $msg = $request->input($key);
            if (is_array($msg) && isset($msg['chat'])) {
                return $msg;
            }
        }

        return null;
    }
}
