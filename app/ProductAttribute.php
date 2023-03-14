<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use Notifiable;

    public $table = "gwc_products_attribute";

    public function getAttribute($key)
    {
        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                ! ( in_array('POST' , Route::current()->methods() )  and Route::current()->uri() == "{locale}/checkout")
            ) {
                if (in_array($key, ['retail_price', 'old_price'])) {
                    return Currency::convertTCountry(parent::getAttribute($key))->price;
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
                ! ( in_array('POST' , Route::current()->methods() )  and Route::current()->uri() == "{locale}/checkout")
            ) {
                $data['retail_price'] = Currency::convertTCountry($data['retail_price'])->price;
                $data['old_price'] = Currency::convertTCountry($data['old_price'])->price;
            }
        }
        return $data;
    }

}
