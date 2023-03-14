<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use App\Country;
use App\State;
use App\Area;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Hash;
use Auth;
use Common;

class AdminStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request) //Request $request
    {
      
	    $settingInfo = Settings::where("keyname","setting")->first();
		$countryInfo = Country::where("id",$request->parent_id)->first();
		
        //check search queries
		if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
        $stateLists = State::where('parent_id',$request->parent_id)->where('name_en','LIKE','%'.$q.'%')
		                             ->orwhere('name_ar','LIKE','%'.$q.'%')
                                     ->orderBy('display_order',$settingInfo->default_sort)
                                     ->paginate($settingInfo->item_per_page_back);  
        $stateLists->appends(['q' => $q]);
		
        }else{
        $stateLists = State::where('parent_id',$request->parent_id)->orderBy('display_order',$settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
		}
        return view('gwc.state.index',compact('stateLists','countryInfo'));
    }
	
	
	/**
	Display the Services listings
	**/
	public function create(Request $request)
    {
	$lastOrderInfo = State::where('parent_id',$request->parent_id)->OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	
	$countryInfo = Country::where("id",$request->parent_id)->first();
	
	return view('gwc.state.create',compact('lastOrder','countryInfo'));
	}
	

	
	/**
	Store New Services Details
	**/
	public function store(Request $request,$parent_id)
    {
	
	
		$settingInfo = Settings::where("keyname","setting")->first();
			
		//field validation
	    $this->validate($request, [
		    'name_en'  =>'required|string|unique:gwc_country,name_en',
            'name_ar'  => 'required|string|unique:gwc_country,name_ar',
        ]);
		
		
		
	   try{
	   
	    
		

		$state = new State;
		$state->parent_id=$parent_id;
		$state->name_en=$request->input('name_en');
		$state->name_ar=$request->input('name_ar');
		$state->display_order=$request->input('display_order');
		$state->express_delivery_fee=$request->input('express_delivery_fee');
		$state->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$state->save();
		
		//save logs
		$key_name   = "state";
		$key_id     = $state->id;
		$message    = "New state record is added as (".$request->input('name_en').")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs

        return redirect('/gwc/'.$parent_id.'/state')->with('message-success','Record is added successfully');
		
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
    public function edit($parent_id,$id)
    {
	
	    $countryInfo = Country::where("id",$parent_id)->first();
	
	    $editstate = State::where('id',$id)->where('parent_id',$parent_id)->first();
        return view('gwc.state.edit',compact('editstate','countryInfo'));
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
		    'name_en' => 'required|string|unique:gwc_country,name_en,'.$id,
            'name_ar' => 'required|string|unique:gwc_country,name_ar,'.$id,
        ]);
		
		
	 try{
	
	
	$apply_all = !empty($request->input('apply_all'))?$request->input('apply_all'):'0';	
	$state = State::find($id);
	$state->name_en=$request->input('name_en');
	$state->name_ar=$request->input('name_ar');
	$state->display_order=$request->input('display_order');
	$state->delivery_fee=$request->input('delivery_fee');
	$state->express_delivery_fee=$request->input('express_delivery_fee');
	$state->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
	$state->save();
	    if($apply_all){
	    $areas = Area::where('parent_id',$id)->get();
		foreach($areas  as $area){
		$areass = Area::where('id',$area->id)->first();
		$areass->delivery_fee=$request->input('delivery_fee');
        $areass->express_delivery_fee=$request->input('express_delivery_fee');
		$areass->save();
		}
		}
	    //save logs
		$key_name   = "state";
		$key_id     = $state->id;
		$message    = "state details are updated for ".$request->input('name_en');
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect('/gwc/'.$state->parent_id.'/state')->with('message-success','Information is updated successfully');
	
	 }catch (\Exception $e) {
	  return redirect()->back()->with('message-error',$e->getMessage());	
	 }
		
	}
	
		
	/**
     * Delete country along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($parent_id,$id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/'.$parent_id.'/state')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $state = State::find($id);
	 //check cat id exist or not
	 if(empty($state->id)){
	 return redirect('/gwc/'.$parent_id.'/state')->with('message-error','No record found'); 
	 }

	 //save logs
		$key_name   = "state";
		$key_id     = $state->id;
		$message    = "State record is removed for ".$state->name_en;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	 //end deleting parent cat image
	 //delete area
	 $this->deletesChild($state->id);
	 $state->delete();
	 return redirect()->back()->with('message-success','Record is deleted successfully');	
	 }
	 
	 //delete states & areas
	 public  function deletesChild($parent_id){
	  $getChilds = State::where("parent_id",$parent_id)->get(); 
	  if(!empty($getChilds) && count($getChilds)>0){
		foreach($getChilds as $getChild){
			State::find($getChild->id)->delete();
		}
	  }
	 }
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = State::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		//save logs
		$key_name   = "state";
		$key_id     = $recDetails->id;
		$message    = "State status is changed to ".$active." for ".$recDetails->name_en;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
	//get ajax states
	public function getAreaAjax($id){
	$states = State::where('parent_id',$id)->where('is_active',1)->get();
	return Response::json($states);	
	}
	
}
