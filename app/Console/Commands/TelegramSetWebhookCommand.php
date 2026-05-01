<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {--url= : Full webhook URL}';

    protected $description = 'Register Telegram webhook for this application';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured.');

            return self::FAILURE;
        }

        $url = $this->option('url') ?: rtrim(config('app.url'), '/').'/webhook/telegram';

        if (! str_starts_with($url, 'https://')) {
            $this->error('Telegram requires HTTPS webhook URL. Current URL: '.$url);

            return self::FAILURE;
        }

        $response = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $url,
            'allowed_updates' => ['message'],
            'drop_pending_updates' => true,
        ]);

        if ($response->failed() || ! $response->json('ok')) {
            $this->error('Failed to set webhook.');
            $this->line($response->body());

            return self::FAILURE;
        }

        $this->info('Telegram webhook has been set successfully.');
        $this->line('Webhook URL: '.$url);

        return self::SUCCESS;
    }
}
