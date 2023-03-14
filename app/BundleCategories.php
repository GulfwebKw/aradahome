<?php

namespace App;

use App\Http\Controllers\webCartController;
use Illuminate\Database\Eloquent\Model;

class BundleCategories extends Model
{
    //define table
    public $table = "gwc_bundle_categories";


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'name_en'];

    public function allproducts()
    {
        return $this->hasMany('App\ProductBundleCategory', 'category_id','id');
    }

    public function parent()
    {
        return $this->belongsTo('App\BundleCategories', 'parent_id');
    }



    public function allTempOrderProduct()
    {
        $allProduct = webCartController::loadTempOrders()->pluck('product_id')->toArray();
//        return $this->hasMany('App\ProductCategory', 'category_id','id');
        return static::with( ['allproducts' => function ($query) use($allProduct) {
            $query->whereIn('product_id', $allProduct);
            $query->with('products');
        }])->where('id' , $this->id )->get();
    }


    public static function allTempOrderProductQuantity()
    {
        return webCartController::loadTempOrders()->mapWithKeys(function ($item, $key) {
            return [$item['product_id'] => $item['quantity']];
        })->toArray();
    }
    public static function allTempOrderProductId()
    {
        return webCartController::loadTempOrders()->mapWithKeys(function ($item, $key) {
            return [$item['product_id'] => $item['id']];
        })->toArray();
    }

    public function childs()
    {
        return $this->hasMany('App\BundleCategories', 'parent_id','id');
    }

    //tree
    public static function tree() {
        return static::with(implode('.', array_fill(0, 100, 'childs')))->where('parent_id', '=', '0')->get();
    }

    //categories for website menus

    public static function CategoriesTree() {
        return static::with( ['childs' => function ($query) {
            $query->where('is_active', '=', '1');
            $query->orderBy('display_order', 'ASC');
        }])
            ->where('parent_id', '=', '0')
            ->where('is_active','=','1')
            ->orderBy('display_order','ASC')
            ->get();
    }

}
