<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Size;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Auth;

class AdminSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $sizeLists = Size::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.size.index',['sizeLists' => $sizeLists]);
    }
	
	
	/**
	Display the size listings
	**/
	public function create()
    {
	
	$lastOrderInfo = Size::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.size.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New size Details
	**/
	public function store(Request $request)
    {
		
		$settingInfo = Settings::where("keyname","setting")->first();
	
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:1|max:190|string|unique:gwc_sizes,title_en',
			'title_ar'     => 'required|min:1|max:190|string|unique:gwc_sizes,title_ar',
        ]);
		
		
		
		try{
		
		
		
		$size = new size;
	
		$size->title_en=$request->input('title_en');
		$size->title_ar=$request->input('title_ar');
        $size->details_en=$request->input('details_en');
        $size->details_ar=$request->input('details_ar');
		$size->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$size->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$size->save();

        //save logs
		$key_name   = "size";
		$key_id     = $size->id;
		$message    = "A new record for size is added. (".$size->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/size')->with('message-success','A new record is added successfully');
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
	    $editsize = Size::find($id);
        return view('gwc.size.edit',compact('editsize'));
    }
	
	
	 /**
     * Show the details of the size.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$sizeDetails = Size::find($id);
        return view('gwc.size.view',compact('sizeDetails'));
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
	
	 //field validation  
	   $this->validate($request, [
			'title_en'     => 'required|min:1|max:190|string|unique:gwc_sizes,title_en,'.$id,
			'title_ar'     => 'required|min:1|max:190|string|unique:gwc_sizes,title_ar,'.$id,
        ]);
		
		
	  try{
	  
	
		
	    $size = Size::find($id);
		$size->title_en=$request->input('title_en');
		$size->title_ar=$request->input('title_ar');
        $size->details_en=$request->input('details_en');
        $size->details_ar=$request->input('details_ar');
		$size->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$size->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$size->save();
		
		
		//save logs
		$key_name   = "news";
		$key_id     = $size->id;
		$message    = "Record for size is edited. (".$size->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	    return redirect('/gwc/size')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	  return redirect()->back()->with('message-error',$e->getMessage());	
	 }
	}
	
	/**
     * Delete size along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/size')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $size = Size::find($id);
	 //check cat id exist or not
	 if(empty($size->id)){
	 return redirect('/gwc/size')->with('message-error','No record found'); 
	 }

	 
	 
	 //save logs
		$key_name   = "size";
		$key_id     = $size->id;
		$message    = "A record is removed. (".$size->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	 //end deleting parent cat image
	 $size->delete();
	 return redirect()->back()->with('message-success','size is deleted successfully');	
	 }
	 
	 	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Size::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "size";
		$key_id     = $recDetails->id;
		$message    = "Size status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
