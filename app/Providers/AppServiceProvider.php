<?php

namespace App\Providers;
use App\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $settingInfo = Settings::where("keyname", "setting")->first();;
        $theme_config =  @$settingInfo->theme_config ?? (object)[];

        View::share([
            'settingInfo'   => $settingInfo,
            'theme_config' => $theme_config,
            'locale' => app()->getLocale() == 'en' ? 'en' : 'ar'
        ]);
        
        Schema::defaultStringLength(191);
        config(['settingInfo' => $settingInfo]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
