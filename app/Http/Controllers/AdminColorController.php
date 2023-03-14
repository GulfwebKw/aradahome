<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Color;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Auth;

class AdminColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	public function index(Request $request) //Request $request
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $colorLists = Color::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.color.index',['colorLists' => $colorLists]);
    }
	
	
	/**
	Display the color listings
	**/
	public function create()
    {
	
	$lastOrderInfo = Color::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.color.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New color Details
	**/
	public function store(Request $request)
    {
	
	$settingInfo = Settings::where("keyname","setting")->first();

		$image_thumb_w = 100;
		$image_thumb_h = 100;
	
		$image_big_w = 200;
		$image_big_h = 200;
	
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_colors,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_colors,title_ar',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
	try{
		
		
		
		//upload image
		$imageName="";
		if($request->hasfile('image')){
		$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/color'), $imageName);
		// open file a image resource
		$imgbig = Image::make(public_path('uploads/color/'.$imageName));
		//resize image
		$imgbig->resize($image_big_w,$image_big_h);//Fixed w,h
		//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
		// insert watermark at bottom-right corner with 10px offset
		//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
		//}
		// save to imgbig thumb
		$imgbig->save(public_path('uploads/color/'.$imageName));
		
		//create thumb
		// open file a image resource
		$img = Image::make(public_path('uploads/color/'.$imageName));
		//resize image
		$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
		// save to thumb
		$img->save(public_path('uploads/color/thumb/'.$imageName));
		}

		$color = new Color;
		//slug
		
		$color->title_en=$request->input('title_en');
		$color->title_ar=$request->input('title_ar');
        $color->details_en=$request->input('details_en');
        $color->details_ar=$request->input('details_ar');
		$color->color_code=$request->input('color_code');
		$color->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$color->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$color->image=$imageName;
		$color->save();

        //save logs
		$key_name   = "color";
		$key_id     = $color->id;
		$message    = "A new record for color is added. (".$color->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/color')->with('message-success','A new record is added successfully');
		
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
	    $editcolor = Color::find($id);
        return view('gwc.color.edit',compact('editcolor'));
    }
	
	
	 /**
     * Show the details of the color.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$colorDetails = Color::find($id);
        return view('gwc.color.view',compact('colorDetails'));
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
	
		$image_thumb_w = 100;
		$image_thumb_h = 100;
		
		$image_big_w = 200;
		$image_big_h = 200;
		
		
	 //field validation  
	   $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_colors,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_colors,title_ar,'.$id,
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
	try{
	
	 
		
	$color = Color::find($id);
	$imageName='';
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($color->image)){
	$web_image_path = "/uploads/color/".$color->image;
	$web_image_paththumb = "/uploads/color/thumb/".$color->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/color'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/color/'.$imageName));
	//resize image
	$imgbig->resize($image_big_w,$image_big_h);//Fixed w,h
	
	//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	//}
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/color/'.$imageName));
	
	//create thumb
	// open file a image resource
    $img = Image::make(public_path('uploads/color/'.$imageName));
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/color/thumb/'.$imageName));
	
	}else{
	$imageName = $color->image;
	}
	
	//slug
		
		$color->title_en=$request->input('title_en');
		$color->title_ar=$request->input('title_ar');
        $color->details_en=$request->input('details_en');
        $color->details_ar=$request->input('details_ar');
		$color->color_code=$request->input('color_code');
		$color->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$color->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$color->image=$imageName;
		$color->save();
		
		
		//save logs
		$key_name   = "color";
		$key_id     = $color->id;
		$message    = "Record for color is edited. (".$color->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	    return redirect('/gwc/color')->with('message-success','Information is updated successfully');
		
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
	$color = Color::find($id);
	//delete image from folder
	if(!empty($color->image)){
	$web_image_path = "/uploads/color/".$color->image;
	$web_image_paththumb = "/uploads/color/thumb/".$color->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$color->image='';
	$color->save();
	
	   //save logs
		$key_name   = "color";
		$key_id     = $color->id;
		$message    = "Image is removed. (".$color->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	/**
     * Delete color along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/color')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $color = Color::find($id);
	 //check cat id exist or not
	 if(empty($color->id)){
	 return redirect('/gwc/color')->with('message-error','No record found'); 
	 }

	 //delete parent cat mage
	 if(!empty($color->image)){
	 $web_image_path = "/uploads/color/".$color->image;
	 $web_image_paththumb = "/uploads/color/thumb/".$color->image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	  }
	 }
	 
	 //save logs
		$key_name   = "color";
		$key_id     = $color->id;
		$message    = "A record is removed. (".$color->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	 //end deleting parent cat image
	 $color->delete();
	 return redirect()->back()->with('message-success','color is deleted successfully');	
	 }
	 
	 
	 
		//download pdf
	
	public function downloadPDF(){
	  $color = Color::get();
      $pdf = PDF::loadView('gwc.color.pdf', compact('color'));
      return $pdf->download('color.pdf');
    }
	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Color::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "color";
		$key_id     = $recDetails->id;
		$message    = "color status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 


    public function updateDisplayOrder(Request $request)
    {
        $id = explode("-", $request->id);
        $color= Color::find($id[1]);
        if($color){
            $color->display_order = $request->display_order;
			$color->save();
            return ["status"=>200,"message"=>""];
        }
        return ["status"=>404,"message"=>""];
    }
}
