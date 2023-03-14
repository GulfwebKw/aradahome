<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class OrdersTemp extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_orders_temp";

    public $fillable = ['unit_price'];
	
	public function options(){
        return $this->hasMany(OrdersTempOption::class , 'oid');
    }
}
