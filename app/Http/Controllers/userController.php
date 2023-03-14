<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Response;
use App\Newsletter;
use App\Settings;
use App\User;
use App\CustomersAddress;
use App\Services\ManufacturerSlug;
use App\Manufacturer;
//email
use App\Mail\SendGrid;
use Mail;

//rules
use App\Rules\Name;
use App\Rules\Mobile;

use Image;

class userController extends Controller
{

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/account';

	public function __construct()
	{
		$this->middleware('guest:webs')->except('logout');
	}

	//supplier registration form
	public function supplierRegistration()
	{
		$isAccountCreated = FALSE;
		return view('website.supplierRegister', compact('isAccountCreated'));
	}
	public function supplierRegistrationdone()
	{
		$isAccountCreated = TRUE;
		return view('website.supplierRegister', compact('isAccountCreated'));
	}


	//create new supplier
	public function createSupplier(Request $request)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		//field validation
		$this->validate(
			$request,
			[
				'name'         => ['required', 'string', 'min:4', 'max:190', new Name],
				'email'        => 'required|email|min:3|max:150|string|unique:gwc_users,email',
				'mobile'       => 'required|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|unique:gwc_users,mobile',
				'image'        => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'username'     => 'required|min:3|max:20|string|unique:gwc_users,username',
				'password'     => 'required|min:3|max:150|string',
			],
			[
				'name.required' => trans('webMessage.name_required'),
				'name.min' => trans('webMessage.min_name_chars_required'),
				'name.max' => trans('webMessage.max_name_chars_required'),
				'name.string' => trans('webMessage.string_chars_required'),
				'email.required' => trans('webMessage.email_required'),
				'email.min' => trans('webMessage.min_name_chars_required'),
				'email.max' => trans('webMessage.max_name_chars_required'),
				'email.string' => trans('webMessage.string_chars_required'),
				'email.unique' => trans('webMessage.email_unique_required'),
				'mobile.required' => trans('webMessage.mobile_required'),
				'mobile.min' => trans('webMessage.min_name_chars_required'),
				'mobile.max' => trans('webMessage.mobile_max_name_chars_required'),
				'mobile.string' => trans('webMessage.string_chars_required'),
				'mobile.unique' => trans('webMessage.mobile_unique_required'),
				'username.required' => trans('webMessage.username_required'),
				'username.min' => trans('webMessage.min_name_chars_required'),
				'username.max' => trans('webMessage.mobile_max_name_chars_required'),
				'username.string' => trans('webMessage.string_chars_required'),
				'username.unique' => trans('webMessage.username_unique_required'),
				'password.required' => trans('webMessage.password_required'),
				'password.min' => trans('webMessage.min_name_chars_required'),
				'password.max' => trans('webMessage.max_name_chars_required'),
				'password.string' => trans('webMessage.string_chars_required'),
			]
		);

