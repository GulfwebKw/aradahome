<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_slideshows";
	
	
}
