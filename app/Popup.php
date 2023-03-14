<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_popups";
	
	
}
