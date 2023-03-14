<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ProductOptions extends Model
{
    use Notifiable;
	
	public $table = "gwc_products_options";


    public function getAttribute($key)
    {
        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods()) and Route::current()->uri() == "{locale}/checkout")
            ) {
                if (in_array($key, ['retail_price'])) {
                    $price = Currency::convertTCountry(parent::getAttribute($key));
                    return $price['price'] ?? $price->price ?? $price[0]->price ?? parent::getAttribute($key);
                }
            }
        }
        return parent::getAttribute($key);
    }
    public function toArray()
    {
        $data = parent::toArray();

        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods()) and Route::current()->uri() == "{locale}/checkout")
            ) {
                $price = Currency::convertTCountry($data['retail_price']);
                $data['retail_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
            }
        }
        return $data;
    }
}
