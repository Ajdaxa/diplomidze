<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPromoMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $promoCode,
        public readonly string $discountText,
    ) {
    }

    public function handle(TelegramService $telegramService): void
    {
        $user = User::query()->find($this->userId);

        if (! $user || ! $user->telegram_chat_id) {
            return;
        }

        $telegramService->sendPromo($user->telegram_chat_id, $this->promoCode, $this->discountText);
    }
}
