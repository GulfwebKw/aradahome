<?php
// $target = '/home/arhome/public_html_demo/uploads';
//   $link = '/home/arhome/aradahhome_main/private/public/uploads';
//   $test = symlink($target, $link);
//   if($test) {
//     echo 'Link created Successfuly.';
//   }else {
    // echo 'link creation failed.';
//   }


/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/
function route($name, $parameters = [], $absolute = true)
{
    try {
        return app('url')->route($name, $parameters, $absolute);
    } catch (\Exception $exception){
        if ( is_array($parameters) )
            $parameters['countrySubDomainCode'] = Request()->countrySubDomainCode ;
        else
            $parameters = [
                'countrySubDomainCode' => Request()->countrySubDomainCode,
                $parameters,
            ];
        return app('url')->route($name, $parameters, $absolute);
    }
}
function redirect($to = null, $status = 302, $headers = [], $secure = null)
{
    if (  \Illuminate\Support\Str::startsWith( trim(Request()->getRequestUri() , '/'), \App\Http\Controllers\Common::noRedirectWildCard(true)) ) {
        $countryCode = strtolower(\App\Http\Controllers\Common::ip_info(Request()->getClientIp(), 'countrycode'));
        Request()->headers->set('host', $countryCode . '.' . config('app.url'));
    }
    if (is_null($to)) {
        return app('redirect');
    }
    return app('redirect')->to($to, $status, $headers, $secure);
}

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
