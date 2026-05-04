<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        // Group settings by their 'group' field
        $settings = SiteSetting::all()->groupBy('group');
        
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        // Validate file uploads (logos, favicons, etc.)
        $request->validate([
            'settings.*' => ['nullable'],
            'settings.site_logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'settings.site_favicon' => ['nullable', 'file', 'mimes:png,ico,svg', 'max:512'],
        ]);

        // Merge text inputs and files for processing
        $inputs = $request->input('settings', []);
        $files = $request->file('settings', []);
        $allKeys = array_unique(array_merge(array_keys($inputs), array_keys($files)));

        // Only allow updating existing setting keys
        $validKeys = SiteSetting::pluck('key')->toArray();

        foreach ($allKeys as $key) {
            if (!in_array($key, $validKeys)) {
                continue; // Skip unknown keys
            }

            // Sanitize text inputs — strip tags from non-HTML settings
            if (isset($inputs[$key]) && is_string($inputs[$key])) {
                $inputs[$key] = trim($inputs[$key]);
            }

            if ($request->hasFile("settings.{$key}")) {
                $file = $request->file("settings.{$key}");
                $path = $file->store('settings', 'public');

                // Delete old file if it exists
                $oldValue = SiteSetting::where('key', $key)->value('value');
                if ($oldValue && str_starts_with($oldValue, '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $oldValue);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                SiteSetting::setValue($key, Storage::url($path));
            } elseif (isset($inputs[$key])) {
                SiteSetting::setValue($key, $inputs[$key]);
            }
        }

        // Clear all setting caches after update
        SiteSetting::clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
