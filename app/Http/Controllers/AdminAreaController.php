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

class AdminAreaController extends Controller
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
		
		if(!empty($countryInfo->parent_id)){
		$PcountryInfo = Country::where("id",$countryInfo->parent_id)->first();
		}
		
        //check search queries
		if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
        $areaLists = Area::where('parent_id',$request->parent_id)->where('name_en','LIKE','%'.$q.'%')
		                             ->orwhere('name_ar','LIKE','%'.$q.'%')
                                     ->orderBy('display_order',$settingInfo->default_sort)
                                     ->paginate($settingInfo->item_per_page_back);  
        $areaLists->appends(['q' => $q]);
		
        }else{
        $areaLists = Area::where('parent_id',$request->parent_id)->orderBy('display_order',$settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
		}
        return view('gwc.area.index',compact('areaLists','countryInfo','PcountryInfo'));
    }
	
	
	/**
	Display the Services listings
	**/
	public function create(Request $request)
    {
	
	
	$PcountryInfo=[];
	
	$lastOrderInfo = Area::where('parent_id',$request->parent_id)->OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	
	$countryInfo = Country::where("id",$request->parent_id)->first();
	if(!empty($countryInfo->parent_id)){
		$PcountryInfo = Country::where("id",$countryInfo->parent_id)->first();
		}
	return view('gwc.area.create',compact('lastOrder','countryInfo','PcountryInfo'));
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
		
		
		

		$area = new Area;
		$area->parent_id=$parent_id;
		$area->name_en=$request->input('name_en');
		$area->name_ar=$request->input('name_ar');
		$area->display_order=$request->input('display_order');
		$area->delivery_fee=$request->input('delivery_fee');
		$area->express_delivery_fee=$request->input('express_delivery_fee');
		$area->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$area->save();
		
		//save logs
		$key_name   = "area";
		$key_id     = $area->id;
		$message    = "new Area record is added as (".$request->input('name_en').")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs

        return redirect('/gwc/'.$parent_id.'/area')->with('message-success','Record is added successfully');
		
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
	    $PcountryInfo=[];
	    $countryInfo = Country::where("id",$parent_id)->first();
	    if(!empty($countryInfo->parent_id)){
		$PcountryInfo = Country::where("id",$countryInfo->parent_id)->first();
		}
	    $editarea = Area::where('id',$id)->where('parent_id',$parent_id)->first();
        return view('gwc.area.edit',compact('editarea','countryInfo','PcountryInfo'));
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
	
	
		
	$area = Area::find($id);
	$area->name_en=$request->input('name_en');
	$area->name_ar=$request->input('name_ar');
	$area->delivery_fee=$request->input('delivery_fee');
	$area->display_order=$request->input('display_order');
	$area->express_delivery_fee=$request->input('express_delivery_fee');
	$area->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
	$area->save();
	
	    //save logs
		$key_name   = "area";
		$key_id     = $area->id;
		$message    = "area details are updated for ".$request->input('name_en');
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect('/gwc/'.$area->parent_id.'/area')->with('message-success','Information is updated successfully');
	
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
	 return redirect('/gwc/'.$parent_id.'/area')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $area = Area::find($id);
	 //check cat id exist or not
	 if(empty($area->id)){
	 return redirect('/gwc/'.$parent_id.'/area')->with('message-error','No record found'); 
	 }

	 //save logs
		$key_name   = "area";
		$key_id     = $area->id;
		$message    = "area record is removed for ".$area->name_en;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	 //end deleting parent cat image
	 $area->delete();
	 return redirect()->back()->with('message-success','Record is deleted successfully');	
	 }
	 
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Area::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		//save logs
		$key_name   = "area";
		$key_id     = $recDetails->id;
		$message    = "area status is changed to ".$active." for ".$recDetails->name_en;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
