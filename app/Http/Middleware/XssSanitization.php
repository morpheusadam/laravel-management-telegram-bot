<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssSanitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,...$exclude)
    {
        $input = $request->all();
        foreach($input as $key=>$val){
            if(empty($exclude) || !in_array($key,$exclude)) {
                if(!is_array($val)) $input[$key] = strip_tags($val);
            }
        }
        $request->merge($input);
        return $next($request);
    }
}
