<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['s' => fn (string $k) => Setting::get($k)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'instagram_handle' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9._]+$/'],
            'tiktok_handle' => ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9._]+$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +().-]{7,}$/'],
            'notify_email' => ['nullable', 'email'],
            'announcement' => ['nullable', 'string', 'max:160'],
            'footer_note' => ['nullable', 'string', 'max:80'],
        ]);
        foreach ($data as $key => $value) {
            Setting::put($key, $value);
        }
        Setting::put('accepting_orders', $request->boolean('accepting_orders') ? '1' : '0');

        return back()->with('ok', 'Settings saved.');
    }
}
