<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'maintenance_mode' => 'nullable|string', // 'on' or null
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'footer_text' => 'nullable|string',
        ]);

        // Handle text settings
        $textSettings = ['app_name', 'contact_email', 'contact_phone', 'currency', 'footer_text'];
        foreach ($textSettings as $key) {
            if (isset($data[$key])) {
                Setting::set($key, $data[$key]);
            }
        }

        // Handle Maintenance Mode
        Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0', 'boolean');

        // Handle Logo Upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', $path, 'image');
        }

        return redirect()->back()->with('success', 'Mipangilio imesasishwa kikamilifu!');
    }
}
