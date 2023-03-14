<?php

namespace App\Http\Controllers\posv1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Settings;
use App\AdminPos; //model
use App\Mail\SendGrid;
use Mail;
use Common;

//rules
use App\Rules\Name;
use App\Rules\Mobile;


class PosAdminController extends Controller
{
    public $successStatus       = 200;
    public $failedStatus        = 400;
    public $unauthorizedStatus  = 401;

    public function supervisor_password(Request $request){
        $settingInfo = Settings::where("keyname","setting")->first();
        if ( Hash::check($request->password ,$settingInfo->pos_supervisor_password  ) )
            return response(null ,200 );
        return response(null ,401 );
    }

    //login api
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status' => 401, 'error' => $validator->errors()->all()];
            return response($response, 401);
        }

        $user = AdminPos::where('username', $request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $userDetails = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'profileImg' =>  !empty($user->image) ? url('uploads/users/thumb/' .  $user->image) : url('uploads/no-image.png'),
                ];
                $response = ['status' => 200, 'message' => 'success', 'token' => $token, 'user' =>  $userDetails ,'site' => asset(''), 'system' => Settings::where("keyname", "setting")->first()];
                return response($response, 200);
            } else {
                $response = ['status' => 401, "message" => "Invalid Credentials, please check Username and Password"];
                return response($response, 401);
            }
        } else {
            $response = ['status' => 404, "message" => "User not found"];
            return response($response, 404);
        }
    }
    //login api
    public function setting()
    {
        $response = ['status' => 200, 'message' => 'success','site' => asset(''), 'system' => Settings::where("keyname", "setting")->first()];
        return response($response, 200);
    }

    //logout
    public function logout(Request $request)
    {

        $token = $request->user()->token();
        $token->revoke();

        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, $this->successStatus);
    }


    public function editProfile(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            $response = ['status' => 401, 'message' => 'Unauthorized Access'];
            return response($response, 401);
        }
        $name = $request->name;
        $mobile = $request->mobile;
        $email = $request->email;

        if ( empty($name) || empty($mobile) || empty($email)) {
            $response = ['status' => 401, "message" => "Fields cannot be Empty"];
            return response($response, 401);
        }

        $updateUser = AdminPos::where('id', $user->id)->first();
        $updateUser->name = $name;
        $updateUser->mobile = $mobile;
        $updateUser->email = $email;
        $updateUser->save();

        $updatedData = [
            'user_id' => $updateUser->id,
            'name' => $updateUser->name,
            'email' => $updateUser->email,
            'mobile' => $updateUser->mobile,
            'profileImg' =>  !empty($updateUser->image) ? url('uploads/users/thumb/' .  $updateUser->image) : url('uploads/no-image.png')
        ];
        $response = ['status' => 200, 'message' => 'success', 'data' => $updatedData];
        return response($response,  200);
    }

    // public function getProfile(Request $request)
    // {
    //     $user = $request->user();
    //     if (empty($user)) {
    //         $response = ['status' => 401, 'message' => 'Unauthorized Access'];
    //         return response($response, 401);
    //     } else {

    //         $userDetails = [
    //             'user_id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'mobile' => $user->mobile,
    //             'profileImg' =>  !empty($user->image) ? url('uploads/users/thumb/' .  $user->image) : url('uploads/no-image.png')
    //         ];

    //         $response = ['status' => 200, 'message' => 'success', 'data' =>  $userDetails];
    //         return response($response, 200);
    //     }
    // }
}
