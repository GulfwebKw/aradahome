<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slideshow;
use App\Settings;
use Image;
use File;
use Response;
use Auth;
use Common;

class AdminSlideshowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
	    $settingInfo = Settings::where("keyname","setting")->first();
        $slideshowLists = Slideshow::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.slideshow.index',['slideshowLists' => $slideshowLists]);
    }
	
	
	/**
	Display the slideshow listings
	**/
	public function create()
    {
	
	$lastOrderInfo = Slideshow::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.slideshow.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New slideshow Details
	**/
	public function store(Request $request)
    {
	
	
		$settingInfo = Settings::where("keyname","setting")->first();
		
		$image_thumb_w = 450;
		$image_thumb_h = 188;
		
		$image_big_w = 1920;
		$image_big_h = 800;
		
		//field validation
	    $this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_slideshows,title_en',
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_slideshows,title_ar',
			'image'        => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'video_link_img'=>'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'video_link'    =>'mimes:mp4'
        ]);
		
		
		
	    try{
		
		//upload image
		$imageName="";
		if($request->hasfile('image')){
		$imageName = 'b-'.md5(time()).'.webp';
		$request->image->move(public_path('uploads/slideshow'), $imageName);
		// open file a image resource
		$img = Image::make(public_path('uploads/slideshow/'.$imageName));
		$img = $img->encode('webp', 98);
		//resize image
		$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
		// save to thumb
		$img->save(public_path('uploads/slideshow/thumb/'.$imageName));
		}
		
		//upload image
		$CoverimageName="";
		if($request->hasfile('video_link_img')){
		$CoverimageName = 'cover-'.md5(time()).'.'.$request->video_link_img->getClientOriginalExtension();
		$request->video_link_img->move(public_path('uploads/slideshow'), $CoverimageName);
		}
		
		$VideoName="";
		if($request->hasfile('video_link')){
		$VideoName = 'video-'.md5(time()).'.'.$request->video_link->getClientOriginalExtension();
		$request->video_link->move(public_path('uploads/slideshow'), $VideoName);
		
		
	    $domainUrl = str_replace("www.","",$request->getSchemeAndHttpHost());
		if(!empty($request->getSchemeAndHttpHost()) && $domainUrl==ENV('APP_URL')){
		$path = base_path();
		$explo = explode("/",$path);
		if($settingInfo->theme==15){
		$detination =  '/home/u582104647/domains/aggrokw.com/public_html/videos/'.$VideoName;
		}else{
		$detination =  '/home/'.$explo[2].'/public_html/videos/'.$VideoName;
		}
		File::copy(public_path('uploads/slideshow/'.$VideoName),$detination);
		}
		 
		}

		$slideshow = new Slideshow;

		$slideshow->title_en      = $request->input('title_en');
		$slideshow->title_ar      = $request->input('title_ar');
		$slideshow->link_id       = $request->input('link_id');
		$slideshow->link_type     = $request->input('link_type');
		$slideshow->link          = $request->input('link');
		$slideshow->is_active     = !empty($request->input('is_active'))?$request->input('is_active'):'0';
		$slideshow->display_order = !empty($request->input('display_order'))?$request->input('display_order'):'0';
		$slideshow->image         = $imageName;
		$slideshow->video_link_img= $CoverimageName;
		$slideshow->video_link    = $VideoName;
		$slideshow->save();

        //save logs
		$key_name   = "slideshow";
		$key_id     = $slideshow->id;
		$message    = "A new record for slideshow is added. (".$slideshow->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/slideshow')->with('message-success','A record is added successfully');
		
		 }catch (\Exception $e) {
	  return redirect()->back()->with('message-error',$e->getMessage());	
	 }
	}
	
	 /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	    $editslideshow = Slideshow::find($id);
        return view('gwc.slideshow.edit',compact('editslideshow'));
    }
	
	
	 /**
     * Show the details of the slideshow.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$slideshowDetails = Slideshow::find($id);
        return view('gwc.slideshow.view',compact('slideshowDetails'));
    }
	
	
	
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	
	$settingInfo = Settings::where("keyname","setting")->first();

		$image_thumb_w = 450;
		$image_thumb_h = 188;
		
		$image_big_w = 1920;
		$image_big_h = 800;
		
		
	 //field validation  
	   $this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_slideshows,title_en,'.$id,
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_slideshows,title_ar,'.$id,
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'video_link_img'=>'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'video_link'    =>'mimes:mp4'
        ]);
		
		
		
	  try{
	  
	 
	$slideshow = Slideshow::find($id);
	$imageName='';
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($slideshow->image)){
	$web_image_path = "/uploads/slideshow/".$slideshow->image;
	$web_image_paththumb = "/uploads/slideshow/thumb/".$slideshow->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.webp';
	
	$request->image->move(public_path('uploads/slideshow'), $imageName);
	// open file a image resource
    $img = Image::make(public_path('uploads/slideshow/'.$imageName));
	$img = $img->encode('webp', 98);
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/slideshow/thumb/'.$imageName));
	
	}else{
	$imageName = $slideshow->image;
	}
	
	
	//upload image
		$CoverimageName="";
		if($request->hasfile('video_link_img')){
		if(!empty($slideshow->video_link_img)){
		$web_image_path = "/uploads/slideshow/".$slideshow->video_link_img;
		if(File::exists(public_path($web_image_path))){
		   File::delete(public_path($web_image_path));
		 }
		}
		$CoverimageName = 'cover-'.md5(time()).'.'.$request->video_link_img->getClientOriginalExtension();
		$request->video_link_img->move(public_path('uploads/slideshow'), $CoverimageName);
		}else{
		$CoverimageName = $slideshow->video_link_img;
		}
		
		$VideoName="";
		if($request->hasfile('video_link')){
		if(!empty($slideshow->video_link)){
		$web_image_path = "/uploads/slideshow/".$slideshow->video_link;
		if(File::exists(public_path($web_image_path))){
		   File::delete(public_path($web_image_path));
		 }
		}
		$VideoName = 'video-'.md5(time()).'.'.$request->video_link->getClientOriginalExtension();
		$request->video_link->move(public_path('uploads/slideshow'), $VideoName);
		
		$domainUrl = str_replace("www.","",$request->getSchemeAndHttpHost());
		if(!empty($request->getSchemeAndHttpHost()) && $domainUrl==ENV('APP_URL')){
		$path = base_path();
		$explo = explode("/",$path);
		if($settingInfo->theme==15){
		$detination =  '/home/u582104647/domains/aggrokw.com/public_html/videos/'.$VideoName;
		}else{
		$detination =  '/home/'.$explo[2].'/public_html/videos/'.$VideoName;
		}
		File::copy(public_path('uploads/slideshow/'.$VideoName),$detination);
		}
		
		}else{
		$VideoName = $slideshow->video_link;
		}
		
	
		
		$slideshow->title_en      = $request->input('title_en');
		$slideshow->title_ar      = $request->input('title_ar');
		$slideshow->link_id       = $request->input('link_id');
		$slideshow->link_type     = $request->input('link_type');
		$slideshow->link          = $request->input('link');
		$slideshow->is_active     = !empty($request->input('is_active'))?$request->input('is_active'):'0';
		$slideshow->display_order = !empty($request->input('display_order'))?$request->input('display_order'):'0';
		$slideshow->image         = $imageName;
		$slideshow->video_link_img= $CoverimageName;
		$slideshow->video_link    = $VideoName;
		$slideshow->save();
		
		
		//save logs
		$key_name   = "slideshow";
		$key_id     = $slideshow->id;
		$message    = "Record for slideshow is edited. (".$slideshow->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	    return redirect('/gwc/slideshow')->with('message-success','Information is updated successfully');
		
		 }catch (\Exception $e) {
	  return redirect()->back()->with('message-error',$e->getMessage());	
	 }
	}
	
	/**
     * Delete the Image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	public function deleteImage($id){
	$slideshow = Slideshow::find($id);
	//delete image from folder
	if(!empty($slideshow->image)){
	$web_image_path = "/uploads/slideshow/".$slideshow->image;
	$web_image_paththumb = "/uploads/slideshow/thumb/".$slideshow->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$slideshow->image='';
	$slideshow->save();
	
	   //save logs
		$key_name   = "slideshow";
		$key_id     = $slideshow->id;
		$message    = "Image is removed. (".$slideshow->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	/**
     * Delete slideshow along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/slideshow')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $slideshow = Slideshow::find($id);
	 //check cat id exist or not
	 if(empty($slideshow->id)){
	 return redirect('/gwc/slideshow')->with('message-error','No record found'); 
	 }

	 //delete parent cat mage
	 if(!empty($slideshow->image)){
	 $web_image_path = "/uploads/slideshow/".$slideshow->image;
	 $web_image_paththumb = "/uploads/slideshow/thumb/".$slideshow->image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	  }
	 }
	 
	 //save logs
		$key_name   = "slideshow";
		$key_id     = $slideshow->id;
		$message    = "A record is removed. (".$slideshow->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $slideshow->delete();
	 return redirect()->back()->with('message-success','slideshow is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Slideshow::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "slideshow";
		$key_id     = $recDetails->id;
		$message    = "slideshow status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
