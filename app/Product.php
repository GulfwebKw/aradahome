<?php

namespace App;

use App\Scopes\ProductsScope;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\CustomersWish;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Product extends Model
{
    use Notifiable;

    public $table = "gwc_products";
    protected $fillable = ['slug', 'sku_no', 'item_code', 'title_en', 'title_ar', 'details_en', 'details_ar', 'retail_price', 'old_price', 'quantity', 'image', 'rollover_image'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProductsScope);
    }


    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    //get brand
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function productcat()
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id')
            ->select('gwc_categories.*', 'gwc_products_category.*')
            ->join('gwc_categories', 'gwc_categories.id', '=', 'gwc_products_category.category_id');
    }

    public function quantity()
    {
        return $this->hasMany(ProductsQuantity::class, 'product_id', 'id');
    }

    /**
     * check product is bundle or no
     * @param bool $checkActivity
     * @return bool
     */
    public function isBundle($checkActivity = true): bool
    {
        if (!(new bundleSetting())->is_active and $checkActivity)
            return false;
        if (!intval(parent::getAttribute('id')) > 0)
            return false;
        $count = ProductBundleCategory::where('product_id', parent::getAttribute('id'))->count();
        return ($count > 0) ? true : false;
    }

    public function getAttribute($key)
    {
        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods()) and Route::current()->uri() == "{locale}/checkout")
            ) {
                if (in_array($key, ['retail_price', 'old_price', 'countdown_price', 'cost_price', 'wholesale_price'])) {
                    $price = Currency::convertTCountry(parent::getAttribute($key));
                    return $price['price'] ?? $price->price ?? $price[0]->price ?? parent::getAttribute($key);
                }
            }
        }
        
        if ((int)Settings::find(1)->new_item_badge === 1 && $this->getNewTagDays() > 0 and in_array($key , ['caption_en' , 'caption_ar', 'caption_color']) and  (int) parent::getAttribute('newtag') === 1 ){
            $caption = parent::getAttribute($key) ;
            if ( $caption == null or empty($caption) or $caption == "" ){ 
                $created_at = parent::getAttribute('created_at');
                if ( $created_at and $created_at->diffInDays(\Illuminate\Support\Carbon::now()) + 1 <= $this->getNewTagDays() ){
                    $return = null ;
                    switch ($key) {
                        case 'caption_en':
                            $locale = app()->getlocale();
                            if ( $locale != "en" ) app()->setlocale("en");
                            $return = trans('webMessage.new');
                            if ( $locale != "en" ) app()->setlocale($locale);
                            break;
                        case 'caption_ar':
                            $locale = app()->getlocale();
                            if ( $locale != "ar" ) app()->setlocale("ar");
                            $return = trans('webMessage.new');
                            if ( $locale != "ar" ) app()->setlocale($locale);
                            break;
                        case 'caption_color':
                            $return = '#008cff';
                            break;
                    }
                    return $return;
                }
            }
            return $caption;
        }
        return parent::getAttribute($key);
    }
    public function toArray()
    {
        $data = parent::toArray();

        if ( Route::current() != null ) {
            if (
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods()) and Route::current()->uri() == "{locale}/checkout")
            ) {
                $price = Currency::convertTCountry(@$data['retail_price']);
                $data['retail_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['old_price']);
                $data['old_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['countdown_price']);
                $data['countdown_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['cost_price']);
                $data['cost_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
                $price = Currency::convertTCountry($data['wholesale_price']);
                $data['wholesale_price'] = $price['price'] ?? $price->price ?? $price[0]->price ?? $price;
            }
        }
        if ((int)Settings::find(1)->new_item_badge === 1 && $this->getNewTagDays() > 0 && (int) $data['newtag'] === 1 ){
            $locale = app()->getlocale();
            $diffDays = $data['created_at'] ? Carbon::parse($data['created_at'])->diffInDays(\Illuminate\Support\Carbon::now()) + 1 : 99999;
            if ( ( $data['caption_en'] == null or empty($data['caption_en']) or $data['caption_en'] == "" )  and $diffDays <= $this->getNewTagDays() ){
                app()->setlocale("en");
                $data['caption_en'] = trans('webMessage.new');
            }
            if (  ( $data['caption_ar'] == null or empty($data['caption_ar']) or $data['caption_ar'] == ""  )  and $diffDays <= $this->getNewTagDays()  ){ 
                app()->setlocale("ar");
                $data['caption_ar'] = trans('webMessage.new');
            }
            if (  ( $data['caption_ar'] == null or empty($data['caption_ar']) or $data['caption_ar'] == ""  ) and ( $data['caption_en'] == null or empty($data['caption_en']) or $data['caption_en'] == "" )  and $diffDays <= $this->getNewTagDays()  ){ 
                $data['caption_color'] = '#ff0000';
            }
            app()->setlocale($locale);
        }
        
        return $data;
    }



    /**
     * get sub Quantity
     * @param bool $checkActivity
     * @return bool
     */
    public function getQuantity($inventory_id = -1 , $attribute_id = -1 , $option_id = -1,$justGet = false,$groupInventory = false,$getDeActive = false)
    {
//        DB::enableQueryLog();
        $query = self::quantity()->when($inventory_id != -1 , function ($query) use($inventory_id){
            $query->where('inventory_id' , $inventory_id);
        })->when($attribute_id != -1 , function ($query) use($attribute_id){
            if ( is_array($attribute_id))
                $query->whereIn('attribute_id' , $attribute_id);
            elseif ( is_null($attribute_id))
                $query->whereNull('attribute_id');
            else
                $query->where('attribute_id' , $attribute_id);
        })->when($option_id != -1 , function ($query) use($option_id){
            if ( is_array($option_id))
                $query->whereIn('option_id' , $option_id);
            elseif ( is_null($option_id))
                $query->whereNull('option_id');
            else
                $query->where('option_id' , $option_id);
        })->when($groupInventory , function ($query){
            $query->groupBy('inventory_id');
            $query->where('is_qty_deduct' , 1);
            $query->selectRaw('inventory_id,sum(quantity) as quantity');
        })->when(! $getDeActive , function ($query){
            $query->whereHas('inventory', function ($query) {
                return $query->where('is_active',  1);
            });
        })->with('inventory');
//        $query->get();
//        $querys = DB::getQueryLog();
//        dd($querys);
        //if ( $query->count() > 1 or $justGet )
        if ( $justGet )
            return $query->get()->sortBy('inventory.priority');
        return $query->first();
    }
    
    public static function getNewTagDays() {
        return cache()->remember('NewTagDaysSetting', 15 * 60, function () {
            $settingInfo = Settings::where("keyname","setting")->first();
            if ( $settingInfo->show_new_tag and intval($settingInfo->new_tag_days) >= 0 )
                return intval($settingInfo->new_tag_days) ;
            return -100;
        });
    }
}
