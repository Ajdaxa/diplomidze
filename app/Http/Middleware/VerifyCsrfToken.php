<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * В Laravel 10 исключения задаются здесь (middleware «web»).
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/yookassa',
    ];
}
