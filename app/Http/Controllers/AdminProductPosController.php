<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Endroid\QrCode\QrCode;

use App\OrdersDetails;
use App\Orders;
use App\Product;
use App\ProductGallery;
use App\ProductAttribute;
use App\ProductCategory;
use App\Categories;
use App\Settings;
use App\Color;
use App\Size;
use App\Section;
use App\ProductReview;
use App\Manufacturer;
use App\Warranty;
use App\Brand;
use App\ProductInquiry;
use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\ProductOptionsCustomChosen;
use App\Tags;
use Image;
use File;
use Response;
use App\Services\ProductSlug;
use App\Services\SectionSlug;
use PDF;
use Auth;

use App\Mail\SendGrid;
use Mail;

use DB;



class AdminProductPosController extends Controller
{
	//pos main page
	public function showpos(Request $request){
	return view('gwc.pos.index');
	}    
	
	public function PosProductsVueJs(Request $request)
    {
	
	    
    	$responsedata = Product::where('is_active','!=',0);//->where('is_attribute','=',1);//
		if(!empty($request->q)){
		$q = $request->q;
		$responsedata = $responsedata->where(function($sq) use($q){
		                          $sq->where('item_code','LIKE','%'.$q.'%')
		                             ->orwhere('title_en','LIKE','%'.$q.'%')
									 ->orwhere('title_ar','LIKE','%'.$q.'%')
									 ->orwhere('sku_no','LIKE','%'.$q.'%');
		});
		}
		$responsedata = $responsedata->paginate(100);
		
    	return response()->json($responsedata);
    }
	
	public function PosCartVueJs(Request $request){
	$orderDetails = OrdersDetails::find($request->oid);
	$responsedata =[];
	if(!empty($request->oid)){
	    $oid = $request->oid;
	    $orderLists = Orders::where('gwc_orders.oid',$oid);
		$orderLists = $orderLists->select('gwc_orders.*','gwc_products.id as pid','gwc_products.image','gwc_products.title_en');
		$orderLists = $orderLists->join('gwc_products','gwc_products.id','=','gwc_orders.product_id');
		$orderLists = $orderLists->orderBy('gwc_orders.id','asc')->get();
	    $responsedata['ordersLists'] = $orderLists;
		$responsedata['ordersDetails'] = [
 		                                   'coupon_fee'=>$orderDetails->coupon_fee,
										   'delivery_charges'=>$orderDetails->delivery_charges,
										   'total_amount'=>self::totalCartAmount($request->oid,'total'),
										   'grand_total'=>self::totalCartAmount($request->oid,'grand'),
										 ];
	}
	
	return response()->json($responsedata);
	}
	
	public function PosCartTotalVueJs(Request $request){
	return 0;
	}
	
	public static function totalCartAmount($oid,$type){
	    $orderDetails = OrdersDetails::find($oid);
        $totalAmt = 0;	
        $orderLists = DB::table('gwc_orders')->where('gwc_orders.oid',$oid);
		$orderLists = $orderLists->select('gwc_orders.*','gwc_products.id');
		$orderLists = $orderLists->join('gwc_products','gwc_products.id','=','gwc_orders.product_id');
		$orderLists = $orderLists->get();

		if(!empty($orderLists) && count($orderLists)>0){
		foreach($orderLists as $orderList){
		$totalAmt+=($orderList->quantity*$orderList->unit_price);
		}
		}
		
		if($type=='total'){
		return $totalAmt;
		}
		
		if(!empty($orderDetails->delivery_charges)){
		$totalAmt=$totalAmt+$orderDetails->delivery_charges;
		}
		
		if(!empty($orderDetails->coupon_fee)){
		$totalAmt=$totalAmt-$orderDetails->coupon_fee;
		}
		
		return $totalAmt;
	}
	
	
	public function PosProductsVueJs_GetAttribute(Request $request){
	$responsedata =[];
	if(!empty($request->oid) && !empty($request->productid)){
	$id = $request->productid;
	//check option type exist or not
	$productoptions = ProductOptionsCustomChosen::where('product_id',$id)->orderBy('custom_option_id','ASC')->get();
	if(!empty($productoptions) && count($productoptions)>0){
	foreach($productoptions as $productoption){
	//size
	if($productoption->custom_option_id==1){
	$responsedata[] = [
	                  'attr_sizes'=>self::getSizeByCustomIdProductId($productoption->custom_option_id,$id)
					  ];
	}else if($productoption->custom_option_id==2){
	$responsedata[] = [
	                  'attr_colors'=>self::getColorByCustomIdProductId($productoption->custom_option_id,$id)
					  ];
	}else if($productoption->custom_option_id==3){
	$responsedata[] = [
	                  'attr_sizescolors'=>self::getColorSizeByCustomIdProductId($productoption->custom_option_id,$id)
					  ];
	}else{
	$responsedata[] = [
	                  'attr_other'=>self::getCustomOptions($productoption->custom_option_id,$id)
					  ];
	
	}
	 
	
	
	}	
	}
	}
	

	return response()->json($responsedata);
	}
   
