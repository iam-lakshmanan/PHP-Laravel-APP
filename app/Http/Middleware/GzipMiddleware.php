<?php

namespace App\Http\Middleware;

use Closure;

class GzipMiddleware
{
    public function handle($request, Closure $next)
    {
        // Get the response
        $response = $next($request);

        // Check if the response is for a text-based resource
        $contentType = $response->headers->get('Content-Type');
        $acceptEncoding = $request->header('Accept-Encoding');

        // Compress if the response is HTML, CSS, or JS
        if (strpos($contentType, 'text/') === 0 ||
            strpos($contentType, 'application/javascript') !== false ||
            strpos($contentType, 'text/css') !== false) {
            // Only use Gzip for now
            if (strpos($acceptEncoding, 'gzip') !== false) {
                $response->headers->set('Content-Encoding', 'gzip');
                $response->setContent(gzencode($response->getContent(), 9));
            }
        }

        return $response;
    }
}
