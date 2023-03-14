<?php

namespace App\Http\Middleware;

use Closure;

class DriverPanel
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
 
		if(auth('driver')->user()){
            return $next($request);
        }
        return redirect('/login')->with('error','You have not admin access');
    }
}
