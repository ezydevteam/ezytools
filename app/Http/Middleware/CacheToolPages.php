<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheToolPages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cache public tool pages for guest users for 1 hour
        if (auth()->guest() && $request->isMethod('GET')) {
            $key = 'page_cache_' . md5($request->fullUrl());
            
            if (Cache::has($key)) {
                return Cache::get($key);
            }
            
            $response = $next($request);
            
            // Only cache successful responses
            if ($response->getStatusCode() === 200) {
                Cache::put($key, $response, 3600);
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
