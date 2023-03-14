<?php

namespace App;
use App\Scopes\OrdersDetailsScope;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class OrdersDetails extends Model
{
    use Notifiable;
		
	public $table = "gwc_orders_details";

    public $dates = [
        "assigned_at",
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new OrdersDetailsScope);
    }
	
	public function area(){
	    return $this->hasOne(Area::class,'id','area_id');
	}

	public function country(){
	    return $this->hasOne(Country::class,'id','country_id');
	}

	public function driver(){
	    return $this->hasOne(Driver::class,'id','driver_id');
	}
	public function assigner(){
	    return $this->hasOne(Admin::class,'id','driver_assigner_id');
	}

    public function getAttribute($key)
    {
        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
            ) {
                if (in_array($key, ['coupon_amount', 'coupon_free', 'delivery_charges', 'total_amount'])) {
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
            ) {
                $price = Currency::convertTCountry($data['coupon_amount']);
                $data['coupon_amount'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['coupon_free']);
                $data['coupon_free'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['delivery_charges']);
                $data['delivery_charges'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['total_amount']);
                $data['total_amount'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
            }
        }
        return $data;
    }
	
}
