<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $settings = (object) $settings;

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $validKeys = [
            'site_name', 'tagline', 'email', 'phone', 'address',
            'facebook', 'instagram', 'tiktok', 'youtube',
            'logo', 'favicon', 'meta_title', 'meta_description', 'meta_keywords',
            'primary_color', 'accent_color',
        ];

        foreach ($validKeys as $key) {
            if ($key === 'logo' && $request->hasFile('logo')) {
                $oldLogo = Setting::where('key', 'logo')->value('value');
                if ($oldLogo) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('logo')->store('settings', 'public');
                Setting::updateOrCreate(['key' => 'logo'], ['value' => $path]);

                continue;
            }

            if ($key === 'favicon' && $request->hasFile('favicon')) {
                $oldFavicon = Setting::where('key', 'favicon')->value('value');
                if ($oldFavicon) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                $path = $request->file('favicon')->store('settings', 'public');
                Setting::updateOrCreate(['key' => 'favicon'], ['value' => $path]);

                continue;
            }

            if ($request->filled($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        clearSettingCache();

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
