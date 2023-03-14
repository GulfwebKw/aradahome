<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class bundleSetting extends Model
{
    protected $table = "gwc_bundle_setting";
    protected $fillable = ['key' , 'value' ];
    public $timestamps = false;

    protected $primaryKey = 'key';
    public $incrementing = false;

    public function getAttribute($key)
    {
        if ( $key == "value" )
            return parent::getAttribute($key);

        $data = Cache::remember('bundlesSetting_'.$key , $minutes=24*60*30 , function() use($key) {
            return bundleSetting::where('key' , $key)->first();
        });
        return $data->value ?? null ;
    }

    public function setAttribute($key, $value)
    {
        if ( $key == "key" or $key == "value" )
            return parent::setAttribute($key,$value);

//        $resource = bundleSetting::where('key' , $key)->first();
        Cache::forget('bundlesSetting_'.$key);
        $resource = new bundleSetting();
        $resource->key =$key;
        $resource->value = $value;
        return $resource->updateOrCreate([
            'key' => $key,
            ],[
            'value' => $value
       ]);
    }
}
