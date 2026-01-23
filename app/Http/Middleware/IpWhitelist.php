<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = explode(',', env('TRACKING_ALLOWED_IPS'));

        if (! in_array($request->ip(), $allowedIps)) {
            abort(403, 'Access denied. IP not allowed.');
        }

        return $next($request);
    }
}
