<?php

namespace App\Http\Middleware;

use Closure;

class CheckHttps {

    public function handle($request, Closure $next) {

        $exclude_force_https = ['webhook/fastspring-ipn'];
        $found = false;
        foreach ($exclude_force_https as $key => $value) {
            if(request()->is($value)){
                $found = true;
                break;
            }
        }

        if(env('FORCE_HTTPS') && !$found) {
            if (!$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
        }

        return $next($request);
    }

}