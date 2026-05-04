<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this feature.');
        }

        if (!auth()->user()->isPro() && !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Pro subscription required.',
                    'requires_upgrade' => true
                ], 403);
            }
            return redirect()->route('user.subscription')->with('error', 'This feature requires a Pro subscription.');
        }

        return $next($request);
    }
}
