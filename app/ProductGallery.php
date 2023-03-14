<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    use Notifiable;
	
	public $table = "gwc_products_gallery";
	protected $fillable = ['product_id','image'];
	
}
