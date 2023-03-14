<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

use Response;
use App\Settings;
use App\User;

use App\Mail\SendGrid;
use Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
	//show send link form
	public function showLinkRequestForm()
	{

		return view('website.password.sendlink');
	}

	//send link
	public function sendResetLinkEmail(Request $request)
	{

		$settingInfo      = Settings::where("keyname", "setting")->first();
		//field validation
		$validator = Validator::make(
			$request->all(),
			[
				'email'   => 'required|email'
			],
			[
				'email.required'   => trans('webMessage.email_required'),
			]
		);
		if ($validator->fails()) {
			return redirect(app()->getLocale().'/password/reset')
				->withErrors($validator)
				->withInput();
		}

		try {




			$clientInfo = User::where("email", $request->email)->first();
			if (empty($clientInfo->id)) {
				return redirect(app()->getLocale().'/password/reset')
					->withErrors(['email' => trans('webMessage.email_not_register')])
					->withInput();
			} else {
				$token = (string)Str::uuid();
				$clientInfo->password_token = $token;
				$clientInfo->save();

				$appendMessage = "<b><a href='" . url(app()->getLocale().'/password/reset/' . $token) . "'>" . trans('webMessage.passwordresetlink') . "</b>";
				$data = [
					'dear' => trans('webMessage.dearuser'),
					'footer' => trans('webMessage.email_footer'),
					'message' => trans('webMessage.you_have_reqtest_fp') . "<br><br>" . $appendMessage,
					'subject' => 'Forgot Password Reset Link',
					'email_from' => $settingInfo->from_email,
					'email_from_name' => $settingInfo->from_name
				];
				Mail::to($request->email)->send(new SendGrid($data));

				return redirect(app()->getLocale().'/password/reset')
					->with('session_msg', trans('webMessage.password_reset_link_sent'));
			}
		} catch (\Exception $e) {
			return redirect()->back()->with('session_msg_error', $e->getMessage());
		}
	}

	//show reset form
	public function showResetForm()
	{
		return view('website.password.sendlink');
	}

	public function resets(Request $request)
	{
		$token=$request->token;
		$settingInfo      = Settings::where("keyname", "setting")->first();
		//field validation
		$validator = Validator::make(
			$request->all(),
			[
				'email'           => 'required|email',
				'new_password'    => 'required|min:3|max:150|string',
				'confirm_password' => 'required|min:3|max:150|string|same:new_password',
			],
			[
				'email.required'  => trans('webMessage.email_required'),
				'new_password.required'      => trans('webMessage.newpassword_required'),
				'confirm_password.required'  => trans('webMessage.confirmpassword_required'),
				'confirm_password.same'      => trans('webMessage.confirmpassword_mismatched'),
			]
		);
		if ($validator->fails()) {
			return redirect(app()->getLocale().'/password/reset/' . $token)
				->withErrors($validator)
				->withInput();
		}

		try {
			$clientInfo = User::where("email", $request->email)->where("password_token", $token)->first();
			if (empty($clientInfo->id)) {

				return redirect(app()->getLocale().'/password/reset/' . $token)
					->withErrors(['email' => trans('webMessage.email_not_register_or_token')])
					->withInput();
			} else {

				$token = (string)Str::uuid();
				$clientInfo->password_token = $token;
				$clientInfo->password   = bcrypt($request->new_password);
				$clientInfo->save();
				$appendMessage  = "";
				$appendMessage .= "<b>" . trans('webMessage.username') . " : </b>" . $clientInfo->username;
				$appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->new_password;
				$data = [
					'dear' => trans('webMessage.dearuser'),
					'footer' => trans('webMessage.email_footer'),
					'message' => trans('webMessage.password_reset_done_success') . "<br><br>" . $appendMessage,
					'subject' => 'Password Successfully Reset',
					'email_from' => $settingInfo->from_email,
					'email_from_name' => $settingInfo->from_name
				];
				Mail::to($request->email)->send(new SendGrid($data));

				return redirect(app()->getLocale().'/login')
					->with('session_msg', trans('webMessage.password_reset_done'));
			}
		} catch (\Exception $e) {
			return redirect()->back()->with('session_msg_error', $e->getMessage());
		}
	}
}
