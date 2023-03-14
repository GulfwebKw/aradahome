<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = "gwc_blog_categories";

    protected $fillable = ['parent_id', 'slug', 'name_en', 'name_ar' , 'is_active' , 'display_order'];

    public function posts()
    {
        return $this->belongsToMany(BlogPost::class, 'gwc_blog_post_category' , 'category_id');
    }


    public function childs()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id','id')->where('is_active','=','1')->orderBy('display_order','ASC');
    }
    //tree
    public static function tree() {
        return static::with(implode('.', array_fill(0, 100, 'childs')))->whereNull('parent_id')->get();
    }

    //categories for website menus

    public static function CategoriesTree() {
        return static::with(implode('.', array_fill(0, 100, 'childs')))->whereNull('parent_id')->where('is_active','=','1')->orderBy('display_order','ASC')->get();
    }
}
