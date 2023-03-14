<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class ProductBundleCategory extends Model
{
    use Notifiable;
	
	public $table = "gwc_products_bundle_category";
	protected $fillable = ['product_id','category_id'];


    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(BundleCategories::class, 'category_id');
    }


}
