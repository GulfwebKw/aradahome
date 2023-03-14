<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class DeliveryTimes extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_orders_delivery_times";
	
	
}
