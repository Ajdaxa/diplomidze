<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * В Laravel 10 исключения задаются здесь (middleware «web»).
     * Webhook Telegram: POST без CSRF-токена со стороны Telegram.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/yookassa',
        'webhook/telegram',
    ];
}
