<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiteSettingRequest;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::getSettings();
        return view('admin.site_settings.index', compact('settings'));
    }

    public function update(StoreSiteSettingRequest $request)
    {
        \Log::info('Site settings update request received', $request->all());

        $settings = SiteSetting::getSettings();

        // Logo yükleme
        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            
            $logoPath = $request->file('logo')->store('site', 'public');
            $settings->logo = '/storage/' . $logoPath;
        }

        if ($request->hasFile('logo_white')) {
            if ($settings->logo_white && Storage::disk('public')->exists($settings->logo_white)) {
                Storage::disk('public')->delete($settings->logo_white);
            }
            $logoWhitePath = $request->file('logo_white')->store('site', 'public');
            $settings->logo_white = '/storage/' . $logoWhitePath;
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $faviconPath = $request->file('favicon')->store('site', 'public');
            $settings->favicon = '/storage/' . $faviconPath;
        }

        $settings->fill($request->except('logo', 'logo_white', 'favicon'));
        $settings->save();

        return redirect()->route('admin.site-settings.index')->with('success', 'Site ayarları başarıyla güncellendi!');
    }
} 