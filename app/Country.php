<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Result;

class Country extends Model
{
    use Notifiable;
    public static $countryInDomainModel = null;
	
	
	public $table = "gwc_country";
	
	public function zones(){
	return $this->hasOne(Zone::class,'id','zone_id');
	}

    public function gateway(){
        return $this->hasMany(CountryGatway::class , 'country_id');
    }

    public static function getGateways($countryCode = null, $active = true){
        if ( $countryCode != null ){
            $country = Country::findOrFail($countryCode);
            $settings = \App\Settings::where("keyname", "setting")->first();
            if($active){
                $gateways = $country->gateway->whereIn('gateway', explode(',', $settings->payments));
            }else{
                $gateways = $country->gateway;
            }
            return $gateways->pluck('gateway')->toArray();
        } else {
            $countries = Country::where('parent_id' , 0)->with('gateway')->get();
            $result = [] ;
            foreach ( $countries as $country)
                $result[$country->id] = $country->gateway->pluck('gateway')->toArray();
            return $result;
        }

    }

    public static function getIsoById($countryCode){
        $country = Country::findOrFail($countryCode);
        return $country->code;
    }

    public function getCurrency(){
        $code  = $this->getAttribute('currency');
        return Currency::getCode($code);
    }
}
