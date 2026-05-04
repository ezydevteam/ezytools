<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolReview;
use Illuminate\Http\Request;

class ToolReviewController extends Controller
{
    /**
     * Simple one-click rating. One rating per IP per tool.
     */
    public function rate(Request $request, Tool $tool)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Upsert: one rating per IP per tool
        ToolReview::updateOrCreate(
            [
                'tool_id'    => $tool->id,
                'ip_address' => $request->ip(),
            ],
            [
                'rating'      => $request->rating,
                'user_id'     => auth()->id(),
                'is_approved' => true,
            ]
        );

        return back();
    }
}
