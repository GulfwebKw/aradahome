<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use Notifiable;
	
	public $table = "gwc_products_category";
	protected $fillable = ['product_id','category_id'];
	
	
}
