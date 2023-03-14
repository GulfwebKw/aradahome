<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class CustomersWish extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_customers_wish";
	
}
