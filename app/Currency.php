<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
	public $table = "gwc_currencies";

	public $guarded = ["id"];

	public static function convert($price, $code = null , $round = 2 ){
	    $data =  self::query()
            ->select('code' ,'title_en' , 'title_ar' , 'symbol' , DB::raw(' ROUND( ( '.floatval($price).' * rate ) , '.intval($round).') as price '))
            ->where('is_active' , 1 )
            ->orderBy('display_order')
            ->when( ! is_null($code) , function ($query) use($code) {
                $query->where('code' , $code);
            });
	    return ! is_null($code) ? $data->first() : $data->get();
    }

    public static function table($price, $showDefault = true , $round = 2 ){
        $price = self::getOriginalPrice($price);
	    $datas = self::convert(floatval(preg_replace('/[^0-9\/.\-]/', '', $price)),null,$round);
	    $html = "";
	    if ( ! $showDefault )
            $settingInfo = Settings::where("keyname","setting")->first();
	    foreach ($datas as $data){
            if ( ! $showDefault and $data->code == $settingInfo->base_currency )
                continue;
            $html .= "<tr><td>". $data['title_'. app()->getLocale()] . "</td><td>".$data->price . ' '. $data->symbol."</td></tr>";
        }
	    return $html;
    }

	public static function getCode($code = null){
	    $data =  self::query()
            ->select('code' ,'title_en' , 'title_ar' , 'symbol')
            ->where('is_active' , 1 )
            ->orderBy('display_order')
            ->when( ! is_null($code) , function ($query) use($code) {
                $query->where('code' , $code);
            });
	    return ! is_null($code) ? $data->first() : $data->get();
    }

    public static function getOriginalPrice($price, $round = 2 ){
        $newCurrency = self::defaultFromCountry(false);
        $data =  self::query()
            ->selectRaw( ' ROUND( ( '.floatval($price).' / rate ) , '.intval($round).') as price ')
            ->where('is_active' , 1 )
            ->where('code' , $newCurrency->code)->first();
        return ! is_null($data) ? $data->price : null;
    }

	public static function default($justSymbol = true , $code = null){
        return self::defaultFromCountry($justSymbol , $code );
//        return self::defaultCurrency($justSymbol , $code );
    }

    public static function defaultCMS($justSymbol = true , $code = null){
        return self::defaultCurrency($justSymbol , $code );
    }

    private static function defaultFromCountry($justSymbol = true , $code = null){
        if ( ! is_null(Country::$countryInDomainModel) )
            $code = Country::$countryInDomainModel->currency ;
        return self::defaultCurrency($justSymbol,$code);
    }

    public static function convertTCountry($price, $code = null , $round = 2 ){
        if ( ! is_null(Country::$countryInDomainModel) )
            $code = Country::$countryInDomainModel->currency ;
        else
            return collect(['price' => round($price , $round)]);
        return self::convert($price, $code , $round );
    }

    private static function defaultCurrency($justSymbol , $code){
        $data = cache()->remember('defaultCurrency_'.$code ,15*60, function() use($code) {
            if ( $code == null ){
                $settingInfo = Settings::where("keyname","setting")->first();
                $code = $settingInfo->base_currency;
            }
            return \App\Currency::query()
                ->select('code' ,'title_en' , 'title_ar' , 'symbol')
                ->where('is_active' , 1 )
                ->orderBy('display_order')
                ->where('code' , $code)
                ->first();
        });
        return $justSymbol ? ($data->symbol ?? $data['title_'.app()->getLocale()]) : $data;
    }
}
