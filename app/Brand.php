<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_brands";
	
	public function products()
    {
        return $this->hasMany(Product::class,'brand_id','id');
    }
	
}
