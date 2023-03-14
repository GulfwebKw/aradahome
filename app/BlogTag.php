<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    protected $table = "gwc_blog_tags";

    protected $fillable = ['tag_en', 'tag_ar'];

    public $timestamps = false;

    public function posts()
    {
        return $this->belongsToMany(BlogPost::class, 'gwc_blog_post_tags' , 'tag_id');
    }
}
