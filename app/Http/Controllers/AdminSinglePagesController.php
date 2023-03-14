<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SinglePages;
use App\Settings;
use Image;
use File;
use Response;
use App\Services\SinglePagesSlug;
use PDF;
use Auth;

class AdminSinglePagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $singlepagesLists = SinglePages::paginate($settingInfo->item_per_page_back);
        return view('gwc.singlepages.index',['singlepagesLists' => $singlepagesLists]);
    }
	
	
	/**
	Display the singlepages listings
	**/
	public function create()
    {
	return view('gwc.singlepages.create');
	}
	

	
	/**
	Store New singlepages Details
	**/
	public function store(Request $request)
    {
	
		$settingInfo = Settings::where("keyname","setting")->first();
		
		$image_thumb_w = 360;
		$image_thumb_h = 239;
		
		$image_big_w = 600;
		$image_big_h = 399;
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_singlepages,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_singlepages,title_ar',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
	    
		try{
		
		
		

		$singlepages = new SinglePages;
		//upload image
		$imageName="";
		if($request->hasfile('image')){
		$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/singlepages'), $imageName);
		// open file a image resource
		$imgbig = Image::make(public_path('uploads/singlepages/'.$imageName));
		//resize image
		$imgbig->resize($image_big_w,$image_big_h);//Fixed w,h
		//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
		// insert watermark at bottom-right corner with 10px offset
		//$imgbig->insert(public_path('uploads/singlepages/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
		//}
		// save to imgbig thumb
		$imgbig->save(public_path('uploads/singlepages/'.$imageName));
		
		//create thumb
		// open file a image resource
		$img = Image::make(public_path('uploads/singlepages/'.$imageName));
		//resize image
		$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
		// save to thumb
		$img->save(public_path('uploads/singlepages/thumb/'.$imageName));
		}

		//slug
		$slug = new SinglePagesSlug;
		
		$singlepages->slug=$slug->createSlug($request->title_en);
		$singlepages->title_en=$request->input('title_en');
		$singlepages->title_ar=$request->input('title_ar');
		$singlepages->details_en=$request->input('details_en');
		$singlepages->details_ar=$request->input('details_ar');
		$singlepages->seo_keywords_en=$request->input('seo_keywords_en');
		$singlepages->seo_keywords_ar=$request->input('seo_keywords_ar');
		$singlepages->seo_description_ar=$request->input('seo_description_ar');
		$singlepages->seo_description_en=$request->input('seo_description_en');
		$singlepages->image=$imageName;
		$singlepages->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$singlepages->save();

        //save logs
		$key_name   = "singlepages";
		$key_id     = $singlepages->id;
		$message    = "A new record for singlepages is added. (".$singlepages->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/singlepages')->with('message-success','A record is added successfully');
		
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
	    $editsinglepages = SinglePages::find($id);
        return view('gwc.singlepages.edit',compact('editsinglepages'));
    }
	
	
	 /**
     * Show the details of the singlepages.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$singlepagesDetails = SinglePages::find($id);
        return view('gwc.singlepages.view',compact('singlepagesDetails'));
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
		
		$image_thumb_w = 360;
		$image_thumb_h = 239;
		
		$image_big_w = 600;
		$image_big_h = 399;
	 //field validation  
	   $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_singlepages,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_singlepages,title_ar,'.$id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
	
	try{
	
	 
		
	    $singlepages = SinglePages::find($id);
		
		$imageName='';
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($singlepages->image)){
	$web_image_path = "/uploads/singlepages/".$singlepages->image;
	$web_image_paththumb = "/uploads/singlepages/thumb/".$singlepages->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/singlepages'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/singlepages/'.$imageName));
	//resize image
	$imgbig->resize($image_big_w,$image_big_h);//Fixed w,h
	
	//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	//}
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/singlepages/'.$imageName));
	
	//create thumb
	// open file a image resource
    $img = Image::make(public_path('uploads/singlepages/'.$imageName));
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/singlepages/thumb/'.$imageName));
	
	}else{
	$imageName = $singlepages->image;
	}
	
		$slug = new SinglePagesSlug;
		
		
		
		$singlepages->slug=$slug->createSlug($request->title_en,$id);
		$singlepages->title_en=$request->input('title_en');
		$singlepages->title_ar=$request->input('title_ar');
		$singlepages->details_en=$request->input('details_en');
		$singlepages->details_ar=$request->input('details_ar');
		
		$singlepages->seo_keywords_en=$request->input('seo_keywords_en');
		$singlepages->seo_keywords_ar=$request->input('seo_keywords_ar');
		$singlepages->seo_description_ar=$request->input('seo_description_ar');
		$singlepages->seo_description_en=$request->input('seo_description_en');
		$singlepages->image=$imageName;
		$singlepages->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$singlepages->save();
		
		//save logs
		$key_name   = "singlepages";
		$key_id     = $singlepages->id;
		$message    = "Record for singlepages is edited. (".$singlepages->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	    return redirect('/gwc/singlepages')->with('message-success','Information is updated successfully');
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
	
	$singlepages = SinglePages::find($id);
	//delete image from folder
	if(!empty($singlepages->image)){
	$web_image_path = "/uploads/singlepages/".$singlepages->image;
	$web_image_paththumb = "/uploads/singlepages/thumb/".$singlepages->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$singlepages->image='';
	$singlepages->save();
	
	   //save logs
		$key_name   = "singlepages";
		$key_id     = $singlepages->id;
		$message    = "Image is removed. (".$singlepages->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	/**
     * Delete singlepages along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/singlepages')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $singlepages = SinglePages::find($id);
	 //check cat id exist or not
	 if(empty($singlepages->id)){
	 return redirect('/gwc/singlepages')->with('message-error','No record found'); 
	 }

     //delete image from folder
	if(!empty($singlepages->image)){
	$web_image_path = "/uploads/singlepages/".$singlepages->image;
	$web_image_paththumb = "/uploads/singlepages/thumb/".$singlepages->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	    //save logs
		$key_name   = "singlepages";
		$key_id     = $singlepages->id;
		$message    = "A record is removed. (".$singlepages->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $singlepages->delete();
	 return redirect()->back()->with('message-success','singlepages is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = SinglePages::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "singlepages";
		$key_id     = $recDetails->id;
		$message    = "singlepages status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
