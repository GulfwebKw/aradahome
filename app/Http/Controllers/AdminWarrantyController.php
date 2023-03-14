<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warranty;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Auth;

class AdminWarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $warrantyLists  = Warranty::orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);
        return view('gwc.warranty.index',['warrantyLists' => $warrantyLists]);
    }
	
	
	/**
	Display the warranty listings
	**/
	public function create()
    {
	
	$lastOrderInfo = Warranty::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.warranty.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New warranty Details
	**/
	public function store(Request $request)
    {
	
	$settingInfo = Settings::where("keyname","setting")->first();
		
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_warranty,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_warranty,title_ar',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string'
			        ]);
		
		try{
		
		
		$warranty = new Warranty;
		$warranty->title_en=$request->input('title_en');
		$warranty->title_ar=$request->input('title_ar');
		$warranty->details_en=$request->input('details_en');
		$warranty->details_ar=$request->input('details_ar');
		$warranty->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$warranty->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$warranty->save();

        //save logs
		$key_name   = "warranty";
		$key_id     = $warranty->id;
		$message    = "A new record for warranty is added. (".$warranty->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/warranty')->with('message-success','A record is added successfully');
		
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
	    $editwarranty = Warranty::find($id);
        return view('gwc.warranty.edit',compact('editwarranty'));
    }
	
	
	 /**
     * Show the details of the warranty.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$warrantyDetails = Warranty::find($id);
        return view('gwc.warranty.view',compact('warrantyDetails'));
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
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_warranty,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_warranty,title_ar,'.$id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string'
        ]);
	 try{
	 
	
		
	    $warranty = Warranty::find($id);
	
		$warranty->title_en=$request->input('title_en');
		$warranty->title_ar=$request->input('title_ar');
		$warranty->details_en=$request->input('details_en');
		$warranty->details_ar=$request->input('details_ar');
		$warranty->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$warranty->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$warranty->save();
		
		
		//save logs
		$key_name   = "warranty";
		$key_id     = $warranty->id;
		$message    = "Record for warranty is edited. (".$warranty->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	    return redirect('/gwc/warranty')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	
	/**
     * Delete warranty along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/warranty')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $warranty = Warranty::find($id);
	 //check cat id exist or not
	 if(empty($warranty->id)){
	 return redirect('/gwc/warranty')->with('message-error','No record found'); 
	 }

	 
	 //save logs
		$key_name   = "warranty";
		$key_id     = $warranty->id;
		$message    = "A record is removed. (".$warranty->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $warranty->delete();
	 return redirect()->back()->with('message-success','warranty is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Warranty::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "warranty";
		$key_id     = $recDetails->id;
		$message    = "warranty status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
