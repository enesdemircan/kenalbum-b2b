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
            // Eski dosyayı sil (eğer varsa)
            if ($settings->logo) {
                $oldPath = str_replace('/storage/', '', $settings->logo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $logoPath = $request->file('logo')->store('site', 'public');
            $settings->logo = '/storage/' . $logoPath;
            \Log::info('Logo uploaded', ['path' => $logoPath, 'full_path' => $settings->logo]);
        }

        if ($request->hasFile('logo_white')) {
            // Eski dosyayı sil (eğer varsa)
            if ($settings->logo_white) {
                $oldPath = str_replace('/storage/', '', $settings->logo_white);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $logoWhitePath = $request->file('logo_white')->store('site', 'public');
            $settings->logo_white = '/storage/' . $logoWhitePath;
            \Log::info('Logo white uploaded', ['path' => $logoWhitePath, 'full_path' => $settings->logo_white]);
        }

        if ($request->hasFile('favicon')) {
            // Eski dosyayı sil (eğer varsa)
            if ($settings->favicon) {
                $oldPath = str_replace('/storage/', '', $settings->favicon);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $faviconPath = $request->file('favicon')->store('site', 'public');
            $settings->favicon = '/storage/' . $faviconPath;
            \Log::info('Favicon uploaded', ['path' => $faviconPath, 'full_path' => $settings->favicon]);
        }

        $settings->fill($request->except('logo', 'logo_white', 'favicon'));
        $settings->save();

        return redirect()->route('admin.site-settings.index')->with('success', 'Site ayarları başarıyla güncellendi!');
    }
} 