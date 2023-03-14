<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_sections";
	
	
}
