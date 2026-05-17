<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'notificationsEnabled' => Setting::get('certificates_notifications_enabled',
                config('certificates.notifications_enabled', false)),
            'expiryNoticeDays' => Setting::get('certificates_expiry_notice_days',
                config('certificates.expiry_notice_days', [30, 14, 7, 3, 1])),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'notifications_enabled' => ['nullable', 'boolean'],
            'expiry_notice_days'    => ['required', 'array', 'min:1'],
            'expiry_notice_days.*'  => ['integer', 'min:1', 'max:365'],
        ]);

        Setting::set(
            'certificates_notifications_enabled',
            (bool) ($data['notifications_enabled'] ?? false),
            'boolean'
        );

        Setting::set(
            'certificates_expiry_notice_days',
            array_values(array_unique(array_map('intval', $data['expiry_notice_days']))),
            'json'
        );

        return back()->with('status', 'settings-saved');
    }
}
