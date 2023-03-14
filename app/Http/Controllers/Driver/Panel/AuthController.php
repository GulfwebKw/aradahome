<?php

namespace App\Http\Controllers\Driver\Panel;

use App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest:driver')->except('logout');
    }

    public function loginView()
    {
        if ( Auth::guard('driver')->check() )
            return redirect()->route('driver.panel.dashboard');
        return view('driver.panel.auth.login');
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
            if (Auth::guard('driver')->attempt(['username' => $request->login_username, 'password' => $request->login_password,'is_active'=>1],$remember)) {
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
                return redirect()->route('driver.panel.dashboard');
            }
            return back()->withInput()->withErrors(['invalidlogin'=>'Invalid login credentials']);
        }catch (\Exception $e) {
            return redirect()->back()->with('invalidlogin',$e->getMessage());
        }
    }

    public function logout(){

        Auth::guard('driver')->logout();
        return redirect()->route('driver.panel.login_view')->with("info","You have successfully logged out from Drivers Panel.");
    }
	
}
