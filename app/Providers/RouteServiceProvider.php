<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapPOSRoutes();

        $this->mapDriverRoutes();
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapDriverRoutes()
    {
        Route::domain('driver.'.config('app.url'))
            ->namespace($this->namespace.'\Driver')
            ->as('driver.')
            ->group(function () {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace('API')
                    ->as('api.')
                    ->group(base_path('routes/driver/api.php'));

                Route::prefix('gwc')
                    ->middleware(['driver','web'])
                    ->namespace('Admin')
                    ->as('admin.')
                    ->group(base_path('routes/driver/admin.php'));

                Route::middleware(['driver','web'])
                    ->namespace('Panel')
                    ->as('panel.')
                    ->group(base_path('routes/driver/panel.php'));
            });
    }

    private function mapPOSRoutes()
    {
        Route::domain('pos.'.config('app.url'))
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/pos.php'));
    }
}
