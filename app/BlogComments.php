<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogComments extends Model
{
    protected $table = "gwc_blog_comments";

    protected $fillable = ['comment', 'is_en', 'name', 'email' , 'reply_id' , 'user_id' , 'status' , 'post_id' , 'verifier_id'];

    public function post()
    {
        return $this->belongsTo(BlogPost::class , 'post_id');
    }

    public function replayTo()
    {
        return $this->belongsTo(BlogComments::class , 'reply_id');
    }


    public function replays()
    {
        return $this->hasMany(BlogComments::class , 'reply_id')->where('status', 'published')->latest();
    }

    public function writer()
    {
        return $this->belongsTo(Customers::class , 'user_id');
    }


    public function verifier()
    {
        return $this->belongsTo(Admin::class , 'verifier_id');
    }

}
