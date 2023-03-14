<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Settings;
//rules
use App\Rules\Name;
use App\Rules\Mobile;
//email
use App\Mail\SendGrid;
use Mail;
use DB;
use Image;
use File;
class ImageController extends Controller
{
   
    public function show($main,$file)
    {
          if(!empty($file)){
		  $path = base_path('public/uploads/'.$main.'/'.$file);
		 
		  if(file_exists($path)) {
			return Image::make($path)->response();
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }

    }
	
	public function showthumb($main,$thumb='',$file)
    {
          if(!empty($file)){
		  
		  if(!empty($thumb)){
		  $path = base_path('public/uploads/'.$main.'/'.$thumb.'/'.$file);
		  }else{
		  $path = base_path('public/uploads/'.$main.'/'.$file);
		  }

		  if(file_exists($path)) {
			return Image::make($path)->response();
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }

    }
	
	public function showvideo($file)
    { 
	
	    
          if(!empty($file)){
		  $path = base_path('public/uploads/slideshow/'.$file);
		 
		  if(file_exists($path)) {
		     $response = Response::make($path, 200);
			 $response->header('Content-Type', 'video/mp4');
			 return $response;
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }
		  }else{
		   $path = base_path('public/uploads/no-image.png');
		   return Image::make($path)->response();
		  }

    }
}
