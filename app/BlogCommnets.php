<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCommnets extends Model
{
    protected $table = "gwc_blog_tags";

    protected $fillable = ['comment', 'is_en', 'name', 'email' , 'comment' , 'reply_id' , 'user_id' , ];

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'gwc_blog_post_category' , 'post_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'gwc_blog_post_tags' , 'post_id');
    }

    public function writer()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function comments()
    {
        return $this->hasMany(BlogCommnets::class, 'post_id');
    }
}
