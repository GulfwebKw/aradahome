<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class CashLog extends Model
{
	public $table = "gwc_pos_cash_log";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pos_id' , 'shift_id', 'amount', 'type', 'description','refrence_id','refrence_type','afterCash','beforeCash'
    ];

    public function pos(){
        return $this->belongsTo(AdminPos::class , 'pos_id');
    }

    public function shift(){
        return $this->belongsTo(WorkTime::class , 'shift_id');
    }

    public function reference(){
        return $this->morphTo('refrence');
    }
}
