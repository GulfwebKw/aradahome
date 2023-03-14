<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Area;
//email
use App\Mail\SendGrid;
use Mail;
use DB;
use Curl;
class DezOrderStuffController extends Controller
{
   
 public function getDezOrderAreas(){
     $apiUrl = "http://dezorder.com/getAreasApi/1";
	 $response = Curl::to($apiUrl)->get();
	 $jsdecode = json_decode($response, true);
	 $areas    = $jsdecode['areas'];
	 foreach($areas  as $key=>$area){
	  if(!empty($area['id']) && !empty($area['latitude']) && !empty($area['longitude'])){
	   self::updateLocalAreaLL($area['id'],$area['latitude'],$area['longitude']);
	  }
	 }
 }     
 
 //update lat/long to local table
 public static function updateLocalAreaLL($areaid,$lat,$long){
   if(!empty($areaid)){
	 $areas = Area::where("id",$areaid)->first();
	 if(!empty($areas->id)){
	 $areas->latitude  = $lat;
	 $areas->longitude = $long;
	 $areas->save();
	 }
   }
 }  
}
