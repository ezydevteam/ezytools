<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdInquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdInquiryController extends Controller
{
    public function index()
    {
        $inquiries = AdInquiry::latest()->paginate(20);

        $stats = [
            'total'     => AdInquiry::count(),
            'pending'   => AdInquiry::status('pending')->count(),
            'contacted' => AdInquiry::status('contacted')->count(),
            'approved'  => AdInquiry::status('approved')->count(),
        ];

        return Inertia::render('Admin/AdInquiries/Index', [
            'inquiries' => $inquiries,
            'stats'     => $stats,
        ]);
    }

    public function update(Request $request, AdInquiry $inquiry)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,contacted,approved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry ' . $inquiry->inquiry_id . ' updated.');
    }

    public function destroy(AdInquiry $inquiry)
    {
        $inquiry->delete();

        return back()->with('success', 'Inquiry deleted.');
    }
}
