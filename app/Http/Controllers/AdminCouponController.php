<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use App\Settings;
use Auth;
use Carbon\Carbon;
use App\Exports\CouponCodeExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminCouponController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		$couponLists = Coupon::paginate($settingInfo->item_per_page_back);
		return view('gwc.coupon.index', ['couponLists' => $couponLists]);
	}


	/**
	Display the coupon listings
	 **/
	public function create()
	{
		return view('gwc.coupon.create');
	}


	public function createMultiple()
	{
		return view('gwc.coupon.create_multiple');
	}






	/**
	Store New coupon Details
	 **/
	public function store(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		$this->validate($request, [
			'title_en'      => 'required|min:3|max:190|string|unique:gwc_coupons,title_en',
			'title_ar'      => 'required|min:3|max:190|string|unique:gwc_coupons,title_ar',
			'coupon_code'   => 'required|min:3|max:50|string|unique:gwc_coupons,coupon_code',
			'coupon_type'   => 'required',
			'coupon_value'  => 'required',
			'start_date'    => 'required',
			'end_date'      => 'required',
			'price_start'   => 'required',
			'price_end'     => 'required',
			'usage_limit'   => 'required'
		]);

		if (strtotime($request->input('start_date')) > strtotime($request->input('end_date'))) {
			return redirect()->back()
				->withInput()
				->withErrors(['start_date' => 'Start Date should not be greater than End Date.']);
		}
		if ($request->input('price_start') >= $request->input('price_end')) {
			return redirect()->back()
				->withInput()
				->withErrors(['price_start' => 'Start Price should not be more than End Price.']);
		}


		try {

			$coupon = new Coupon;

			$coupon->title_en     = $request->input('title_en');
			$coupon->title_ar     = $request->input('title_ar');
			$coupon->coupon_code  = $request->input('coupon_code');
			$coupon->coupon_type  = $request->input('coupon_type');
			$coupon->coupon_value = $request->input('coupon_value');
			$coupon->start_date   = $request->input('start_date');
			$coupon->end_date     = $request->input('end_date');
			$coupon->price_start  = $request->input('price_start');
			$coupon->price_end    = $request->input('price_end');
			$coupon->usage_limit  = $request->input('usage_limit');
			$coupon->is_active    = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$coupon->is_free      = !empty($request->input('is_free')) ? $request->input('is_free') : '0';
			$coupon->is_zone_free      = (!empty($request->input('is_free')) and !empty($request->input('is_zone_free'))) ? $request->input('is_zone_free') : '0';
			$coupon->is_for      = !empty($request->input('is_for')) ? $request->input('is_for') : 'web';
			$coupon->save();

			//save logs
			$key_name   = "coupon";
			$key_id     = $coupon->id;
			$message    = "A new record for coupon is added. (" . $coupon->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/coupon')->with('message-success', 'A record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}



	public function storeMultipleCoupon(Request $request)
	{

		$this->validate($request, [
			'title_en'      => 'required|min:3|max:190|string',
			'title_ar'      => 'required|min:3|max:190|string',
			'qty_coupons'   => 'required|integer',
			'coupon_type'   => 'required',
			'coupon_value'  => 'required',
			'start_date'    => 'required',
			'end_date'      => 'required',
			'price_start'   => 'required',
			'price_end'     => 'required',
			'usage_limit'   => 'required'
		]);

		if (strtotime($request->input('start_date')) > strtotime($request->input('end_date'))) {
			return redirect()->back()
				->withInput()
				->withErrors(['start_date' => 'Start Date should not be greater than End Date.']);
		}
		if ($request->input('price_start') >= $request->input('price_end')) {
			return redirect()->back()
				->withInput()
				->withErrors(['price_start' => 'Start Price should not be more than End Price.']);
		}


		try {


			$couponQty = $request->input('qty_coupons');

			for ($i = 1; $i <= $couponQty; $i++) {
				$uniqueCode =  "T" . strval(substr(md5(mt_rand()), 0, 5));
				$coupon = new Coupon;
				$coupon->title_en     = $request->input('title_en');
				$coupon->title_ar     = $request->input('title_ar');
				$coupon->coupon_code  =  $uniqueCode;
				$coupon->coupon_type  = $request->input('coupon_type');
				$coupon->coupon_value = $request->input('coupon_value');
				$coupon->start_date   = $request->input('start_date');
				$coupon->end_date     = $request->input('end_date');
				$coupon->price_start  = $request->input('price_start');
				$coupon->price_end    = $request->input('price_end');
				$coupon->usage_limit  = $request->input('usage_limit');
				$coupon->is_active    = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
				$coupon->is_free      = !empty($request->input('is_free')) ? $request->input('is_free') : '0';
                $coupon->is_zone_free      = (!empty($request->input('is_free')) and !empty($request->input('is_zone_free'))) ? $request->input('is_zone_free') : '0';
                $coupon->is_for      = !empty($request->input('is_for')) ? $request->input('is_for') : 'web';
				$coupon->save();
			}


			//save logs
			$key_name   = "coupon";
			$key_id     = $coupon->id;
			$message    = "Multiple new record for coupon are added. (" . $request->input('title_en') . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/coupon')->with('message-success', 'Multiple record are added successfully');
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
		$editcoupon = Coupon::find($id);
		return view('gwc.coupon.edit', compact('editcoupon'));
	}


	/**
	 * Show the details of the coupon.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$couponDetails = Coupon::find($id);
		return view('gwc.coupon.view', compact('couponDetails'));
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

		//field validation  
		$this->validate($request, [
			'title_en'      => 'required|min:3|max:190|string|unique:gwc_coupons,title_en,' . $id,
			'title_ar'      => 'required|min:3|max:190|string|unique:gwc_coupons,title_ar,' . $id,
			'coupon_code'   => 'required|min:3|max:50|string|unique:gwc_coupons,coupon_code,' . $id,
			'coupon_type'   => 'required',
			'coupon_value'  => 'required',
			'start_date'    => 'required',
			'end_date'      => 'required',
			'price_start'   => 'required',
			'price_end'     => 'required',
			'usage_limit'   => 'required'
		]);



		try {



			$coupon = Coupon::find($id);

			$coupon->title_en     = $request->input('title_en');
			$coupon->title_ar     = $request->input('title_ar');
			$coupon->coupon_code  = $request->input('coupon_code');
			$coupon->coupon_type  = $request->input('coupon_type');
			$coupon->coupon_value = $request->input('coupon_value');
			$coupon->start_date   = $request->input('start_date');
			$coupon->end_date     = $request->input('end_date');
			$coupon->price_start  = $request->input('price_start');
			$coupon->price_end    = $request->input('price_end');
			$coupon->usage_limit  = $request->input('usage_limit');
			$coupon->is_active    = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$coupon->is_free      = !empty($request->input('is_free')) ? $request->input('is_free') : '0';
            $coupon->is_zone_free      = (!empty($request->input('is_free')) and !empty($request->input('is_zone_free'))) ? $request->input('is_zone_free') : '0';
            $coupon->is_for      = !empty($request->input('is_for')) ? $request->input('is_for') : 'web';

			$coupon->save();

			//save logs
			$key_name   = "coupon";
			$key_id     = $coupon->id;
			$message    = "Record for coupon is edited. (" . $coupon->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs
			return redirect('/gwc/coupon')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}


	/**
	 * Delete coupon along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/coupon')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$coupon = Coupon::find($id);
		//check cat id exist or not
		if (empty($coupon->id)) {
			return redirect('/gwc/coupon')->with('message-error', 'No record found');
		}

		//save logs
		$key_name   = "coupon";
		$key_id     = $coupon->id;
		$message    = "A record is removed. (" . $coupon->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$coupon->delete();
		return redirect()->back()->with('message-success', 'coupon is deleted successfully');
	}




	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Coupon::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "coupon";
		$key_id     = $recDetails->id;
		$message    = "coupon status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	public function exportCoupons()
	{
		$coupons = Coupon::orderBy('created_at', 'DESC')->get();

		// return	Excel::download($coupons, 'coupon-codes-' . Carbon::now()->today()->format('d-M-y') . '.xlsx',null,);

		// //    return Coupon::orderBy('id', 'DESC')->get()->downloadExcel(
		// //         'coupon-codes-'. Carbon::now()->today()->format('d-M-y') .'.xlsx',
		// //         $writerType = null,
		// //         $headings = false
		// //     );

		return Excel::download(new CouponCodeExport, 'coupon-codes-' . Carbon::now()->today()->format('d-M-y') . '.xlsx');
	}
}
