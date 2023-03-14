<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $table = "gwc_blog_posts";

    protected $fillable = ['title_en', 'title_ar','slug' , 'details_en', 'details_ar' , 'status' , 'viewed' , 'created_by' , 'image' ];

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
        return $this->hasMany(BlogComments::class, 'post_id');
    }
    public function publsihComments()
    {
        return $this->hasMany(BlogComments::class, 'post_id')->where('status', 'published');
    }
    public function publsihFirstComments()
    {
        return $this->hasMany(BlogComments::class, 'post_id')->where('status', 'published')->whereNull('reply_id')->latest();
    }
}
