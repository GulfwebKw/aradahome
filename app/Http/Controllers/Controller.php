<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function customerinfo($id){
	$userDetails = User::where('id',$id)->first();
	if(!empty($userDetails->image)){
	$profileImage = url("uploads/customers/".$userDetails->image);
	}else{
	$profileImage = url("uploads/noimage.png");
	}
	$userarray =[
	             "id"=>$userDetails->id,
				 "name"=>!empty($userDetails->name)?$userDetails->name:'',
				 "email"=>!empty($userDetails->email)?$userDetails->email:'',
				 "mobile"=>!empty($userDetails->mobile)?$userDetails->mobile:'',
				 "gender"=>!empty($userDetails->gender)?$userDetails->gender:'',
				 "dob"=>!empty($userDetails->dob)?$userDetails->dob:'',
				 "image"=>$profileImage,
				 "username"=>!empty($userDetails->username)?$userDetails->username:'',
				 "is_active"=>!empty($userDetails->is_active)?$userDetails->is_active:'0',
				 "is_newsletter_active"=>!empty($userDetails->is_newsletter_active)?$userDetails->is_newsletter_active:'0',
				 "api_token"=>!empty($userDetails->api_token)?$userDetails->api_token:'',
				 "created_at"=>!empty($userDetails->created_at)?$userDetails->created_at:'',
	            ];
				
	 return $userarray;			
	}
}
