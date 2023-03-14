<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Hash;
use File;
use Image;
use Response;
use App\Settings;
use App\CustomersWish;
use App\CustomersAddress;
use App\User;
use App\Country;
//rules
use App\Rules\Name;
use App\Rules\Mobile;

class accountController extends Controller
{
//account view
    public function index(){
	  $id = Auth::guard('webs')->user()->id;
	  $customerAddress = CustomersAddress::where('customer_id',$id)->get();	
	  return view('website.account',compact('customerAddress'));
	}
		
	//edit profile view
	public function editprofileForm(){
	  return view('website.editprofile');
	}
	//chane password
	public function changepassForm(){
	  return view('website.changepass');
	}
	//get address details
	public static function getCustAddress($addressid){
	 if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	 $address = CustomersAddress::find($addressid);
	 $addr='';
	 $country = Country::find($address->country_id);
	 $state   = Country::find($address->state_id);
	 $area    = Country::find($address->area_id);
	 
	 if(!empty($country['name_'.$strLang])){
	 $addr.='<p><b>'.trans('webMessage.country').' : </b>'.$country['name_'.$strLang].'</p>';
	 }
	 if(!empty($state['name_'.$strLang])){
	 $addr.='<p><b>'.trans('webMessage.state').' : </b>'.$state['name_'.$strLang].'</p>';
	 }
	 if(!empty($area['name_'.$strLang])){
	 $addr.='<p><b>'.trans('webMessage.area').' : </b>'.$area['name_'.$strLang].'</p>';
	 }
	 if(!empty($address->block)){
	 $addr.='<p><b>'.trans('webMessage.block').' : </b>'.$address->block.'</p>';
	 }
	 if(!empty($address->street)){
	 $addr.='<p><b>'.trans('webMessage.street').' : </b>'.$address->street.'</p>';
	 }
	 if(!empty($address->avenue)){
	 $addr.='<p><b>'.trans('webMessage.avenue').' : </b>'.$address->avenue.'</p>';
	 }
	 if(!empty($address->house)){
	 $addr.='<p><b>'.trans('webMessage.house').' : </b>'.$address->house.'</p>';
	 }
	 if(!empty($address->floor)){
	 $addr.='<p><b>'.trans('webMessage.floor').' : </b>'.$address->floor.'</p>';
	 }
	 if(!empty($address->latitude)){
	 $addr.='<p><b>'.trans('webMessage.latitude').' : </b>'.$address->latitude.'</p>';
	 }
	 if(!empty($address->longitude)){
	 $addr.='<p><b>'.trans('webMessage.longitude').' : </b>'.$address->longitude.'</p>';
	 }
	 
	 return $addr;
	}
	
	public function newaddress(){
	return view('website.newaddress');
	}
	
	public function editaddress(Request $request){
	$editAddress = CustomersAddress::find($request->id);
	return view('website.editaddress',compact('editAddress'));
	}
	
	public function addressSave(Request $request){
	$this->validate($request, [
		    'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
			'state'        => 'required|numeric|gt:0',
			'area'         => 'nullable|numeric|gt:0',
			'block'        => 'required',
			'street'       => 'required',
			'house'        => 'required'
        ],[
		'title.required'=>trans('webMessage.title_required'),
		'country.required'=>trans('webMessage.country_required'),
		'state.required'=>trans('webMessage.state_required'),
		'area.required'=>trans('webMessage.area_required'),
		'country.gt'=>trans('webMessage.country_required'),
		'state.gt'=>trans('webMessage.state_required'),
		'area.gt'=>trans('webMessage.area_required'),
		'block.required'=>trans('webMessage.block_required'),
		'street.required'=>trans('webMessage.street_required'),
		'house.required'=>trans('webMessage.house_required'),
		]);
	try{
	
		
		$id = Auth::guard('webs')->user()->id;
		$address = new CustomersAddress;
		$address->customer_id = $id;
		$address->title       = $request->input('title');
		$address->country_id  = $request->input('country');
		$address->state_id    = $request->input('state');
		$address->area_id     = $request->input('area') ?? $request->input('state');
		$address->block       = $request->input('block');
		$address->street      = $request->input('street');
		$address->avenue      = $request->input('avenue');
		$address->house       = $request->input('house');
		$address->floor       = $request->input('floor');
		$address->latitude    = $request->input('latitude');
		$address->longitude   = $request->input('longitude');
		$address->is_default  = !empty($request->input('is_default'))?$request->input('is_default'):'0';
		$address->save();
		//save other 0
		if(!empty($request->input('is_default'))){
		self::changeDefaultOther($id,$address->id);
		}
		return redirect()->back()->with('session_msg',trans('webMessage.address_added_success'));
		
		}catch (\Exception $e) {
		return redirect()->back()->with('session_msg',$e->getMessage());	
		}
	}
	
