<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class WebPushMessage extends Model
{
    use Notifiable;
	public $table = "gwc_web_push_message";
}
