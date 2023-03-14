<?php
namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use File;
use Image;
use App\CustomersWish;
use App\Country;
use App\State;
use App\Area;
use App\Newsletter;
use App\Settings;
use App\User;
use App\Product;
use App\Size;
use App\Color;
use App\Orders;
use App\OrdersDetails;
use App\OrdersTrack;
use App\OrdersOption;
use App\ProductOptions;
use App\ProductOptionsCustom;
use App\Transaction;
use App\CustomersAddress;
use App\ProductReview;
use App\ProductInquiry;

use App\Mail\SendGrid;
use Mail;
use DB;

//rules
use App\Rules\Name;
use App\Rules\Mobile;

class apiUserAccountController  extends Controller
{
    public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;
    
		
	//get user details 
	public function userDetails(Request $request){
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	}
	$user = User::where('api_token',$request->bearerToken())->first();	
	return response()->json(['data'=>$user],$this->successStatus);
	}
	//edit profile
	public function postEditProfile(Request $request){
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	}
	$user = User::where('api_token',$request->bearerToken())->first();	
	 
	$id = $user->id;

	$settingInfo = Settings::where("keyname","setting")->first();
	
		$image_thumb_w = 150;
		$image_thumb_h = 150;
		
		$image_big_w = 500;
		$image_big_h = 500;
		
		
	 //field validation  
	   $validator = Validator::make($request->all(), [
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
		
	if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
		
	$customers = User::find($id);
	
	$imageName='';
	//upload image
	if(!empty($request->hasfile('image'))){
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
	/*
	if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    $imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	}*/
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
	$imageName = !empty($customers->image)?$customers->image:'';
	}
	
	$customers->name    = $request->input('name');
	$customers->email   = $request->input('email');
	$customers->mobile  = $request->input('mobile');
	$customers->username= $request->input('username');
	$customers->dob     = $request->input('dob');
	$customers->gender  = $request->input('gender');
	$customers->is_newsletter_active  = $request->input('is_newsletter_active');
	$customers->image   = $imageName;
	$customers->save();
	
	$customersDetails = $this->customerinfo($customers->id);
	
	return response()->json(['data' =>$customersDetails ], $this->successStatus);	
	}
	
	//change password
	public function postChangePassword(Request $request){
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	}
	$user = User::where('api_token',$request->bearerToken())->first();	
	
	    $id = $user->id;
		
	    $validator = Validator::make($request->all(), [
		'oldpassword'      => 'required',
        'newpassword'      => 'required',
        'confirmpassword'  => 'required|same:newpassword',
         ],[
		 'oldpassword.required'    =>trans('webMessage.oldpassword_required'),
		 'newpassword.required'    =>trans('webMessage.newpassword_required'),
		 'confirmpassword.required'=>trans('webMessage.confirmpassword_required'),
		 'confirmpassword.same'=>trans('webMessage.confirmpassword_mismatched'),
		 ]);

		if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
		
		$customers = User::find($id);
		
        if(Hash::check($request->oldpassword, $customers->password)){
        $customers->password   = bcrypt($request->newpassword);
        $customers->updated_at = date("Y-m-d H:i:s");
        $customers->save();
		return response()->json(['data' =>trans('webMessage.password_updated_success') ], $this->successStatus);	
		}else{ 
		return response()->json(['data' =>trans('webMessage.oldpasswordnotexist') ], $this->failedStatus);	
		}
	}
	
	//////////////////////////////////////////////////////////////WISH ITEMS/////////////////////////////////////////////////
	
	
	//add to wish list
	public function saveWishItem(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	$settingInfo = Settings::where("keyname","setting")->first();
    if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	}
	$user = User::where('api_token',$request->bearerToken())->first();	
		
	if(empty($request->product_id)){
	return response()->json(['data' =>trans('webMessage.productidmissing') ], $this->failedStatus);	
	}
	//check item exist or not
	
	$productDetails  = Product::where('id',$request->product_id)->where('is_active','!=',0)->first();	
	if(empty($productDetails->id)){
	return response()->json(['data' =>trans('webMessage.itemdoesexist') ], $this->failedStatus);	
	}
	
	$wish = CustomersWish::where("product_id",$request->product_id)->where("customer_id",$user->id)->first();
	if(!empty($wish->id)){	
    return response()->json(['data' =>trans('webMessage.itemisalreadyaddedtowish') ], $this->failedStatus);		
	}
	
	$wish = new CustomersWish;
	$wish->product_id = $request->product_id;
	$wish->customer_id = $user->id;
	$wish->save();
	return response()->json(['data' =>trans('webMessage.itemaddedtowishlist') ], $this->successStatus);	
	}
	
	
    //view the wish list items
	public function getWishItems(Request $request){
		if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	    $settingInfo = Settings::where("keyname","setting")->first();
		if(empty($this->isTokenValid($request->bearerToken()))){
	    return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	    }
	    $user = User::where('api_token',$request->bearerToken())->first();	
	
	    $id = $user->id;
		
		$limit = 100;
		if(!empty($request->offset)){
		$offset = $request->offset;
		}else{
		$offset = 0;
		}
		
        $wishLists = CustomersWish::where("customer_id",$id)
		->join('gwc_products','gwc_products.id','=','gwc_customers_wish.product_id')
		->select('gwc_products.id as product_id','gwc_products.title_en','gwc_products.title_en','gwc_products.title_en','gwc_products.title_en','gwc_products.title_ar','gwc_products.image','gwc_products.old_price','gwc_products.retail_price','gwc_products.caption_en','gwc_products.caption_ar','gwc_products.caption_color','gwc_customers_wish.*')
		->where('gwc_products.is_active','!=',0)
		->orderBy('id', 'DESC')->offset($offset)->limit($limit)->get();
		$wishListBox=[];
		if(!empty($wishLists) && count($wishLists)>0){
		foreach($wishLists as $wishList){
         $wish['id']=$wishList->id;
		 $wish['product_id']=$wishList->product_id;
		 $wish['title']=$wishList['title_'.$strLang];
		 $wish['image']=url('uploads/product/thumb/'.$wishList->image);
		 $wish['retail_price']=round($wishList->retail_price,3);
		 $wish['old_price']=round($wishList->old_price,3);
		 $wish['caption_name']=(string)$wishList['caption_'.$strLang];
		 $wish['caption_color']=(string)$wishList->caption_color;
		 $wishListBox[]=$wish;
        }		
		$success['data']=$wishListBox;	
	    return response()->json($success,$this->successStatus);	
		}else{
		$success['data']=trans('webMessage.norecordfound');	
	    return response()->json($success,$this->failedStatus);	
		}
	}
	
	
	///////remove wish list
	public function deleteWishItem(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	$settingInfo = Settings::where("keyname","setting")->first();
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);		
	}
	$user = User::where('api_token',$request->bearerToken())->first();
	
	$wish = CustomersWish::where("id",$request->id)->where("customer_id",$user->id)->first();
	if(!empty($wish->id)){
	$wish->delete();
	$success['data']=trans('webMessage.itemremovedfromwishlist');	
	return response()->json($success,$this->successStatus);
	}else{
	$success['data']=trans('webMessage.norecordfound');	
	return response()->json($success,$this->failedStatus);
	}
	}
	///////////////////////////////////////////////////////Transaction////////////////////////////////////
	
	public function getTransactionsLists(Request $request){
		if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	    $settingInfo = Settings::where("keyname","setting")->first();
		
		if(empty($this->isTokenValid($request->bearerToken()))){
	    return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	    }
	    $user = User::where('api_token',$request->bearerToken())->first();	
	    $id = $user->id;
		
		$limit = 100;
		if(!empty($request->offset)){
		$offset = $request->offset;
		}else{
		$offset = 0;
		}
		
        $transLists = OrdersDetails::where("customer_id",$id)
		->join('gwc_transaction','gwc_transaction.trackid','=','gwc_orders_details.order_id')
		->select(
		'gwc_transaction.id',
		'gwc_transaction.payment_id',
		'gwc_transaction.presult',
		'gwc_transaction.udf2 as amount',
		'gwc_transaction.tranid',
		'gwc_transaction.auth',
		'gwc_transaction.ref',
		'gwc_transaction.trackid',
		'gwc_transaction.created_at'
		);
		
		if(!empty($request->searchkey)){
		$search = $request->searchkey;
		$transLists = $transLists
		->where('gwc_transaction.payment_id', 'like', '%' .$search . '%')
	    ->orwhere('gwc_transaction.presult', 'like', '%' .$search . '%')
		->orwhere('gwc_transaction.auth', 'like', '%' .$search . '%')
		->orwhere('gwc_transaction.ref', 'like', '%' .$search . '%')
		->orwhere('gwc_transaction.trackid', 'like', '%' .$search . '%')
		->orwhere('gwc_transaction.created_at', 'like', '%' .$search . '%')
	    ->orwhere('gwc_transaction.tranid', '=', '%' .$search . '%');	
		}
		
		$transLists = $transLists->orderBy('created_at', 'DESC')->offset($offset)->limit($limit)->get();

		$transListsBox=[];
		if(!empty($transLists) && count($transLists)>0){
		foreach($transLists as $transList){
            $trans['payment_id']= $transList->payment_id;
			$trans['presult']   = $transList->presult;
			$trans['amount']    = !empty($transList->amount)?$transList->amount:'';
			$trans['tranid']    = !empty($transList->tranid)?$transList->tranid:'';
			$trans['auth']      = !empty($transList->auth)?$transList->auth:'';
			$trans['ref']       = !empty($transList->ref)?$transList->ref:'';
			$trans['trackid']   = !empty($transList->trackid)?$transList->trackid:'';
			$trans['created_at']= $transList->created_at;
		    $transListsBox[]    = $trans;
        }		
		$success['data']=$transListsBox;	
	    return response()->json($success,$this->successStatus);	
		}else{
		$success['data']=trans('webMessage.norecordfound');	
	    return response()->json($success,$this->failedStatus);	
		}
	}
	
	
	
	
	//////get user orders
	public function getUserOrders(Request $request){
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	}
	
	$limit = 100;
	if(!empty($request->offset)){
	$offset = $request->offset;
	}else{
	$offset = 0;
	}
	
	
	$user = User::where('api_token',$request->bearerToken())->first();	
	$ordersLists = OrdersDetails::where('customer_id',$user->id)
				   ->select(
				   'gwc_orders_details.id',
				   'gwc_orders_details.order_id',
				   'gwc_orders_details.name',
				   'gwc_orders_details.mobile',
				   'gwc_orders_details.order_status',
				   'gwc_orders_details.total_amount',
				   'gwc_orders_details.is_paid',
				   'gwc_orders_details.pay_mode',
				   'gwc_orders_details.created_at'
				   );
	
	$ordersLists = $ordersLists->orderBy('gwc_orders_details.id','desc')->offset($offset)->limit($limit)->get();	
	return response()->json(['data'=>$ordersLists],$this->successStatus);
	}
	
	
	
	//get order details 
	public function getUserOrdersDetails(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	

	if(empty($request->orderid)){
	return response()->json(['data' =>trans('webMessage.invalidorderid') ], $this->failedStatus);		
	}
	
	if(!empty($this->isTokenValid($request->bearerToken()))){
	$user = User::where('api_token',$request->bearerToken())->first();		
	$ordersDetailsStore  = OrdersDetails::where('customer_id',$user->id)->where('order_id',$request->orderid)->first();	
	}else{
	$ordersDetailsStore  = OrdersDetails::where('order_id',$request->orderid)->first();	
	}
	
	if(empty($ordersDetailsStore->id)){
	return response()->json(['data' =>trans('webMessage.invalidorderid') ], $this->failedStatus);
	}
	
	//country
	$countryName='';
	if(!empty($ordersDetailsStore->country_id)){
	$countryDetails = Country::where('is_active',1)->where('id',$ordersDetailsStore->country_id)->first();		
	if($countryDetails->id){
	$countryName = $countryDetails['name_'.$strLang];	
	}
	}
	//state
	$stateName='';
	if(!empty($ordersDetailsStore->state_id)){
	$stateDetails = State::where('id',$ordersDetailsStore->state_id)->first();		
	if($stateDetails->id){
	$stateName = $stateDetails['name_'.$strLang];	
	}
	}
	//area
	$areaName='';
	if(!empty($ordersDetailsStore->area_id)){
	$areaDetails = Area::where('id',$ordersDetailsStore->area_id)->first();		
	if($areaDetails->id){
	$areaName = $areaDetails['name_'.$strLang];	
	}
	}
	
	$ordersDetails['id']       = $ordersDetailsStore['id'];
	$ordersDetails['order_id'] = $ordersDetailsStore['order_id'];
	$ordersDetails['name']     = $ordersDetailsStore['name'];
	$ordersDetails['email']    = $ordersDetailsStore['email'];
	$ordersDetails['mobile']   = $ordersDetailsStore['mobile'];
	$ordersDetails['country']  = $countryName;
	$ordersDetails['state']    = $stateName;
	$ordersDetails['area']     = $areaName;
	$ordersDetails['block']    = !empty($ordersDetailsStore['block'])?$ordersDetailsStore['block']:'';
	$ordersDetails['street']   = !empty($ordersDetailsStore['street'])?$ordersDetailsStore['street']:'';
	$ordersDetails['avenue']   = !empty($ordersDetailsStore['avenue'])?$ordersDetailsStore['avenue']:'';
	$ordersDetails['house']    = !empty($ordersDetailsStore['house'])?$ordersDetailsStore['house']:'';
	$ordersDetails['floor']    = !empty($ordersDetailsStore['floor'])?$ordersDetailsStore['floor']:'';
	$ordersDetails['landmark'] = !empty($ordersDetailsStore['landmark'])?$ordersDetailsStore['landmark']:'';
	$ordersDetails['pay_mode'] = !empty($ordersDetailsStore['pay_mode'])?$ordersDetailsStore['pay_mode']:'';
	$ordersDetails['is_coupon_used'] = !empty($ordersDetailsStore['is_coupon_used'])?$ordersDetailsStore['is_coupon_used']:'0';
	$ordersDetails['coupon_code']    = !empty($ordersDetailsStore['coupon_code'])?$ordersDetailsStore['coupon_code']:'0';
	$ordersDetails['coupon_amount']  = !empty($ordersDetailsStore['coupon_amount'])?$ordersDetailsStore['coupon_amount']:'0';
	$ordersDetails['is_coupon_free'] = !empty($ordersDetailsStore['coupon_free'])?$ordersDetailsStore['coupon_free']:'0';
	$ordersDetails['delivery_charges']= !empty($ordersDetailsStore['delivery_charges'])?$ordersDetailsStore['delivery_charges']:'0';
	$ordersDetails['is_paid']         = !empty($ordersDetailsStore['is_paid'])?trans('webMessage.paid'):trans('webMessage.notpaid');
	$ordersDetails['order_status']    = trans('webMessage.'.$ordersDetailsStore['order_status']);
	$ordersDetails['longitude']      = !empty($ordersDetailsStore['longitude'])?$ordersDetailsStore['longitude']:'';
	$ordersDetails['latitude']       = !empty($ordersDetailsStore['latitude'])?$ordersDetailsStore['latitude']:'';
	$ordersDetails['created_at']     = !empty($ordersDetailsStore['created_at'])?date("Y-m-d H:i:s",strtotime($ordersDetailsStore['created_at'])):'';
	$ordersDetails['ordersLists']    = self::getOrdersLists($ordersDetailsStore['id'],$strLang,$ordersDetailsStore['delivery_charges'],$ordersDetailsStore['coupon_amount'],$ordersDetailsStore['coupon_free']);
	$ordersDetails['payments']       = self::getTransactions($ordersDetailsStore['order_id']);
	$success['data']=[$ordersDetails];
	
	return response()->json($success,$this->successStatus);
	}
	//get orders items lists
	public static function getOrdersLists($oid,$strLang,$delivery_charges,$coupon_amount,$coupon_free){
	$suborders = []; $orders=[];$storechild=[];
	$tempOrders = Orders::where('oid',$oid)->get();
	$ordertxt_child='';$subtotalprice=0;$grandtotal=0;$totalprice=0;
	foreach($tempOrders as $tempOrder){
	$productDetails =self::getProductDetails($tempOrder->product_id);
	$suborders['item_name']=!empty($productDetails['title_'.$strLang])?$productDetails['title_'.$strLang]:'Not available';
	if(!empty($tempOrder->size_id)){
    $sizeName = self::sizeNameStatic($tempOrder->size_id,$strLang);
    }else{$sizeName='';}
	$suborders['size_name']=$sizeName;
    if(!empty($tempOrder->color_id)){
    $colorName = self::colorNameStatic($tempOrder->color_id,$strLang);
    }else{$colorName='';}
	$suborders['color_name']=$colorName;
	
	//$orderOptions = self::getOptionsOrdersDtailsBr($tempOrder->id);
	$orderOptions = self::getOptionsDtailsOrder($tempOrder->id);
	if(!empty($orderOptions)){
	$suborders['other_options'] = $orderOptions;
	}else{
	$suborders['other_options'] = [];
	}
	
	$subtotalprice = $tempOrder->unit_price*$tempOrder->quantity;

    $suborders['unit_price']   = $tempOrder->unit_price;
	$suborders['quantity']     = $tempOrder->quantity;
    $suborders['sub_total']    = $subtotalprice;
	$suborders['image']        = !empty($productDetails['image'])?url('uploads/product/thumb/'.$productDetails['image']):url('uploads/no-image.png');	
	$storechild[]=$suborders;
	$totalprice+=$subtotalprice;
	}
	//apply delivery charges
	$grandtotal=$totalprice;
	if(!empty($delivery_charges)){
	$grandtotal=$totalprice+$delivery_charges;	
	}
	//apply coupon
	$coupon_amount = !empty($coupon_amount)?$coupon_amount:'0';
	if(!empty($coupon_amount) && empty($coupon_free)){
	$grandtotal=$grandtotal-$coupon_amount;		
	}
	$orders['orders']       = $storechild;
	$orders['totalprice']   = $totalprice;
	$orders['coupon_free']  = $coupon_free;
	$orders['coupon_amount']= $coupon_amount;
	$orders['delivery_charges']= $delivery_charges;
	$orders['grandtotal']   = $grandtotal;
	return $orders;	
	}
	
	
	public static function getOptionsDtailsOrder($oid){
	$options=[];$option_name='';
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	$optionDetails = OrdersOption::where("oid",$oid)->get();	
	if(!empty($optionDetails) && count($optionDetails)>0){
	foreach($optionDetails as $optionDetail){
	$optionParentDetails = ProductOptionsCustom::where("id",$optionDetail->option_id)->first();
	$option_name = $strLang=="en"?$optionParentDetails->option_name_en:$optionParentDetails->option_name_ar;
	$options[] = [
	             "custom_option_id"=>$optionParentDetails->id,
				 "custom_option_name"=>$option_name,
				 "child_options"=>self::getChildOptionsDtails($optionDetail->option_child_ids)
	             ];
	}	
	}
	return $options;
	}
	
	
	public static function getChildOptionsDtails($ids){
	
	$optxt=[];
	$explode = explode(",",$ids);
	if(count($explode)>0){
	for($i=0;$i<count($explode);$i++){
	$optxt[]=self::getJoinOptions($explode[$i]);
	}
	}else{
	$optxt[]=self::getJoinOptions($ids);
	}
	return $optxt;	
	}
	
	public static function getJoinOptions($id){
	$optionsy='';$optionName='';
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	$options = ProductOptions::where("gwc_products_options.id",$id);
	$options = $options->select('gwc_products_options.*','gwc_products_option_custom_child.id as oid','gwc_products_option_custom_child.option_value_name_en','gwc_products_option_custom_child.option_value_name_ar');
	$options = $options->join('gwc_products_option_custom_child','gwc_products_option_custom_child.id','=','gwc_products_options.option_value_id');
	$options = $options->orderBy('gwc_products_options.option_value_id','ASC')->get();
	if(!empty($options) && count($options)>0){
	foreach($options as $option){
	$optionName=($strLang=="en"?$option->option_value_name_en:$option->option_value_name_ar);
	$optionsy.=$optionName.',';
	}	
	}
	return trim($optionsy,",");
	}
	
	//get Size Name
	public static function sizeNameStatic($id,$strLang){
	$txt='--';
	$Details   = Size::where('id',$id)->first();
	if(!empty($Details['title_'.$strLang])){
	$txt=$Details['title_'.$strLang];
	}
	return $txt;
	}
	//get color name
	public static function colorNameStatic($id,$strLang){
	$txt='--';
	$Details   = Color::where('id',$id)->first();
	if(!empty($Details['title_'.$strLang])){
	$txt=$Details['title_'.$strLang];
	}
	return $txt;
	}
	//get product details
	public static function getProductDetails($id){
	$prodDetails = Product::where('id',$id)->first();
	return $prodDetails;
	}
	//get transaction details
	public static function getTransactions($order_id){
	$trans = [];
	$transactionDetails = Transaction::where('trackid',$order_id)->first();	
	if(!empty($transactionDetails->id)){
	$trans['payment_id']= $transactionDetails->payment_id;
	$trans['presult']   = $transactionDetails->presult;
	$trans['amount']    = !empty($transactionDetails->udf2)?$transactionDetails->udf2:'';
	$trans['tranid']    = !empty($transactionDetails->tranid)?$transactionDetails->tranid:'';
	$trans['auth']      = !empty($transactionDetails->auth)?$transactionDetails->auth:'';
	$trans['ref']       = !empty($transactionDetails->ref)?$transactionDetails->ref:'';
	$trans['created_at']= $transactionDetails->created_at;
	}
	return $trans;
	}
	//get user address
	public function userAddress(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	}
	
	$user = User::where('api_token',$request->bearerToken())->first();	

	$addressDetails = CustomersAddress::where('customer_id',$user->id)->get();
	if(!empty($addressDetails) && count($addressDetails)>0){
		$add=[];$subadd=[];$countryName='';$stateName='';$areaName='';
		foreach($addressDetails as $addressDetail){
			$add['id']=$addressDetail->id;
			$add['country_id']=$addressDetail->country_id;
			$add['state_id']=$addressDetail->state_id;
			$add['area_id']=$addressDetail->area_id;
		    //country
			$countryDetails = Country::where('is_active',1)->where('id',$addressDetail->country_id)->first();		
			if($countryDetails->id){
			$countryName = $countryDetails['name_'.$strLang];	
			}
			//state
			$stateDetails = State::where('id',$addressDetail->state_id)->first();		
			if($stateDetails->id){
			$stateName = $stateDetails['name_'.$strLang];	
			}
			//area
			$areaDetails = Area::where('id',$addressDetail->area_id)->first();		
			if($areaDetails->id){
			$areaName = $areaDetails['name_'.$strLang];	
			}
			
			$add['country_name'] = $countryName;
			$add['state_name']   = $stateName;
			$add['area_name']    = $areaName;
			$add['block']        = !empty($addressDetail->block)?$addressDetail->block:'';
			$add['street']       = !empty($addressDetail->street)?$addressDetail->street:'';
			$add['avenue']       = !empty($addressDetail->avenue)?$addressDetail->avenue:'';
			$add['house']        = !empty($addressDetail->house)?$addressDetail->house:'';
			$add['floor']        = !empty($addressDetail->floor)?$addressDetail->floor:'';
			$add['title']        = !empty($addressDetail->title)?$addressDetail->title:'My Address';
			$add['is_default']   =  !empty($addressDetail->is_default)?$addressDetail->is_default:'0';
			$add['landmark']     =  !empty($addressDetail->landmark)?$addressDetail->landmark:'';
			$add['latitude']     =  !empty($addressDetail->latitude)?$addressDetail->latitude:'';
			$add['longitude']    =  !empty($addressDetail->longitude)?$addressDetail->longitude:'';
			
			$subadd[]=$add;
		}
	$success['data']=$subadd;	
	return response()->json($success,$this->successStatus);	
	}else{
	$success['data']=trans('webMessage.norecordfound');	
	return response()->json($success,$this->failedStatus);
	}
	}
	
	public function newAddress(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	}
	
	$user = User::where('api_token',$request->bearerToken())->first();	
	
	if(empty($user->id)){
	return response()->json(['data' =>trans('webMessage.invalidapitoken').'(User Info)'], $this->failedStatus);		
	}
	
	
		$validator = Validator::make($request->all(), [
		    'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
			'state'        => 'required|numeric|gt:0',
			'area'         => 'required|numeric|gt:0',
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
		
		if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
				
		$address = new CustomersAddress;
		$address->customer_id=$user->id;
		$address->title=$request->input('title');
		$address->country_id=$request->input('country');
		$address->state_id=$request->input('state');
		$address->area_id=$request->input('area');
		$address->block=$request->input('block');
		$address->street=$request->input('street');
		$address->avenue=$request->input('avenue');
		$address->house=$request->input('house');
		$address->floor=$request->input('floor');
		$address->landmark=$request->input('landmark');
		$address->latitude=$request->input('latitude');
		$address->longitude=$request->input('longitude');
		$address->is_default=!empty($request->input('is_default'))?$request->input('is_default'):'0';
		$address->save();
		//save other 0
		self::changeDefaultOther($user->id,$address->id);
		
		return response()->json(['data'=>trans('webMessage.address_added_success')], $this->successStatus); 
	}
	
	public function editAddress(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	}
	
	if(empty($request->id)){
	return response()->json(['data' =>trans('webMessage.idmissing')], $this->failedStatus);		
	}
	
	$user = User::where('api_token',$request->bearerToken())->first();	
	
		$validator = Validator::make($request->all(), [
		    'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
			'state'        => 'required|numeric|gt:0',
			'area'         => 'required|numeric|gt:0',
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
		
		if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
				
		$address = CustomersAddress::find($request->id);
		$address->customer_id=$user->id;
		$address->title=$request->input('title');
		$address->country_id=$request->input('country');
		$address->state_id=$request->input('state');
		$address->area_id=$request->input('area');
		$address->block=$request->input('block');
		$address->street=$request->input('street');
		$address->avenue=$request->input('avenue');
		$address->house=$request->input('house');
		$address->floor=$request->input('floor');
		$address->landmark=$request->input('landmark');
		$address->latitude=$request->input('latitude');
		$address->longitude=$request->input('longitude');
		$address->is_default=!empty($request->input('is_default'))?$request->input('is_default'):'0';
		$address->save();
		//save other 0
		self::changeDefaultOther($user->id,$address->id);
		
		return response()->json(['data'=>trans('webMessage.address_updated_success')], $this->successStatus); 
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
	//get user review listings
	public function getUserReviews(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	
	$limit = 100;
	if(!empty($request->offset)){
	$offset = $request->offset;
	}else{
	$offset = 0;
	}
		
	if(empty($this->isTokenValid($request->bearerToken()))){
	return response()->json(['data' =>trans('webMessage.invalidapitoken')], $this->failedStatus);		
	}
	
	$user = User::where('api_token',$request->bearerToken())->first();	
	if(empty($user->id)){
	return response()->json(['data' =>trans('webMessage.recordnotfound')], $this->failedStatus);		
	}
	
	$ReviewsLists = ProductReview::where('is_active',1)->where('customer_id',$user->id)->orderBy('created_at','DESC')->offset($offset)->limit($limit)->get();
	if(!empty($ReviewsLists) && count($ReviewsLists)){
	$myreviews = [];
	foreach($ReviewsLists as $ReviewsList){
	$productDetails = Product::where('id',$ReviewsList->product_id)->first();
	if(!empty($productDetails['image'])){
	$imageurl = url('uploads/product/thumb/'.$productDetails['image']);	
	}else{
	$imageurl = url('uploads/no-image.png');
	}
	$myreviews[]=[
	             'id'=>$ReviewsList->id,
				 'customer_id'=>$ReviewsList->customer_id,
				 'product_id'=>$ReviewsList->product_id,
				 'name'=>$ReviewsList->name,
				 'email'=>$ReviewsList->email,
				 'message'=>$ReviewsList->message,
				 'ratings'=>$ReviewsList->ratings,
				 'product_title'=>$productDetails['title_'.$strLang],
				 'product_image'=>$imageurl,
				 ];
	
	}
	
	$success['data'] = $myreviews;
	}else{
	$success['data'] = trans('webMessage.norecordfound');;
	}
    return response()->json($success, $this->successStatus);
	}
	
	//post product reviews
	
	public function postReview(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}	
	
	if(!empty($request->bearerToken())){
	$user = User::where('api_token',$request->bearerToken())->first();	
	$customer_id=$user->id;
	}else{
	$customer_id=0;
	}
	
	$settingInfo      = Settings::where("keyname","setting")->first();
	
	      $validator = Validator::make($request->all(),[
		    'product_id'=>'required',
		    'ratings'   => 'required|gt:0',
            'name'      => ['required','string','min:4','max:190',new Name],
            'email'     => 'required|email',
			'message'   => 'required|string|min:10|max:900',
            ],
            [ 
			'product_id.required' => trans('webMessage.product_id_required'),
			'ratings.required' => trans('webMessage.ratings_required'),
			'name.required'    => trans('webMessage.name_required'),
			'email.required'   => trans('webMessage.email_required'),
			'message.required' => trans('webMessage.message_required')
			]
			);
	  if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
		
	 $productDetails = self::getProductDetails($request->input('product_id'));	
			
	 $reviews = new ProductReview;		
	 $reviews->name=$request->input('name');
	 $reviews->email=$request->input('email');
	 $reviews->customer_id=$customer_id;
	 $reviews->product_id=$request->input('product_id');
	 $reviews->message=strip_tags($request->input('message'));
	 $reviews->ratings=$request->input('ratings');
	 $reviews->is_active=0;
	 $reviews->created_at=date("Y-m-d H:i:s");
	 $reviews->updated_at=date("Y-m-d H:i:s");
	 $reviews->save();	
	 //send email notification
	 if(!empty($request->input('email'))){
	 $data = [
	 'dear'    => trans('webMessage.dear').' '.$request->input('name'),
	 'footer'  => trans('webMessage.email_footer'),
	 'message' => trans('webMessage.reviews_body')."<br><img src='".url('uploads/product/'.$productDetails['image'])."' width='150'><br><h2>".$productDetails['title_'.$strLang]."</h2>",
	 'subject' =>"Product Review Notification",
	 'email_from' =>$settingInfo->from_email,
	 'email_from_name' =>$settingInfo->from_name
	 ];
     Mail::to($request->input('email'))->send(new SendGrid($data));
	 }
	 //
	 if(!empty($settingInfo->email)){
	 $appendMessage ="";
	 $appendMessage .= "<br><b>".trans('webMessage.name')." : </b>".$request->input('name');
	 $appendMessage .= "<br><b>".trans('webMessage.email')." : </b>".$request->input('email');
	 $appendMessage .= "<br><b>".trans('webMessage.productname')." : </b>".$productDetails['title_'.$strLang];
	 $appendMessage .= "<br><b>".trans('webMessage.message')." : </b>".strip_tags($request->input('message'));
	 $dataadmin = [
	 'dear' => trans('webMessage.dearadmin'),
	 'footer' => trans('webMessage.email_footer'),
	 'message' => trans('webMessage.review_admin_body')."<br><img src='".url('uploads/product/'.$productDetails['image'])."' width='150'><br><h2>".$productDetails['title_'.$strLang]."</h2>"."<br><br>".$appendMessage,
	 'subject' =>"Product Review Notification",
	 'email_from' =>$settingInfo->from_email,
	 'email_from_name' =>$settingInfo->from_name
	 ];
     Mail::to($settingInfo->email)->send(new SendGrid($dataadmin));	 
	 }
	  //end sending email	
     $success['data'] = trans('webMessage.review_message_sent');
     return response()->json($success, $this->successStatus);	
	}
	
	//post product inquiry
	
	//post product inquiry
	public function postInquiry(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}	
	
	if(!empty($request->bearerToken())){
	$user = User::where('api_token',$request->bearerToken())->first();	
	$customer_id=$user->id;
	}else{
	$customer_id=0;
	}
	
	$settingInfo      = Settings::where("keyname","setting")->first();
	
	
	if(empty($request->product_id)){	
	$success['data'] = trans('webMessage.idmissing');
    return response()->json($success, $this->successStatus);
	}
	if(empty($request->inquiry_name)){
	$success['data'] = trans('webMessage.name_required');
     return response()->json($success, $this->successStatus);
	}
	
	if(empty($request->inquiry_email) && empty($request->inquiry_mobile)){
	$success['data'] = trans('webMessage.email_required').' OR '.trans('webMessage.mobile_required');
     return response()->json($success, $this->successStatus);	
	}
	
	if(!empty($request->inquiry_email) && !filter_var($request->inquiry_email, FILTER_VALIDATE_EMAIL)){
	$success['data'] = trans('webMessage.email_valid_required');
     return response()->json($success, $this->successStatus);
	}
	
	if(empty($request->inquiry_message)){	
	$success['data'] = trans('webMessage.message_required');
     return response()->json($success, $this->successStatus);
	}
	
	 $productDetails = self::getProductDetails($request->input('product_id'));	
			
	 $reviews = new ProductInquiry;		
	 $reviews->name        = $request->input('inquiry_name');
	 $reviews->email       = $request->input('inquiry_email');
	 $reviews->mobile      = $request->input('inquiry_mobile');
	 $reviews->customer_id = $customer_id;
	 $reviews->product_id  = $request->input('product_id');
	 $reviews->message     = $request->input('inquiry_message');
	 $reviews->created_at  = date("Y-m-d H:i:s");
	 $reviews->updated_at  = date("Y-m-d H:i:s");
	 $reviews->save();	
	 //send email notification
	 if(!empty($request->input('inquiry_email'))){
	 $data = [
	 'dear'    => trans('webMessage.dear').' '.$request->input('inquiry_name'),
	 'footer'  => trans('webMessage.email_footer'),
	 'message' => trans('webMessage.inquiry_body')."<br><img src='".url('uploads/product/'.$productDetails['image'])."' width='150'><br><h2>".$productDetails['title_'.$strLang]."</h2>",
	 'subject' =>"Product Inquiry Notification",
	 'email_from' =>$settingInfo->from_email,
	 'email_from_name' =>$settingInfo->from_name
	 ];
     Mail::to($request->input('inquiry_email'))->send(new SendGrid($data));
	 }
	 //
	 if(!empty($settingInfo->email)){
	 $appendMessage ="";
	 if(!empty($request->input('inquiry_name'))){
	 $appendMessage .= "<br><b>".trans('webMessage.name')." : </b>".$request->input('inquiry_name');
	 }
	 if(!empty($request->input('inquiry_mobile'))){
	 $appendMessage .= "<br><b>".trans('webMessage.mobile')." : </b>".$request->input('inquiry_mobile');
	 }
	 if(!empty($request->input('inquiry_email'))){
	 $appendMessage .= "<br><b>".trans('webMessage.email')." : </b>".$request->input('inquiry_email');
	 }
	 $appendMessage .= "<br><b>".trans('webMessage.productname')." : </b>".$productDetails['title_'.$strLang];
	 if(!empty($request->input('inquiry_message'))){
	 $appendMessage .= "<br><b>".trans('webMessage.message')." : </b>".$request->input('inquiry_message');
	 }
	 
	 $dataadmin = [
	 'dear'            => trans('webMessage.dearadmin'),
	 'footer'          => trans('webMessage.email_footer'),
	 'message'         => trans('webMessage.inquiry_admin_body')."<br><img src='".url('uploads/product/'.$productDetails['image'])."' width='150'><br><h2>".$productDetails['title_'.$strLang]."</h2>"."<br><br>".$appendMessage,
	 'subject'         => "Product Inquiry Notification",
	 'email_from'      => $settingInfo->from_email,
	 'email_from_name' => $settingInfo->from_name
	 ];
     Mail::to($settingInfo->email)->send(new SendGrid($dataadmin));	 
	 }
	 //end sending email	
	 $success['data'] = trans('webMessage.inquiry_message_sent');
     return response()->json($success, $this->successStatus);	
	}
	
	
	//delete address
	public function deleteAddress(Request $request){
		$user = User::where('api_token',$request->bearerToken())->first();		
		$id = $user->id;
		if(empty($request->id)){
		return response()->json(['data' =>trans('webMessage.idmissing')], $this->failedStatus);		
		}
		
		$address = CustomersAddress::where('id',$request->id)->where('customer_id',$id)->first();
		if(empty($address->id)){
		return response()->json(['data' =>trans('webMessage.invalid_infornation')], $this->failedStatus);	
		}
		$address->delete();
		return response()->json(['data' =>trans('webMessage.address_removed_success')], $this->successStatus);	
	}
	////////////////////////////////////////////////////////////////////////////////logout
	public function logout(Request $request)
    {
	
		if(empty($request->bearerToken())){
		return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);	
		}
		$user = User::where('api_token',$request->bearerToken())->first();
		if(empty($user)){
		return response()->json(['data' =>trans('webMessage.invalidapitoken') ], $this->failedStatus);	
		}
		if($user){
			$user->api_token = null;
			$user->save();
		}
		return response()->json(['data' => trans('webMessage.youhaveloggedoutsuccess')], $this->unauthorizedStatus);
    }

	//check token valid or not 
	public function isTokenValid($token){
	$flag =1;
	if(empty($token)){
	$flag = 0;	
	}	
	$user = User::where('api_token',$token)->first();
    if(empty($user)){
	$flag = 0;
	}
	return $flag;
	}
	
	
}