	public function editaddressSave(Request $request){
	$this->validate($request, [
		    'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
			'state'        => 'required|numeric|gt:0',
			'area'         => 'nullable|numeric|gt:0',
			'block'        => 'required',
			'street'       => 'required',
			'house'        => 'required'
        ],[
		'title.required'=>trans('webMessage.title_required'),
		'country.required'=>trans('webMessage.country_required'),
		'state.required'=>trans('webMessage.state_required'),
		'area.required'=>trans('webMessage.area_required'),
		'country.gt'=>trans('webMessage.country_required'),
		'state.gt'=>trans('webMessage.state_required'),
		'area.gt'=>trans('webMessage.area_required'),
		'block.required'=>trans('webMessage.block_required'),
		'street.required'=>trans('webMessage.street_required'),
		'house.required'=>trans('webMessage.house_required'),
		]);
		
	  try{
	  
		
		$id = Auth::guard('webs')->user()->id;
		
		$address = CustomersAddress::find($request->id);
		$address->customer_id = $id;
		$address->title       = $request->input('title');
		$address->country_id  = $request->input('country');
		$address->state_id    = $request->input('state');
		$address->area_id     = $request->input('area') ?? $request->input('state');
		$address->block       = $request->input('block');
		$address->street      = $request->input('street');
		$address->avenue      = $request->input('avenue');
		$address->house       = $request->input('house');
		$address->floor       = $request->input('floor');
		$address->latitude    = $request->input('latitude');
		$address->longitude   = $request->input('longitude');
		$address->is_default  = !empty($request->input('is_default'))?$request->input('is_default'):'0';
		$address->save();
		//save other 0
		if(!empty($request->input('is_default'))){
		self::changeDefaultOther($id,$address->id);
		}
		return redirect()->back()->with('session_msg',trans('webMessage.address_updated_success'));
		
		}catch (\Exception $e) {
		return redirect()->back()->with('session_msg',$e->getMessage());	
		}
	}
	
	public static function changeDefaultOther($customerid,$defaultid){
	$address = CustomersAddress::where('customer_id',$customerid)->where('id','!=',$defaultid)->get();
	if(!empty($address) && count($address)){
	foreach($address as $addres){
	$newAddres = CustomersAddress::find($addres->id);
	$newAddres->is_default=0;
	$newAddres->save();
	}	
	}
	}
	//delete address
	public function addressdelete(Request $request){
	
	$id = Auth::guard('webs')->user()->id;
	$address = CustomersAddress::where('id',$request->id)->where('customer_id',$id)->first();
	if(empty($address->id)){
	return redirect()->back()->with('session_msg_f',trans('webMessage.invalid_infornation'));
	}
	$address->delete();
	return redirect()->back()->with('session_msg',trans('webMessage.address_removed_success'));
	}
	//edit profile
	public function editprofileSave(Request $request){
	$id = Auth::guard('webs')->user()->id;

	$settingInfo = Settings::where("keyname","setting")->first();
	
		$image_thumb_w = 150;
		$image_thumb_h = 150;
		
		$image_big_w = 500;
		$image_big_h = 500;
		
		
	 //field validation  
	    $this->validate($request, [
		    'name'    => ['required','string','min:4','max:190',new Name],
            'email'   => 'required|email|min:3|max:150|string|unique:gwc_customers,email,'.$id,
			'mobile'  => 'required|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|unique:gwc_customers,mobile,'.$id,
			'username'=> 'required|min:3|max:20|unique:gwc_customers,username,'.$id,
			'image'   => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ],[
		    'name.required' =>trans('webMessage.name_required'),
			'name.min'      =>trans('webMessage.name_min_error'),
			'name.max'      =>trans('webMessage.name_max_error'),
			'name.string'   =>trans('webMessage.name_string_error'),
			'email.required'=>trans('webMessage.email_required'),
			'email.min'     =>trans('webMessage.email_min_error'),
			'email.max'     =>trans('webMessage.email_max_error'),
			'email.string'  =>trans('webMessage.email_string_error'),
			'email.unique'  =>trans('webMessage.email_unique_error'),
			'email.email'   =>trans('webMessage.email_error'),
			'image.mimes'   =>trans('webMessage.image_mime_error'),
			'image.max'     =>trans('webMessage.image_max_error'),
			'mobile.required'=>trans('webMessage.mobile_required'),
			'mobile.min'=>trans('webMessage.min_name_chars_required'),
			'mobile.max'=>trans('webMessage.mobile_max_name_chars_required'),
			'mobile.string'=>trans('webMessage.string_chars_required'),
			'mobile.unique'=>trans('webMessage.mobile_unique_required'),
			'username.required'=>trans('webMessage.username_required'),
			'username.min'=>trans('webMessage.min_name_chars_required'),
			'username.max'=>trans('webMessage.mobile_max_name_chars_required'),
			'username.string'=>trans('webMessage.string_chars_required'),
			'username.unique'=>trans('webMessage.username_unique_required'),
		  ]
		);
	try{
		
	$customers = User::find($id);
	
	$imageName='';
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($customers->image)){
	$web_image_path = "/uploads/customers/".$customers->image;
	$web_image_paththumb = "/uploads/customers/thumb/".$customers->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'c-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/customers'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/customers/'.$imageName));
	//resize image
	$imgbig->resize($image_big_w,$image_big_h);//Fixed w,h
	
	//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	//}
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/customers/'.$imageName));
	
	//create thumb
	// open file a image resource
    $img = Image::make(public_path('uploads/customers/'.$imageName));
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h);//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/customers/thumb/'.$imageName));
	
	}else{
	$imageName = $customers->image;
	}
	

