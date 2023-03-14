<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Hash;
use Auth;
use App\Menus;
use App\Admin;
use App\Manufacturer;
use App\Services\ManufacturerSlug;
use DB;
use App\AdminLogs;
use App\Newsletter;
use App\Settings;
use Response;
///use session;
use Image;
use File;


class AdminUserController extends Controller
{
   
     
    public function index(Request $request)
    {
       $settingInfo = Settings::where("keyname","setting")->first();
        //check search queries
        if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
		if(auth()->guard('admin')->user()->can('logs-list-self-only')){
		$usersLists = Admin::where('userType','admin')->where('created_by',auth()->guard('admin')->user()->id)
		                     ->where('name','LIKE','%'.$q.'%')
                            ->orderBy('name', 'ASC')
                            ->paginate($settingInfo->item_per_page_back); 
		}else{
        $usersLists = Admin::where('userType','admin')->where('name','LIKE','%'.$q.'%')
                            ->orderBy('name', 'ASC')
                            ->paginate($settingInfo->item_per_page_back);  
		 }					
        $usersLists->appends(['q' => $q]);
		
        }else{
		if(auth()->guard('admin')->user()->can('logs-list-self-only')){
		$usersLists = Admin::where('userType','admin')->where('created_by',auth()->guard('admin')->user()->id)->orderBy('name', 'ASC')->paginate($settingInfo->item_per_page_back);
		}else{
        $usersLists = Admin::where('userType','admin')->orderBy('name', 'ASC')->paginate($settingInfo->item_per_page_back);
		}
        }
        return view('gwc.user.adminUsers',['usersLists' => $usersLists]);
    }
	//view logs
	public function logs(Request $request)
    {
       $settingInfo = Settings::where("keyname","setting")->first();
        //check search queries
        if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
        $logsLists = AdminLogs::where('message','LIKE','%'.$q.'%')
                            ->orderBy('created_at', 'DESC')
                            ->paginate($settingInfo->item_per_page_back);  
        $logsLists->appends(['q' => $q]);
		
        }else{
        $logsLists = AdminLogs::orderBy('created_at', 'DESC')->paginate($settingInfo->item_per_page_back);
        }
        return view('gwc.user.adminLogs',['logsLists' => $logsLists]);
    }
	
	
	public function subscribers(Request $request)
    {
       $settingInfo = Settings::where("keyname","setting")->first();
        //check search queries
        if(!empty($request->get('q'))){
        $q = $request->get('q');
        }else{
        $q = $request->q;
        }
        
       
        //menus records
        if(!empty($q)){
        $subscriberLists = Newsletter::where('newsletter_email','LIKE','%'.$q.'%')
                            ->orderBy('created_at', 'DESC')
                            ->paginate($settingInfo->item_per_page_back);  
        $subscriberLists->appends(['q' => $q]);
		
        }else{
        $subscriberLists = Newsletter::orderBy('created_at', 'DESC')->paginate($settingInfo->item_per_page_back);
        }
        return view('gwc.user.adminSubscriber',['subscriberLists' => $subscriberLists]);
    }
    //view form
    public function adminUserForm($id="",Request $request){
	    
        if(!empty($id)){
        $userDetails = Admin::find($id); 
		$roles = Role::pluck('name','name')->all();
		$userRole = $userDetails->roles->pluck('name','name')->all();
		return view('gwc.user.adminUsersForm',['userDetails'=>$userDetails,'roles'=>$roles,'userRole'=>$userRole]);
        }else{
        $userDetails = array();
		$roles = Role::pluck('name','name')->all();
		$userRole = array();
		return view('gwc.user.adminUsersForm',['userDetails'=>$userDetails,'roles'=>$roles,'userRole'=>$userRole]);
        }
		
		
		
    }

    public function AddRecord(Request $request){ 
	
	 //field validation
		$v = Validator::make($request->all(), [
		'name'    => 'required|string|min:3|max:255',
        'email'   => 'required|email|unique:gwc_users|max:255',
        'mobile'  => 'required|numeric|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10),
		'username'=> 'required|string|unique:gwc_users|min:3|max:255',
		'password'=> 'required|string|min:3|max:15',
		'roles'   => 'required'
         ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors())->withInput();
		}
		
		
	try{
	
       
		
        $Admin = new Admin;

        $Admin->name     = $request->name;
        $Admin->email    = $request->email;
        $Admin->mobile   = $request->mobile;
        $Admin->username = $request->username;
        $Admin->password = bcrypt($request->password);
        $Admin->created_at = date("Y-m-d H:i:s");
        $Admin->updated_at = date("Y-m-d H:i:s");
        $Admin->save();
		//assign roles 
		$Admin->assignRole($request->input('roles'));
		
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Account is created for ".$request->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        Session::flash('message-success','Record is added successfully.');
        return redirect("/gwc/users");
		
	    }catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }	
    }
	
	//save profile
	public function adminSaveProfile(Request $request){
	
	$v = Validator::make($request->all(), [
	    'id'      => 'required',
		'name'    => 'required|string|min:3|max:255',
        'email'   => 'required|email|unique:gwc_users,email,'.$request->id.'|max:255',
        'mobile'  => 'required|numeric|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10),
		'image'   => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
		'roles'   => 'required'
         ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors());
		}
		
		
	
	 try{
	
	    
		
        $Admin = Admin::where("id",$request->id)->first();
	    //dd($Admin);
		//upload logo
		if($request->hasfile('image')){
		//delete logo from folder
		if(!empty($Admin->image)){
		$web_image_path = "/uploads/users/".$Admin->image;  // Value is not URL but directory file path
		if(File::exists(public_path($web_image_path))) {
			File::delete(public_path($web_image_path));
		  }
		}
		$imageName = 'admin-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/users'), $imageName);
        $Admin->image=$imageName;
		}
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Profile is updated for ".$Admin['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        $Admin->name     = $request->name;
        $Admin->email    = $request->email;
        $Admin->mobile   = $request->mobile;
        $Admin->updated_at = date("Y-m-d H:i:s");
        $Admin->save();
		//assign roles 
		DB::table('model_has_roles')->where('model_id',$request->id)->delete();
        $Admin->assignRole($request->input('roles'));
		
        Session::flash('message-success','Record is updated successfully.');
        return redirect()->back();
		
		}catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	//update loggedin profile
	public function adminSaveEditProfile(Request $request){
	   $v = Validator::make($request->all(), [
	    'id'      => 'required',
		'name'    => 'required|string|min:3|max:255',
        'email'   => 'required|email|unique:gwc_users,email,'.$request->id.'|max:255',
        'mobile'  => 'required|numeric|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10),
		'image'   => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
         ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors());
		}
		
		
	try{
	
	    
		
        $Admin = Admin::where("id",$request->id)->first();
		//upload logo
		if($request->hasfile('image')){
		//delete logo from folder
		if(!empty($Admin->image)){
		$web_image_path = "/uploads/users/".$Admin->image;  // Value is not URL but directory file path
		if(File::exists(public_path($web_image_path))) {
			File::delete(public_path($web_image_path));
		  }
		}
		$imageName = 'admin-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/users'), $imageName);
        $Admin->image=$imageName;
		}
		
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Profile is updated for ".$Admin['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
        $Admin->name     = $request->name;
        $Admin->email    = $request->email;
        $Admin->mobile   = $request->mobile;
        $Admin->updated_at = date("Y-m-d H:i:s");
        $Admin->save();
		
        Session::flash('message-success','Record is updated successfully.');
        return redirect()->back();
		
		}catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	//change password
	public function adminChangePass(Request $request){
	
	$v = Validator::make($request->all(), [
	    'id'                => 'required',
		'current_password'  => 'required',
        'new_password'      => 'required',
        'confirm_password'  => 'required|same:new_password',
         ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors())->withInput();
		}
		
		
	try{
	
	    
		
		$Admin = Admin::where("id",$request->id)->first();
		
        if(Hash::check($request->current_password, $Admin->password)){
		
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Password is changed for ".$Admin['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        $Admin->password   = bcrypt($request->new_password);
        $Admin->updated_at = date("Y-m-d H:i:s");
        $Admin->save();
        Session::flash('message-success','Password is updated successfully.');
        return redirect()->back();
		}else{
		$error = array('current_password' => 'Please enter correct current password');
        return redirect()->back()->withErrors($error)->withInput(); 
		}
		
		}catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	
	//change password
	public function vendorChangePass(Request $request){
	
	$v = Validator::make($request->all(), [
	    'id'                => 'required',
		'current_password'  => 'required',
        'new_password'      => 'required',
        'confirm_password'  => 'required|same:new_password',
         ]);

		if ($v->fails())
		{
			return redirect()->back()->withErrors($v->errors())->withInput();
		}
		
		
	try{
	
	    
		
		$Admin = Admin::where("id",$request->id)->first();
		
        if(Hash::check($request->current_password, $Admin->password)){
		
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Password is changed for ".$Admin['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
        $Admin->password   = bcrypt($request->new_password);
        $Admin->updated_at = date("Y-m-d H:i:s");
        $Admin->save();
        Session::flash('message-success','Password is updated successfully.');
        return redirect()->back();
		}else{
		$error = array('current_password' => 'Please enter correct current password');
        return redirect()->back()->withErrors($error)->withInput(); 
		}
		
		}catch (\Exception $e) {
	     return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}

    public function deleteUser($id=0){
     if(!empty($id)){
		$Admin = Admin::find($id);
		//save logs
		$key_name   = "user";
		$key_id     = $Admin->id;
		$message    = "Account is deleted for ".$Admin['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		$Admin->delete();
        Session::flash('message-success','Record is deleted successfully.');
     }else{
        Session::flash('message-error','Failed to delete');
     }
     return redirect()->back();
    }
	
	public function deleteLogs($id=0){
     if(!empty($id)){
		$AdminLogs = AdminLogs::find($id);
		
		//save logs
		$key_name   = "logs";
		$key_id     = $AdminLogs->id;
		$message    = "Logs is deleted.(".$AdminLogs->message.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
			
		$AdminLogs->delete();	
        Session::flash('message-success','Record is deleted successfully.');
     }else{
        Session::flash('message-error','Failed to delete');
     }
     return redirect()->back();
    }
	
	
	public function deleteSubscriber($id=0){
     if(!empty($id)){
		$Newsletter = Newsletter::find($id);
		
		//save logs
		$key_name   = "subscribe";
		$key_id     = $Newsletter->id;
		$message    = "Email subscriber is removed.(".$Newsletter->newsletter_email.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
			
		$Newsletter->delete();	
        Session::flash('message-success','Record is deleted successfully.');
     }else{
        Session::flash('message-error','Failed to delete');
     }
     return redirect()->back();
    }


   
   //update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = Admin::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		//save logs
		$key_name   = "user";
		$key_id     = $recDetails->id;
		$message    = "Status is changed to ".$active." for ".$recDetails['name'];
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	}
	
	//edit profile
	public function editprofile(){
	if(Auth::guard('admin')->user()->userType=="admin"){
	return view('gwc.user.adminEditProfileForm');
	}
	if(Auth::guard('admin')->user()->userType=="vendor"){
	$editmanufacturer = Admin::where('id',Auth::guard('admin')->user()->id)->first(); 
	return view('gwc.vendor.editprofile',compact('editmanufacturer'));
	}
	}
	//chane pass
	public function changepassword(){
	
	if(Auth::guard('admin')->user()->userType=="admin"){
	return view('gwc.user.adminEditProfileForm');
	}
	if(Auth::guard('admin')->user()->userType=="vendor"){
	return view('gwc.vendor.changepassword');
	}
	}
	
	public static function getUserDetails($id){
	$userDetails = Admin::find($id); 
	return $userDetails;
	}
	
	
	public function vendorSaveEditProfile(Request $request)
    {
	
	    $id = Auth::guard('admin')->user()->id;
	 
	    $settingInfo = Settings::where("keyname","setting")->first();
	    $image_thumb_w = 450;
		$image_thumb_h = 450;
	
		$image_big_w = 990;
		$image_big_h = 900;
	
		//field validation
	    $this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_users,title_en,'.$id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_users,title_ar,'.$id,
			'mobile'       => 'nullable|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|string|unique:gwc_users,mobile,'.$id,
			'email'        => 'nullable|min:3|max:190|string|unique:gwc_users,email,'.$id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'header_image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		

	  try{
	 
		
	$manufacturer = Manufacturer::find($id);
	
	$imageName='';
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($manufacturer->image)){
	$web_image_path = "/uploads/users/".$manufacturer->image;
	$web_image_paththumb = "/uploads/users/thumb/".$manufacturer->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/users'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/users/'.$imageName));
	//resize image
	$imgbig->resize($image_big_w,$image_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/users/'.$imageName));
	// open file a image resource
    $img = Image::make(public_path('uploads/users/'.$imageName));
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/users/thumb/'.$imageName));
	
	}else{
	$imageName = $manufacturer->image;
	}
	//header
	$imageHeaderName='';
	//upload image
	if($request->hasfile('header_image')){
	//delete image from folder
	if(!empty($manufacturer->header_image)){
	$web_image_path = "/uploads/users/".$manufacturer->header_image;
	$web_image_paththumb = "/uploads/users/thumb/".$manufacturer->header_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageHeaderName = 'h-'.md5(time()).'.'.$request->header_image->getClientOriginalExtension();
	
	$request->header_image->move(public_path('uploads/users'), $imageHeaderName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/users/'.$imageHeaderName));
	//resize image
	$imgbig->resize($image_big_w,$image_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/users/'.$imageHeaderName));
	// open file a image resource
    $img = Image::make(public_path('uploads/users/'.$imageHeaderName));
	//resize image
	$img->resize($image_thumb_w,$image_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/users/thumb/'.$imageHeaderName));
	
	}else{
	$imageHeaderName = $manufacturer->image;
	}
	//slug
		$slug = new ManufacturerSlug;
		
		$manufacturer->slug          = $slug->createSlug($request->title_en,$id);
		$manufacturer->title_en      = $request->input('title_en');
		$manufacturer->title_ar      = $request->input('title_ar');
		$manufacturer->details_en    = $request->input('details_en');
		$manufacturer->details_ar    = $request->input('details_ar');
		
		$manufacturer->mobile        = !empty($request->input('mobile'))?$request->input('mobile'):'';
		$manufacturer->email         = !empty($request->input('email'))?$request->input('email'):'';
		
		if(empty($manufacturer->username) && !empty($request->input('mobile'))){
		$manufacturer->username      = !empty($request->input('mobile'))?$request->input('mobile'):'';
		}
		if(empty($manufacturer->password) && !empty($request->input('mobile'))){
		$manufacturer->password      = !empty($request->input('mobile'))?bcrypt($request->input('mobile')):'';
		}
		
		$manufacturer->is_active     = 1;
		$manufacturer->display_order = !empty($request->input('display_order'))?$request->input('display_order'):'0';
		$manufacturer->image         = $imageName;
		$manufacturer->header_image  = $imageHeaderName;
		$manufacturer->userType      = 'vendor';
		
		$manufacturer->save();
		
	    return redirect('/vendor/editprofile')->with('message-success','Information is updated successfully');
		
		}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
    //get parent menus in dropdonwn
    /*public function getMenuDropDown($id=0){
        $opt='';
        $menusLists = Menus::where('parent_id',0);
        foreach($menusLists as $menu){
        $opt.='<option value="">{{$menu->name}}</option>';
        }
        return $opt;
    }*/
	
	public function deleteImage($id){
	$manufacturer = Manufacturer::find($id);
	//delete image from folder
	if(!empty($manufacturer->image)){
	$web_image_path = "/uploads/users/".$manufacturer->image;
	$web_image_paththumb = "/uploads/users/thumb/".$manufacturer->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$manufacturer->image='';
	$manufacturer->save();
	
	   //save logs
		$key_name   = "news";
		$key_id     = $manufacturer->id;
		$message    = "Image is removed. (".$manufacturer->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	public function deleteHeaderImage($id){
		$manufacturer = Manufacturer::find($id);
		//delete image from folder
		if(!empty($manufacturer->header_image)){
		$web_image_path = "/uploads/users/".$manufacturer->header_image;
		$web_image_paththumb = "/uploads/users/thumb/".$manufacturer->header_image;
		if(File::exists(public_path($web_image_path))){
		   File::delete(public_path($web_image_path));
		   File::delete(public_path($web_image_paththumb));
		 }
		}
		
		$manufacturer->header_image='';
		$manufacturer->save();
	
	   //save logs
		$key_name   = "news";
		$key_id     = $manufacturer->id;
		$message    = "Image is removed. (".$manufacturer->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
	return redirect()->back()->with('message-success','Header Image is deleted successfully');	
	}
	
	
	public function exportSubscriber()
      {
    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=file.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    );

    $reviews = Newsletter::orderBy('newsletter_email','ASC')->get();
    $columns = array('newsletter_email');

    $callback = function() use ($reviews, $columns)
    {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach($reviews as $review) {
            fputcsv($file, array($review->newsletter_email));
        }
        fclose($file);
    };
    return Response::stream($callback, 200, $headers);
   }
   
   
}
