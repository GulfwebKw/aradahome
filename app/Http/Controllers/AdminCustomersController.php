<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;

use App\ProductOptionsCustomChosen;
use App\Customers;
use App\CustomersAddress;
use App\Settings;
use App\Country;
use App\OrdersDetails;
use App\Orders;
use App\Product;
use App\ProductAttribute;
use App\ProductOptions;
use App\ProductOptionsChild;
use App\OrdersTrack;
use App\OrdersOption;
use App\ProductReview;
use App\CustomersWish;
use App\Transaction;
use App\WebPush;
use Image;
use File;
use Response;
use PDF;
use Hash;
use Auth;
use DB;
use Common;

use App\Mail\SendGrid;
use Mail;

class AdminCustomersController extends Controller
{

	public function listmanufactureorders(Request $request)
	{
		if (empty($request->mid)) {
			abort(404);
		}

		$settingInfo = Settings::where("keyname", "setting")->first();

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}

		$orderLists = new Orders;
		$orderLists = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id', 'gwc_orders_details.*');
		$orderLists = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists = $orderLists->join('gwc_orders_details', 'gwc_orders_details.order_id', '=', 'gwc_orders.order_id');
		$orderLists = $orderLists->where('gwc_products.manufacturer_id', $request->mid);

		if (!empty($q)) {
			$orderLists = $orderLists->where(function ($sq) use ($q) {
				$sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.order_id', 'LIKE', '%' . $q . '%');
			});
		}
		//filter by date range
		if (!empty(Session::get('order_filter_dates'))) {
			$explodeDates = explode("-", Session::get('order_filter_dates'));
			if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
				$date1 = date("Y-m-d", strtotime($explodeDates[0]));
				$date2 = date("Y-m-d", strtotime($explodeDates[1]));
				$orderLists = $orderLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
			}
		}
		if (!empty(Session::get('order_filter_status')) && Session::get('order_filter_status') <> "all") {
			$orderLists = $orderLists->where('gwc_orders_details.order_status', '=', Session::get('order_filter_status'));
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'paid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '=', 1);
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'notpaid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '!=', 1);
		}
		if (!empty(Session::get('order_customers'))) {
			$orderLists = $orderLists->where('gwc_orders_details.customer_id', '=', Session::get('order_customers'));
		}


		$orderLists = $orderLists->orderBy('gwc_orders.id', 'DESC')->groupBy('gwc_orders.order_id')->paginate($settingInfo->item_per_page_back);

		//collect customers listing for dropdown
		$customersLists = DB::table('gwc_orders_details')
			->select('gwc_orders_details.customer_id', 'gwc_customers.id', 'gwc_orders_details.name')
			->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_orders_details.customer_id')
			->GroupBy('gwc_orders_details.customer_id')
			->get();



		return view('gwc.manufacturer.indexorders', compact('orderLists', 'settingInfo'));
	}


	public static function countmanufactureAmount($orderid, $mid)
	{
		$totalAmt = 0;
		$orderLists = DB::table('gwc_orders')->where('gwc_orders.order_id', $orderid);
		$orderLists = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
		$orderLists = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists = $orderLists->where('gwc_products.manufacturer_id', $mid);
		$orderLists = $orderLists->get();

		if (!empty($orderLists) && count($orderLists) > 0) {
			foreach ($orderLists as $orderList) {
				$totalAmt += ($orderList->quantity * $orderList->unit_price);
			}
		}

		return $totalAmt;
	}

	public static function countmanufactureOrders($mid)
	{
		$orderLists = DB::table('gwc_orders')->where('gwc_products.manufacturer_id', $mid);
		$orderLists = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
		$orderLists = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists = $orderLists->groupBy('gwc_orders.order_id')->get();
		return count($orderLists);
	}

	public function manufactureordersdetails(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		$orderLists = DB::table('gwc_orders')
			->where('gwc_products.manufacturer_id', $request->mid)
			->where('gwc_orders.oid', $request->oid);
		$orderLists = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
		$orderLists = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists = $orderLists->get();

		$orderDetails = OrdersDetails::where('id', $request->oid)->first();

		return view('gwc.manufacturer.view', compact('orderDetails', 'settingInfo', 'orderLists'));
	}


	public static function loadmodalforordernotification(Request $request)
	{
		$orderid = $request->orderid;
		$type    = $request->type;
		if (!empty($orderid) && $type == "send_email") {
			$orderDetails = OrdersDetails::where('order_id', $orderid)->first();
			$txtMessage = '<div class="row"><div class="col-lg-12"><strong>' . trans('adminMessage.orderid') . ':</strong>' . $orderid . '</div></div>';
		} else if (!empty($orderid) && $type == "send_sms") {
		} else {
			return ["statuc" => 400, "message" => trans('adminMessage.invalidrequest')];
		}
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request) //Request $request
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}


		//menus records
		if (!empty($q)) {
			$customersLists = Customers::where('name', 'LIKE', '%' . $q . '%')
				->orwhere('email', 'LIKE', '%' . $q . '%')
				->orwhere('mobile', 'LIKE', '%' . $q . '%')
				->orwhere('username', 'LIKE', '%' . $q . '%')
				->orderBy('created_at', 'DESC')
				->paginate($settingInfo->item_per_page_back);
			$customersLists->appends(['q' => $q]);
		} else {
			$customersLists = Customers::orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);
		}
		return view('gwc.customers.index', ['customersLists' => $customersLists]);
	}


	/**
	 * Display the Services listings
	 **/
	public function create()
	{
		return view('gwc.customers.create');
	}



	/**
	 * Store New Services Details
	 **/
	public function store(Request $request)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		if (!empty($settingInfo->image_thumb_w) && !empty($settingInfo->image_thumb_h)) {
			$image_thumb_w = $settingInfo->image_thumb_w;
			$image_thumb_h = $settingInfo->image_thumb_h;
		} else {
			$image_thumb_w = 200;
			$image_thumb_h = 200;
		}

		if (!empty($settingInfo->image_big_w) && !empty($settingInfo->image_big_h)) {
			$image_big_w = $settingInfo->image_big_w;
			$image_big_h = $settingInfo->image_big_h;
		} else {
			$image_big_w = 800;
			$image_big_h = 800;
		}
		//field validation
		if (!@$request->send_sms_new_user) {
			$this->validate($request, [
				'name'         => 'required|min:3|max:150|string',
				'email'        => 'required|email|min:3|max:150|string|unique:gwc_customers,email',
				'mobile'       => 'required|digits_between:' . config('MOBILE_VALIDATION_MIN', 3) . ',' . config('MOBILE_VALIDATION_MAX', 10) . '|unique:gwc_customers,mobile',
				'username'     => 'required|min:3|max:20|string|unique:gwc_customers,username',
				'password'     => 'required|min:3|max:150|string',
				'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
			]);
		} else {
			$this->validate($request, [
				'name'         => 'required|min:3|max:150|string',
				'mobile'       => 'required|digits_between:' . config('MOBILE_VALIDATION_MIN', 3) . ',' . config('MOBILE_VALIDATION_MAX', 10) . '|unique:gwc_customers,mobile',
			]);
		}



		try {


			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
				$imageName = 'c-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/customers'), $imageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/customers/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
				// insert watermark at bottom-right corner with 10px offset
				//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
				//}
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/customers/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/customers/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/customers/thumb/' . $imageName));
			}

			$customers = new Customers;
			$customers->name = $request->input('name');
			$customers->email = @$request->input('email');
			$customers->mobile = $request->input('mobile');
			$customers->username = @$request->input('username');
			$customers->password = @$request->input('password') ? bcrypt($request->input('password')) : bcrypt($request->input('mobile'));
			$customers->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$customers->image = $imageName;
			$customers->save();

			//save logs
			$key_name   = "customers";
			$key_id     = $customers->id;
			$message    = "New customer record is added as (" . $request->input('name') . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			if (@$request->send_sms_new_user) {
				return redirect('/gwc/payment-links/create?customer_id=' . $customers->id)->with('message-success', 'Customer is added successfully');
			} else {
				return redirect('/gwc/customers')->with('message-success', 'Customer is added successfully');
			}
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
		$editcustomers = Customers::find($id);
		return view('gwc.customers.edit', compact('editcustomers'));
	}

	/**
	 * Show the form for change password the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function changepass($id)
	{
		$editcustomers = Customers::find($id);
		return view('gwc.customers.changepass', compact('editcustomers'));
	}



	/**
	 * Show the details of the services.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$customerDetails = Customers::find($id);
		$listCountries   = Country::where('parent_id', '0')->where('is_active', 1)->get();
		$listaddresss    = CustomersAddress::where('customer_id', $id)->get();
		return view('gwc.customers.view', compact('customerDetails', 'listCountries', 'listaddresss'));
	}



	public function editchangepass(Request $request, $id)
	{

		$v = Validator::make($request->all(), [
			'new_password'      => 'required',
			'confirm_password'  => 'required|same:new_password',
		]);

		if ($v->fails()) {
			return redirect()->back()->withErrors($v->errors())->withInput();
		}


		try {



			$customers = Customers::where("id", $id)->first();

			//save logs
			$key_name   = "customers";
			$key_id     = $customers->id;
			$message    = "Customer Password is changed for " . $customers['name'];
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			$customers->password   = bcrypt($request->new_password);
			$customers->updated_at = date("Y-m-d H:i:s");
			$customers->save();
			return redirect()->back()->with('message-success', 'Password is changed successfully.');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
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
		if (!empty($settingInfo->image_thumb_w) && !empty($settingInfo->image_thumb_h)) {
			$image_thumb_w = $settingInfo->image_thumb_w;
			$image_thumb_h = $settingInfo->image_thumb_h;
		} else {
			$image_thumb_w = 200;
			$image_thumb_h = 200;
		}

		if (!empty($settingInfo->image_big_w) && !empty($settingInfo->image_big_h)) {
			$image_big_w = $settingInfo->image_big_w;
			$image_big_h = $settingInfo->image_big_h;
		} else {
			$image_big_w = 800;
			$image_big_h = 800;
		}

		//field validation  
		$this->validate($request, [
			'name'         => 'required|min:3|max:150|string',
			'email'        => 'required|email|min:3|max:150|string|unique:gwc_customers,email,' . $id,
			'mobile'       => 'required|digits_between:' . config('MOBILE_VALIDATION_MIN', 3) . ',' . config('MOBILE_VALIDATION_MAX', 10) . '|unique:gwc_customers,email,' . $id,
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);



		try {


			$customers = Customers::find($id);
			$imageName = '';
			//upload image
			if ($request->hasfile('image')) {
				//delete image from folder
				if (!empty($customers->image)) {
					$web_image_path = "/uploads/customers/" . $customers->image;
					$web_image_paththumb = "/uploads/customers/thumb/" . $customers->image;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$imageName = 'c-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();

				$request->image->move(public_path('uploads/customers'), $imageName);
				//create thumb
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/customers/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h

				//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
				// insert watermark at bottom-right corner with 10px offset
				//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
				//}
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/customers/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/customers/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/customers/thumb/' . $imageName));
			} else {
				$imageName = $customers->image;
			}

			$customers->name = $request->input('name');
			$customers->email = $request->input('email');
			$customers->mobile = $request->input('mobile');
			$customers->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$customers->image = $imageName;
			$customers->save();

			//save logs
			$key_name   = "customers";
			$key_id     = $customers->id;
			$message    = "Customer details are updated for " . $request->input('name');
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs


			return redirect('/gwc/customers')->with('message-success', 'Information is updated successfully');
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
		$customers = Customers::find($id);
		//delete image from folder
		if (!empty($customers->image)) {
			$web_image_path = "/uploads/customers/" . $customers->image;
			$web_image_paththumb = "/uploads/customers/thumb/" . $customers->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "customers";
		$key_id     = $customers->id;
		$message    = "Customer image is removed for " . $customers->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$customers->image = '';
		$customers->save();
		return redirect()->back()->with('message-success', 'Image is deleted successfully');
	}

	/**
	 * Delete customers along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/customers')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$customers = Customers::find($id);
		//check cat id exist or not
		if (empty($customers->id)) {
			return redirect('/gwc/customers')->with('message-error', 'No record found');
		}

		//delete parent cat mage
		if (!empty($customers->image)) {
			$web_image_path = "/uploads/customers/" . $customers->image;
			$web_image_paththumb = "/uploads/customers/thumb/" . $customers->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}
		//save logs
		$key_name   = "customers";
		$key_id     = $customers->id;
		$message    = "Customer account is removed for " . $customers->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		//remove belongings
		$this->deleteBelongsAddress($customers->id);

		$customers->delete();
		return redirect()->back()->with('message-success', 'customers is deleted successfully');
	}

	//delete address
	public function deleteBelongsAddress($id)
	{
		$totalAddress = CustomersAddress::where('customer_id', $id)->get();
		if (count($totalAddress) > 1) {
			foreach ($totalAddress as $myaddress) {
				$PrevDetails  = CustomersAddress::where('id', $myaddress->id)->first();
				if (!empty($PrevDetails->id)) {
					$PrevDetails->delete();
				}
			}
		}
	}

	//download pdf

	public function downloadPDF()
	{
		$customers = Customers::get();
		$pdf = PDF::loadView('gwc.customers.pdf', compact('customers'));
		return $pdf->download('customers.pdf');
	}

	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Customers::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}
		//save logs
		$key_name   = "customers";
		$key_id     = $recDetails->id;
		$message    = "Customer status is changed to " . $active . " for " . $recDetails->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	public function updateSellerStatusAjax(Request $request)
	{
		$recDetails = Customers::where('id', $request->id)->first();
		if ($recDetails['is_seller'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}
		//save logs
		$key_name   = "customers";
		$key_id     = $recDetails->id;
		$message    = "Customer seller status is changed to " . $active . " for " . $recDetails->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$recDetails->is_seller = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	///add address
	public function addAddress(Request $request, $id)
	{
		$this->validate($request, [
			'title'        => 'required|min:3|max:150|string',
			'country'      => 'required|numeric|gt:0',
			'state'        => 'required|numeric|gt:0',
			'area'         => 'required|numeric|gt:0',
			'block'        => 'required'
		]);

		try {


			$customer = Customers::find($id);

			$address = new CustomersAddress;
			$address->customer_id = $id;
			$address->title = $request->input('title');
			$address->country_id = $request->input('country');
			$address->state_id = $request->input('state');
			$address->area_id = $request->input('area');
			$address->block = $request->input('block');
			$address->street = $request->input('street');
			$address->avenue = $request->input('avenue');
			$address->house = $request->input('house');
			$address->floor = $request->input('floor');
			$address->save();

			//save logs
			$key_name   = "customers";
			$key_id     = $address->id;
			$message    = "New Address (" . $address->title . ") added for " . $customer->name;
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs


			return redirect()->back()->with('message-success', 'Record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}
	//get address
	public static function getCustAddress($addressid)
	{
		$address = CustomersAddress::find($addressid);
		$addr = '';
		$country = Country::find($address->country_id);
		$state   = Country::find($address->state_id);
		$area = Country::find($address->area_id);

		if (!empty($country->name_en)) {
			$addr .= '<p><b>Country : </b>' . $country->name_en . '</p>';
		}
		if (!empty($state->name_en)) {
			$addr .= '<p><b>State : </b>' . $state->name_en . '</p>';
		}
		if (!empty($area->name_en)) {
			$addr .= '<p><b>Area : </b>' . $area->name_en . '</p>';
		}
		if (!empty($address->block)) {
			$addr .= '<p><b>Block : </b>' . $address->block . '</p>';
		}
		if (!empty($address->street)) {
			$addr .= '<p><b>Street : </b>' . $address->street . '</p>';
		}
		if (!empty($address->avenue)) {
			$addr .= '<p><b>Avenue : </b>' . $address->avenue . '</p>';
		}
		if (!empty($address->house)) {
			$addr .= '<p><b>House : </b>' . $address->house . '</p>';
		}
		if (!empty($address->floor)) {
			$addr .= '<p><b>Floor : </b>' . $address->floor . '</p>';
		}
		return $addr;
	}

	//choose default address
	public function chooseDefaultAddress($id)
	{
		$recDetails  = CustomersAddress::where('id', $id)->first();
		//reset previous status
		$totalAddress = CustomersAddress::where('customer_id', $recDetails->customer_id)->get();
		if (count($totalAddress) > 1) {
			foreach ($totalAddress as $myaddress) {
				$PrevDetails  = CustomersAddress::where('id', $myaddress->id)->first();
				if (!empty($PrevDetails)) {
					$PrevDetails->is_default = 0;
					$PrevDetails->save();
				}
			}
		}


		$custDetails = Customers::where('id', $recDetails->customer_id)->first();
		if ($recDetails['is_default'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}
		//save logs
		$key_name   = "customers";
		$key_id     = $recDetails->id;
		$message    = "Default Address is changed to " . $active . " for " . $custDetails->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$recDetails->is_default = $active;
		$recDetails->save();

		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}
	//remove address
	public function deleteAddress($cid, $id)
	{

		//check param ID
		if (empty($id)) {
			return redirect('/gwc/customers/' . $cid . '/view')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$customersAdd = CustomersAddress::find($id);
		//check cat id exist or not
		if (empty($customersAdd->id)) {
			return redirect('/gwc/customers' . $cid . '/view')->with('message-error', 'No record found');
		}

		//save logs
		$key_name   = "customers";
		$key_id     = $customersAdd->id;
		$message    = "Customer Address is removed for " . $customersAdd->title;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		//end deleting parent cat image
		$customersAdd->delete();
		return redirect()->back()->with('message-success', 'Address is deleted successfully');
	}

	////////////////////////////////////////customers orders ////////////////////////////
	public function listVendorOrders(Request $request)
	{
		$paymodelist = DB::table('gwc_orders_details')->groupBy('pay_mode')->get();

		$settingInfo = Settings::where("keyname", "setting")->first();

		$orderIDs = Orders::whereIn('product_id', Product::where('manufacturer_id', Auth::guard('admin')->user()->id)->pluck('id'))->pluck('order_id');

		$orderLists  = OrdersDetails::whereIn('order_id', $orderIDs)->with(['area', 'products', 'orders']);

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}

		if (!empty($q)) {
			$orderLists = $orderLists->where(function ($sq) use ($q) {
				$sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.order_id', 'LIKE', '%' . $q . '%');
			});
		}
		//filter by date range
		if (!empty(Session::get('order_filter_dates'))) {
			$explodeDates = explode("-", Session::get('order_filter_dates'));
			if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
				$date1 = date("Y-m-d", strtotime($explodeDates[0]));
				$date2 = date("Y-m-d", strtotime($explodeDates[1]));
				$orderLists = $orderLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
			}
		}
		if (!empty(Session::get('order_filter_status')) && Session::get('order_filter_status') <> "all") {
			$orderLists = $orderLists->where('gwc_orders_details.order_status', '=', Session::get('order_filter_status'));
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'paid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '=', 1);
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'notpaid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '!=', 1);
		}
		if (!empty(Session::get('order_customers'))) {
			$orderLists = $orderLists->where('gwc_orders_details.customer_id', '=', Session::get('order_customers'));
		}

		if (!empty(Session::get('pay_mode'))) {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', Session::get('pay_mode'));
		}

		if (!empty($request->pmode) && $request->pmode == "COD") {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', 'COD')->where('gwc_orders_details.is_paid', 1)->where('gwc_orders_details.order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "COD_KNET") {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', 1)->where('gwc_orders_details.order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "KNET") {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '!=', 'COD')->where('gwc_orders_details.is_paid', 1)->where('gwc_orders_details.order_status', 'completed');
		}

		$orderLists  = $orderLists->orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);
		// dd($orderLists);

		//collect customers listing for dropdown
		$customersLists = Customers::with("orderDetails")->get(['id', 'name']);

		return view('gwc.vendor.orders.index', compact('orderLists', 'settingInfo', 'customersLists', 'paymodelist'));
	}

	public function listVendorPayment(Request $request)
	{

		$paymodelist = DB::table('gwc_orders_details')->groupBy('pay_mode')->get();

		$settingInfo = Settings::where("keyname", "setting")->first();

		$orderIDs = Orders::whereIn('product_id', Product::where('manufacturer_id', Auth::guard('admin')->user()->id)->pluck('id'))->pluck('order_id');

		$orderLists  = OrdersDetails::whereIn('order_id', $orderIDs)->with(['area', 'products', 'orders']);

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}

		if (!empty($q)) {
			$orderLists = $orderLists->where(function ($sq) use ($q) {
				$sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.order_id', 'LIKE', '%' . $q . '%');
			});
		}
		//filter by date range
		if (!empty(Session::get('vpayment_filter_dates'))) {
			$explodeDates = explode("-", Session::get('vpayment_filter_dates'));
			if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
				$date1 = date("Y-m-d", strtotime($explodeDates[0]));
				$date2 = date("Y-m-d", strtotime($explodeDates[1]));
				$orderLists = $orderLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
			}
		}

		if (!empty(Session::get('vpay_status')) && Session::get('vpay_status') == 'paid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '=', 1);
		}
		if (!empty(Session::get('vpay_status')) && Session::get('vpay_status') == 'notpaid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '!=', 1);
		}

		if (!empty(Session::get('vpayment_customers'))) {
			$orderLists = $orderLists->where('gwc_orders_details.customer_id', '=', Session::get('vpayment_customers'));
		}

		if (!empty(Session::get('vpay_mode'))) {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', Session::get('vpay_mode'));
		}

		$orderLists  = $orderLists->orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);

		//collect customers listing for dropdown

		$customersLists = Customers::with("orderDetails")->get(['id', 'name']);

		return view('gwc.vendor.orders.payments', compact('orderLists', 'settingInfo', 'customersLists', 'paymodelist'));
	}

	public static function vendorTotalAmount($orderid)
	{
		$totalAmt = 0;
		$orderLists  = DB::table('gwc_orders')->where('gwc_orders.order_id', $orderid);
		$orderLists  = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
		$orderLists  = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists  = $orderLists->where('gwc_products.manufacturer_id', Auth::guard('admin')->user()->id);
		$orderLists  = $orderLists->groupBy('gwc_orders.id')->get();
		if (!empty($orderLists) && count($orderLists) > 0) {
			foreach ($orderLists as $listOrder) {
				$totalAmt += ($listOrder->quantity * $listOrder->unit_price);
			}
		}
		return $totalAmt;
	}


	public function ViewVendorOrder(Request $request, $oid)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$orderDetails = OrdersDetails::where('order_id', $oid)->first();
		//get order items
		$orderLists  = DB::table('gwc_orders')->where('gwc_orders.order_id', $oid);
		$orderLists  = $orderLists->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
		$orderLists  = $orderLists->join('gwc_products', 'gwc_products.id', '=', 'gwc_orders.product_id');
		$orderLists  = $orderLists->where('gwc_products.manufacturer_id', Auth::guard('admin')->user()->id);
		$orderLists  = $orderLists->groupBy('gwc_orders.id')->get();

		return view('gwc.vendor.orders.view', compact('orderDetails', 'settingInfo', 'orderLists'));
	}

	//get customer orders
	public function listCustomersOrders(Request $request)
	{

		$paymodelist = DB::table('gwc_orders_details')->groupBy('pay_mode')->get();

		$settingInfo = Settings::where("keyname", "setting")->first();

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}

		$orderLists = OrdersDetails::with('area')->where('order_status', '!=', '');
		//search keywords
		if (!empty($q)) {
			$orderLists = $orderLists->where(function ($sq) use ($q) {
				$sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%')
					->orwhere('gwc_orders_details.order_id', 'LIKE', '%' . $q . '%');
			});
		}
		//filter by date range
		if (!empty(Session::get('order_filter_dates'))) {
			$explodeDates = explode("-", Session::get('order_filter_dates'));
			if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
				$date1 = date("Y-m-d", strtotime($explodeDates[0]));
				$date2 = date("Y-m-d", strtotime($explodeDates[1]));
				$orderLists = $orderLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
			}
		}
		if (!empty(Session::get('order_filter_status')) && Session::get('order_filter_status') <> "all") {
			$orderLists = $orderLists->where('gwc_orders_details.order_status', '=', Session::get('order_filter_status'));
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'paid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '=', 1);
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'notpaid') {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', '!=', 1);
		}
		if (!empty(Session::get('order_customers')) and false) {
			$orderLists = $orderLists->where('gwc_orders_details.customer_id', '=', Session::get('order_customers'));
		}
		if (!empty(Session::get('order_countries'))) {
			$orderLists = $orderLists->where('gwc_orders_details.country_id', '=', Session::get('order_countries'));
		}

		if (!empty(Session::get('pay_mode'))) {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', Session::get('pay_mode'));
		}

		if (!empty($request->pmode) && $request->pmode == "COD") {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "COD_KNET") {
			$orderLists = $orderLists->where('gwc_orders_details.is_paid', 1)->where('order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "KNET") {
			$orderLists = $orderLists->where('gwc_orders_details.pay_mode', '!=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		}
		$orderLists2 = $orderLists;
		$orderLists = $orderLists->orderBy('gwc_orders_details.id', 'DESC')->paginate($settingInfo->item_per_page_back);

		if (!empty($request->pmode)) {
			$orderLists->appends(['pmode' => $request->pmode]);
		}

		//collect customers listing for dropdown
		//		$customersLists = DB::table('gwc_orders_details')
		//			->select('gwc_orders_details.customer_id', 'gwc_customers.id', 'gwc_orders_details.name')
		//			->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_orders_details.customer_id')
		//			->GroupBy('gwc_orders_details.customer_id')
		//			->get();
		$customersLists = [];


		$orderLists2->join('gwc_orders', 'gwc_orders.oid', 'gwc_orders_details.id');
		$orderLists2->groupby('gwc_orders_details.id');
		$orderLists2->select(\Illuminate\Support\Facades\DB::raw('SUM(gwc_orders_details.delivery_charges) / COUNT(`gwc_orders_details`.`id`) as delivery_charges , SUM(gwc_orders.unit_price * gwc_orders.quantity) as total_order, SUM(gwc_orders_details.total_amount) / COUNT(`gwc_orders_details`.`id`) as totalPrice'));
		$totals = DB::query()
			->fromSub($orderLists2->getQuery(), 'a')
			->select(\Illuminate\Support\Facades\DB::raw('SUM(delivery_charges) as delivery_charges , SUM(total_order) as total_order, SUM(totalPrice) as totalPrice'))
			->forPage(1, 1)
			->first();
		$totalOrders = $totalPrice = $totalDelivery = 0;
		if ($totals != null) {
			$totalDelivery = $totals->delivery_charges;
			$totalOrders = $totals->total_order;
			$totalPrice = $totals->totalPrice;
		}

		return view('gwc.orders.index', compact('orderLists', 'settingInfo', 'customersLists', 'paymodelist', 'totalDelivery', 'totalOrders', 'totalPrice'));
	}
	//remove order 
	public function deleteOrder($id)
	{
		$orderdetails = OrdersDetails::where("id", $id)->first();
		$orderLists   = Orders::where("oid", $id)->get();
		if (!empty($orderLists) && count($orderLists) > 0) {
			foreach ($orderLists as $orderList) {
				//option
				$OrderOptions = OrdersOption::where("oid", $orderList->id)->get();
				if (!empty($OrderOptions) && count($OrderOptions) > 0) {
					if (empty($orderdetails->is_removed) && empty($orderdetails->is_qty_rollbacked)) {
						foreach ($OrderOptions as $OrderOption) {
							$optionsDt = OrdersOption::where('id', $OrderOption->id)->first();
							webCartController::changeOptionQuantity($OrderOption->product_id, 'a', $OrderOption->option_child_ids, $orderList->quantity, $orderList->inventory); //add qty
							$optionsDt->delete();
						}
					}
				}
				$order = Orders::where("id", $orderList->id)->first();
				if (empty($orderdetails->is_removed) && empty($orderdetails->is_qty_rollbacked)) {
					webCartController::rollbackedQuantity($orderList->product_id, $orderList->quantity, $orderList->size_id, $orderList->color_id, $orderList->inventory);
				}
				$order->delete();
			}
		}
		//remove track
		$orderListsTracks   = OrdersTrack::where("oid", $id)->get();
		if (!empty($orderListsTracks) && count($orderListsTracks) > 0) {
			foreach ($orderListsTracks as $orderListsTrack) {
				$ordertrack = OrdersTrack::where("id", $orderListsTrack->id)->first();
				$ordertrack->delete();
			}
		}

		$orderdetails->delete();
		return redirect()->back()->with('message-success', 'Order is deleted successfully');
	}

	//ajax
	public static function storeValuesInCookies(Request $request)
	{
		//date range
		if (!empty($request->dates)) {
			Session::put('order_filter_dates', $request->dates);
		}
		if (!empty($request->vdates)) {
			Session::put('vpayment_filter_dates', $request->vdates);
		}

		if (!empty($request->vpay_status)) {
			Session::put('vpay_status', $request->vpay_status);
		}


		//date range payment
		if (!empty($request->payment_dates)) {
			Session::put('payment_filter_dates', $request->payment_dates);
		}
		//order status
		if (!empty($request->order_status)) {
			Session::put('order_filter_status', $request->order_status);
		}
		//order status
		if (!empty($request->payment_status)) {
			Session::put('payment_filter_status', $request->payment_status);
		}
		//payment status for Reports -> Delivery
		if ($request->has('payment_status_Reports_Delivery')) {
			Session::put('payment_filter_status_Reports_Delivery', $request->payment_status_Reports_Delivery);
		}
		//payment status for Reports -> Most sold out
		if ($request->has('payment_status_Reports_MSO')) {
			Session::put('payment_filter_status_Reports_MSO', $request->payment_status_Reports_MSO);
		}
		//payment status
		if (!empty($request->pay_status)) {
			Session::put('pay_filter_status', $request->pay_status);
		}


		return ["status" => 200, "message" => ""];
	}

	public static function orderResetFilter()
	{
		Session::forget('vpayment_filter_dates');
		Session::forget('vpayment_filter_status');


		Session::forget('payment_filter_status');
		Session::forget('payment_filter_dates');
		Session::forget('order_filter_dates');
		Session::forget('order_filter_status');
		Session::forget('pay_filter_status');
		return ["status" => 200, "message" => ""];
	}
	//view customer order details
	public function ViewCustomerOrder(Request $request, $oid)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$orderDetails = OrdersDetails::find($oid);
		//get order items
		$orderLists = Orders::where('oid', $oid)->orderBy('created_at', 'DESC')->get();

		return view('gwc.orders.view', compact('orderDetails', 'settingInfo', 'orderLists'));
	}





	//delete product from order
	public function deleteProductFromOrder($orderid, $productid)
	{
		$order   = Orders::find($orderid);
		if ($order) {
			$tempQuantity = $order->quantity;
			$inventories = (array) json_decode($order->inventory, true);
			$inventories = array_reverse($inventories);
			foreach ($inventories as $inventory) {
				if ($tempQuantity <= 0)
					break;
				if ($tempQuantity > $inventory['q']) {
					$deductQuantity = $inventory['q'];
					$tempQuantity -= $inventory['q'];
					$inventory['q'] = 0;
				} else {
					$deductQuantity = $tempQuantity;
					$inventory['q'] = $inventory['q'] - $tempQuantity;
					$tempQuantity = 0;
				}
				$this->deductQuantity($order->product_id, $inventory['IID'], $deductQuantity, $order->size_id,  $order->color_id, 1);
			}
			$order->delete();
			$orderDetails = OrdersDetails::findOrFail($order->oid);
			$orderDetails->total_amount = $this->getOrderAmounts($order->oid);
			$orderDetails->save();
			return redirect('/gwc/orders/' . $order->oid . '/view')->with('message-success', 'Product is deleted successfully');
		}
		return redirect()->back()->with('message-error', 'Operation failed!');
	}


	//deduct quantity
	public static function deductQuantity($product_id, $inventory_id, $quantity, $size_id = null, $color_id = null, $mod = -1)
	{
		$inventoryUse = [];
		$productDetails   = Product::where('id', $product_id)->first();
		if (empty($productDetails['is_attribute'])) {
			if ($mod == 1) {
				$productQuantity = $productDetails->getQuantity($inventory_id, null, null, false, false, true);
				if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
					$productQuantity->quantity += $quantity;
					$productQuantity->save();
				}
			} else {
				$productQuantities = $productDetails->getQuantity(-1, null, null, true);
				$tempQuantity = $quantity;
				foreach ($productQuantities as $productQuantity) {
					if ($productQuantity->is_qty_deduct == 1) {
						if ($tempQuantity > $productQuantity->quantity) {
							$tempQuantity = $tempQuantity - $productQuantity->quantity;
							$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
							$productQuantity->quantity = 0;
							$productQuantity->save();
						} else {
							$productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
							$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
							$productQuantity->save();
							break;
						}
					}
				}
			}
		} else {
			if (!is_null($size_id) && !is_null($color_id)) {
				if ($mod == 1) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
					$productQuantities = $productDetails->getQuantity($inventory_id, $attributes->pluck('id')->toArray(), null, true, false, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
							$productQuantity->quantity += $quantity;
							$productQuantity->save();
							break;
						}
					}
				} else {
					$tempQuantity = $quantity;
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
					$productQuantities = $productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity->is_qty_deduct == 1) {
							if ($tempQuantity > $productQuantity->quantity) {
								$tempQuantity = $tempQuantity - $productQuantity->quantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
								$productQuantity->quantity = 0;
								$productQuantity->save();
							} else {
								$productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
								$productQuantity->save();
								break;
							}
						}
					}
				}
			} else if (!is_null($size_id) && is_null($color_id)) {
				if ($mod ==  1) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
					$productQuantities = $productDetails->getQuantity($inventory_id, $attributes->pluck('id')->toArray(), null, true, false, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
							$productQuantity->quantity += $quantity;
							$productQuantity->save();
							break;
						}
					}
				} else {
					$tempQuantity = $quantity;
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
					$productQuantities = $productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity->is_qty_deduct == 1) {
							if ($tempQuantity > $productQuantity->quantity) {
								$tempQuantity = $tempQuantity - $productQuantity->quantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
								$productQuantity->quantity = 0;
								$productQuantity->save();
							} else {
								$productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
								$productQuantity->save();
								break;
							}
						}
					}
				}
			} else if (is_null($size_id) && !is_null($color_id)) {
				if ($mod == 1) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
					$productQuantities = $productDetails->getQuantity($inventory_id, $attributes->pluck('id')->toArray(), null, true, false, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
							$productQuantity->quantity += $quantity;
							$productQuantity->save();
							break;
						}
					}
				} else {
					$tempQuantity = $quantity;
					$attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
					$productQuantities = $productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true);
					foreach ($productQuantities as $productQuantity) {
						if ($productQuantity->is_qty_deduct == 1) {
							if ($tempQuantity > $productQuantity->quantity) {
								$tempQuantity = $tempQuantity - $productQuantity->quantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
								$productQuantity->quantity = 0;
								$productQuantity->save();
							} else {
								$productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
								$inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
								$productQuantity->save();
								break;
							}
						}
					}
				}
			}
		}
		return $inventoryUse;
	}

	//is quantity exist
	public static function getProductQuantity($product_id, $size_id = 0, $color_id = 0, $order = null)
	{
		$quantity = 0;
		$productDetails   = Product::where('id', $product_id)->first();
		if (empty($productDetails['is_attribute'])) {
			$quantity = $productDetails['quantity'];
		} else {
			if (!empty($size_id) && !empty($color_id)) {
				$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
				foreach (($productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true)) as $InventoryHave)
					$quantity = $quantity + $InventoryHave->quantity;
			} else if (!empty($size_id) && empty($color_id)) {
				$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
				foreach (($productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true)) as $InventoryHave)
					$quantity = $quantity + $InventoryHave->quantity;
			} else if (empty($size_id) && !empty($color_id)) {
				$attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
				foreach (($productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true)) as $InventoryHave)
					$quantity = $quantity + $InventoryHave->quantity;
			}
			if ($order != null) {
				$orderOptions = OrdersOption::where("oid", $order->id)->get();
				if (!empty($orderOptions) && count($orderOptions) > 0) {
					foreach ($orderOptions as $orderOption) {
						$Option = ProductOptions::find($orderOption->option_child_ids);
						$Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
						foreach ($productDetails->getQuantity(-1, null, $Options->pluck('id')->toArray(), true) as $quantities) {
							$quantity = $quantity + $quantities->quantity;
						}
					}
				}
			}
		}
		return $quantity;
	}
	//update product quantity of order
	public function updateProductQtyOfOrder(Request $request)
	{
		$orderId   = $request->orderId;
		$quantity  = $request->quantity;

		$order   = Orders::findOrFail($orderId);
		if ($quantity < $order->quantity) {
			$tempQuantity = $quantity;
			$tempQuantity = $order->quantity - $tempQuantity;
			$inventories = (array) json_decode($order->inventory, true);
			$inventories = array_reverse($inventories);
			foreach ($inventories as $i => $inventory) {
				if ($tempQuantity <= 0)
					break;
				if ($tempQuantity > $inventory['q']) {
					$deductQuantity = $inventory['q'];
					$tempQuantity -= $inventory['q'];
					$inventory['q'] = 0;
				} else {
					$deductQuantity = $tempQuantity;
					$inventory['q'] = $inventory['q'] - $tempQuantity;
					$tempQuantity = 0;
				}
				if ($inventory['q'] > 0) {
					$inventories[$i]['q'] = $inventory['q'];
				} else {
					unset($inventories[$i]);
				}
				$this->deductQuantity($order->product_id, $inventory['IID'], $deductQuantity, $order->size_id,  $order->color_id, 1);
			}
			$orderInventory2 = [];
			$OrderOptions = OrdersOption::where("oid", $order->id)->get();
			if (!empty($OrderOptions) && count($OrderOptions) > 0) {
				$tempQuantity = $quantity;
				$tempQuantity = $order->quantity - $tempQuantity;
				$productDetails = Product::find($order->product_id);
				foreach ($OrderOptions as $OrderOption) {
					//deduct qty from option
					$Option = ProductOptions::find($OrderOption->option_child_ids);
					$Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
					$OptionsID = $Options->pluck('id')->toArray();
					$inventories2 = (array) json_decode($order->inventory, true);
					$inventories2 = array_reverse($inventories2);
					foreach ($inventories2 as $inventory) {
						$productQuantity = $productDetails->getQuantity($inventory['IID'], null, -1, true, false, true);
						foreach ($productQuantity as $quantityOne) {
							if (in_array($quantityOne->option_id, $OptionsID)) {
								if ($tempQuantity > $inventory['q']) {
									$quantityOne->quantity += $inventory['q'];
									$tempQuantity -= $inventory['q'];
									$quantityOne->save();
								} else {
									$quantityOne->quantity += $tempQuantity;
									$tempQuantity  = 0;
									$quantityOne->save();
									break;
								}
							}
							if ($tempQuantity == 0) {
								break 3;
							}
						}
					}
					//                    $orderInventory2 = webCartController::changeOptionQuantity($OrderOption->product_id , 'a', implode(',' , $Options->pluck('id')->toArray() ) , $tempQuantity, $order->inventory);
				}
			}
			foreach ($orderInventory2 as $inventoryUse) {
				$find = false;
				foreach ($inventories as $i => $inventory) {
					if ($inventory['IID'] == $inventoryUse['IID']) {
						$inventories[$i]['q'] -= $inventoryUse['q'];
						$find = true;
						break;
					}
				}
				if (!$find) {
					$inventories[] = $inventoryUse;
				}
			}
			$tempOrderInventories = $inventories;
			foreach ($tempOrderInventories as $i => $orderInventory)
				if (!($orderInventory['q'] > 0))
					unset($inventories[$i]);
			usort($inventories, function ($a, $b) {
				$inventory1 = Inventory::find($a['IID']);
				$inventory2 = Inventory::find($b['IID']);
				return $inventory1->priority - $inventory2->priority;
			});
			$order->update(['inventory' => json_encode($inventories), 'quantity' => $quantity]);
			$orderDetails = OrdersDetails::findOrFail($order->oid);
			$orderDetails->total_amount = $this->getOrderAmounts($order->oid);
			$orderDetails->save();
			return ["status" => 200, "message" => "Quantity updated successfully"];
		} else {
			$existQty = self::getProductQuantity($order->product_id, $order->size_id, $order->color_id, $order);
			$tempQuantity = $quantity - $order->quantity;
			$inventories = (array) json_decode($order->inventory, true);
			if ($existQty >= $tempQuantity) {
				$inventoriesUse = $this->deductQuantity($order->product_id, null, $tempQuantity, $order->size_id,  $order->color_id);
				foreach ($inventoriesUse as $inventoryUse) {
					$find = false;
					foreach ($inventories as $i => $inventory) {
						if ($inventory['IID'] == $inventoryUse['IID']) {
							$inventories[$i]['q'] += $inventoryUse['q'];
							$find = true;
							break;
						}
					}
					if (!$find) {
						$inventories[] = $inventoryUse;
					}
				}

				$orderInventory2 = [];
				$OrderOptions = OrdersOption::where("oid", $order->id)->get();
				if (!empty($OrderOptions) && count($OrderOptions) > 0) {
					$tempQuantity = $quantity;
					$tempQuantity = $tempQuantity - $order->quantity;
					foreach ($OrderOptions as $OrderOption) {
						//deduct qty from option
						$orderInventory2 = webCartController::changeOptionQuantity($OrderOption->product_id, 'd', $OrderOption->option_child_ids, $tempQuantity);
					}
				}
				foreach ($orderInventory2 as $inventoryUse) {
					$find = false;
					foreach ($inventories as $i => $inventory) {
						if ($inventory['IID'] == $inventoryUse['IID']) {
							$inventories[$i]['q'] += $inventoryUse['q'];
							$find = true;
							break;
						}
					}
					if (!$find) {
						$inventories[] = $inventoryUse;
					}
				}
				$tempOrderInventories = $inventories;
				foreach ($tempOrderInventories as $i => $orderInventory)
					if (!($orderInventory['q'] > 0))
						unset($inventories[$i]);

				usort($inventories, function ($a, $b) {
					$inventory1 = Inventory::find($a['IID']);
					$inventory2 = Inventory::find($b['IID']);
					return $inventory1->priority - $inventory2->priority;
				});
				$order->update(['inventory' => json_encode($inventories), 'quantity' => $quantity]);
				$orderDetails = OrdersDetails::findOrFail($order->oid);
				$orderDetails->total_amount = $this->getOrderAmounts($order->oid);
				$orderDetails->save();
				return ["status" => 200, "message" => "Quantity updated successfully"];
			} else {
				return ["status" => 400, "message" => "There is not enough products available"];
			}
		}
	}


	//search item code of the product for adding to invoice
	public function searchItemCode(Request $request)
	{
		$results = '';
		$itemCode = $request->itemCode;
		$orderId = $request->orderId;

		$product = Product::where('item_code', $itemCode)->where('is_active', '!=', 0)->first();
		if ($product) {
			$productId = $product->id;
			$existing = Orders::where('order_id', $orderId)->where('product_id', $productId)->first();
			if ($existing) {
				return ["status" => 400, "message" => "This product is added to order before"];
			} else {
				//calculate available quantity
				$qty = 0;
				//				if (empty($product->is_attribute)) {
				$qty = $product->quantity;
				//				} else {
				//					$qty = ProductAttribute::where('product_id', $product->id)->get()->sum('quantity');
				//					$optionQty = ProductOptions::where('product_id', $product->id)->get()->sum('quantity'); //option
				//					$qty = $qty + $optionQty;
				//					//save qty
				//					$product->quantity = $qty;
				//					$product->save();
				//				}

				//getting product details
				$results .= '<form>';
				$results .= '<div class="row">';
				$results .= '<div class="col-2"><img src="' . url("uploads/product/thumb/" . $product->image) . '" style="width: 100px;height: 100px" ></div>';
				$results .= '<div class="col-4">' . $product->title_en . '</div>';
				//price
				$results .= '<div class="col-2">';
				if (!empty($product->countdown_datetime) && strtotime($product->countdown_datetime) > strtotime(date('Y-m-d'))) {
					if ($product->old_price) $results .= '<span class="new-price" style="color:red" >';
					else $results .= '<span class="new-price" >';
					$results .= '<span id="display_price">' . round($product->countdown_price, 3) . '</span>';
					$results .= \App\Currency::default();
					$results .= '</span>';
					$results .= '&nbsp;';
					$results .= '<span class="old-price price_black" id="oldprices"><small><span id="">' . round($product->retail_price, 3) . '</span>' . \App\Currency::default() . '</small></span>';
				} else {
					if ($product->old_price) $results .= '<span class="new-price" style="color:red" >';
					else $results .= '<span class="new-price" >';
					$results .= '<span id="display_price">' . round($product->retail_price, 3) . '</span>';
					$results .= \App\Currency::default();
					$results .= '</span>';
					$results .= '&nbsp;';
					$results .= '<span class="old-price price_black" id="oldprices"><small><span id="display_oldprice">' . round($product->old_price, 3) . '</span>' . \App\Currency::default() . '</small></span>';
				}
				$results .= '<br><br>';
				$results .= '<span id="available-qty">' . $qty . '</span> Items Available';
				$results .= '<span id="product-to-add-id" style="visibility: hidden">' . $product->id . '</span>';
				$results .= '</div>';

				$results .= '<div class="col-3">';
				//getting the options
				$productOption = ProductOptionsCustomChosen::where('product_id', $product->id)->first();
				if (!empty($product->is_attribute) && $qty > 0) {
					$results .= '<div class="tt-swatches-container">';
					$results .= '<img id="loader-gif" src="' . url("assets/images/loader.svg") . '" style="position:absolute;margin-left:30%;display:none;margin-top:-40px;">';
					if (!empty($productOption)) {
						//size
						if ($productOption->custom_option_id == 1) {
							$results .= '<input type="hidden" name="option_sc" id="option_sc_' . $productOption->id . '" value="' . $productOption->custom_option_id . '">';
							$SizeAttributes = webCartController::getSizeByCustomIdProductId($productOption->custom_option_id, $product->id);
							if (!empty($SizeAttributes) && count($SizeAttributes) > 0) {
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">' . __('webMessage.size') . ' *: </div>';
								$results .= '<div class="form-group">';
								$results .= '<select class="form-control size_attr" name="size_attr" style="height:auto" id="size_attr_' . $product->id . '">';
								foreach ($SizeAttributes as $SizeAttribute) {
									$sizeName = $SizeAttribute->title_en;
									$results .= '<option value="' . $SizeAttribute->size_id . '">' . $sizeName . '</option>';
								}
								$results .= '</select>';
								$results .= '</div>';
								$results .= '</div>';
							}
						}
						//color
						elseif ($productOption->custom_option_id == 2) {
							$results .= '<input type="hidden" name="option_sc" id="option_sc_' . $productOption->id . '" value="' . $productOption->custom_option_id . '">';
							$ColorAttributes = webCartController::getColorByCustomIdProductId($productOption->custom_option_id, $product->id);
							if (!empty($ColorAttributes) && count($ColorAttributes) > 0) {
								$results .= '<input type="hidden" name="is_color" id="is_color" value="1">';
								$results .= '<input type="hidden" name="color_attr" id="color_attr" value="' . $ColorAttributes[0]->color_id . '">';
								$results .= '<span id="color_box">';
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">' . __('webMessage.texture') . ' : </div>';
								$results .= '<ul class="tt-options-swatch options-large">';
								$i = 0;
								foreach ($ColorAttributes as $ColorAttribute) {
									$colorCode = $ColorAttribute->color_code ?: 'none';
									if (!empty($ColorAttribute->image)) {
										if ($i == 0) $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="active color-to-choose">';
										else $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="color-to-choose">';
										$results .= '<a class="options-color" onclick="setColorAttr(' . $ColorAttribute->color_id . ')" id="' . $ColorAttribute->color_id . '">';
										$results .= '<span class="swatch-img">';
										$results .= '<img src="' . url("uploads/color/thumb/" . $ColorAttribute->image) . '" alt="">';
										$results .= '</span>';
										$results .= '<span class="swatch-label color-black"></span>';
										$results .= '</a>';
										$results .= '</li>';
									} else {
										if ($i == 0) $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="active color-to-choose">';
										else $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="color-to-choose">';
										$results .= '<a onclick="setColorAttr(' . $ColorAttribute->color_id . ')" class="options-color" style="background-color:' . $colorCode . ';" id="' . $ColorAttribute->color_id . '"></a>';
										$results .= '</li>';
									}
									$i++;
								}
								$results .= '</ul>';
								$results .= '<br clear="all">';
								$results .= '</div>';
								$results .= '</span>';
							}
						}
						//size and color
						elseif ($productOption->custom_option_id == 3) {
							$results .= '<input type="hidden" name="option_sc" id="option_sc_' . $productOption->id . '" value="' . $productOption->custom_option_id . '">';
							//first getting the size
							$SizeAttributes = webCartController::getSizeByCustomIdProductId($productOption->custom_option_id, $product->id);
							if (!empty($SizeAttributes) && count($SizeAttributes) > 0) {
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">' . __('webMessage.size') . ' *: </div>';
								$results .= '<div class="form-group">';
								$results .= '<select class="form-control size_attr" name="size_attr" style="height:auto" id="size_attr_' . $product->id . '">';
								foreach ($SizeAttributes as $SizeAttribute) {
									$sizeName = $SizeAttribute->title_en;
									$results .= '<option value="' . $SizeAttribute->size_id . '">' . $sizeName . '</option>';
								}
								$results .= '</select>';
								$results .= '</div>';
								$results .= '</div>';
							}
							//then getting the color
							$ColorAttributes = webCartController::getColorByCustomIdProductId($productOption->custom_option_id, $product->id);
							if (!empty($ColorAttributes) && count($ColorAttributes) > 0) {
								$results .= '<input type="hidden" name="is_color" id="is_color" value="1">';
								$results .= '<input type="hidden" name="color_attr" id="color_attr" value="' . $ColorAttributes[0]->color_id . '">';
								$results .= '<span id="color_box">';
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">' . __('webMessage.texture') . ' : </div>';
								$results .= '<ul class="tt-options-swatch options-large">';
								$i = 0;
								foreach ($ColorAttributes as $ColorAttribute) {
									$colorCode = $ColorAttribute->color_code ?: 'none';
									if (!empty($ColorAttribute->image)) {
										if ($i == 0) $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="active color-to-choose">';
										else $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="color-to-choose">';
										$results .= '<a class="options-color" onclick="setColorAttr(' . $ColorAttribute->color_id . ')" id="' . $ColorAttribute->color_id . '">';
										$results .= '<span class="swatch-img">';
										$results .= '<img src="' . url("uploads/color/thumb/" . $ColorAttribute->image) . '" alt="">';
										$results .= '</span>';
										$results .= '<span class="swatch-label color-black"></span>';
										$results .= '</a>';
										$results .= '</li>';
									} else {
										if ($i == 0) $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="active color-to-choose">';
										else $results .= '<li id="li-' . $ColorAttribute->color_id . '" class="color-to-choose">';
										$results .= '<a onclick="setColorAttr(' . $ColorAttribute->color_id . ')" class="options-color" style="background-color:' . $colorCode . ';" id="' . $ColorAttribute->color_id . '"></a>';
										$results .= '</li>';
									}
									$i++;
								}
								$results .= '</ul>';
								$results .= '<br clear="all">';
								$results .= '</div>';
								$results .= '</span>';
							}
						}
						//other custom options
						else {
							$customOptions = webCartController::getCustomOptions($productOption->custom_option_id, $product->id);
							//radio box
							if (!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType'] == "radio") {
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">';
								$results .= $customOptions['CustomOptionName'];
								if (!empty($productOption->is_required)) {
									$results .= '*';
								}
								$results .= '</div>';
								$results .= '<ul class="optionradio">';
								if (!empty($customOptions['childs']) && count($customOptions['childs']) > 0) {
									$is_cadd_txt = '';
									foreach ($customOptions['childs'] as $child) {
										if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 1) {
											$is_cadd = "+";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 2) {
											$is_cadd = "-";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && empty($child->is_price_add)) {
											$is_cadd = "";
											$is_cadd_txt = $child->retail_price . ' ' . \App\Currency::default();
										} else {
											$is_cadd = "";
											$is_cadd_txt = "";
										}
										$option_value_name = $child->option_value_name_en;
										$results .= '<li>';
										$results .= '<label for="option-' . $product->id . '-' . $productOption->custom_option_id . '-' . $child->id . '">';
										$results .= '<input class="checkOptionPrice" type="radio" name="option-' . $product->id . '-' . $productOption->custom_option_id . '" id="option-' . $product->id . '-' . $productOption->custom_option_id . '-' . $child->id . '" value="' . $child->id . '">';
										$results .= $option_value_name . '(' . $is_cadd_txt . ')';
										$results .= '</label>';
										$results .= '</li>';
									}
								}
								$results .= '</ul>';
								$results .= '</div>';
							}
							//check box
							if (!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType'] == "checkbox") {
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">';
								$results .= $customOptions['CustomOptionName'];
								if (!empty($productOption->is_required)) {
									$results .= '*';
								}
								$results .= '</div>';
								$results .= '<ul class="optionradio">';
								if (!empty($customOptions['childs']) && count($customOptions['childs']) > 0) {
									$is_cadd_txt = '';
									foreach ($customOptions['childs'] as $child) {
										if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 1) {
											$is_cadd = "+";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 2) {
											$is_cadd = "-";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && empty($child->is_price_add)) {
											$is_cadd = "";
											$is_cadd_txt = $child->retail_price . ' ' . \App\Currency::default();
										} else {
											$is_cadd = "";
											$is_cadd_txt = "";
										}
										$option_value_name = $child->option_value_name_en;
										$results .= '<li>';
										$results .= '<label for="checkbox-' . $product->id . '-' . $productOption->custom_option_id . '-' . $child->id . '">';
										$results .= '<input class="checkOptionPricechk" type="checkbox" name="checkbox-' . $product->id . '-' . $productOption->custom_option_id . '[]" id="checkbox-' . $product->id . '-' . $productOption->custom_option_id . '-' . $child->id . '" value="' . $child->id . '">';
										$results .= $option_value_name . '(' . $is_cadd_txt . ')';
										$results .= '</label>';
										$results .= '</li>';
									}
								}
								$results .= '</ul>';
								$results .= '</div>';
							}
							//select box
							if (!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType'] == "select") {
								$results .= '<div class="tt-wrapper">';
								$results .= '<div class="tt-title-options">';
								$results .= $customOptions['CustomOptionName'];
								if (!empty($productOption->is_required)) {
									$results .= '*';
								}
								$results .= '</div>';
								$results .= '<div class="form-group">';
								$results .= '<select class="form-control choose_select_options" name="select-' . $product->id . '-' . $productOption->custom_option_id . '" id="select-' . $product->id . '-' . $productOption->custom_option_id . '">';
								$results .= '<option value="0">---</option>';
								if (!empty($customOptions['childs']) && count($customOptions['childs']) > 0) {
									$is_cadd_txt = '';
									foreach ($customOptions['childs'] as $child) {
										if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 1) {
											$is_cadd = "+";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add == 2) {
											$is_cadd = "-";
											$is_cadd_txt = $is_cadd . ' ' . $child->retail_price . ' ' . \App\Currency::default();
										} else if (!empty($child->retail_price) && empty($child->is_price_add)) {
											$is_cadd = "";
											$is_cadd_txt = $child->retail_price . ' ' . \App\Currency::default();
										} else {
											$is_cadd = "";
											$is_cadd_txt = "";
										}
										$option_value_name = $child->option_value_name_en;
										$results .= '<option value="select-' . $product->id . '-' . $productOption->custom_option_id . '-' . $child->id . '">';
										$results .= $option_value_name . '(' . $is_cadd_txt . ')';
										$results .= '</option>';
									}
								}
								$results .= '</select>';
								$results .= '</div>';
								$results .= '</div>';
							}
						}
					}
					$results .= '</div>';
				}
				$results .= '</div>';
				$results .= '<div class="col-1">';
				$results .= '<button type="button" class="btn btn-success" id="add-product-form" onclick="addProductToOrder()">Add</button><br>';
				$results .= '</div>';
				$results .= '</div>';
				$results .= '</form>';
				return $results;
			}
		}
		return ["status" => 404, "message" => "Product Not Found!"];
	}

	////////////////////////////////orders track history//////////////////////////////////////////////////
	public function listorderhistory(Request $request, $oid)
	{
		$trackhistoryLists = OrdersTrack::where('oid', $oid)->orderBy('display_order', 'DESC')->paginate();
		return view('gwc.orders-track.index', compact('trackhistoryLists'));
	}
	//show create form
	public function createTrackHistory($oid)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$OrderInfo     = OrdersDetails::where('id', $oid)->first();
		$lastOrderInfo = OrdersTrack::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}



		return view('gwc.orders-track.create', compact('lastOrder', 'OrderInfo', 'settingInfo'));
	}

	public function postTrackHistory(Request $request, $oid)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();

		if (empty($oid)) {
			die('Invalid request');
		}
		//field validation
		$this->validate($request, [
			'details_en'   => 'required|min:3|string',
			'details_ar'   => 'required|min:3|string',
			'details_date' => 'required|min:3|string',
		]);

		$tracks = new OrdersTrack;
		$tracks->oid    = $oid;
		$tracks->details_en    = $request->details_en;
		$tracks->details_ar    = $request->details_ar;
		$tracks->details_date  = $request->details_date;
		$tracks->is_active     = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
		$tracks->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
		$tracks->save();

		//change order status
		$tracksOrder = OrdersDetails::where('id', $oid)->first();
		$tracksOrder->order_status = $request->input('order_status');
		$tracksOrder->save();
		//send email notification

		$name  = $tracksOrder->name;
		$email = $tracksOrder->email;
		$orderid = $tracksOrder->order_id;
		$trackmessage = $tracks->details_en . '<br><br>ORDER ID #' . $orderid;
		if (!empty($email)) {
			self::sendEmailNotificationForOrderStatus($name, $email, $trackmessage, $orderid);
		}
		//send push notification
		if (!empty($tracksOrder->customer_id) && !empty($settingInfo->pushy_api_token)) {
			$deviceLists = WebPush::where('user_id', $tracksOrder->customer_id);
			$deviceLists = $deviceLists->where(function ($sq) {
				$sq->where('device_type', 'android')->orwhere('device_type', 'ios');
			});
			$deviceLists = $deviceLists->get();
			$token = [];
			if (!empty($deviceLists) && count($deviceLists) > 0) {
				foreach ($deviceLists as $deviceList) {
					$token[] = $deviceList->device_token;
				}
				if (!empty($token) && count($token) > 0) {
					$title   = "Order tacking for #" . $tracksOrder->order_id;
					$message = $tracks->details_en . " #" . $tracksOrder->order_id;
					Common::sendMobilePush($token, $title, $message, 'order');
				}
			}
		}
		//save logs
		$key_name   = "ordetrack";
		$key_id     = $tracks->id;
		$message    = "A new track history is added. (" . $request->details_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		//send sms notification
		$isValidMobile = Common::checkMobile($tracksOrder->mobile);
		if (!empty($request->is_sms_active) && !empty($isValidMobile)) {
			if ($tracksOrder->strLang == "en") {
				$smsMessage = $request->details_en;
			} else {
				$smsMessage = $request->details_ar;
			}
			$to      = $tracksOrder->mobile;
			$sms_msg = $smsMessage . " #" . $tracksOrder->order_id;
			Common::SendSms($to, $sms_msg);
		}
		//end sms notification

		return redirect('/gwc/orders-track/' . $oid)->with('message-success', 'Tracking message is added successfully');
	}

	//change track status
	//update status
	public function updateOrderStatusAjax(Request $request)
	{
		$recDetails = OrdersTrack::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "ordertrack";
		$key_id     = $recDetails->id;
		$message    = "Order Track history status is changed to " . $active . " (" . $recDetails->details_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}


	public function deletePayment(Request $request)
	{
		$recDetails = Transaction::where('id', $request->id)->first();

		//save logs
		$key_name   = "payment";
		$key_id     = $recDetails->id;
		$message    = "Payment is removed (" . $recDetails->trackid . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$recDetails->delete();
		return redirect()->back()->with('message-success', 'Record is removed successfully');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edittrack($id)
	{
		$edittrack  = OrdersTrack::where('id', $id)->first();
		$OrderInfo  = OrdersDetails::where('id', $edittrack->oid)->first();
		return view('gwc.orders-track.edit', compact('edittrack', 'OrderInfo'));
	}


	public function updatetrack(Request $request, $id)
	{
		if (empty($id)) {
			die('Invalid request');
		}
		//field validation
		$this->validate($request, [
			'details_en'   => 'required|min:3|string',
			'details_ar'   => 'required|min:3|string',
			'details_date' => 'required|min:3|string',
		]);

		try {


			$tracks = OrdersTrack::find($id);
			$tracks->details_en    = $request->details_en;
			$tracks->details_ar    = $request->details_ar;
			$tracks->details_date  = $request->details_date;
			$tracks->is_active     = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$tracks->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$tracks->save();
			//change order status
			$tracksOrder = OrdersDetails::where('id', $tracks->oid)->first();
			$tracksOrder->order_status = $request->input('order_status');
			$tracksOrder->save();

			//send email notification
			$name         = $tracksOrder->name;
			$email        = $tracksOrder->email;
			$orderid      = $tracksOrder->order_id;
			$trackmessage = $tracks->details_en . '<br><br>ORDER ID #' . $orderid;
			self::sendEmailNotificationForOrderStatus($name, $email, $trackmessage, $orderid);

			//send push notification
			if (!empty($tracksOrder->customer_id) && !empty($settingInfo->pushy_api_token)) {
				$deviceLists = WebPush::where('user_id', $tracksOrder->customer_id);
				$deviceLists = $deviceLists->where(function ($sq) {
					$sq->where('device_type', 'android')->orwhere('device_type', 'ios');
				});
				$deviceLists = $deviceLists->get();
				$token = [];
				if (!empty($deviceLists) && count($deviceLists) > 0) {
					foreach ($deviceLists as $deviceList) {
						$token[] = $deviceList->device_token;
					}
					if (!empty($token) && count($token) > 0) {
						$title   = "Order tacking for #" . $tracksOrder->order_id;
						$message = $tracks->details_en . " #" . $tracksOrder->order_id;
						Common::sendMobilePush($token, $title, $message, 'order');
					}
				}
			}

			//save logs
			$key_name   = "ordetrack";
			$key_id     = $tracks->id;
			$message    = "A track history is edited. (" . $request->details_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs
			return redirect('/gwc/orders-track/' . $tracks->oid)->with('message-success', 'Tracking message is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}

	/**
	 * Delete manufacturer along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyTrack($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect()->back()->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$order = OrdersTrack::find($id);
		//check cat id exist or not
		if (empty($order->id)) {
			return redirect()->back()->with('message-error', 'No record found');
		}


		//save logs
		$key_name   = "ordertrack";
		$key_id     = $order->id;
		$message    = "A record is removed. (" . $order->details_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		//end deleting parent cat image
		$order->delete();
		return redirect()->back()->with('message-success', 'Record is deleted successfully');
	}


	//change order status
	public static function orderStatus(Request $request)
	{
		if (empty($request->id)) {
			return ["status" => 400, "message" => "Invalid id"];
		}
		$order = OrdersDetails::find($request->id);
		$order->order_status = $request->order_status;
		if (Auth::guard('driver')->check() and !$order->is_paid) {
			$order->is_paid = $request->pay_status;
			if ($request->pay_status)
				$order->pay_mode = 'COD';
		} elseif (Auth::guard('driver')->check() and $order->is_paid and  $order->pay_mode == 'COD') {
			$order->is_paid = $request->pay_status;
		} elseif (!Auth::guard('driver')->check())
			$order->is_paid = $request->pay_status;
		$order->extra_comment = !empty($request->extra_comment) ? $request->extra_comment : '';

		//rollbacked quantity if status is not rollbacked
		if (empty($order->is_qty_rollbacked) && ($request->order_status == "canceled" || $request->order_status == "returned")) {
			$orderLists   = Orders::where("oid", $request->id)->get();

			if (!empty($orderLists) && count($orderLists) > 0) {
				foreach ($orderLists as $orderList) {
					//option
					$OrderOptions = OrdersOption::where("oid", $orderList->id)->get();
					if (!empty($OrderOptions) && count($OrderOptions) > 0) {
						foreach ($OrderOptions as $OrderOption) {
							webCartController::changeOptionQuantity($OrderOption->product_id, 'a', $OrderOption->option_child_ids, $orderList->quantity, $orderList->inventory); //add qty
						}
					}
					//end option
					webCartController::rollbackedQuantity($orderList->product_id, $orderList->quantity, $orderList->size_id, $orderList->color_id, $orderList->inventory);
				}
			}
			$order->is_qty_rollbacked = 1;
		} else if (!empty($order->is_qty_rollbacked) && $request->order_status == "returned") {
			$orderLists   = Orders::where("oid", $request->id)->get();

			if (!empty($orderLists) && count($orderLists) > 0) {
				foreach ($orderLists as $orderList) {
					//option
					$OrderOptions = OrdersOption::where("oid", $orderList->id)->get();
					if (!empty($OrderOptions) && count($OrderOptions) > 0) {
						foreach ($OrderOptions as $OrderOption) {
							webCartController::changeOptionQuantity($OrderOption->product_id, 'a', $OrderOption->option_child_ids, $orderList->quantity, $orderList->inventory); //add qty
						}
					}
					//end option
					webCartController::rollbackedQuantity($orderList->product_id, $orderList->quantity, $orderList->size_id, $orderList->color_id, $orderList->inventory);
				}
			}
			$order->is_qty_rollbacked = 1;
		}

		$order->save();

		//send push notification
		if (!empty($order->customer_id)) {
			$token = [];
			$deviceLists = WebPush::where('user_id', $order->customer_id);
			$deviceLists = $deviceLists->where(function ($sq) {
				$sq->where('device_type', 'android')->orwhere('device_type', 'ios');
			});
			$deviceLists = $deviceLists->get();
			if (!empty($deviceLists) && count($deviceLists) > 0) {
				foreach ($deviceLists as $deviceList) {
					$token[]   = $deviceList->device_token;
				}
				if (!empty($token) && count($token) > 0) {
					$title   = "Order tacking for #" . $order->order_id;
					$message = (!empty($order->extra_comment) ? $order->extra_comment : 'Your order status is changed to ' . $request->order_status) . "#" . $order->order_id;
					Common::sendMobilePush($token, $title, $message, 'order');
				}
			}
		}


		//save logs
		$key_name   = "orders";
		$key_id     = $order->id;
		$message    = "Order/Payment status is changed to " . $request->order_status . "/" . $request->pay_status . " (" . $order->order_id . ") by " . (Auth::guard('admin')->check() ? 'admin: ' . Auth::guard('admin')->user()->name : 'Driver: ' . Auth::guard('driver')->user()->full_name);
		$created_by = Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('driver')->id();
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return ["status" => 200, "message" => "Order status is updated successfully"];
	}
	//get country  state area
	public static function getCountryStatesArea($id)
	{
		$data = Country::find($id);
		return $data['name_en'] ?? "--";
	}
	//get total order amount
	public static function getOrderAmounts($id)
	{
		$totalAmt = 0;
		$orderDetails = OrdersDetails::Where('id', $id)->first();
		$listOrders   = Orders::where('oid', $id)->get();
		if (!empty($listOrders) && count($listOrders) > 0) {
			foreach ($listOrders as $listOrder) {
				$totalAmt += ($listOrder->quantity * $listOrder->unit_price);
			}
			//apply coupon if its not free
			if (!empty($orderDetails->bundle_discount)) {
				$totalAmt = $totalAmt - $orderDetails->bundle_discount;
			}
			//apply coupon if its not free
			if (!empty($orderDetails->coupon_code)) {
				$totalAmt = $totalAmt - $orderDetails->coupon_amount;
			}
			//apply delivery charges if coupon is empty
			if (empty($orderDetails->coupon_free)) {
				$totalAmt = $totalAmt + $orderDetails->delivery_charges;
			}
			//apply delivery charges if coupon is empty
			if (!empty($orderDetails->seller_discount)) {
				$totalAmt = $totalAmt - $orderDetails->seller_discount;
			}
		}

		return $totalAmt;
	}

	//view customers wish items
	public function viewCustomerWishItems(Request $request)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}
		if (!empty($q)) {
			$wishLists = DB::table('gwc_customers_wish')
				->select('gwc_products.image', 'gwc_customers.name', 'gwc_customers_wish.created_at', 'gwc_customers_wish.id', 'gwc_products.title_en', 'gwc_products.retail_price', 'gwc_products.item_code')
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_customers_wish.product_id')
				->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_customers_wish.customer_id')
				->where(['gwc_products.is_active' => 1])
				->where(function ($sq) use ($q) {
					$sq->where('gwc_customers.name', 'LIKE', '%' . $q . '%')
						->orwhere('gwc_products.title_en', 'LIKE', '%' . $q . '%')
						->orwhere('gwc_products.item_code', 'LIKE', '%' . $q . '%');
				});

			if (!empty(Session::get('wish_customers'))) {
				$wishLists = $wishLists->where('gwc_customers_wish.customer_id', '=', Session::get('wish_customers'));
			}

			$wishLists = $wishLists->orderBy('gwc_customers_wish.id', 'DESC')
				->paginate($settingInfo->item_per_page_back);
			$wishLists->appends(['q' => $q]);
		} else {
			$wishLists = DB::table('gwc_customers_wish')
				->select('gwc_products.image', 'gwc_customers.name', 'gwc_customers_wish.created_at', 'gwc_customers_wish.id', 'gwc_products.title_en', 'gwc_products.retail_price', 'gwc_products.item_code')
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_customers_wish.product_id')
				->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_customers_wish.customer_id')
				->where(['gwc_products.is_active' => 1]);

			if (!empty(Session::get('wish_customers'))) {
				$wishLists = $wishLists->where('gwc_customers_wish.customer_id', '=', Session::get('wish_customers'));
			}

			$wishLists = $wishLists->orderBy('gwc_customers_wish.id', 'DESC')
				->paginate($settingInfo->item_per_page_back);
		}

		$customersLists = DB::table('gwc_customers_wish')
			->select('gwc_customers_wish.customer_id', 'gwc_customers.id', 'gwc_customers.name')
			->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_customers_wish.customer_id')
			->GroupBy('gwc_customers_wish.customer_id')
			->get();


		return view('gwc.customers.wishitems', compact('wishLists', 'customersLists'));
	}
	//delete wish item
	public function deleteWishItem($id)
	{
		$wish = CustomersWish::find($id);
		$wish->delete();
		return redirect()->back()->with('message-success', 'Record is deleted successfully');
	}

	/////payments
	public function listPayments(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}


		//menus records
		if (!empty($q)) {

			$paymentsLists = Transaction::Where('gwc_transaction.id', '!=', '0')
				->join('gwc_orders_details', 'gwc_orders_details.order_id', '=', 'gwc_transaction.trackid')
				->select(
					'gwc_transaction.*',
					'gwc_orders_details.order_id',
					'gwc_orders_details.name',
					'gwc_orders_details.email',
					'gwc_orders_details.mobile',
					'gwc_orders_details.area_id',
					'gwc_orders_details.block',
					'gwc_orders_details.order_id_md5',
					'gwc_orders_details.id as oid'
				)
				->where(function ($sq) use ($q) {
					$sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%');
					$sq->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%');
					$sq->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%');
					$sq->orwhere('payment_id', 'LIKE', '%' . $q . '%');
					$sq->orwhere('trackid', 'LIKE', '%' . $q . '%');
					$sq->orwhere('tranid', 'LIKE', '%' . $q . '%');
					$sq->orwhere('ref', 'LIKE', '%' . $q . '%');
					$sq->orwhere('auth', 'LIKE', '%' . $q . '%');
					$sq->orwhere('presult', 'LIKE', '%' . $q . '%');
				});
			//filter by date range
			if (!empty(Session::get('payment_filter_dates'))) {
				$explodeDates = explode("-", Session::get('payment_filter_dates'));
				if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
					$date1 = date("Y-m-d", strtotime($explodeDates[0]));
					$date2 = date("Y-m-d", strtotime($explodeDates[1]));
					$paymentsLists = $paymentsLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
				}
			}
			//
			if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'paid') {
				$paymentsLists = $paymentsLists->where('presult', 'CAPTURED');
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'notpaid') {
				$paymentsLists = $paymentsLists->where('presult', '!=', 'CAPTURED');
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'release') {
				$paymentsLists = $paymentsLists->where('release_pay', '=', 1);
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'nrelease') {
				$paymentsLists = $paymentsLists->where('release_pay', '!=', 1);
			}

			$paymentsLists = $paymentsLists->orderBy('created_at', 'DESC')
				->paginate($settingInfo->item_per_page_back);
			$paymentsLists->appends(['q' => $q]);
		} else {
			$paymentsLists = Transaction::Where('gwc_transaction.id', '!=', '0')
				->join('gwc_orders_details', 'gwc_orders_details.order_id', '=', 'gwc_transaction.trackid')
				->select(
					'gwc_transaction.*',
					'gwc_orders_details.order_id',
					'gwc_orders_details.name',
					'gwc_orders_details.email',
					'gwc_orders_details.mobile',
					'gwc_orders_details.area_id',
					'gwc_orders_details.block',
					'gwc_orders_details.order_id_md5',
					'gwc_orders_details.id as oid'
				);
			//filter by date range
			if (!empty(Session::get('payment_filter_dates'))) {
				$explodeDates = explode("-", Session::get('payment_filter_dates'));
				if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
					$date1 = date("Y-m-d", strtotime($explodeDates[0]));
					$date2 = date("Y-m-d", strtotime($explodeDates[1]));
					$paymentsLists = $paymentsLists->whereBetween('gwc_orders_details.created_at', [$date1, $date2]);
				}
			}

			if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'paid') {
				$paymentsLists = $paymentsLists->where('presult', 'CAPTURED');
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'notpaid') {
				$paymentsLists = $paymentsLists->where('presult', '!=', 'CAPTURED');
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'release') {
				$paymentsLists = $paymentsLists->where('release_pay', '=', 1);
			} else if (!empty(Session::get('payment_filter_status')) && Session::get('payment_filter_status') == 'nrelease') {
				$paymentsLists = $paymentsLists->where('release_pay', '!=', 1);
			}

			$paymentsLists = $paymentsLists->orderBy('created_at', 'DESC')
				->paginate($settingInfo->item_per_page_back);
		}
		return view('gwc.orders.payments', ['paymentLists' => $paymentsLists, 'settingInfo' => $settingInfo]);
	}

	///send email notification once qty is updated
	public static function sendEmailNotificationForOrderStatus($name, $email, $trackmessage, $orderid)
	{
		$settingInfo      = Settings::where("keyname", "setting")->first();
		$data = [
			'dear'            => trans('webMessage.dear') . ' ' . $name . ',',
			'footer'          => trans('webMessage.email_footer'),
			'message'         => $trackmessage,
			'subject'         => "Order Track Notification,#" . $orderid,
			'email_from' => $settingInfo->from_email,
			'email_from_name' => $settingInfo->from_name
		];
		Mail::to($email)->send(new SendGrid($data));
	}


	//store value to cookie
	public static function storetocookie(Request $request)
	{
		if (!empty($request->val)) {
			Session::put($request->key, $request->val);
		} else {
			Session::forget($request->key);
		}

		return ['status' => 200, 'message' => '', "userType" => Auth::guard('admin')->user()->userType];
	}
	//change qty in prodruct table for attribute
	public static function ChangeUpdateQuantity($product_id)
	{
		$qty = 0;
		$productUpdate   = Product::where('id', $product_id)->first();
		if (!empty($productUpdate->is_attribute)) {
			$qty   = ProductAttribute::where('product_id', $productUpdate->id)->get()->sum('quantity');
			$productUpdate->quantity = $qty;
			$productUpdate->save();
		}
	}

	//apply discount amount
	public function applydiscountAmount(Request $request)
	{
		if (empty($request->oid)) {
			return ['status' => 400, 'message' => 'Order ID is missing'];
		}
		$orderDetails    = OrdersDetails::where('id', $request->oid)->first();
		$orderDetails->delivery_date = $request->delivery_date;
		$orderDetails->seller_discount = $request->seller_discount;
		$orderDetails->landmark = $request->landmark;
		$orderDetails->floor = $request->floor;
		$orderDetails->house = $request->house;
		$orderDetails->avenue = $request->avenue;
		$orderDetails->street = $request->street;
		$orderDetails->block = $request->block;
		$orderDetails->area_id = $request->area;
		if (!empty($request->delivery_time)) {
			$delivryDetailsInfo = webCartController::getDeliberyTimeDetails($request->delivery_time);
			$orderDetails->delivery_time_id = $delivryDetailsInfo->id;
			$orderDetails->delivery_time_en = $delivryDetailsInfo->title_en;
			$orderDetails->delivery_time_ar = $delivryDetailsInfo->title_ar;
		}
		$orderDetails->mobile = $request->mobile;
		$orderDetails->country_id = webCartController::get_country_of_area($request->input('area'));;
		$orderDetails->state_id = webCartController::state($request->input('area'));
		if ($request->delivery_charges >= 0 and $request->delivery_charges != null and $request->delivery_charges != "")
			$orderDetails->delivery_charges = $request->delivery_charges;
		$orderDetails->save();
		$orderDetails->total_amount = $this->getOrderAmounts($orderDetails->id);
		$orderDetails->save();
		return redirect('/gwc/orders/' . $orderDetails->id . '/view')->with('message-success', 'Details update successfully');
		return ['status' => 200, 'message' => 'Discount amount is applied'];
	}


	//get customer details
	public static function getCustomerDetails($id)
	{
		$customersLists = [];
		if (!empty($id)) {
			$customersLists = Customers::where('id', $id)->first();
			return $customersLists;
		}
	}

	public function printOrdersInRole(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		//check search queries
		if (!empty($request->get('q'))) {
			$q = $request->get('q');
		} else {
			$q = $request->q;
		}

		$orderLists = OrdersDetails::with('area')->where('order_status', '!=', '');
		//search keywords
		if (!empty($q)) {
			$orderLists = $orderLists->where(function ($sq) use ($q) {
				$sq->where('name', 'LIKE', '%' . $q . '%')
					->orwhere('email', 'LIKE', '%' . $q . '%')
					->orwhere('mobile', 'LIKE', '%' . $q . '%')
					->orwhere('order_id', 'LIKE', '%' . $q . '%');
			});
		}
		//filter by date range
		if (!empty(Session::get('order_filter_dates'))) {
			$explodeDates = explode("-", Session::get('order_filter_dates'));
			if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
				$date1 = date("Y-m-d", strtotime($explodeDates[0]));
				$date2 = date("Y-m-d", strtotime($explodeDates[1]));
				$orderLists = $orderLists->whereBetween('created_at', [$date1, $date2]);
			}
		}
		if (!empty(Session::get('order_filter_status')) && Session::get('order_filter_status') <> "all") {
			$orderLists = $orderLists->where('order_status', '=', Session::get('order_filter_status'));
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'paid') {
			$orderLists = $orderLists->where('is_paid', '=', 1);
		}
		if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'notpaid') {
			$orderLists = $orderLists->where('is_paid', '!=', 1);
		}
		if (!empty(Session::get('order_customers'))) {
			$orderLists = $orderLists->where('customer_id', '=', Session::get('order_customers'));
		}

		if (!empty(Session::get('pay_mode'))) {
			$orderLists = $orderLists->where('pay_mode', '=', Session::get('pay_mode'));
		}

		if (!empty($request->pmode) && $request->pmode == "COD") {
			$orderLists = $orderLists->where('pay_mode', '=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "COD_KNET") {
			$orderLists = $orderLists->where('is_paid', 1)->where('order_status', 'completed');
		} else if (!empty($request->pmode) && $request->pmode == "KNET") {
			$orderLists = $orderLists->where('pay_mode', '!=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		}

		$orderLists = $orderLists->orderBy('id', 'DESC');

		if (!empty($request->pmode)) {
			$orderLists->appends(['pmode' => $request->pmode]);
		}
		$orderLists = $orderLists->get();
		$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
.page-break {
    page-break-after: always;
}
.bt {
    border-top: thin solid;
}
.bb {
    border-bottom: thin solid;
}
.bbs {
    border-bottom: solid;
}
</style>';

		if (count($orderLists)) {
			$BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;
			foreach ($orderLists as  $orderList) {
				$itemsHtml = "<table style='width: 100%'><thead><tr style='text-align: left;'><th class='bt bb'>#</th><th class='bt bb'>Item</th><th class='bt bb'>QTY</th><th class='bt bb'>Total</th></tr></thead><tbody>";
				$totalDiscount = $totalQTY = $totalAmt = 0;
				$listOrders   = Orders::where('oid', $orderList->id)->leftJoin('gwc_products', 'gwc_products.id', 'gwc_orders.product_id')->select('gwc_products.title_en', 'gwc_products.item_code', 'gwc_orders.unit_price', 'gwc_orders.quantity')->get();
				if (!empty($listOrders) && count($listOrders) > 0) {
					foreach ($listOrders as $listOrder) {
						$thisTotal = ($listOrder->quantity * $listOrder->unit_price);
						$totalAmt += $thisTotal;
						$totalQTY += $listOrder->quantity;
						$itemsHtml .= "<tr><td style='padding-right: 5px;'>" . $listOrder->item_code . "</td><td style='padding: 10px 0;'>" . str_limit($listOrder->title_en, 20) . "</td><td>" . $listOrder->quantity . "</td><td>" . number_format($thisTotal, 3) . "</td></tr>";
					}
					$subTotal = $totalAmt;
					//apply coupon if its not free
					if (!empty($orderList->coupon_code) && empty($orderList->coupon_free)) {
						$totalAmt = $totalAmt - $orderList->coupon_amount;
						$totalDiscount += $orderList->coupon_amount;
					}
					//apply delivery charges if coupon is empty
					if (empty($orderList->coupon_free)) {
						$totalAmt = $totalAmt + $orderList->delivery_charges;
					}
					//apply delivery charges if coupon is empty
					if (!empty($orderList->seller_discount)) {
						$totalAmt = $totalAmt - $orderList->seller_discount;
						$totalDiscount += $orderList->coupon_amount;
					}
				}
				$itemsHtml .= "<tr><td colspan='3' class='bbs'><strong>Subtotal:</strong></td><td class='bbs'>" . number_format($subTotal, 3) . "</td></tr></tbody></table>";
				$totalAmounts = $totalAmt;
				//$html .= (!empty($settingInfo->owner_name) ? $settingInfo->owner_name : $settingInfo->name_en);
				//$html .= ' order - ' . $orderList->pay_mode;
				$html .= "<div style='width:100%;text-align:center;font-size: 24px;'><strong>Invoice No #" . $orderList->order_id . "</strong></div>";
				$html .= "<table style='width: 100%; margin-top: 15px;'><tr><td><strong>Customer:</strong>" . $orderList->name . "</td><td><strong>Mobile:</strong>" . $orderList->mobile . "</td></tr></table>";
				//$html .= 'Date: '. $orderList->created_at->format('Y-m-d') .' - ' . $orderList->pay_mode;
				//$html .= '<br>ORDER ID : ' . $orderList->order_id . '<br>Order Status : ' . $orderList->order_status;
				//$html .= '<br>Payment Status : ' . ($orderList->is_paid ? 'Paid' : 'Not Pay!') . '<br>NAME : ' . $orderList->name . '<br>';
				if (($countryName = self::getCountryStatesArea($orderList->country_id)) != "--")
					$html .= $countryName . ', ';
				if (!empty($orderList->area->name_en))
					$html .= $orderList->area->name_en;
				if (!empty($orderList->block))
					$html .= 'Block: ' . $orderList->block . ', ';
				if (!empty($orderList->street))
					$html .= 'Street: ' . $orderList->street . ', ';
				if (!empty($orderList->block))
					$html .= 'House: ' . $orderList->house . ', ';
				if (!empty($orderList->floor))
					$html .= 'Foor: ' . $orderList->floor . ', ';
				if (!empty($orderList->delivery_time_en))
					$html .= '<br><strong>Delivery Time:</strong> ' . $orderList->delivery_time_en;

				$html .= "<table style='width: 100%;margin-top: 15px;'><tr><td><strong>" . $orderList->order_status . "</strong></td><td style='text-align: right;'><span style='text-align: center;display: inline-block;'>" . $orderList->created_at->format('Y/m/d') . "<br>" . $orderList->created_at->format('H:i') . "</span></td></tr></table>";
				$html .= $itemsHtml;
				$html .= "<table style='width: 100%;'>";
				$html .= "<tr><td style='width: 20%;'><strong>QTY:</strong>" . $totalQTY . "</td><td style='text-align:right;width: 50%;'><strong>Delivery:</strong></td><td style='text-align:right;width: 30%;'>" . number_format($orderList->delivery_charges, 3) . " " . \App\Currency::default() . "</td></tr>";
				$html .= "<tr><td style='width: 20%;'>" . ($orderList->is_express_delivery ? "Express Delivery" : "") . "</td><td style='text-align:right;'><strong>Discount:</strong></td><td style='text-align:right;'>" . number_format($totalDiscount, 3) . " " . \App\Currency::default() . "</td></tr>";
				$html .= "<tr><td></td><td style='text-align:right;'><strong>Total:</strong></td><td style='text-align:right;'>" . number_format($totalAmounts, 3) . " " . \App\Currency::default() . "</td></tr>";
				$html .= "</table><table style='width: 100%;margin-top: 15px;'>";
				$html .= "<tr><td colspan='3' style='text-align:center;padding: 5px;border: solid;border-radius: 7px'>" . ($orderList->is_paid ? '<strong>Paid</strong> with ' . __('webMessage.payment_' . $orderList->pay_mode) : 'Not Paid!') . "</td></tr>";
				$html .= "</table>";
				$barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode($orderList->order_id, $BRGenerator::TYPE_CODE_128, 2, 50));
				$html .= "<div style='width:100%;text-align:center;margin-top: 15px;margin-bottom: 15px;'><img src='" . $barcode . "'></div>";
				//$html .= '<br> MOBILE : ' . $orderList->mobile .' <br>Order Items: <br>'.$itemsHtml;
				//$html .= '<br> Delivery : ' . number_format($orderList->delivery_charges, 3) . ' ' . \App\Currency::default() ;
				//$html .= '<br> Total : '  . number_format($totalAmounts, 3) . ' ' . \App\Currency::default() ;
				$html .= '<div class="page-break"></div>';
			}
		}
		//return $html;
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8',
			'format' => 'A6',
			'default_font_size' => 12,
			'default_font' => 'verdana',
			'margin_left' => 5,
			'margin_right' => 5,
			'margin_top' => 5,
			'margin_bottom' => 10,
			'margin_header' => 10,
			'margin_footer' => 10
		]);

		$mpdf->SetAutoPageBreak(false);
		$mpdf->WriteHTML($html);
		return $mpdf->Output();
	}

	public function orderPaymentInfo($order_id)
	{
		// $order = Orders::where('order_id', $order_id)->first();
		$transaction = Transaction::where('trackid', $order_id)
			->orWhere('udf1', $order_id)
			->latest()->first();

		return view('gwc.orders.paymentStatus', compact('transaction'));
	}
}
