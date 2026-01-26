<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class TrackingIpController extends Controller
{
    public function index()
    {
        $allowedIps = Setting::get('tracking_allowed_ips', '');

        return view('admin.settings.tracking-ips', compact('allowedIps'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'allowed_ips' => 'nullable|string'
        ]);

        Setting::updateOrCreate(
            ['key' => 'tracking_allowed_ips'],
            ['value' => $request->allowed_ips]
        );

        Cache::forget('tracking_allowed_ips');

        return redirect()
            ->back()
            ->with('success', 'Allowed IPs updated successfully.');
    }
}
