<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message', []);
        $text = trim((string) data_get($message, 'text', ''));
        $chatId = (string) data_get($message, 'chat.id', '');

        if (! Str::startsWith($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        $parts = preg_split('/\s+/', $text);
        $token = $parts[1] ?? null;

        if (! $token || ! $chatId) {
            return response()->json(['ok' => true]);
        }

        $user = User::query()
            ->where('telegram_link_token', $token)
            ->whereNotNull('telegram_link_token_expires_at')
            ->where('telegram_link_token_expires_at', '>', now())
            ->first();

        if (! $user) {
            return response()->json(['ok' => true]);
        }

        $user->update([
            'telegram_chat_id' => $chatId,
            'telegram_link_token' => null,
            'telegram_link_token_expires_at' => null,
        ]);

        return response()->json(['ok' => true]);
    }
}
