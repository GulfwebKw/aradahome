<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class AdminPos extends Authenticatable
{
    
	use Notifiable;
	use HasRoles;
	use HasApiTokens;
	
	protected $gaurd_name = "admin";
	
	public $table = "gwc_users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function workTimes(){
        return $this->hasMany(WorkTime::class,'pos_id');
    }
    public function openWorkTime(){
        return $this->hasOne(WorkTime::class,'pos_id')->whereNull('ended')->first();
    }
    public function lastOpenWorkTime(){
        return $this->hasOne(WorkTime::class,'pos_id')->whereNotNull('ended')->orderByDesc('ended')->first();
    }
    
    public function cashs(){
        return $this->hasMany(CashLog::class,'pos_id');
    }
}
