<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class WebPush extends Model
{
    use Notifiable;
	public $table = "gwc_web_device_register";
}
