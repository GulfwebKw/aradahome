<?php

namespace App\Http\Middleware;

use Closure;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config([
            'MOBILE_VALIDATION_MIN' => env('MOBILE_VALIDATION_MIN' ,'3'),
            'MOBILE_VALIDATION_MAX' => env('MOBILE_VALIDATION_MIN' ,'15'),
            'MOBILE_VALIDATION_FIRST' => env('MOBILE_VALIDATION_MIN' ,false)
        ]);
	    $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
