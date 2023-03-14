<?php

namespace App\Http\Middleware;

use Closure;

class Localization
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
        // Check header request and determine localizaton
		if($request->hasHeader('X-localization')){	
		 $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'en';
		 \App::setLocale($local);
		}
		if(\Session::has('locale'))
        {
          \App::setlocale(\Session::get('locale'));
        }
		return $next($request);
    }
}
