<?php

namespace App\Services;

use App\Models\Tool;
use App\Models\ToolCategory;

class MetaService
{
    public function forTool(Tool $tool): array
    {
        // Ensure seoContent is loaded for description fallback
        if (!$tool->relationLoaded('seoContent')) {
            $tool->load('seoContent');
        }

        return [
            'title'       => $this->toolTitle($tool),
            'description' => $this->toolDescription($tool),
            'keywords'    => $tool->meta_keywords,
            'og_title'    => $this->toolTitle($tool),
            'og_description' => $this->toolDescription($tool),
            'og_image'    => $this->toolOgImage($tool),
            'og_url'      => route('tools.show', [$tool->category->slug, $tool->slug]),
            'og_type'     => 'website',
            'twitter_card'=> 'summary_large_image',
            'canonical'   => route('tools.show', [$tool->category->slug, $tool->slug]),
        ];
    }

    public function forHome(): array
    {
        $siteName = \App\Models\SiteSetting::getValue('site_name', config('app.name'));
        $ogImage = \App\Models\SiteSetting::getValue('og_default_image');

        return [
            'title'       => \App\Models\SiteSetting::getValue('meta_title', $siteName . ' — Best Free Online Tools Collection'),
            'description' => \App\Models\SiteSetting::getValue('meta_description', 'Collection of 150+ free online tools including AI, PDF, video, image converters, calculators, and more.'),
            'keywords'    => \App\Models\SiteSetting::getValue('meta_keywords', 'free online tools, converters, calculators, AI tools, PDF tools, image tools'),
            'og_image'    => $ogImage ?: asset('images/og-home.png'),
            'og_type'     => 'website',
            'canonical'   => url('/'),
        ];
    }

    public function forCategory(ToolCategory $category): array
    {
        $title = $category->name . ' — Free Online Tools Collection | EzyTools';
        $description = 'Free online ' . $category->name . ' tools. ' . $category->description;
        $url = route('tools.category', $category->slug);

        return [
            'title'          => $title,
            'description'    => $description,
            'keywords'       => strtolower($category->name) . ', free ' . strtolower($category->name) . ', free online tools, best online tools, free tools, ezytools',
            'og_title'       => $title,
            'og_description' => $description,
            'og_url'         => $url,
            'og_type'        => 'website',
            'canonical'      => $url,
        ];
    }

    private function toolTitle(Tool $tool): string
    {
        return $tool->meta_title
            ?: $tool->name . ' — Best Free Online Tool | ' . \App\Models\SiteSetting::getValue('site_name', 'EzyTools');
    }

    private function toolDescription(Tool $tool): string
    {
        if ($tool->meta_description) {
            return $tool->meta_description;
        }

        // Use SEO about content (stripped of markdown) as meta description
        $about = $tool->seoContent->about_content_en ?? $tool->seoContent->about_content ?? null;
        if ($about) {
            $clean = strip_tags($about);
            $clean = preg_replace('/\*\*([^*]+)\*\*/', '$1', $clean); // remove **bold**
            $clean = preg_replace('/\n+/', ' ', $clean); // newlines to spaces
            $clean = preg_replace('/\s+/', ' ', trim($clean)); // collapse whitespace
            return mb_substr($clean, 0, 160);
        }

        return 'Use ' . $tool->name . ' online for free. ' . $tool->short_description;
    }

    private function toolOgImage(Tool $tool): string
    {
        // Check if tool has custom OG image or use dynamic generator later
        return asset("images/tools/og/{$tool->slug}.png");
    }
}
