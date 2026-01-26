<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class IpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        $ips = Setting::get('tracking_allowed_ips');

        if (!$ips) {
            abort(403, 'IP whitelist not configured.');
        }

        $allowedIps = array_map('trim', explode("\n", $ips));

        if (! in_array($request->ip(), $allowedIps)) {
            abort(403, 'Access denied. IP not allowed.');
        }

        return $next($request);
    }
}
