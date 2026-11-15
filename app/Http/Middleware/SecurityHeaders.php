<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers for the public site, including a nonce-based
 * Content-Security-Policy. Inline scripts must carry the nonce:
 * <script nonce="{{ Vite::cspNonce() }}">.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('Content-Security-Policy', $this->policy());
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function policy(): string
    {
        $nonce = Vite::cspNonce();
        $script = ["'self'", "'nonce-{$nonce}'"];
        $connect = ["'self'"];

        if ($host = config('portfolio.analytics.plausible_host')) {
            $script[] = $host;
            $connect[] = $host;
        }

        // Vite dev server (npm run dev) serves scripts and hot-reload over its own origin.
        if (Vite::isRunningHot()) {
            $origin = rtrim(file_get_contents(Vite::hotFile()));
            $script[] = $origin;
            $connect[] = $origin;
            $connect[] = preg_replace('#^http#', 'ws', $origin);
        }

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => $script,
            'style-src' => ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', ...(Vite::isRunningHot() ? [$origin] : [])],
            'font-src' => ["'self'", 'https://fonts.gstatic.com', 'data:'],
            'img-src' => ["'self'", 'data:', 'https:'],
            'connect-src' => $connect,
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $name) => $name.' '.implode(' ', array_unique($sources)))
            ->implode('; ');
    }
}
