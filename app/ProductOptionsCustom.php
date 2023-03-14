<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class ProductOptionsCustom extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_products_option_custom";
	
}
