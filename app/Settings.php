<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $table = "gwc_settings";
    public $hidden = [
        "pos_supervisor_password"
    ];
}
