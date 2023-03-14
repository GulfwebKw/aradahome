<?php

namespace App\Http\Middleware;

use App\Country;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Wildacrd
{
    protected $existSubdomain = [
        'driver',
        'pos'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( in_array( str_replace('.'.config('app.url') , '' , $request->header('host')) , $this->existSubdomain ) )
            return $next($request);

        config([
            'MOBILE_VALIDATION_MIN' => env('MOBILE_VALIDATION_MIN' ,'3'),
            'MOBILE_VALIDATION_MAX' => env('MOBILE_VALIDATION_MIN' ,'15'),
            'MOBILE_VALIDATION_FIRST' => env('MOBILE_VALIDATION_MIN' ,false)
        ]);
        if ( ! ( Str::startsWith( trim($request->getRequestUri() , '/'), \App\Http\Controllers\Common::noRedirectWildCard()) ) ) {
            $host = $request->header('host');
//        if (substr($host, 0, 4) != 'www.' and ! ( Str::startsWith( trim($request->getRequestUri() , '/'), \App\Http\Controllers\Common::noRedirectWildCard()) )) {
            if ($host == config('app.url')) {
                $countryCode = strtolower(\App\Http\Controllers\Common::ip_info($request->getClientIp(), 'countrycode'));
                $countryCode = Country::where('code', $countryCode)->where('is_active', 1)->exists() ? $countryCode : 'kw';
                $request->headers->set('host', $countryCode . '.' . config('app.url'));
                return Redirect::to($request->fullUrl());
            }
            $countryCode = $request->route('countryCode');
            $countryCode = str_replace('.'.config('app.url')  , '', $host);
            $country = Country::where('code', $countryCode)
                ->where('is_active', 1)
                ->where('parent_id', 0)->first();
            if ($country == null) {
                $countryName = \App\Http\Controllers\Common::ip_info($request->getClientIp(), 'country');
                $request->headers->set('host', 'kw.' . config('app.url'));
                return Redirect::to($request->fullUrl())->withErrors(['getLocation' => 'We dont support ' . ($countryName ?? "Your country") . '!']);
            } else {
                if ( $countryCode == "kw" ){
                    config([
                        'MOBILE_VALIDATION_MIN' => '8',
                        'MOBILE_VALIDATION_MAX' => '8',
                        'MOBILE_VALIDATION_FIRST' => true
                    ]);
                } else {
                    config([
                        'MOBILE_VALIDATION_MIN' => '3',
                        'MOBILE_VALIDATION_MAX' => '15',
                        'MOBILE_VALIDATION_FIRST' => false
                    ]);
                }
            }
            Country::$countryInDomainModel = $country;
            View::share('domainCountry' , $country);
        }
        return $next($request);
    }
}
