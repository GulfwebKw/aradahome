<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
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
        $locale = $request->segment(1) === 'en' ? 'en' : 'ar';
        app()->setLocale($locale);
        \Session::put('locale', $locale);
        return $next($request);
    }
}
