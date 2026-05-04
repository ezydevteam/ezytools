<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tool;

class SchemaService
{
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => \App\Models\SiteSetting::getValue('site_name', 'EzyTools'),
            'url'      => config('app.url'),
            'logo'     => asset(\App\Models\SiteSetting::getValue('site_logo', 'images/logo.png')),
            'sameAs'   => array_filter([
                \App\Models\SiteSetting::getValue('social_facebook'),
                \App\Models\SiteSetting::getValue('social_youtube'),
                \App\Models\SiteSetting::getValue('social_reddit'),
            ]),
        ];
    }

    public function website(): array
    {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => \App\Models\SiteSetting::getValue('site_name', 'EzyTools'),
            'url'             => config('app.url'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => config('app.url') . '/tools?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public function webApplication(Tool $tool): array
    {
        $schema = [
            '@context'           => 'https://schema.org',
            '@type'              => 'WebApplication',
            'name'               => $tool->name,
            'url'                => route('tools.show', [$tool->category->slug, $tool->slug]),
            'description'        => $this->cleanDescription($tool),
            'applicationCategory'=> 'UtilitiesApplication',
            'operatingSystem'    => 'Web Browser',
            'browserRequirements'=> 'Requires JavaScript',
            'inLanguage'         => ['bn', 'en'],
            'isAccessibleForFree'=> !$tool->is_premium,
            'offers' => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'BDT',
            ],
        ];

        // Add AggregateRating for Google rich results (stars in search)
        $reviewCount = $tool->review_count;
        $averageRating = $tool->average_rating;

        if ($reviewCount > 0 && $averageRating > 0) {
            $schema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => round($averageRating, 1),
                'bestRating'  => 5,
                'worstRating' => 1,
                'ratingCount' => $reviewCount,
            ];
        }

        return $schema;
    }

    public function breadcrumb(array $items): array
    {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => collect($items)->map(fn($item, $i) => [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $item['name'],
                'item'     => $item['url'],
            ])->toArray(),
        ];
    }

    public function faqPage(array $faqs): array
    {
        if (empty($faqs)) return [];

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn($faq) => [
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ],
            ])->toArray(),
        ];
    }

    public function toScript(array $schema): string
    {
        return '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . '</script>';
    }

    /**
     * Get clean description from SEO about content, fallback to short_description.
     */
    private function cleanDescription(Tool $tool): string
    {
        if (!$tool->relationLoaded('seoContent')) {
            $tool->load('seoContent');
        }

        $about = $tool->seoContent->about_content_en ?? $tool->seoContent->about_content ?? null;

        if ($about) {
            $clean = strip_tags($about);
            $clean = preg_replace('/\*\*([^*]+)\*\*/', '$1', $clean);
            $clean = preg_replace('/\n+/', ' ', $clean);
            $clean = preg_replace('/\s+/', ' ', trim($clean));
            return mb_substr($clean, 0, 300);
        }

        return $tool->short_description ?? $tool->name;
    }

    /**
     * Article schema for blog posts (Google rich results).
     */
    public function article(Post $post): array
    {
        $siteName = \App\Models\SiteSetting::getValue('site_name', 'EzyTools');

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $post->meta_title ?: $post->title,
            'description'   => $post->meta_description ?: $post->excerpt,
            'url'           => route('blog.show', $post->slug),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified'  => $post->updated_at?->toIso8601String(),
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => $siteName,
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => asset(\App\Models\SiteSetting::getValue('site_logo', 'images/logo.png')),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => route('blog.show', $post->slug),
            ],
        ];

        // Author
        if ($post->relationLoaded('author') && $post->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name'  => $post->author->name,
            ];
        } else {
            $schema['author'] = [
                '@type' => 'Organization',
                'name'  => $siteName,
            ];
        }

        // Featured image
        if ($post->featured_image) {
            $schema['image'] = asset('storage/' . $post->featured_image);
        }

        return $schema;
    }

    /**
     * Product/SaaS schema for pricing page.
     */
    public function product(): array
    {
        $siteName = \App\Models\SiteSetting::getValue('site_name', 'EzyTools');

        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $siteName . ' Pro',
            'description' => 'Premium subscription for ' . $siteName . ' — unlimited AI credits, ad-free experience, and access to all premium tools.',
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $siteName,
            ],
            'offers'      => [
                [
                    '@type'         => 'Offer',
                    'name'          => 'Monthly Pro',
                    'price'         => \App\Models\SiteSetting::getValue('pro_price_monthly_usd', '5'),
                    'priceCurrency' => 'USD',
                    'availability'  => 'https://schema.org/InStock',
                    'url'           => route('pricing'),
                ],
                [
                    '@type'         => 'Offer',
                    'name'          => 'Yearly Pro',
                    'price'         => \App\Models\SiteSetting::getValue('pro_price_yearly_usd', '49'),
                    'priceCurrency' => 'USD',
                    'availability'  => 'https://schema.org/InStock',
                    'url'           => route('pricing'),
                ],
            ],
        ];
    }
}
