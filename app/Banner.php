<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_banners";
	
	
}
