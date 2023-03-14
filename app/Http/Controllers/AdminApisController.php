<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AppApis;
use App\Settings;
use Image;
use File;
use Response;
use PDF;
use Auth;

class AdminApisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $apisLists   = AppApis::orderBy('id', 'desc')->paginate($settingInfo->item_per_page_back);
        return view('gwc.apis.index',['apisLists' => $apisLists]);
    }
	
	
	/**
	Display the apis listings
	**/
	public function create()
    {
	return view('gwc.apis.create');
	}
	

	
	/**
	Store New apis Details
	**/
	public function store(Request $request)
    {
	
		$settingInfo = Settings::where("keyname","setting")->first();

		
		//field validation
	    $this->validate($request, [
			'url'     => 'required|min:3|max:190|string|unique:gwc_apis,url',
			'details' => 'required|string',
        ]);
		
		
	    
		try{
		

		$apis = new AppApis;
		$apis->url        = $request->input('url');
		$apis->details    = $request->input('details');
		$apis->parameters = $request->input('parameters');
		$apis->method     = $request->input('method');
		$apis->save();

        //save logs
		$key_name   = "apis";
		$key_id     = $apis->id;
		$message    = "A new record for apis is added. (".$apis->url.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/apis')->with('message-success','A record is added successfully');
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
	    $editapis = AppApis::find($id);
        return view('gwc.apis.edit',compact('editapis'));
    }
	
	
	 /**
     * Show the details of the apis.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$apisDetails = AppApis::find($id);
        return view('gwc.apis.view',compact('apisDetails'));
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
			'url'     => 'required|min:3|max:190|string|unique:gwc_apis,url,'.$id,
			'details' => 'required|string'
        ]);
		
	  try{
	  
	
	    $apis = AppApis::find($id);
		
		$apis->url        = $request->input('url');
		$apis->details    = $request->input('details');
		$apis->parameters = $request->input('parameters');
		$apis->method     = $request->input('method');
		
		$apis->save();
		
		//save logs
		$key_name   = "apis";
		$key_id     = $apis->id;
		$message    = "Record for apis is edited. (".$apis->url.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	    return redirect('/gwc/apis')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	
	/**
     * Delete apis along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/apis')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $apis = AppApis::find($id);
	 //check cat id exist or not
	 if(empty($apis->id)){
	 return redirect('/gwc/apis')->with('message-error','No record found'); 
	 }

	    //save logs
		$key_name   = "apis";
		$key_id     = $apis->id;
		$message    = "A record is removed. (".$apis->url.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $apis->delete();
	 return redirect()->back()->with('message-success','apis is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = AppApis::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "apis";
		$key_id     = $recDetails->id;
		$message    = "apis status is changed to ".$active." (".$recDetails->url.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
