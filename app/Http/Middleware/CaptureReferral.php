<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref');
        if (is_string($ref) && $ref !== '') {
            $request->session()->put('referral_code', strtoupper(trim($ref)));
        }

        return $next($request);
    }
}
