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

        // ✅ If no IPs configured → allow all
        if (empty($ips)) {
            return $next($request);
        }

        // Convert stored IPs into array
        $allowedIps = array_filter(array_map('trim', explode("\n", $ips)));

        // Check IP
        if (!in_array($request->ip(), $allowedIps)) {
            abort(403, 'Access denied. IP not allowed.');
        }

        return $next($request);
    }
}
