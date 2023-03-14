<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Orders extends Model
{
    use Notifiable;
		
	public $table = "gwc_orders";
	public $fillable = ['inventory' , 'quantity'];

    public function getAttribute($key)
    {
        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                ) {
                if (in_array($key, ['unit_price'])) {
                    $price =  Currency::convertTCountry(parent::getAttribute($key));
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
            ) {
                $price = Currency::convertTCountry($data['unit_price']);
                $data['unit_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
            }
        }
        return $data;
    }

    public function orderDetails(){
        return $this->belongsTo(OrdersDetails::class, 'order_id', 'order_id');
    }
}
