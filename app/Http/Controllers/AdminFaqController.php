<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Faq;
use App\Settings;
use Image;
use File;
use Response;
use App\Services\FaqSlug;
use PDF;
use Auth;

class AdminFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	
	 public function index(Request $request)
    {
       
	    $settingInfo = Settings::where("keyname","setting")->first();
        $faqLists = Faq::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.faq.index',['faqLists' => $faqLists]);
    }
	
	
	/**
	Display the faq listings
	**/
	public function create()
    {
	
	$lastOrderInfo = Faq::OrderBy('display_order','desc')->first();
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	return view('gwc.faq.create')->with(['lastOrder'=>$lastOrder]);
	}
	

	
	/**
	Store New faq Details
	**/
	public function store(Request $request)
    {
	
		$settingInfo = Settings::where("keyname","setting")->first();

		
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_faqs,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_faqs,title_ar',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
        ]);
		
		
	    
		try{
		

		$faq = new Faq;
		//slug
		$slug = new FaqSlug;
		
		$faq->slug=$slug->createSlug($request->title_en);
		$faq->title_en=$request->input('title_en');
		$faq->title_ar=$request->input('title_ar');
		$faq->details_en=$request->input('details_en');
		$faq->details_ar=$request->input('details_ar');
		$faq->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$faq->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$faq->save();

        //save logs
		$key_name   = "faq";
		$key_id     = $faq->id;
		$message    = "A new record for faq is added. (".$faq->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/faq')->with('message-success','A record is added successfully');
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
	    $editfaq = Faq::find($id);
        return view('gwc.faq.edit',compact('editfaq'));
    }
	
	
	 /**
     * Show the details of the faq.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$faqDetails = Faq::find($id);
        return view('gwc.faq.view',compact('faqDetails'));
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
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_faqs,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_faqs,title_ar,'.$id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
        ]);
		
	  try{
	  
	
	    $faq = Faq::find($id);
	
		$slug = new FaqSlug;
		
		$faq->slug=$slug->createSlug($request->title_en,$id);
		$faq->title_en=$request->input('title_en');
		$faq->title_ar=$request->input('title_ar');
		$faq->details_en=$request->input('details_en');
		$faq->details_ar=$request->input('details_ar');
		$faq->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$faq->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$faq->save();
		
		//save logs
		$key_name   = "faq";
		$key_id     = $faq->id;
		$message    = "Record for faq is edited. (".$faq->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	    return redirect('/gwc/faq')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	
	/**
     * Delete faq along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/faq')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $faq = Faq::find($id);
	 //check cat id exist or not
	 if(empty($faq->id)){
	 return redirect('/gwc/faq')->with('message-error','No record found'); 
	 }

	    //save logs
		$key_name   = "faq";
		$key_id     = $faq->id;
		$message    = "A record is removed. (".$faq->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		

	 $faq->delete();
	 return redirect()->back()->with('message-success','Faq is deleted successfully');	
	 }
	 
	 

	
    //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Faq::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "faq";
		$key_id     = $recDetails->id;
		$message    = "faq status is changed to ".$active." (".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
}
