<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Manufacturer;
use App\Settings;
use App\Product;
use Image;
use File;
use Response;
use App\Services\ManufacturerSlug;
use PDF;
use Auth;
use Hash;
use DB;

//email
use App\Mail\SendGrid;
use Mail;

class AdminManufacturerController extends Controller
{

	public static function countmanufactureProduct($mfid)
	{
		$totalItems = Product::where('manufacturer_id', $mfid)->count();
		$totalItems_publish   = Product::where('manufacturer_id', $mfid)->where('is_active', 1)->count();
		$totalItems_unpublish = Product::where('manufacturer_id', $mfid)->where('is_active', 0)->count();
		$totalItems_preorder  = Product::where('manufacturer_id', $mfid)->where('is_active', 2)->count();
		return [
			"totalItems" => $totalItems,
			'totalItems_publish' => $totalItems_publish,
			'totalItems_unpublish' => $totalItems_unpublish,
			'totalItems_preorder' => $totalItems_preorder
		];
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request) //Request $request
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		$manufacturerLists = Manufacturer::where('userType', 'vendor')->orderBy('id', 'desc')->paginate($settingInfo->item_per_page_back);
		return view('gwc.manufacturer.index', ['manufacturerLists' => $manufacturerLists]);
	}


	/**
	Display the manufacturer listings
	 **/
	public function create()
	{

		$lastOrderInfo = Manufacturer::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}
		return view('gwc.manufacturer.create')->with(['lastOrder' => $lastOrder]);
	}



	/**
	Store New manufacturer Details
	 **/
	public function store(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		$image_thumb_w = 450;
		$image_thumb_h = 450;

		$image_big_w = 990;
		$image_big_h = 900;

		//field validation
		$this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_users,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_users,title_ar',
			'mobile'       => 'nullable|min:3|max:190|string|unique:gwc_users,mobile',
			'email'        => 'nullable|min:3|max:190|string|unique:gwc_users,email',
			'username'     => 'required|string|unique:gwc_users|min:3|max:255',
			'password'     => 'required|string|min:3|max:15',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'header_image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);


		try {


			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
				$imageName = 'b-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/users'), $imageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/users/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/users/' . $imageName));
				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/users/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/users/thumb/' . $imageName));
			}

			//header
			$imageHeaderName = "";
			if ($request->hasfile('header_image')) {
				$imageHeaderName = 'h-' . md5(time()) . '.' . $request->header_image->getClientOriginalExtension();
				$request->header_image->move(public_path('uploads/users'), $imageHeaderName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/users/' . $imageHeaderName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/users/' . $imageHeaderName));
				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/users/' . $imageHeaderName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/users/thumb/' . $imageHeaderName));
			}


			$manufacturer = new Manufacturer;
			//slug
			$slug         = new ManufacturerSlug;

			$manufacturer->slug          = $slug->createSlug($request->title_en);
			$manufacturer->title_en      = $request->input('title_en');
			$manufacturer->title_ar      = $request->input('title_ar');
			$manufacturer->details_en    = $request->input('details_en');
			$manufacturer->details_ar    = $request->input('details_ar');

			$manufacturer->mobile        = !empty($request->input('mobile')) ? $request->input('mobile') : '';
			$manufacturer->email         = !empty($request->input('email')) ? $request->input('email') : '';
			if (!empty($request->input('username'))) {
				$manufacturer->username      = !empty($request->input('username')) ? $request->input('username') : '';
			}
			if (!empty($request->input('password'))) {
				$manufacturer->password      = !empty($request->input('password')) ? bcrypt($request->input('password')) : '';
			}

			$manufacturer->is_active     = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$manufacturer->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$manufacturer->image         = $imageName;
			$manufacturer->header_image  = $imageHeaderName;
			$manufacturer->userType      = 'vendor';
			$manufacturer->save();

			//save logs
			$key_name   = "manufacturer";
			$key_id     = $manufacturer->id;
			$message    = "A new record for manufacturer is added. (" . $manufacturer->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/manufacturer')->with('message-success', 'A new record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$editmanufacturer = Manufacturer::find($id);
		return view('gwc.manufacturer.edit', compact('editmanufacturer'));
	}


	/**
	 * Show the details of the manufacturer.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$manufacturerDetails = Manufacturer::find($id);
		return view('gwc.manufacturer.view', compact('manufacturerDetails'));
	}



	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{


		$settingInfo = Settings::where("keyname", "setting")->first();
		$image_thumb_w = 450;
		$image_thumb_h = 450;

		$image_big_w = 990;
		$image_big_h = 900;

		//field validation
		$this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_users,title_en,' . $id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_users,title_ar,' . $id,
			'mobile'       => 'nullable|min:3|max:190|string|unique:gwc_users,mobile,' . $id,
			'email'        => 'nullable|min:3|max:190|string|unique:gwc_users,email,' . $id,
			'username'     => 'nullable|min:3|max:190|string|unique:gwc_users,username,' . $id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'header_image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);


		try {


			$manufacturer = Manufacturer::find($id);

			$imageName = '';
			//upload image
			if ($request->hasfile('image')) {
				//delete image from folder
				if (!empty($manufacturer->image)) {
					$web_image_path = "/uploads/users/" . $manufacturer->image;
					$web_image_paththumb = "/uploads/users/thumb/" . $manufacturer->image;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$imageName = 'b-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();

				$request->image->move(public_path('uploads/users'), $imageName);
				//create thumb
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/users/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/users/' . $imageName));
				// open file a image resource
				$img = Image::make(public_path('uploads/users/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/users/thumb/' . $imageName));
			} else {
				$imageName = $manufacturer->image;
			}
			//header
			$imageHeaderName = '';
			//upload image
			if ($request->hasfile('header_image')) {
				//delete image from folder
				if (!empty($manufacturer->header_image)) {
					$web_image_path = "/uploads/users/" . $manufacturer->header_image;
					$web_image_paththumb = "/uploads/users/thumb/" . $manufacturer->header_image;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$imageHeaderName = 'h-' . md5(time()) . '.' . $request->header_image->getClientOriginalExtension();

				$request->header_image->move(public_path('uploads/users'), $imageHeaderName);
				//create thumb
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/users/' . $imageHeaderName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/users/' . $imageHeaderName));
				// open file a image resource
				$img = Image::make(public_path('uploads/users/' . $imageHeaderName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/users/thumb/' . $imageHeaderName));
			} else {
				$imageHeaderName = $manufacturer->image;
			}
			//slug
			$slug = new ManufacturerSlug;

			$manufacturer->slug          = $slug->createSlug($request->title_en, $id);
			$manufacturer->title_en      = $request->input('title_en');
			$manufacturer->title_ar      = $request->input('title_ar');
			$manufacturer->details_en    = $request->input('details_en');
			$manufacturer->details_ar    = $request->input('details_ar');

			$manufacturer->mobile        = !empty($request->input('mobile')) ? $request->input('mobile') : '';
			$manufacturer->email         = !empty($request->input('email')) ? $request->input('email') : '';

			if (empty($manufacturer->username) && !empty($request->input('username'))) {
				$manufacturer->username      = !empty($request->input('username')) ? $request->input('username') : '';
			}
			if (empty($manufacturer->password) && !empty($request->input('password'))) {
				$manufacturer->password      = !empty($request->input('password')) ? bcrypt($request->input('password')) : '';
			}

			$manufacturer->is_active     = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$manufacturer->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$manufacturer->image         = $imageName;
			$manufacturer->header_image  = $imageHeaderName;
			$manufacturer->userType      = 'vendor';
			$manufacturer->save();


			//save logs
			$key_name   = "news";
			$key_id     = $manufacturer->id;
			$message    = "Record for manufacturer is edited. (" . $manufacturer->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs


			return redirect('/gwc/manufacturer')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}

	/**
	 * Delete the Image.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	public function deleteImage($id)
	{
		$manufacturer = Manufacturer::find($id);
		//delete image from folder
		if (!empty($manufacturer->image)) {
			$web_image_path = "/uploads/users/" . $manufacturer->image;
			$web_image_paththumb = "/uploads/users/thumb/" . $manufacturer->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$manufacturer->image = '';
		$manufacturer->save();

		//save logs
		$key_name   = "news";
		$key_id     = $manufacturer->id;
		$message    = "Image is removed. (" . $manufacturer->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		return redirect()->back()->with('message-success', 'Image is deleted successfully');
	}

	//update status
	public function updateStatusHomeAjax(Request $request)
	{
		$recDetails = Manufacturer::where('id', $request->id)->first();

		if ($recDetails->is_home == 1) {
			$active = 0;
		} else {
			$active = 1;
		}


		//save logs
		$key_name   = "Manufacturer";
		$key_id     = $recDetails->id;
		$message    = "Manufacturer home status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_home = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	public function deleteHeaderImage($id)
	{
		$manufacturer = Manufacturer::find($id);
		//delete image from folder
		if (!empty($manufacturer->header_image)) {
			$web_image_path = "/uploads/users/" . $manufacturer->header_image;
			$web_image_paththumb = "/uploads/users/thumb/" . $manufacturer->header_image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$manufacturer->header_image = '';
		$manufacturer->save();

		//save logs
		$key_name   = "news";
		$key_id     = $manufacturer->id;
		$message    = "Image is removed. (" . $manufacturer->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		return redirect()->back()->with('message-success', 'Header Image is deleted successfully');
	}
	/**
	 * Delete manufacturer along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/manufacturer')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$manufacturer = Manufacturer::find($id);
		//check cat id exist or not
		if (empty($manufacturer->id)) {
			return redirect('/gwc/manufacturer')->with('message-error', 'No record found');
		}

		//delete parent cat mage
		if (!empty($manufacturer->image)) {
			$web_image_path = "/uploads/users/" . $manufacturer->image;
			$web_image_paththumb = "/uploads/users/thumb/" . $manufacturer->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "news";
		$key_id     = $manufacturer->id;
		$message    = "A record is removed. (" . $manufacturer->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		//end deleting parent cat image
		$manufacturer->delete();
		return redirect()->back()->with('message-success', 'manufacturer is deleted successfully');
	}



	//download pdf

	public function downloadPDF()
	{
		$manufacturer = Manufacturer::get();
		$pdf = PDF::loadView('gwc.manufacturer.pdf', compact('manufacturer'));
		return $pdf->download('manufacturer.pdf');
	}

	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Manufacturer::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "news";
		$key_id     = $recDetails->id;
		$message    = "manufacturer status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();

		if ($active == 1) {
			if (!empty($recDetails->email)) {
				//send email notification to supplier
				$settingInfo = Settings::where("keyname", "setting")->first();
				$data = [
					'dear' => trans('webMessage.dear') . ' ' . $request->input('name'),
					'footer' => trans('webMessage.email_footer'),
					'message' => trans('webMessage.your_supplier_account_approved_success_txt'),
					'subject' => trans('webMessage.supplier_registration_email_subject'),
					'email_from' => $settingInfo->from_email,
					'email_from_name' => $settingInfo->from_name
				];
				Mail::to($recDetails->email)->send(new SendGrid($data));
			}
		}

		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}


	///check cost price & profit
	public static function getStatistics($supplierid)
	{
		$vendorProductIds = Product::where('manufacturer_id', $supplierid)->pluck('id');
		$odIds = \App\Orders::whereIn('product_id', $vendorProductIds)->pluck('order_id');
		$orderLists =   \App\OrdersDetails::where('gwc_orders_details.order_status', 'completed')
			->whereIn('gwc_orders_details.order_id', $odIds)
			->join('gwc_orders', 'gwc_orders.order_id', '=', 'gwc_orders_details.order_id')
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id')
			->selectRaw('gwc_products.cost_price * gwc_orders.quantity as cp, gwc_orders.unit_price  * gwc_orders.quantity as rp')
			->get();

		return [
			'costPrice' => $orderLists->sum('cp'),
			'retailPrice' => $orderLists->sum('rp'),
			'profitPrice' => ($orderLists->sum('rp') - $orderLists->sum('cp'))
		];
	}
}
