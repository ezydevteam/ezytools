<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemaps = [
            ['url' => url('/sitemap-tools.xml'), 'lastmod' => now()->toAtomString()],
            // Add blog sitemap here when blog is implemented
        ];

        return response()->view('sitemaps.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    public function tools(): Response
    {
        $tools = Tool::with('category')
            ->where('is_active', true)
            ->get();

        return response()->view('sitemaps.tools', compact('tools'))
            ->header('Content-Type', 'application/xml');
    }
}
