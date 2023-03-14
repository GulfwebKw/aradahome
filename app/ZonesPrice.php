<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZonesPrice extends Model
{
    protected $table = "gwc_zones_price";

    protected $fillable = [
        "id",
        "zone_id",
        "from",
        "to",
        "price",
    ];

    public $timestamps = false;

    public function zone(){
        return $this->belongsTo(Zone::class , 'zone_id');
    }
}
