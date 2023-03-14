<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class CountryGatway extends Model
{

	public $table = "gwc_country_gateway";
    public $timestamps = false;
    public $fillable = ['gateway'];

    public function country(){
        return $this->belongsTo(Country::class , 'country_id');
    }
}
