<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class ProductOptionsCustomChosen extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_products_option_custom_chosen";
	
	public function mainoption(){
	return $this->hasOne(ProductOptionsCustom::class,'id','custom_option_id');
	}

    public function inventory(){
        return $this->belongsTo(Inventory::class , 'inventory_id');
    }
}
