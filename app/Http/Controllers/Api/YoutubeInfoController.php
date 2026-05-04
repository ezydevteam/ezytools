<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class YoutubeInfoController extends Controller
{
    /**
     * Extract full metadata from a YouTube video.
     * Uses page scraping to get description, tags, views, likes, duration etc.
     */
    public function info(Request $request)
    {
        $request->validate(['url' => 'required|string']);

        $videoId = $this->extractVideoId($request->url);
        if (!$videoId) {
            return response()->json(['error' => 'Invalid YouTube URL'], 422);
        }

        try {
            $data = $this->scrapeVideoData($videoId);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch video info: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Extract video ID from various YouTube URL formats.
     */
    private function extractVideoId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=)([a-zA-Z0-9_-]{11})/',
            '/(?:youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            '/(?:youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
            '/(?:youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
            '/(?:youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        // Check if it's just the ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * Scrape video page for metadata.
     */
    private function scrapeVideoData(string $videoId): array
    {
        // Fetch the YouTube page
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get("https://www.youtube.com/watch?v={$videoId}");

        if (!$response->successful()) {
            throw new \Exception('Could not fetch YouTube page');
        }

        $html = $response->body();

        // Extract data from ytInitialPlayerResponse
        $playerData = $this->extractJson($html, 'ytInitialPlayerResponse');
        // Extract data from ytInitialData
        $initialData = $this->extractJson($html, 'ytInitialData');

        $result = [
            'videoId' => $videoId,
            'title' => null,
            'author' => null,
            'channelId' => null,
            'description' => null,
            'thumbnail' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
            'thumbnails' => [
                "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                "https://img.youtube.com/vi/{$videoId}/sddefault.jpg",
                "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
            ],
            'duration' => null,
            'views' => null,
            'likes' => null,
            'published' => null,
            'category' => null,
            'tags' => [],
            'isLive' => false,
            'isPrivate' => false,
        ];

        // Extract from player response
        if ($playerData) {
            $videoDetails = $playerData['videoDetails'] ?? [];
            $microformat = $playerData['microformat']['playerMicroformatRenderer'] ?? [];

            $result['title'] = $videoDetails['title'] ?? $this->extractMeta($html, 'og:title');
            $result['author'] = $videoDetails['author'] ?? $microformat['ownerChannelName'] ?? null;
            $result['channelId'] = $videoDetails['channelId'] ?? null;
            $result['description'] = $videoDetails['shortDescription'] ?? $microformat['description']['simpleText'] ?? null;
            $result['tags'] = $videoDetails['keywords'] ?? [];
            $result['views'] = (int) ($videoDetails['viewCount'] ?? 0);
            $result['isLive'] = $videoDetails['isLiveContent'] ?? false;
            $result['isPrivate'] = $videoDetails['isPrivate'] ?? false;

            // Duration in seconds → formatted
            $lengthSeconds = (int) ($videoDetails['lengthSeconds'] ?? 0);
            if ($lengthSeconds > 0) {
                $hours = floor($lengthSeconds / 3600);
                $minutes = floor(($lengthSeconds % 3600) / 60);
                $seconds = $lengthSeconds % 60;
                $result['duration'] = $hours > 0
                    ? sprintf('%d:%02d:%02d', $hours, $minutes, $seconds)
                    : sprintf('%d:%02d', $minutes, $seconds);
                $result['durationSeconds'] = $lengthSeconds;
            }

            // Published date
            $result['published'] = $microformat['publishDate'] ?? $microformat['uploadDate'] ?? null;

            // Category
            $result['category'] = $microformat['category'] ?? null;
        }

        // Try to extract likes from initialData
        if ($initialData) {
            $result['likes'] = $this->extractLikes($initialData);
            // Extract comment count
            $result['comments'] = $this->extractCommentCount($initialData);
        }

        // Fallback to meta tags
        if (!$result['title']) {
            $result['title'] = $this->extractMeta($html, 'og:title') ?? $this->extractMeta($html, 'title');
        }
        if (!$result['description']) {
            $result['description'] = $this->extractMeta($html, 'og:description');
        }

        return $result;
    }

    /**
     * Extract JSON variable from page source.
     */
    private function extractJson(string $html, string $varName): ?array
    {
        // Pattern: var ytInitialPlayerResponse = {...};
        $pattern = '/var\s+' . preg_quote($varName) . '\s*=\s*(\{.+?\})\s*;/s';
        if (preg_match($pattern, $html, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json) return $json;
        }

        // Alternative pattern: ytInitialPlayerResponse = {...};
        $pattern = '/' . preg_quote($varName) . '\s*=\s*(\{.+?\})\s*;/s';
        if (preg_match($pattern, $html, $matches)) {
            // Use a smarter extraction - find matching braces
            $start = strpos($html, $varName . ' = {');
            if ($start === false) $start = strpos($html, $varName . '={');
            if ($start !== false) {
                $braceStart = strpos($html, '{', $start);
                $extracted = $this->extractJsonBraces($html, $braceStart);
                if ($extracted) {
                    $json = json_decode($extracted, true);
                    if ($json) return $json;
                }
            }
        }

        return null;
    }

    /**
     * Extract balanced JSON from position.
     */
    private function extractJsonBraces(string $html, int $start): ?string
    {
        $depth = 0;
        $length = strlen($html);
        $inString = false;
        $escape = false;

        for ($i = $start; $i < $length && $i < $start + 5000000; $i++) {
            $char = $html[$i];

            if ($escape) {
                $escape = false;
                continue;
            }

            if ($char === '\\') {
                $escape = true;
                continue;
            }

            if ($char === '"' && !$escape) {
                $inString = !$inString;
                continue;
            }

            if (!$inString) {
                if ($char === '{') $depth++;
                if ($char === '}') {
                    $depth--;
                    if ($depth === 0) {
                        return substr($html, $start, $i - $start + 1);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract meta tag content.
     */
    private function extractMeta(string $html, string $property): ?string
    {
        // Try property
        if (preg_match('/<meta\s+property="' . preg_quote($property) . '"\s+content="([^"]*)"/', $html, $m)) {
            return html_entity_decode($m[1]);
        }
        // Try name
        if (preg_match('/<meta\s+name="' . preg_quote($property) . '"\s+content="([^"]*)"/', $html, $m)) {
            return html_entity_decode($m[1]);
        }
        // Try reversed attribute order
        if (preg_match('/<meta\s+content="([^"]*)"\s+property="' . preg_quote($property) . '"/', $html, $m)) {
            return html_entity_decode($m[1]);
        }
        return null;
    }

    /**
     * Extract like count from ytInitialData.
     */
    private function extractLikes(array $data): ?int
    {
        $json = json_encode($data);
        // Look for like button accessibility text
        if (preg_match('/"accessibilityText":"([\d,]+)\s+likes?"/', $json, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }
        // Alternative: look for toggledText with like count
        if (preg_match('/"defaultText":\{"accessibility":\{"accessibilityData":\{"label":"([\d,]+)\s/', $json, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }
        return null;
    }

    /**
     * Extract comment count from ytInitialData.
     */
    private function extractCommentCount(array $data): ?int
    {
        $json = json_encode($data);
        if (preg_match('/"commentCount":\{"simpleText":"([\d,]+)"/', $json, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }
        if (preg_match('/"Comments".*?"([\d,]+)"/', $json, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }
        return null;
    }
}
