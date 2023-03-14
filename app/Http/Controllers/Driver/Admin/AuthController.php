<?php

namespace App\Http\Controllers\Driver\Admin;

use App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function loginView()
    {
        return view('driver.auth.login');
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'login_username' => 'required|min:4',
            'login_password' => 'required|min:6'
        ],[
            'login_username.required' => 'Please enter login username',
            'login_password.required' => 'Please enter login password']
        );
        try{
            $remember = $request->remember_me ? true : false;
            $remember = true ;
            if (Auth::guard('admin')->attempt(['username' => $request->login_username, 'password' => $request->login_password,'is_active'=>1,'userType'=>'admin'],$remember)) {
                //save logs
                $key_name   = "login";
                $key_id     = Auth::guard('admin')->user()->id;
                $message    = Auth::guard('admin')->user()->name."(".Auth::guard('admin')->user()->userType.") is logged in to Admin Panel of Driver.";
                $created_by = Auth::guard('admin')->user()->id;
                Common::saveLogs($key_name,$key_id,$message,$created_by);
                //end save logs
                //store values in cookies
                if($remember==true){
                    $minutes=3600;
                    Cookie::queue('login_username', $request->login_username, $minutes);
                    Cookie::queue('login_password', $request->login_password, $minutes);
                    Cookie::queue('remember_me', 1, $minutes);
                }else{
                    $minutes=0;
                    Cookie::queue('login_username', '', $minutes);
                    Cookie::queue('login_password', '', $minutes);
                    Cookie::queue('remember_me', 0, $minutes);
                }
                return redirect()->intended('/gwc/home/');
            }
            return back()->withInput()->withErrors(['invalidlogin'=>'Invalid login credentials']);
        }catch (\Exception $e) {
            return redirect()->back()->with('invalidlogin',$e->getMessage());
        }
    }

    public function logout(){
        //save logs
        $key_name   = "logout";
        $key_id     = Auth::guard('admin')->user()->id;
        $message    = Auth::guard('admin')->user()->name."(".Auth::guard('admin')->user()->userType.") is logged out from Admin Panel of drivers.";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name,$key_id,$message,$created_by);
        Auth::guard('admin')->logout();
        return redirect()->route('driver.admin.login_view')->with("info","You have successfully logged out from Admin Panel of drivers.");
    }
	
}
