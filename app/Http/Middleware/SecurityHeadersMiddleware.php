<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach production security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(self), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content-Security-Policy with external CDN support for Google Fonts, Bootstrap Icons, Unsplash, jsdelivr source maps, Cloudflare Insights, etc.
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://www.googletagmanager.com https://www.google-analytics.com https://connect.facebook.net https://static.cloudflareinsights.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
               "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:; " .
               "img-src 'self' data: blob: https://images.unsplash.com https://www.googletagmanager.com https://www.facebook.com https://*.gumroad.com https://*.tiktok.com https://*.youtube.com https://*.ytimg.com; " .
               "frame-src 'self' https://www.youtube.com https://www.tiktok.com https://*.gumroad.com; " .
               "connect-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://fonts.googleapis.com https://fonts.gstatic.com https://www.google-analytics.com https://analytics.google.com https://*.google-analytics.com https://connect.facebook.net https://*.gumroad.com https://cloudflareinsights.com https://*.cloudflareinsights.com data: blob:; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self' https://*.gumroad.com;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
