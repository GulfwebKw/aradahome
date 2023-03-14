<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Driver extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'gwc_drivers';
    protected $fillable = [
        'id',
        'first_name_en',
        'first_name_ar',
        'last_name_en',
        'last_name_ar',
        'avatar',
        'username',
        'phone',
        'password',
        'is_active',
    ];
    protected $hidden = [
        'password'
    ];
    protected $casts = [
        'is_active' =>  'boolean'
    ];

    public function getAttribute($key)
    {
        if ( strtolower($key) == "fullname" or strtolower($key) == "full_name" )
            return parent::getAttribute('first_name_en') .' '. parent::getAttribute('last_name_en');
        elseif ( strtolower($key) == "fullname_ar" or strtolower($key) == "full_name_ar"  or strtolower($key) == "full_namear"  or strtolower($key) == "fullname_ar" )
            return parent::getAttribute('first_name_ar') .' '. parent::getAttribute('last_name_ar');
        return parent::getAttribute($key);
    }
}
