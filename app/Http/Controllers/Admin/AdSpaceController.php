<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSpace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdSpaceController extends Controller
{
    public function index()
    {
        $ads = AdSpace::all();
        return Inertia::render('Admin/AdSpaces/Index', [
            'ads' => $ads,
        ]);
    }

    public function update(Request $request, AdSpace $ad)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:adsense,custom_html,image',
            'code'            => 'nullable|string',
            'image_url'       => 'nullable|string|max:255',
            'link_url'        => 'nullable|string|max:255',
            'is_active'       => 'boolean',
            'is_available'    => 'boolean',
            'show_to'         => 'required|in:all,free,guest',
            'description'     => 'nullable|string|max:500',
            'dimensions'      => 'nullable|string|max:100',
            'est_impressions' => 'nullable|string|max:100',
            'price_3d'        => 'nullable|numeric|min:0',
            'price_7d'        => 'nullable|numeric|min:0',
            'price_30d'       => 'nullable|numeric|min:0',
        ]);

        $ad->update($validated);

        return redirect()->back()->with('success', 'Ad space updated successfully.');
    }
}
