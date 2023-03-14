<?php
namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Newsletter;
use App\Settings;
use App\User;
use App\OrdersDetails;
use App\Mail\SendGrid;
use Mail;
use Common;

//rules
use App\Rules\Name;
use App\Rules\Mobile;


class apiUserController extends Controller
{
    public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;
    
	
	
	//send link
	public function sendResetForgotPassCode(Request $request){
	
	$settingInfo      = Settings::where("keyname","setting")->first();
	//field validation
	  $validator = Validator::make($request->all(),[
            'email'   => 'required|email'
            ],[ 
			'email.required'   => trans('webMessage.email_required'),
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
	
	try{
	$clientInfo = User::where("email",$request->email)->first();	
	if(empty($clientInfo->id)){
	$success['data'] = trans('webMessage.email_not_register'); 
    return response()->json($success, $this->failedStatus); 
	}else{
	 $token = (string)Str::uuid();
	 $passcode = rand(1234,9876);
	 $clientInfo->password_token = $token;
	 $clientInfo->passcode       = $passcode;
	 $clientInfo->save();
	 
	 $appendMessage = "<b><a href='".url('password/reset/'.$token)."'>".trans('webMessage.passwordresetlink')."</b>";
	 $appendMessage.= "<br><br> <b>".trans('webMessage.passcode')." : ".$passcode."</b>";
	 $data = [
	 'dear'            => trans('webMessage.dearuser'),
	 'footer'          => trans('webMessage.email_footer'),
	 'message'         => trans('webMessage.you_have_reqtest_fp')."<br><br>".$appendMessage,
	 'subject'         => 'Forgot Password Reset Link',
	 'email_from'      => $settingInfo->from_email,
	 'email_from_name' => $settingInfo->from_name
	 ];
     Mail::to($request->email)->send(new SendGrid($data));	 
	 
	 $success['data'] = trans('webMessage.password_reset_link_sent'); 
     return response()->json($success, $this->successStatus);				 	
	 }
	
	  }catch (\Exception $e){
	  $success['data'] = $e->getMessage(); 
      return response()->json($success, $this->failedStatus); 
	  }
	}
	
	
	public function resetForgotPassword(Request $request){
	
	$settingInfo      = Settings::where("keyname","setting")->first();
	//field validation
	$validator = Validator::make($request->all(),[
            'email'           => 'required|email',
			'passcode'        => 'required',
			'new_password'    => 'required|min:3|max:150|string',
			'confirm_password'=> 'required|min:3|max:150|string|same:new_password',
            ],[ 
			'email.required'  => trans('webMessage.email_required'),
			'new_password.required'      => trans('webMessage.newpassword_required'),
			'confirm_password.required'  => trans('webMessage.confirmpassword_required'),
			'confirm_password.same'      => trans('webMessage.confirmpassword_mismatched'),
			'passcode.required'      => trans('webMessage.email_not_register_or_token'),
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
		
	try{
	
	$clientInfo = User::where("email",$request->email)->where("passcode",$request->passcode)->first();	
	if(empty($clientInfo->id)){
	$success['data'] = trans('webMessage.email_not_register_or_token'); 
    return response()->json($success, $this->failedStatus);  					
	}else{
	 $passcode = rand(1234,9876);
	 $token = (string)Str::uuid();
	 $clientInfo->passcode=$passcode;
	 $clientInfo->password_token=$token;
	 $clientInfo->password   = bcrypt($request->new_password);
	 $clientInfo->save();
	 $appendMessage  ="";
	 $appendMessage .= "<b>".trans('webMessage.username')." : </b>".$clientInfo->username;
	 $appendMessage .= "<br><b>".trans('webMessage.password')." : </b>".$request->new_password;
	 $data = [
	 'dear'            => trans('webMessage.dearuser'),
	 'footer'          => trans('webMessage.email_footer'),
	 'message'         => trans('webMessage.password_reset_done_success')."<br><br>".$appendMessage,
	 'subject'         => 'Password Successfully Reset',
	 'email_from'      => $settingInfo->from_email,
	 'email_from_name' => $settingInfo->from_name
	 ];
     Mail::to($request->email)->send(new SendGrid($data));	 
	 
	$success['data'] = trans('webMessage.password_reset_done'); 
    return response()->json($success, $this->successStatus); 				 	
	}
	
	 }catch (\Exception $e) {
	   $success['data'] = $e->getMessage(); 
       return response()->json($success, $this->failedStatus);  	
	  }
	  
	}
	
	
	//create new account
	public function createNewAccount(Request $request)
    {
	  
		$settingInfo = Settings::where("keyname","setting")->first();
		//field validation
	    $validator = Validator::make($request->all(),[
		    'name'         => ['required','string','min:4','max:190',new Name],
            'email'        => 'required|email|min:3|max:150|string|unique:gwc_customers,email',
			'mobile'       => 'required|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|unique:gwc_customers,mobile',
			'username'     => 'required|min:3|max:20|string|unique:gwc_customers,username',
			'password'     => 'required|min:3|max:150|string',
        ],[
		    'name.required'=>trans('webMessage.name_required'),
			'name.min'=>trans('webMessage.min_name_chars_required'),
			'name.max'=>trans('webMessage.max_name_chars_required'),
			'name.string'=>trans('webMessage.string_chars_required'),
			'email.required'=>trans('webMessage.email_required'),
			'email.min'=>trans('webMessage.min_name_chars_required'),
			'email.max'=>trans('webMessage.max_name_chars_required'),
			'email.string'=>trans('webMessage.string_chars_required'),
			'email.unique'=>trans('webMessage.email_unique_required'),
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
			'password.required'=>trans('webMessage.password_required'),
			'password.min'=>trans('webMessage.min_name_chars_required'),
			'password.max'=>trans('webMessage.max_name_chars_required'),
			'password.string'=>trans('webMessage.string_chars_required'),
		]);
		
		if($validator->fails()){ 
		   $errmsg='';$allError=[];
           foreach ($validator->errors()->messages() as $error){
           array_push($allError,$error[0]);
           }
		   $success['data'] = $allError[0]; 
           return response()->json($success, $this->failedStatus);            
        }
		
		
		$token = $this->getTokens();
		$customers = new User;
		$customers->name=$request->input('name');
		$customers->email=$request->input('email');
		$customers->mobile=$request->input('mobile');
		$customers->username=$request->input('username');
		$customers->password=bcrypt($request->input('password'));
		$customers->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'1';
		$customers->api_token = $token;
		$customers->register_from = $request->route('platform_prefix');
		$customers->device_type = $request->device_type;
		$customers->device_token = $request->device_token;
		$customers->save();
		if(!empty($request->email)){
	    $this->NewsLettersSubscription($request->email);
	    }
		//send email notification
		if(!empty($request->email)){
		 $appendMessage = "<b>".trans('webMessage.username')." : </b>".$request->input('username');
		 $appendMessage .= "<br><b>".trans('webMessage.password')." : </b>".$request->input('password');
		 $data = [
		 'dear'     => trans('webMessage.dear').' '.$request->input('name'),
		 'footer'   => trans('webMessage.email_footer'),
		 'message'  => trans('webMessage.your_account_created_success_txt')."<br><br>".$appendMessage,
		 'subject'  =>'Account is created successfully',
		 'email_from' =>$settingInfo->from_email,
		 'email_from_name' =>$settingInfo->from_name
		 ];
		 Mail::to($request->email)->send(new SendGrid($data));	 
		}
		
		//start register device
		if(!empty($request->input('device_token')) && !empty($request->input('device_type'))){
		Common::registerDevice($request->input('device_token'),$request->input('device_type'),$customers->id);
		}
		//end register device
        $customersDetails = $this->customerinfo($customers->id);
		
        return response()->json(['data'=>$customersDetails], $this->successStatus);  
	}
	//process login 
	public function loginAuthenticate(Request $request)
    {
	    $validator = Validator::make($request->all(), [
            'login_username' => 'required|min:4',
            'login_password' => 'required|min:6'
            ],[
			'login_username.required' => trans('webMessage.username_required'),
			'login_password.required' => trans('webMessage.password_required'),
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
		
		if(Auth::guard('webs')->attempt(['username' => $request->login_username, 'password' => $request->login_password])){ 
		    $token = $this->getTokens();
            $user = Auth::guard('webs')->user(); 
			
			//update token if its empty
			if(empty($user->api_token)){
			$user->api_token = $token;
			$user->save();
			}
			
			$success['data']  = $user;
			//start register device
		    if(!empty($request->input('device_token')) && !empty($request->input('device_type'))){
		    Common::registerDevice($request->input('device_token'),$request->input('device_type'),$user->id);
		    }
		    //end register device
            return response()->json($success, $this-> successStatus); 
        }else if(filter_var($request->login_username, FILTER_VALIDATE_EMAIL) && Auth::guard('webs')->attempt(['email' => $request->login_username, 'password' => $request->login_password])){ 
		    $token = $this->getTokens();
            $user = Auth::guard('webs')->user(); 
			//update token if its empty
			if(empty($user->api_token)){
			$user->api_token = $token;
			$user->save();
			}

			$success['data']  = $this->customerinfo($user->id);
			//start register device
		    if(!empty($request->input('device_token')) && !empty($request->input('device_type'))){
		    Common::registerDevice($request->input('device_token'),$request->input('device_type'),$user->id);
		    }
		    //end register device
            return response()->json($success, $this->successStatus); 
        }else{ 
		    $success['data'] = trans('webMessage.invalidcredentials');
            return response()->json($success, $this->failedStatus); 
        } 
		
	}	
	
   //token
    public function getTokens()
    {
        $token = Str::random(60);
        $token =  hash('sha256', $token);
        return $token;
    }   
	//newsletter start
	public function NewsLettersSubscription($email){
	if(!empty($email)){
	$newsletter = Newsletter::where("newsletter_email",$email)->first();
	 if(empty($newsletter->id)){
	$newsletter = new Newsletter;
	$newsletter->newsletter_email=$email;
	$newsletter->save();
	                           }
	                  }
	}
	//end news letter
}
