<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * Blocks all non-admin users when maintenance_mode is enabled in site settings.
     * Admin users and admin routes are always allowed through.
     * API webhook routes are also excluded to prevent payment/notification failures.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for admin routes (so admins can still manage the site)
        if ($request->is('admin/*') || $request->is('admin')) {
            return $next($request);
        }

        // Skip check for API webhook routes
        if ($request->is('api/payment/*')) {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        $isMaintenanceMode = (bool) SiteSetting::getValue('maintenance_mode', false);

        if (!$isMaintenanceMode) {
            return $next($request);
        }

        // Allow admin users through
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Allow login/logout routes so admins can authenticate
        if ($request->is('login') || $request->is('logout') || $request->routeIs('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Return 503 maintenance response
        return Inertia::render('Error', ['status' => 503])
            ->toResponse($request)
            ->setStatusCode(503);
    }
}
