<?php

namespace App\Http\Controllers;

use App\Models\AdInquiry;
use App\Models\AdSpace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdvertiseController extends Controller
{
    /**
     * Show the public Advertise page with available ad spaces and pricing.
     */
    public function index()
    {
        $adSpaces = AdSpace::where('is_available', true)
            ->get(['id', 'name', 'position', 'description', 'dimensions', 'est_impressions', 'price_3d', 'price_7d', 'price_30d']);

        return Inertia::render('Pages/Advertise', [
            'adSpaces' => $adSpaces,
        ]);
    }

    /**
     * Handle ad inquiry form submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'company'   => 'nullable|string|max:255',
            'website'   => 'nullable|url|max:255',
            'ad_spaces' => 'required|array|min:1',
            'ad_spaces.*' => 'exists:ad_spaces,id',
            'duration'  => 'required|in:3d,7d,30d,custom',
            'budget'    => 'nullable|string|max:100',
            'message'   => 'required|string|max:2000',
        ]);

        $email = $validated['email'];

        // Block if there's already a pending or contacted inquiry from this email
        $pendingInquiry = AdInquiry::where('email', $email)
            ->whereIn('status', ['pending', 'contacted'])
            ->latest()
            ->first();

        if ($pendingInquiry) {
            return back()->withErrors([
                'email' => 'You already have a pending inquiry (' . $pendingInquiry->inquiry_id . '). Please wait until it is processed before submitting a new one.',
            ])->withInput();
        }

        // Block if rejected within the last 24 hours
        $recentRejection = AdInquiry::where('email', $email)
            ->where('status', 'rejected')
            ->where('updated_at', '>=', now()->subHours(24))
            ->latest()
            ->first();

        if ($recentRejection) {
            return back()->withErrors([
                'email' => 'Your previous inquiry was declined. Please wait 24 hours before submitting a new one.',
            ])->withInput();
        }

        $validated['inquiry_id'] = AdInquiry::generateInquiryId();

        AdInquiry::create($validated);

        return back()->with('success', 'Your advertising inquiry has been submitted! We will contact you within 24 hours. Your reference ID is: ' . $validated['inquiry_id']);
    }
}
