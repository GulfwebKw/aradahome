<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use toggleTrait;
    use softDeletes;

    protected $table = "gwc_inventories";

    protected $fillable = [
        "title",
        "priority",
        "description",
        "is_active",
        "can_delete",
    ];

    public static function get( $id = null , $justActive = true){
        if ( $id == null ){
            return  self::when($justActive , function ($query) {
                $query->where('is_active' , 1 );
            })->orderBy('priority')->get();
        } elseif ( $id == -1) {
            return self::when($justActive , function ($query) {
                $query->where('is_active' , 1 );
            })->where('can_delete' , 0 )->get();
        } else {
            return self::when($justActive , function ($query) {
                $query->where('is_active' , 1 );
            })->find($id);
        }
    }
}