	$customers->name=$request->input('name');
	$customers->email=$request->input('email');
	$customers->mobile=$request->input('mobile');
	$customers->username=$request->input('username');
	$customers->image=$imageName;
	$customers->save();
	return redirect(app()->getLocale().'/editprofile')->with('session_msg',trans('webMessage.profileupdatedsuccess'));	
	}catch (\Exception $e) {
	return redirect()->back()->with('session_msg',$e->getMessage());	
	}
	}
	
	//change password
	public function changepass(Request $request){
	
	   $id = Auth::guard('webs')->user()->id;
		
	    $v = Validator::make($request->all(), [
		'oldpassword'      => 'required',
        'newpassword'      => 'required',
        'confirmpassword'  => 'required|same:newpassword',
         ],[
		 'oldpassword.required'    =>trans('webMessage.oldpassword_required'),
		 'newpassword.required'    =>trans('webMessage.newpassword_required'),
		 'confirmpassword.required'=>trans('webMessage.confirmpassword_required'),
		 'confirmpassword.same'=>trans('webMessage.confirmpassword_mismatched'),
		 ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors())->withInput();
		}
		
	try{
	
	    
		
		$customers = User::find($id);
		
        if(Hash::check($request->oldpassword, $customers->password)){
        $customers->password   = bcrypt($request->newpassword);
        $customers->updated_at = date("Y-m-d H:i:s");
        $customers->save();
		return redirect(app()->getLocale().'/changepass')->with('session_msg',trans('webMessage.password_updated_success'));	
		}else{
		$error = array('oldpassword' => trans('webMessage.oldpasswordnotexist'));
        return redirect()->back()->withErrors($error)->withInput(); 
		}
		
		}catch (\Exception $e) {
		return redirect()->back()->with('session_msg',$e->getMessage());	
		}
	}
	
    //view the wish list items
	public function viewwishlist(Request $request){
	    $settingInfo = Settings::where("keyname","setting")->first();
        $wishLists = CustomersWish::where("customer_id",Auth::guard('webs')->user()->id)->orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);
        return view('website.wishlist',['wishLists' => $wishLists]);
	}
	//remove wish list
	public function ajax_remove_wish_list(Request $request){
		if(empty(Auth::guard('webs')->user()->id)){
		$message ='<div class="alert-danger">'.trans('webMessage.pleaseloginfirst').'</div>';
		return ["status"=>200,"message"=>$message];	
		}
		$wish = CustomersWish::where("id",$request->id)->where("customer_id",Auth::guard('webs')->user()->id)->first();
		if(!empty($wish->id)){
		$wish->delete();
		$message ='<div class="alert-success">'.trans('webMessage.itemremovedfromwishlist').'</div>';
		return ["status"=>200,"message"=>$message];		
		}else{
		$message ='<div class="alert-danger">'.trans('webMessage.informationdoesnotexist').'</div>';
		return ["status"=>200,"message"=>$message];	
		}	
	}
	
	//logout
	
	/**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        
		Auth::guard('webs')->logout();
        //set cookies empty
		Cookie::queue('name','',0);
		Cookie::queue('email','',0);
		Cookie::queue('mobile','',0);
		Cookie::queue('country','',0);
		Cookie::queue('state','',0);
		Cookie::queue('area','',0);
		Cookie::queue('block','',0);
		Cookie::queue('street','',0);
		Cookie::queue('avenue','',0);
		Cookie::queue('house','',0);
		Cookie::queue('floor','',0);
		Cookie::queue('landmark','',0);
		Cookie::queue('payment_method','',0);
		Cookie::queue('is_checkout',0,0);
		Cookie::queue('gb_coupon_code',0,0);
	    Cookie::queue('gb_coupon_discount',0,0);
	    Cookie::queue('gb_coupon_discount_text',0,0);
	    Cookie::queue('gb_coupon_free',0,0);
		Cookie::queue('latitude',0,0);
		Cookie::queue('longitude',0,0);
		
		//end
        return redirect(app()->getLocale().'/login')->with("session_msg",trans('webMessage.youhaveloggedoutsuccess'));
    }
}