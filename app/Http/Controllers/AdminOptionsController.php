<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\Settings;
use Image;
use File;
use Response;
use Auth;

class AdminOptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
	    $settingInfo  = Settings::where("keyname","setting")->first();
        $optionsLists = ProductOptionsCustom::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.options.index',['optionsLists'=>$optionsLists]);
    }
	
	
	/**
	Display the banner listings
	**/
	public function create()
    {
	$lastOrderInfo = ProductOptionsCustom::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	
	$optionsLists['Choose'] = ['select','radio','checkbox','select for each order'];
	$optionsLists['Input']  = ['textarea'];
	$optionsLists['File']   = ['file'];
	$optionsLists['Date']   = ['datetime'];

	return view('gwc.options.create',compact('lastOrder','optionsLists'));
	}
	

	
	/**
	Store New banner Details
	**/
	public function store(Request $request)
    {
	
	   
		if(empty($request->option_name_en)){
		return redirect()->back()->with('message-error','Please enter option name(En)')->withInput();
		}
		if(empty($request->option_name_ar)){
		return redirect()->back()->with('message-error','Please enter option name(Ar)')->withInput();
		}
		if(empty($request->option_type)){
		return redirect()->back()->with('message-error','Please choose option type')->withInput();
		}
		if(empty($request->attach) && ($request->option_type=='select' || $request->option_type=='select for each order' || $request->option_type=='radio' || $request->option_type=='checkbox')){
		return redirect()->back()->with('message-error','Please add at least one value name')->withInput();
		}
			
	    
		try{
		
		$settingInfo = Settings::where("keyname","setting")->first();
			
		

		$options = new ProductOptionsCustom;
        $options->option_name_en   = $request->input('option_name_en');
		$options->option_name_ar   = $request->input('option_name_ar');
		$options->option_type      = $request->input('option_type');
		$options->display_order    = !empty($request->input('display_order'))?$request->input('display_order'):'0';
		$options->save();


       if(!empty($options->id) && !empty($request->attach))
         {
			$i=0;
			foreach($request->attach as $key=>$file)
            {
			    
				$option_value_name_en = $request->attach[$key]['option_value_name_en'];
				$option_value_name_ar = $request->attach[$key]['option_value_name_ar'];
				$display_order        = $request->attach[$key]['option_display_order'];
				
				$filerec = new ProductOptionsCustomChild;
				$filerec->custom_option_id     = $options->id;
				$filerec->option_value_name_en = !empty($option_value_name_en)?$option_value_name_en:'';
				$filerec->option_value_name_ar = !empty($option_value_name_ar)?$option_value_name_ar:'';
				$filerec->display_order        = !empty($display_order)?$display_order:'0';
				$filerec->save(); 
				$i++;
            }
				 	
         }
        //save logs
		$key_name   = "options";
		$key_id     = $options->id;
		$message    = "A new record for option is added. (".$options->option_name.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/options')->with('message-success','A record is added successfully');
		
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
	    $lastOrderInfo = ProductOptionsCustom::OrderBy('display_order','desc')->first();
		if(!empty($lastOrderInfo->display_order)){
		$lastOrder=($lastOrderInfo->display_order+1);
		}else{
		$lastOrder=1;
		}

		$optionsLists['Choose'] = ['select','radio','checkbox','select for each order'];
		$optionsLists['Input']  = ['textarea'];
		$optionsLists['File']   = ['file'];
		$optionsLists['Date']   = ['datetime'];
		
	    $editoptions      = ProductOptionsCustom::find($id);
		$editoptionschlds = ProductOptionsCustomChild::where('custom_option_id',$id)->get();
        return view('gwc.options.edit',compact('editoptions','lastOrder','optionsLists','editoptionschlds'));
    }
	
	
	 /**
     * Show the details of the banner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$bannerDetails = ProductOptionsCustom::find($id);
        return view('gwc.banner.view',compact('bannerDetails'));
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
	 
      if(empty($request->option_name_en)){
		return redirect()->back()->with('message-error','Please enter option name(En)')->withInput();
	  }
	  if(empty($request->option_name_ar)){
		return redirect()->back()->with('message-error','Please enter option name(Ar)')->withInput();
	  }
	  
	  if(empty($request->option_type)){
	  return redirect()->back()->with('message-error','Please choose option type')->withInput();
	  }
	  
	  if(empty($request->attach) && ($request->option_type=='select' || $request->option_type=='select for each order' || $request->option_type=='radio' || $request->option_type=='checkbox')){
	  return redirect()->back()->with('message-error','Please add at least one value name')->withInput();
	  }
		
		
		
	try{
	
	  
		
		
	    $options = ProductOptionsCustom::find($id);
		
	    $options->option_name_en   = $request->input('option_name_en');
		$options->option_name_ar   = $request->input('option_name_ar');
		$options->option_type      = $request->input('option_type');
		$options->display_order    = !empty($request->input('display_order'))?$request->input('display_order'):'0';
		$options->save();
		
		if(!empty($options->id) && !empty($request->attach))
         {
			$i=0;
			foreach($request->attach as $key=>$file)
            {
			    
				$option_value_name_en = $request->attach[$key]['option_value_name_en'];
				$option_value_name_ar = $request->attach[$key]['option_value_name_ar'];
				$display_order        = $request->attach[$key]['option_display_order'];
				
				if(!empty($request->attach[$key]['editsuboptionhidden']) && !empty($request->attach[$key]['option_value_name_en'])){
				$filerec= ProductOptionsCustomChild::where('id',$request->attach[$key]['editsuboptionhidden'])->first();
				$filerec->option_value_name_en  = !empty($option_value_name_en)?$option_value_name_en:'';
				$filerec->option_value_name_ar  = !empty($option_value_name_en)?$option_value_name_ar:'';
				$filerec->display_order         = !empty($display_order)?$display_order:'0';
				$filerec->save();
				}else{	
				if(!empty($request->attach[$key]['option_value_name_en'])){			
				$filerec= new ProductOptionsCustomChild;
				$filerec->custom_option_id      = $options->id;
				$filerec->option_value_name_en  = !empty($option_value_name_en)?$option_value_name_en:'';
				$filerec->option_value_name_ar  = !empty($option_value_name_en)?$option_value_name_ar:'';
				$filerec->display_order         = !empty($display_order)?$display_order:'0';
				$filerec->save(); 
				}
				}
				$i++;
            }
				 	
         }
		
		//save logs
		$key_name   = "options";
		$key_id     = $options->id;
		$message    = "Record for options is edited. (".$options->option_name.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	    return redirect('/gwc/options')->with('message-success','Information is updated successfully');
		}catch (\Exception $e) {
	  return redirect()->back()->with('message-error',$e->getMessage());	
	 }
	}
	
	
	
	/**
     * Delete banner along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/options')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $options = ProductOptionsCustom::find($id);
	 //check cat id exist or not
	 if(empty($options->id)){
	 return redirect('/gwc/options')->with('message-error','No record found'); 
	 }

     //delete sub option
	 $subOptions = ProductOptionsCustomChild::where('custom_option_id',$options->id)->get();
	 if(!empty($subOptions) && count($subOptions)>0){
	 foreach($subOptions as $subOption){
	 $childOption = ProductOptionsCustomChild::where("id",$subOption->id)->first();
	 $childOption->delete();
	  }
	 }
	 //save logs
		$key_name   = "option";
		$key_id     = $options->id;
		$message    = "A record is removed. (".$options->option_name.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $options->delete();
	 return redirect()->back()->with('message-success','Option is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = ProductOptionsCustom::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "options";
		$key_id     = $recDetails->id;
		$message    = "Option status is changed to ".$active." (".$recDetails->option_name.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
	//remove sub option
	public function deletechildoption(Request $request){
	//check param ID
	 if(empty($request->id)){
	 return ['status'=>400,'message'=>'ID is missing'];
	 }
	 //get cat info
	 $options = ProductOptionsCustomChild::find($request->id);
	 //check cat id exist or not
	 if(empty($options->id)){
	 return ['status'=>400,'message'=>'Record is not available'];
	 }
	
	 //save logs
		$key_name   = "option";
		$key_id     = $options->id;
		$message    = "A record is removed for option value name (".$options->option_value_name.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
	 //end save logs
	 $options->delete();
	 return ['status'=>200,'message'=>'Option record is removed successfully'];
	}
	
}
