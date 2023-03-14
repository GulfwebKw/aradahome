<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class WorkTime extends Model
{
	public $table = "gwc_pos_work_time";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pos_id', 'start', 'ended','startCash','endCash','contradictionCashOfSystem','cashPay','cardPay','customers','sell','totalSell' , 'countCash' ,'countCard' ,'contradictionCountCash' ,'contradictionCountCard'
    ];

    protected $dates = [
        'start', 'ended'
    ];

    public function pos(){
        return $this->belongsTo(AdminPos::class , 'pos_id');
    }

    public function cashs(){
        return $this->hasMany(CashLog::class , 'shift_id');
    }

    public function startCash(){
        return $this->hasOne(CashLog::class , 'shift_id')
            ->where('gwc_pos_cash_log.refrence_id' , parent::getAttribute('id'))
            ->where('gwc_pos_cash_log.refrence_type' , WorkTime::class);
    }

    public function lastCash(){
        return $this->hasOne(CashLog::class , 'shift_id')->orderByDesc('gwc_pos_cash_log.id')->first();
    }
    public function lastCashRelation(){
        return $this->hasOne(CashLog::class , 'shift_id')->orderByDesc('gwc_pos_cash_log.id');
    }

    public function hasOpenShift(){
        return WorkTime::query()->where('pos_id' , parent::getAttribute('pos_id'))
            ->whereNull('ended')
            ->exists() ;
    }
}