   public static function getCustomOptions($custom_option_id,$product_id){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	$customOptionDetails = ProductOptionsCustom::where('id',$custom_option_id)->first();
	
	$customOptionChilds  = ProductOptions::where('gwc_products_options.product_id',$product_id)
	                                       ->where('gwc_products_options.quantity','>',0)
	                                       ->where('gwc_products_options.custom_option_id',$custom_option_id);
	$customOptionChilds  = $customOptionChilds->select(
	                                                  'gwc_products_option_custom_chosen.custom_option_id',
													  'gwc_products_option_custom_chosen.product_id',
													  'gwc_products_option_custom_chosen.is_required',
	                                                  'gwc_products_option_custom_child.*','gwc_products_option_custom_child.id as pocid',
													  'gwc_products_options.*'
													  );
	$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_child','gwc_products_option_custom_child.id','=','gwc_products_options.option_value_id');
	
	$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_chosen',['gwc_products_option_custom_chosen.product_id'=>'gwc_products_options.product_id','gwc_products_option_custom_chosen.custom_option_id'=>'gwc_products_options.custom_option_id']);
	
	

	$customOptionChilds  = $customOptionChilds->get();
	
	if($strLang=="en" && !empty($customOptionDetails->option_name_en)){
	$option_name  = $customOptionDetails->option_name_en;
	}else if($strLang=="ar" && !empty($customOptionDetails->option_name_ar)){
	$option_name  = $customOptionDetails->option_name_ar;
	}else{
	$option_name  = 'No Name';
	}
	
	if(!empty($customOptionDetails->option_type)){
	$option_type = $customOptionDetails->option_type;
	}else{
	$option_type = 'NONE';
	}
	///return ['CustomOptionName'=>$option_name,'CustomOptionType'=>$option_type,'childs'=>$customOptionChilds];
	return $customOptionChilds;
	}
	
	
   public static function getSizeByCustomIdProductId($custom_option_id,$product_id){

	$Attributes = ProductAttribute::where('gwc_products_attribute.product_id',$product_id)->where('gwc_products_attribute.custom_option_id',$custom_option_id);
	$Attributes = $Attributes->select(
	                           'gwc_sizes.*','gwc_sizes.id as sizeid',
							   'gwc_products_attribute.size_id',
							   'gwc_products_attribute.product_id',
							   'gwc_products_attribute.custom_option_id'
							   );								
	$Attributes = $Attributes->join("gwc_sizes","gwc_sizes.id","=","gwc_products_attribute.size_id");
	$Attributes = $Attributes->where('gwc_products_attribute.size_id','!=',0)
	                         ->where('gwc_products_attribute.quantity','>',0)
							 ->groupBy('gwc_products_attribute.size_id')
							 ->get();
	return $Attributes;
	}
	
	
   public static function getColorByCustomIdProductId($custom_option_id,$product_id){

	$Attributes = ProductAttribute::where('product_id',$product_id)->where('custom_option_id',$custom_option_id);
	$Attributes = $Attributes->select(
	                           'gwc_colors.*','gwc_colors.id as colorid',
							   'gwc_products_attribute.color_id',
							   'gwc_products_attribute.product_id',
							   'gwc_products_attribute.custom_option_id'
							   );								
	$Attributes = $Attributes->join("gwc_colors","gwc_colors.id","=","gwc_products_attribute.color_id");
	$Attributes = $Attributes->where('gwc_products_attribute.color_id','!=',0)
	                         ->where('gwc_products_attribute.quantity','>',0)
							 ->groupBy('gwc_products_attribute.color_id')
							 ->get();
	return $Attributes;
	}
	
	
	public static function getColorSizeByCustomIdProductId($custom_option_id,$product_id){

	$Attributes = ProductAttribute::where('gwc_products_attribute.product_id',$product_id)->where('gwc_products_attribute.custom_option_id',$custom_option_id);
	$Attributes = $Attributes->select(
	                           'gwc_colors.*','gwc_colors.id as colorid','gwc_colors.title_en as color_name',
							   'gwc_sizes.*','gwc_sizes.id as sizeid','gwc_sizes.title_en as size_name',
							   'gwc_products_attribute.color_id',
							   'gwc_products_attribute.size_id',
							   'gwc_products_attribute.product_id',
							   'gwc_products_attribute.custom_option_id',
							   'gwc_products_attribute.*'
							   );								
	$Attributes = $Attributes->join("gwc_colors","gwc_colors.id","=","gwc_products_attribute.color_id");
	$Attributes = $Attributes->join("gwc_sizes","gwc_sizes.id","=","gwc_products_attribute.size_id");
	$Attributes = $Attributes->where('gwc_products_attribute.color_id','!=',0)
	                         ->where('gwc_products_attribute.size_id','!=',0)
	                         ->where('gwc_products_attribute.quantity','>',0)
							 ->get();
	return $Attributes;
	}
	
		
}	