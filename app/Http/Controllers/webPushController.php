<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Settings;
use App\WebPush;
use App\WebPushMessage;
use App\OrdersDetails;
//email
use App\Mail\SendGrid;
use Mail;
use DB;
class webPushController extends Controller
{


//display listings
public function index(Request $request){
        $settingInfo = Settings::where("keyname","setting")->first();
		
		//check search queries
		if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
        $webPushLists = WebPushMessage::where('title','LIKE','%'.$q.'%')
		                             ->orwhere('message','LIKE','%'.$q.'%')
									 ->orderBy('id','DESC')
                                     ->paginate($settingInfo->item_per_page_back);  
        $webPushLists->appends(['q' => $q]);
		
        }else{
        $webPushLists = WebPushMessage::orderBy('id','DESC')->paginate($settingInfo->item_per_page_back);
		}
        return view('gwc.webpush.index',compact('webPushLists'));	
} 


//list tokens
public function devicetokens(){
        $settingInfo = Settings::where("keyname","setting")->first();	
        $webPushLists = WebPush::paginate($settingInfo->item_per_page_back);	
        return view('gwc.webpush.webtokens',compact('webPushLists'));	
} 
//save WebPushs
	 public function saveWebPush(Request $request){
	 //field validation
	    $this->validate($request, [
			'title'     => 'required|min:3|max:190|string',
			'message'   => 'required|min:3|max:190|string'
        ]);
		
	  try{
	  
	   
	    $WebPushs = new WebPushMessage;	
		$WebPushs->title   = $request->input('title');
		$WebPushs->message = $request->input('message');
		
		if($request->input('message_for')=="web"){
		$WebPushs->large_image_url = $request->input('large_image_url');
		$WebPushs->logo_image_url  = $request->input('logo_image_url');
		$WebPushs->badge_image_url = $request->input('badge_image_url');
		$WebPushs->action_url  = $request->input('action_url');
		$WebPushs->alignment   = $request->input('alignment');
		}
		
		$WebPushs->message_for = $request->input('message_for');
		$WebPushs->save();
        //send message
		if(!empty($request->input('sendnow')) && $request->input('message_for')=="web"){
			$this->pushMessageProcessing($WebPushs->title,$WebPushs->message,$WebPushs->logo_image_url,$WebPushs->badge_image_url,$WebPushs->large_image_url,$WebPushs->action_url,$WebPushs->alignment);
		}
		if(!empty($request->input('sendnow')) && $request->input('message_for')<>"web"){
			$this->pushPorcessing($WebPushs->title,$WebPushs->message);
		}
        //save logs
		$key_name   = "WebPushs";
		$key_id     = $WebPushs->id;
		$message    = "A new WebPush is added.(".$WebPushs->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/webpush')->with('message-success','WebPush Message is added successfully');
		
		}catch (\Exception $e) {
		return redirect()->back()->with('message-error',$e->getMessage());	
		}
			
	}
	//edit WebPush
	public function saveEditWebPush(Request $request,$id){
	//field validation
	    $this->validate($request, [
			'title'     => 'required|min:3|max:190|string',
			'message'   => 'required|min:3|max:190|string'
        ]);
	
	   try{
	   
	   
	    $WebPushs = WebPushMessage::where('id',$request->id)->first();	
		$WebPushs->title   = $request->input('title');
		$WebPushs->message = $request->input('message');
		
		if($request->input('message_for')=="web"){
		$WebPushs->large_image_url = $request->input('large_image_url');
		$WebPushs->logo_image_url  = $request->input('logo_image_url');
		$WebPushs->badge_image_url = $request->input('badge_image_url');
		$WebPushs->action_url  = $request->input('action_url');
		$WebPushs->alignment   = $request->input('alignment');
		}
		
		$WebPushs->message_for = $request->input('message_for');
		$WebPushs->save();
        //send message
		if(!empty($request->input('sendnow')) && $request->input('message_for')=="web"){
			$this->pushMessageProcessing($WebPushs->title,$WebPushs->message,$WebPushs->logo_image_url,$WebPushs->badge_image_url,$WebPushs->large_image_url,$WebPushs->action_url,$WebPushs->alignment);
		}
		
		if(!empty($request->input('sendnow')) && $request->input('message_for')<>"web"){
			$this->pushPorcessing($WebPushs->title,$WebPushs->message);
		}
		
	
        //save logs
		$key_name   = "WebPushs";
		$key_id     = $WebPushs->id;
		$message    = "A new WebPush is added.(".$WebPushs->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        return redirect('/gwc/webpush')->with('message-success','Information is updated successfully');
		}catch (\Exception $e) {
		return redirect()->back()->with('message-error',$e->getMessage());	
		}	
	}
	
	/**
     * Delete services along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroyWebPushs($id){
	 
	 
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/webpush')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $WebPushs = WebPushMessage::find($id);
	 //check cat id exist or not
	 if(empty($WebPushs->id)){
	 return redirect('/gwc/webpush')->with('message-error','No record found'); 
	 } 
	 
	 //save logs
		$key_name   = "WebPushs";
		$key_id     = $WebPushs->id;
		$message    = "A WebPush is removed.(".$WebPushs->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	 //end deleting parent cat image
	 $WebPushs->delete();
	 return redirect()->back()->with('message-success','WebPush is deleted successfully');	
	 
	
		
	 }
	//delete token 
	public function deletedevicetokens($id){

	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/webpush/devicetokens')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $WebPushs = WebPush::find($id);
	 //check cat id exist or not
	 if(empty($WebPushs->id)){
	 return redirect('/gwc/devicetokens')->with('message-error','No record found'); 
	 } 
	 
	 //save logs
		$key_name   = "WebPushs";
		$key_id     = $WebPushs->id;
		$message    = "A WebPush token is removed.(".$WebPushs->device_token.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	 //end deleting parent cat image
	 $WebPushs->delete();
	 return redirect()->back()->with('message-success','Token is deleted successfully');	
	
	 }
	 //update status
	public function updateStatusWebPushAjax(Request $request)
    {
		$recDetails = WebPushMessage::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "WebPush";
		$key_id     = $recDetails->id;
		$message    = "WebPush status is changed to ".$active.".(".$recDetails->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
	
//save push web device token to db
public static function saveToken(Request $request){
		if(!empty($request->token)){
		$webPush = WebPush::where("device_token",$request->token)->first();
		if(empty($webPush->id)){
		$webpushnew = new WebPush;	
		$webpushnew->device_token = $request->token;
		$webpushnew->save();
		}
		}
}
//collect tokens and send message
public function pushMessageProcessing($title,$body,$icon,$badge,$image,$action,$alignment){
$webpushLists = WebPush::where('id','!=','0')->where('device_type','=','web')->get();
if(!empty($webpushLists) && count($webpushLists)>0){
foreach($webpushLists as $webpushList){
	$token = $webpushList->device_token;
	$this->sendWebPushFinal($token,$title,$body,$icon,$badge,$image,$action,$alignment);
}	
}
}

//send web push message 
public function sendWebPushFinal($token,$title,$body,$icon="",$badge="",$image="",$action="",$alignment='auto'){

    $settingInfo = Settings::where("keyname","setting")->first();
    if(!empty($settingInfo->web_server_key)){
	$payload=array(
	"notification"=>array(
								 "title"=>$title,
								 "body"=>$body,
								 "icon"=>$icon,
								 "badge"=>$badge,
								 "image"=>$image,
								 "vibrate"=>array(200, 100, 200, 100, 200, 100, 400),
								 "sound"=>"",
								 "dir"=>$alignment,
								 "click_action"=>$action,
								 "requireInteraction"=>true
							  ),
	"to"=>$token
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($payload,true));
	
	$headers = array();
	$headers[] = 'Authorization: key='.$settingInfo->web_server_key;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	$jsonresult = json_decode($result,true);
	//return $jsonresult['success'];
	curl_close($ch);
	}
}
//end

//processing for pushy
public function pushPorcessing($title,$message){
$data = array
(

	'subtitle'	 => $title,
	'tickerText'   => $title,
	'message'      => $message,
	'vibrate'	  => 1,
	'sound'		=> 1,
	'largeIcon'	=> 'large_icon',
	'smallIcon'	=> 'small_icon',
	'type'         => 'regular'
	

);
// Optional push notification options (such as iOS notification fields)

$options = array(
    'notification' => array(
        'badge' => 1,
        'sound' => "ping.aiff",
        'title' => $title,
		'body'  => $message
		
    )
);

$webpushLists = WebPush::where('id','!=','0')->where(function($sq){
$sq->where('device_type','=','android')->orwhere('device_type','=','ios');
})->get();

if(!empty($webpushLists) && count($webpushLists)>0){
$token=[];
foreach($webpushLists as $webpushList){
	$token[] = $webpushList->device_token;
	
}
if(!empty($token)){
$this->sendMobilePushFinal($data, $token, $options);	
}
}

}
///send push notification vis pushy
public  function sendMobilePushFinal($data, $to, $options) {
     $settingInfo = Settings::where("keyname","setting")->first();
    if(!empty($settingInfo->pushy_api_token)){
	
        // Insert your Secret API Key here
        $apiKey = $settingInfo->pushy_api_token;

        // Default post data to provided options or empty array
        $post = $options ?: array();

        // Set notification payload and recipients
        $post['to'] = $to;
        $post['data'] = $data;
		$post['content_available']=TRUE;

        // Set Content-Type header since we're sending JSON
        $headers = array(
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $apiKey);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);

        // Debug API response
		$jsonresul    = json_decode($result,true);
        if(!empty($jsonresul['success']) && $jsonresul['success']==1){
		return 1;
		}else{
		return 0;
		}
	return 0;
	 }
	 return 0;
	}	
	
	//cron job for order push
	public function cronForOrderPushNotification(){
	$settingInfo = Settings::where("keyname","setting")->first();
    if(!empty($settingInfo->pushy_api_token)){
	$orderDetails = OrdersDetails::where("gwc_orders_details.is_push_sent",0)->where("gwc_orders_details.customer_id","!=",0);
	$orderDetails = $orderDetails->select("gwc_orders_details.*","gwc_web_device_register.user_id","gwc_web_device_register.device_token","gwc_web_device_register.device_type");
	$orderDetails = $orderDetails->join('gwc_web_device_register','gwc_web_device_register.user_id','=','gwc_orders_details.customer_id');
	$orderDetails = $orderDetails->where(function($sq){
	$sq->where('gwc_web_device_register.device_type','android')->orwhere('gwc_web_device_register.device_type','ios');
	});
	$orderDetails = $orderDetails->first();
	if(!empty($orderDetails->device_token) && (!empty($orderDetails->is_paid) || $orderDetails->pay_mode=='COD')){
	
	$orderDetails->is_push_sent=1;
	$orderDetails->save();
	
	$data = array
		(
			'subtitle'	 => "Order Notification #".$orderDetails->order_id,
			'tickerText'   => "Order Notification #".$orderDetails->order_id,
			'message'      => "Your order is placed, Your order ID is #".$orderDetails->order_id,
			'vibrate'	  => 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon',
			'type'         => 'order'
		
		);
		// Optional push notification options (such as iOS notification fields)
		
		$options = array(
			'notification' => array(
				'badge' => 1,
				'sound' => "ping.aiff",
				'title' => "Order Notification #".$orderDetails->order_id,
				'body'  =>  "Your order is placed, Your order ID is #".$orderDetails->order_id
				
			)
		);
		
		

        // Insert your Secret API Key here
        $apiKey = $settingInfo->pushy_api_token;

        // Default post data to provided options or empty array
        $post = $options ?: array();

        // Set notification payload and recipients
        $post['to']   = self::getTokenFromCustomer($orderDetails->customer_id);
        $post['data'] = $data;
		$post['content_available']=TRUE;

        // Set Content-Type header since we're sending JSON
        $headers = array(
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $apiKey);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);
	
	}	
	}
	}
	
	///test pushy 
	
	public function testpushy(Request $request){
	$settingInfo = Settings::where("keyname","setting")->first();
    if(!empty($settingInfo->pushy_api_token)){
	
	    $data = array
		(
			'subtitle'	 => "Sub title notification -".rand(1,9),
			'tickerText'   => "Title notification -".rand(1,9),
			'message'      => "Body text message".rand(1,9),
			'vibrate'	  => 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		
		);
		// Optional push notification options (such as iOS notification fields)
		
		$options = array(
			'notification' => array(
				'badge' => 1,
				'sound' => "ping.aiff",
				'title' => "Title notification -".rand(1,9),
				'body'  => "Body text message".rand(1,9),
			)
		);
		
		

        // Insert your Secret API Key here
        $apiKey = $settingInfo->pushy_api_token;

        // Default post data to provided options or empty array
        $post = $options ?: array();

        // Set notification payload and recipients
        $post['to'] = $request->token;
        $post['data'] = $data;
		$post['content_available']=TRUE;

        // Set Content-Type header since we're sending JSON
        $headers = array(
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $apiKey);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);

        // Debug API response
		return $result;
        
	 }
	 return 0;
	}
	
	
	//get device token
	public static function getTokenFromCustomer($customerid){
	$token=[];
	$webpushLists = WebPush::where('user_id','=',$customerid)->where('device_type','=','android')->orwhere('device_type','=','ios')->get();
		if(!empty($webpushLists) && count($webpushLists)>0){
		
		foreach($webpushLists as $webpushList){
			$token[] = $webpushList->device_token;
			
		}
	  }
	  return $token;
	}	
}
