<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliveryTimes;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Auth;

class AdmindeliverytimesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $deliverytimesLists  = DeliveryTimes::orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);
        return view('gwc.deliverytimes.index',['deliverytimesLists' => $deliverytimesLists]);
    }
	
	
	/**
	Display the deliverytimes listings
	**/
	public function create()
    {
	
	$lastOrderInfo = DeliveryTimes::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.deliverytimes.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New deliverytimes Details
	**/
	public function store(Request $request)
    {
	$settingInfo = Settings::where("keyname","setting")->first();
		
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_orders_delivery_times,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_orders_delivery_times,title_ar',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string'
			        ]);
					
		try{
		
		
		$deliverytimes = new DeliveryTimes;
		$deliverytimes->title_en=$request->input('title_en');
		$deliverytimes->title_ar=$request->input('title_ar');
		$deliverytimes->details_en=$request->input('details_en');
		$deliverytimes->details_ar=$request->input('details_ar');
		$deliverytimes->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$deliverytimes->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$deliverytimes->save();

        //save logs
		$key_name   = "deliverytimes";
		$key_id     = $deliverytimes->id;
		$message    = "A new record for deliverytimes is added. (".$deliverytimes->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/deliverytimes')->with('message-success','A record is added successfully');
		
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
	    $editdeliverytimes = DeliveryTimes::find($id);
        return view('gwc.deliverytimes.edit',compact('editdeliverytimes'));
    }
	
	
	 /**
     * Show the details of the deliverytimes.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$deliverytimesDetails = DeliveryTimes::find($id);
        return view('gwc.deliverytimes.view',compact('deliverytimesDetails'));
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
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_orders_delivery_times,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_orders_delivery_times,title_ar,'.$id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string'
        ]);
		
	  try{
	  
	
		
	    $deliverytimes = DeliveryTimes::find($id);
	
		$deliverytimes->title_en=$request->input('title_en');
		$deliverytimes->title_ar=$request->input('title_ar');
		$deliverytimes->details_en=$request->input('details_en');
		$deliverytimes->details_ar=$request->input('details_ar');
		$deliverytimes->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$deliverytimes->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$deliverytimes->save();
		
		
		//save logs
		$key_name   = "deliverytimes";
		$key_id     = $deliverytimes->id;
		$message    = "Record for deliverytimes is edited. (".$deliverytimes->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	    return redirect('/gwc/deliverytimes')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	
	/**
     * Delete deliverytimes along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/deliverytimes')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $deliverytimes = DeliveryTimes::find($id);
	 //check cat id exist or not
	 if(empty($deliverytimes->id)){
	 return redirect('/gwc/deliverytimes')->with('message-error','No record found'); 
	 }

	 
	 //save logs
		$key_name   = "deliverytimes";
		$key_id     = $deliverytimes->id;
		$message    = "A record is removed. (".$deliverytimes->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $deliverytimes->delete();
	 return redirect()->back()->with('message-success','deliverytimes is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = DeliveryTimes::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "deliverytimes";
		$key_id     = $recDetails->id;
		$message    = "deliverytimes status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
