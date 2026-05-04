<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class WebToolController extends Controller
{
    /**
     * DNS Lookup
     */
    public function dnsLookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $this->cleanDomain($validated['domain']);

        try {
            $records = [];
            $types = ['A', 'AAAA', 'CNAME', 'MX', 'NS', 'TXT', 'SOA'];

            foreach ($types as $type) {
                $dnsType = constant("DNS_{$type}");
                $result = @dns_get_record($domain, $dnsType);
                if ($result) {
                    foreach ($result as $record) {
                        $records[] = $this->formatDnsRecord($record, $type);
                    }
                }
            }

            return response()->json([
                'domain' => $domain,
                'records' => $records,
                'total' => count($records),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'lookup_failed',
                'message' => 'DNS lookup failed. Please check the domain name.',
            ], 422);
        }
    }

    /**
     * IP Lookup / Geolocation
     */
    public function ipLookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ip' => 'nullable|string|max:45',
        ]);

        $ip = $validated['ip'] ?? $request->ip();

        // Validate IP format
        if ($ip && !filter_var($ip, FILTER_VALIDATE_IP)) {
            // Maybe it's a domain, resolve it
            $resolved = @gethostbyname($ip);
            if ($resolved === $ip) {
                return response()->json([
                    'error' => 'invalid_input',
                    'message' => 'Invalid IP address or domain.',
                ], 422);
            }
            $ip = $resolved;
        }

        try {
            // Use ip-api.com (free, no API key needed, 45 req/min)
            $response = Http::timeout(10)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,continent,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query',
            ]);

            if ($response->failed() || $response->json('status') === 'fail') {
                return response()->json([
                    'error' => 'lookup_failed',
                    'message' => $response->json('message', 'IP lookup failed.'),
                ], 422);
            }

            $data = $response->json();

            return response()->json([
                'ip' => $data['query'],
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'region' => $data['regionName'] ?? null,
                'city' => $data['city'] ?? null,
                'zip' => $data['zip'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lon' => $data['lon'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'isp' => $data['isp'] ?? null,
                'org' => $data['org'] ?? null,
                'as' => $data['as'] ?? null,
                'continent' => $data['continent'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'lookup_failed',
                'message' => 'IP lookup service unavailable.',
            ], 500);
        }
    }

    /**
     * WHOIS Lookup
     */
    public function whoisLookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $this->cleanDomain($validated['domain']);

        try {
            // Use a WHOIS server based on TLD
            $tld = strtolower(pathinfo($domain, PATHINFO_EXTENSION));
            $whoisServer = $this->getWhoisServer($tld);

            $rawWhois = $this->queryWhoisServer($whoisServer, $domain);

            if (!$rawWhois) {
                return response()->json([
                    'error' => 'lookup_failed',
                    'message' => 'Could not retrieve WHOIS data for this domain.',
                ], 422);
            }

            // Parse key fields from raw WHOIS
            $parsed = $this->parseWhoisData($rawWhois);

            return response()->json([
                'domain' => $domain,
                'raw' => $rawWhois,
                'parsed' => $parsed,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'lookup_failed',
                'message' => 'WHOIS lookup failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Meta Tags Checker — fetches a URL and extracts meta tags
     */
    public function metaTagsChecker(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'EzyTools Meta Checker/1.0'])
                ->get($validated['url']);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'fetch_failed',
                    'message' => 'Could not fetch the URL. Status: ' . $response->status(),
                ], 422);
            }

            $html = $response->body();
            $tags = $this->extractMetaTags($html);

            return response()->json([
                'url' => $validated['url'],
                'status_code' => $response->status(),
                'tags' => $tags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'fetch_failed',
                'message' => 'Failed to fetch URL: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ping — check if a host responds
     */
    public function ping(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host' => 'required|string|max:255',
        ]);

        $host = $this->cleanDomain($validated['host']);

        $results = [];
        $successful = 0;

        for ($i = 0; $i < 4; $i++) {
            $start = microtime(true);
            $socket = @fsockopen($host, 80, $errno, $errstr, 3);
            $latency = round((microtime(true) - $start) * 1000, 2);

            if ($socket) {
                fclose($socket);
                $results[] = ['seq' => $i + 1, 'latency' => $latency, 'status' => 'ok'];
                $successful++;
            } else {
                // Try port 443
                $start = microtime(true);
                $socket = @fsockopen($host, 443, $errno, $errstr, 3);
                $latency = round((microtime(true) - $start) * 1000, 2);

                if ($socket) {
                    fclose($socket);
                    $results[] = ['seq' => $i + 1, 'latency' => $latency, 'status' => 'ok'];
                    $successful++;
                } else {
                    $results[] = ['seq' => $i + 1, 'latency' => null, 'status' => 'timeout'];
                }
            }
        }

        $latencies = array_filter(array_column($results, 'latency'));

        return response()->json([
            'host' => $host,
            'ip' => gethostbyname($host),
            'results' => $results,
            'stats' => [
                'sent' => 4,
                'received' => $successful,
                'lost' => 4 - $successful,
                'loss_percent' => round(((4 - $successful) / 4) * 100),
                'min' => $latencies ? round(min($latencies), 2) : null,
                'max' => $latencies ? round(max($latencies), 2) : null,
                'avg' => $latencies ? round(array_sum($latencies) / count($latencies), 2) : null,
            ],
        ]);
    }

    /**
     * Hosting Checker — detect hosting provider
     */
    public function hostingChecker(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $this->cleanDomain($validated['domain']);

        try {
            $ip = gethostbyname($domain);
            if ($ip === $domain) {
                return response()->json([
                    'error' => 'resolve_failed',
                    'message' => 'Could not resolve domain to IP address.',
                ], 422);
            }

            // Get IP info
            $ipInfo = Http::timeout(10)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country,countryCode,isp,org,as,query',
            ])->json();

            // Get NS records
            $nsRecords = @dns_get_record($domain, DNS_NS);
            $nameservers = $nsRecords ? array_column($nsRecords, 'target') : [];

            // Detect provider from NS or ASN
            $provider = $this->detectHostingProvider($nameservers, $ipInfo['org'] ?? '', $ipInfo['isp'] ?? '', $ipInfo['as'] ?? '');

            return response()->json([
                'domain' => $domain,
                'ip' => $ip,
                'country' => $ipInfo['country'] ?? null,
                'country_code' => $ipInfo['countryCode'] ?? null,
                'isp' => $ipInfo['isp'] ?? null,
                'org' => $ipInfo['org'] ?? null,
                'as_number' => $ipInfo['as'] ?? null,
                'nameservers' => $nameservers,
                'provider' => $provider,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'check_failed',
                'message' => 'Hosting check failed.',
            ], 422);
        }
    }

    /**
     * Google Cache Checker — checks if a URL is cached by Google
     */
    public function googleCacheChecker(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $url = $validated['url'];
        $cacheUrl = "https://webcache.googleusercontent.com/search?q=cache:" . urlencode($url);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ])
                ->get($cacheUrl);

            $isCached = $response->successful();
            $cacheDate = null;

            if ($isCached) {
                // Try to extract cache date from the response
                if (preg_match('/It is a snapshot of the page as it appeared on (\d+ \w+ \d+ \d+:\d+:\d+ GMT)/i', $response->body(), $matches)) {
                    $cacheDate = $matches[1];
                }
            }

            return response()->json([
                'url' => $url,
                'is_cached' => $isCached,
                'cache_url' => $isCached ? $cacheUrl : null,
                'cache_date' => $cacheDate,
                'status_code' => $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'url' => $url,
                'is_cached' => false,
                'cache_url' => null,
                'cache_date' => null,
                'error' => 'check_failed',
            ]);
        }
    }

    // ── Helper Methods ──────────────────────────────

    private function cleanDomain(string $input): string
    {
        $input = trim($input);
        $input = preg_replace('#^https?://#', '', $input);
        $input = strtok($input, '/');
        $input = strtok($input, '?');
        return strtolower(trim($input));
    }

    private function formatDnsRecord(array $record, string $type): array
    {
        $value = match ($type) {
            'A' => $record['ip'] ?? null,
            'AAAA' => $record['ipv6'] ?? null,
            'CNAME' => $record['target'] ?? null,
            'MX' => ($record['pri'] ?? '') . ' ' . ($record['target'] ?? ''),
            'NS' => $record['target'] ?? null,
            'TXT' => $record['txt'] ?? null,
            'SOA' => ($record['mname'] ?? '') . ' (serial: ' . ($record['serial'] ?? '') . ')',
            default => json_encode($record),
        };

        return [
            'type' => $type,
            'name' => $record['host'] ?? '',
            'value' => trim($value),
            'ttl' => $record['ttl'] ?? null,
            'priority' => $record['pri'] ?? null,
        ];
    }

    private function getWhoisServer(string $tld): string
    {
        $servers = [
            'com' => 'whois.verisign-grs.com',
            'net' => 'whois.verisign-grs.com',
            'org' => 'whois.pir.org',
            'info' => 'whois.afilias.net',
            'io' => 'whois.nic.io',
            'co' => 'whois.nic.co',
            'dev' => 'whois.nic.google',
            'app' => 'whois.nic.google',
            'me' => 'whois.nic.me',
            'xyz' => 'whois.nic.xyz',
            'bd' => 'whois.apnic.net',
        ];

        return $servers[$tld] ?? 'whois.iana.org';
    }

    private function queryWhoisServer(string $server, string $domain): ?string
    {
        $socket = @fsockopen($server, 43, $errno, $errstr, 10);
        if (!$socket) {
            return null;
        }

        fwrite($socket, $domain . "\r\n");
        $response = '';
        while (!feof($socket)) {
            $response .= fgets($socket, 128);
        }
        fclose($socket);

        return $response ?: null;
    }

    private function parseWhoisData(string $raw): array
    {
        $parsed = [];
        $fields = [
            'Domain Name' => 'domain_name',
            'Registrar' => 'registrar',
            'Registrar URL' => 'registrar_url',
            'Creation Date' => 'created_date',
            'Updated Date' => 'updated_date',
            'Registry Expiry Date' => 'expiry_date',
            'Registrar WHOIS Server' => 'whois_server',
            'Domain Status' => 'status',
            'Name Server' => 'name_servers',
        ];

        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if (!$line || str_starts_with($line, '%') || str_starts_with($line, '#')) continue;

            foreach ($fields as $label => $key) {
                if (stripos($line, $label . ':') === 0) {
                    $value = trim(substr($line, strlen($label) + 1));
                    if ($key === 'name_servers' || $key === 'status') {
                        $parsed[$key][] = $value;
                    } else {
                        $parsed[$key] = $value;
                    }
                }
            }
        }

        return $parsed;
    }

    private function extractMetaTags(string $html): array
    {
        $tags = [];

        // Title
        if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) {
            $tags['title'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
        }

        // Meta tags
        preg_match_all('/<meta\s[^>]+>/si', $html, $matches);
        foreach ($matches[0] as $tag) {
            $name = null;
            $content = null;

            // name or property attribute
            if (preg_match('/(?:name|property)\s*=\s*["\']([^"\']+)["\']/i', $tag, $nm)) {
                $name = strtolower($nm[1]);
            }
            if (preg_match('/content\s*=\s*["\']([^"\']*?)["\']/i', $tag, $ct)) {
                $content = html_entity_decode(trim($ct[1]), ENT_QUOTES, 'UTF-8');
            }
            // charset
            if (preg_match('/charset\s*=\s*["\']?([^"\'>\s]+)/i', $tag, $cs)) {
                $tags['charset'] = $cs[1];
                continue;
            }

            if ($name && $content !== null) {
                $tags[$name] = $content;
            }
        }

        // Canonical
        if (preg_match('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']+)["\']/si', $html, $m)) {
            $tags['canonical'] = $m[1];
        }

        // Favicon
        if (preg_match('/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+href=["\']([^"\']+)["\']/si', $html, $m)) {
            $tags['favicon'] = $m[1];
        }

        return $tags;
    }

    private function detectHostingProvider(array $nameservers, string $org, string $isp, string $asn): string
    {
        $nsString = strtolower(implode(' ', $nameservers));
        $combined = strtolower($org . ' ' . $isp . ' ' . $asn . ' ' . $nsString);

        $providers = [
            'cloudflare' => 'Cloudflare',
            'amazon' => 'Amazon Web Services (AWS)',
            'google' => 'Google Cloud',
            'microsoft' => 'Microsoft Azure',
            'digitalocean' => 'DigitalOcean',
            'linode' => 'Linode (Akamai)',
            'akamai' => 'Akamai',
            'hetzner' => 'Hetzner',
            'ovh' => 'OVH',
            'godaddy' => 'GoDaddy',
            'namecheap' => 'Namecheap',
            'hostgator' => 'HostGator',
            'bluehost' => 'Bluehost',
            'siteground' => 'SiteGround',
            'hostinger' => 'Hostinger',
            'vultr' => 'Vultr',
            'netlify' => 'Netlify',
            'vercel' => 'Vercel',
            'shopify' => 'Shopify',
            'squarespace' => 'Squarespace',
            'wix' => 'Wix',
            'wordpress' => 'WordPress.com',
            'wpengine' => 'WP Engine',
            'kinsta' => 'Kinsta',
            'contabo' => 'Contabo',
            'ionos' => 'IONOS',
            'dreamhost' => 'DreamHost',
            'fastly' => 'Fastly',
            'rackspace' => 'Rackspace',
        ];

        foreach ($providers as $key => $name) {
            if (str_contains($combined, $key)) {
                return $name;
            }
        }

        return $org ?: $isp ?: 'Unknown';
    }
}
