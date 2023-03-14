<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class SmsNotify extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_sms_notify";
	
	
}
