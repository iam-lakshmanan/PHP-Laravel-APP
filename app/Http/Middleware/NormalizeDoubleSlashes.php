<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeDoubleSlashes
{
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();

        // If there are double slashes (excluding the first `//` in http://)
        if (preg_match('/\/{2,}/', $uri)) {
            $normalized = preg_replace('/\/{2,}/', '/', $uri);
            return redirect($normalized, 301); // permanent redirect
        }

        return $next($request);
    }
}
