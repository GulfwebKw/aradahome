<?php

namespace App\Scopes;

use App\Country;
use App\Settings;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use function foo\func;

class ProductsScope implements Scope
{

    public $canNotView = [
        'web.brandsListing',
        'offer',
        'web.allsections',
        'categories',
        'web.product-tag',
        'productsCatidSlug',
        'web.search',
        'webBundle',
        'home',
        'webBundleProduct',
    ];
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ( Route::current() != null ) {
            if (!(Str::startsWith(trim(Route::current()->uri(), '/'), ['gwc']))) {
                if ( in_array(Route::current()->getName() , $this->canNotView )) {
                    $settingInfo = cache()->remember('settingInfoScope', 15 * 60, function () {
                        return Settings::where("keyname", "setting")->first();
                    });
                    $builder->where('gwc_products.Quantity', '>', $settingInfo->show_out_of_stock == 0 ? 0 : -1);
                }
                $builder->where('gwc_products.is_active', '!=', 0);
            }

            if (!(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))) {
                if ( request()->countrySubDomainCode != null ) {
                    $country = Country::where('code', request()->countrySubDomainCode)
                        ->where('is_active', 1)
                        ->where('parent_id', 0)->first();
                    if ( in_array($country->shipment_method  ,["zoneprice" , "dhl"]) ){
                        $builder->where(function ($query){
                            $query->where(function ($query){
                                $query->where('gwc_products.height', '>', 0)
                                    ->where('gwc_products.width', '>', 0)
                                    ->where('gwc_products.depth', '>', 0);
                            })->orWhere('gwc_products.weight' , '>' , 0);
                        });
                    }
                }
            }
        }
    }
}