		try {
			if (!empty($settingInfo->image_thumb_w) && !empty($settingInfo->image_thumb_h)) {
				$image_thumb_w = $settingInfo->image_thumb_w;
				$image_thumb_h = $settingInfo->image_thumb_h;
			} else {
				$image_thumb_w = 280;
				$image_thumb_h = 280;
			}

			$imageName = "";
			if ($request->hasfile('image')) {
				$imageName = 'manufacturer-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/users'), $imageName);

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/users/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/users/thumb/' . $imageName));
			}



			$slug = new ManufacturerSlug;
			$supplier = new Manufacturer();
			$supplier->slug = $slug->createSlug($request->input('name'));
			$supplier->title_en = $request->input('name');
			$supplier->title_ar = $request->input('name');
			$supplier->email = $request->input('email');
			$supplier->mobile = $request->input('mobile');
			$supplier->image = $imageName;
			$supplier->username = $request->input('username');
			$supplier->password = bcrypt($request->input('password'));
			$supplier->is_active = 0;
			$supplier->userType = 'vendor';
			$supplier->save();

			//send email notification to supplier
			$appendMessage = "<b>" . trans('webMessage.username') . " : </b>" . $request->input('username');
			$appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->input('password');
			$data = [
				'dear' => trans('webMessage.dear') . ' ' . $request->input('name'),
				'footer' => trans('webMessage.email_footer'),
				'message' => trans('webMessage.your_supplier_account_created_success_txt') . "<br><br>" . $appendMessage,
				'subject' => trans('webMessage.supplier_registration_email_subject'),
				'email_from' => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($request->email)->send(new SendGrid($data));

			//send email notification to admin
			$appendMessage = "<b>" . trans('webMessage.username') . " : </b>" . $request->input('username');
			$appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->input('password');
			$data = [
				'dear' => trans('webMessage.dear') . ' Admin',
				'footer' => trans('webMessage.email_footer'),
				'message' => trans('webMessage.supplier_account_created_success_txt') . "<br><br>" . $appendMessage,
				'subject' => trans('webMessage.supplier_registration_email_subject'),
				'email_from' => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($settingInfo->from_email)->send(new SendGrid($data));

			return redirect(app()->getLocale().'/supplier-registration-done')->with('session_msg', trans('webMessage.suppluieraccountcreatedsuccess'));
		} catch (\Exception $e) {
			return redirect()->back()->with('session_msg_error', $e->getMessage());
		}
	}

	////////user section
	//show login form
	public function loginForm()
	{
		return view('website.login');
	}

	//show register form
	public function registerform()
	{
		return view('website.register');
	}
	//create new account
	public function createAccount(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		//field validation
		$this->validate($request, [
			'name'         => ['required', 'string', 'min:4', 'max:190', new Name],
			'email'        => 'required|email|min:3|max:150|string|unique:gwc_customers,email',
			'mobile'       => 'required|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|unique:gwc_customers,mobile',
			'username'     => 'required|min:3|max:20|string|unique:gwc_customers,username',
			'password'     => 'required|min:3|max:150|string',
		], [
			'name.required' => trans('webMessage.name_required'),
			'name.min' => trans('webMessage.min_name_chars_required'),
			'name.max' => trans('webMessage.max_name_chars_required'),
			'name.string' => trans('webMessage.string_chars_required'),
			'email.required' => trans('webMessage.email_required'),
			'email.min' => trans('webMessage.min_name_chars_required'),
			'email.max' => trans('webMessage.max_name_chars_required'),
			'email.string' => trans('webMessage.string_chars_required'),
			'email.unique' => trans('webMessage.email_unique_required'),
			'mobile.required' => trans('webMessage.mobile_required'),
			'mobile.min' => trans('webMessage.min_name_chars_required'),
			'mobile.max' => trans('webMessage.mobile_max_name_chars_required'),
			'mobile.string' => trans('webMessage.string_chars_required'),
			'mobile.unique' => trans('webMessage.mobile_unique_required'),
			'username.required' => trans('webMessage.username_required'),
			'username.min' => trans('webMessage.min_name_chars_required'),
			'username.max' => trans('webMessage.mobile_max_name_chars_required'),
			'username.string' => trans('webMessage.string_chars_required'),
			'username.unique' => trans('webMessage.username_unique_required'),
			'password.required' => trans('webMessage.password_required'),
			'password.min' => trans('webMessage.min_name_chars_required'),
			'password.max' => trans('webMessage.max_name_chars_required'),
			'password.string' => trans('webMessage.string_chars_required'),
		]);



		try {

			$grecaptcharesponse = !empty($request->input('g-recaptcha-response')) ? $request->input('g-recaptcha-response') : '';
			
			if (empty($grecaptcharesponse)) {
				return redirect(app()->getLocale().'/register')
					->withErrors(['recaptchaError' => trans('webMessage.choose_captcha_validation')])
					->withInput();
			}


			$customers = new User;
			$customers->name = $request->input('name');
			$customers->email = $request->input('email');
			$customers->mobile = $request->input('mobile');
			$customers->username = $request->input('username');
			$customers->password = bcrypt($request->input('password'));
			$customers->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '1';

			if (!empty($request->is_newsletter_active)) {
				$this->NewsLettersSubscription($request->email);
				$customers->is_newsletter_active = !empty($request->input('is_newsletter_active')) ? $request->input('is_newsletter_active') : '0';
			}

			$customers->register_from = "web";
			$customers->register_ip   = $_SERVER['REMOTE_ADDR'];

			$customers->save();

			//send email notification
			$appendMessage = "<b>" . trans('webMessage.username') . " : </b>" . $request->input('username');
			$appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->input('password');
			$data = [
				'dear' => trans('webMessage.dear') . ' ' . $request->input('name'),
				'footer' => trans('webMessage.email_footer'),
				'message' => trans('webMessage.your_account_created_success_txt') . "<br><br>" . $appendMessage,
				'subject' => 'Account is created successfully',
				'email_from' => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($request->email)->send(new SendGrid($data));

			return redirect(app()->getLocale().'/login')->with('session_msg', trans('webMessage.accountcreatedsuccess'));
		} catch (\Exception $e) {
			return redirect()->back()->with('session_msg_error', $e->getMessage());
		}
	}
	//process login 
	public function loginAuthenticate(Request $request)
	{
		$this->validate(
			$request,
			[
				'login_username' => 'required|min:4',
				'login_password' => 'required|min:6'
			],
			[
				'login_username.required' => trans('webMessage.username_required'),
				'login_password.required' => trans('webMessage.password_required'),
			]
		);



		try {




			$remember = $request->remember_me ? true : false;

			if (filter_var($request->login_username, FILTER_VALIDATE_EMAIL) && Auth::guard('webs')->attempt(['email' => $request->login_username, 'password' => $request->login_password, 'is_active' => 1], $remember)) {
				//store values in cookies 
				if ($remember == true) {
					$minutes = 3600;
					Cookie::queue('xlogin_username', $request->login_username, $minutes);
					Cookie::queue('xlogin_password', $request->login_password, $minutes);
					Cookie::queue('xremember_me', 1, $minutes);
				} else {
					$minutes = 0;
					Cookie::queue('xlogin_username', '', $minutes);
					Cookie::queue('xlogin_password', '', $minutes);
					Cookie::queue('xremember_me', 0, $minutes);
				}
				//store country/area/state in cookie
				if (!empty(Auth::guard('webs')->user()->id)) {
					$userid = Auth::guard('webs')->user()->id;
					$userAddress = CustomersAddress::where('customer_id', $userid)->where('is_default', '1')->first();
					if (!empty($userAddress->country_id)) {
						Cookie::queue('country_id', $userAddress->country_id, 3600);
					}
					if (!empty($userAddress->state_id)) {
						Cookie::queue('state_id', $userAddress->state_id, 3600);
					}
					if (!empty($userAddress->area_id)) {
						Cookie::queue('area_id', $userAddress->area_id, 3600);
						Cookie::queue('area', $userAddress->area_id, 3600);
					}
				}
				//end
				return redirect()->intended(app()->getLocale() . '/account');
			} else if (Auth::guard('webs')->attempt(['username' => $request->login_username, 'password' => $request->login_password, 'is_active' => 1], $remember)) {
				//store values in cookies 
				if ($remember == true) {
					$minutes = 3600;
					Cookie::queue('xlogin_username', $request->login_username, $minutes);
					Cookie::queue('xlogin_password', $request->login_password, $minutes);
					Cookie::queue('xremember_me', 1, $minutes);
				} else {
					$minutes = 0;
					Cookie::queue('xlogin_username', '', $minutes);
					Cookie::queue('xlogin_password', '', $minutes);
					Cookie::queue('xremember_me', 0, $minutes);
				}

				//store country/area/state in cookie
				if (!empty(Auth::guard('webs')->user()->id)) {
					$userid = Auth::guard('webs')->user()->id;
					$userAddress = CustomersAddress::where('customer_id', $userid)->where('is_default', '1')->first();
					if (!empty($userAddress->country_id)) {
						Cookie::queue('country_id', $userAddress->country_id, 3600);
					}
					if (!empty($userAddress->state_id)) {
						Cookie::queue('state_id', $userAddress->state_id, 3600);
					}
					if (!empty($userAddress->area_id)) {
						Cookie::queue('area_id', $userAddress->area_id, 3600);
						Cookie::queue('area', $userAddress->area_id, 3600);
					}
				}
				//end


				return redirect()->intended(app()->getLocale() . '/account');
			}

			return back()->withInput()->withErrors(['login_username' => 'Invalid login credentials']);
		} catch (\Exception $e) {
			return redirect()->back()->with('session_msg_error', $e->getMessage());
		}
	}

	//newsletter start
	public function NewsLettersSubscription($email)
	{
		if (!empty($email)) {
			$newsletter = Newsletter::where("newsletter_email", $email)->first();
			if (empty($newsletter->id)) {
				$newsletter = new Newsletter;
				$newsletter->newsletter_email = $email;
				$newsletter->save();
			}
		}
	}
	//end news letter
}
