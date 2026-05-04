<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $baseUrl;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');
        $this->baseUrl = $token ? "https://api.telegram.org/bot{$token}/" : '';
    }

    /**
     * Ответ пользователю, который нажал /start без deep-link токена с сайта.
     */
    public function replyWelcomeNeedSiteLink(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            'Чтобы привязать аккаунт, откройте сайт Дəб → вход через Telegram и следуйте ссылке с параметром <code>/start</code> (кнопка в браузере откроет бота уже с токеном).',
            'Telegram welcome'
        );
    }

    public function sendMessage(string $chatId, string $message, string $action = 'System Notification'): void
    {
        $token = config('services.telegram.bot_token');
        if (! $token || ! $chatId) {
            Log::warning('Telegram sendMessage skipped: missing bot_token or chat_id');

            return;
        }

        $response = Http::timeout(15)->post($this->baseUrl.'sendMessage', [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->failed()) {
            Log::warning('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        }

        $this->logSystemNotification($chatId, $message, $action);
    }

    public function sendOtp(string $chatId, string $otpCode): void
    {
        $text = "<b>Твой код подтверждения в ДЯБ:</b>\n\n<code>{$otpCode}</code>\n\nНикому не говори его, даже брату!";

        $this->sendMessage($chatId, $text, 'System Notification');
    }

    public function courierArrived(string $chatId): void
    {
        $text = 'Джан, курьер уже у твоего подъезда! Выходи встречай обновки.';

        $this->sendMessage($chatId, $text, 'System Notification');
    }

    public function orderPaid(string $chatId, int $orderId): void
    {
        $this->sendMessage($chatId, "Заказ #{$orderId} оплачен! Начинаем собирать.", 'System Notification');
    }

    public function orderDelivered(string $chatId): void
    {
        $this->sendMessage($chatId, 'Спасибо за покупку в ДЯБ! Носи с удовольствием.', 'System Notification');
    }

    public function sendPromo(string $chatId, string $promoCode, string $discount): void
    {
        $text = "🎁 <b>Лови подгон от ДЯБ!</b>\n\nТвой промокод: <code>{$promoCode}</code>\nСкидка: <b>{$discount}</b>\n\nПрименяй на сайте и кайфуй!";

        $this->sendMessage($chatId, $text, 'System Notification');
    }

    private function logSystemNotification(string $chatId, string $message, string $action): void
    {
        $user = User::query()->where('telegram_chat_id', $chatId)->first();

        if (! $user) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $user->id,
            'entity_type' => 'telegram_message',
            'entity_id' => $user->id,
            'action' => $action,
            'description' => $message,
            'after_state' => [
                'chat_id' => $chatId,
            ],
        ]);
    }
}
