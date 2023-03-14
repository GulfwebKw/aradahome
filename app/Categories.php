<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
  //define table
  public $table = "gwc_categories";


     /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
  protected $fillable = ['parent_id', 'name_en'];
  
  public function allproducts()
  {
    return $this->hasMany('App\ProductCategory', 'category_id','id');
  }
  
  
  public function childs()
  {
    return $this->hasMany('App\Categories', 'parent_id','id');
  }
  //tree 
  public static function tree() {
    return static::with(implode('.', array_fill(0, 100, 'childs')))->where('parent_id', '=', '0')->get();
  }
  
  //categories for website menus

  public static function CategoriesTree() {
    return static::with(implode('.', array_fill(0, 100, 'childs')))->where('parent_id', '=', '0')->where('is_active','=','1')->orderBy('display_order','ASC')->get();
  }
  
}
