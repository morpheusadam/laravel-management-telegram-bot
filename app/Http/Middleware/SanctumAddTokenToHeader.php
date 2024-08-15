<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanctumAddTokenToHeader
{
    public function handle(Request $request, Closure $next)
    {
        // If the URL contains a token parameter - attach it as the Authorization header
        if ($request->has('apiToken') && !$request->headers->has('Authorization')) {
            $request->headers->set('Authorization', 'Bearer ' .urldecode($request->apiToken));
        }
        return $next($request);
    }
}
