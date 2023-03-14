<?php

namespace App\Http\Middleware;

use App\Country;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Driver
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
        View::share('settingInfo' , \App\Http\Controllers\webController::settings());
        View::share('BRGenerator' , new \Picqer\Barcode\BarcodeGeneratorPNG);
        return $next($request);
    }
}
