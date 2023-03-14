<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Settings;
use App\Country;
use App\Currency;
use Illuminate\Support\Facades\Auth;


class AdminCurrenciesController extends Controller
{



	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request) //Request $request
	{
		$baseCurrency = \App\Currency::default();
		$currencyLists = Currency::OrderBy('display_order')->get();
		
		return view('gwc.currencies.index', ['currencies' => $currencyLists, 'baseCurrency' => $baseCurrency]);
	}


	/**
	Display the Currency listings
	 **/
	public function create()
	{
		$lastOrderInfo = Currency::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}
		return view('gwc.currencies.create', compact('lastOrder'));
	}



	/**
	Store New Currency Details
	 **/
	public function store(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		//field validation
		$this->validate($request, [
			'code'         => 'required|string|unique:gwc_currencies,code',
			'symbol'        => 'nullable|string',
			'rate'       	=> 'required',
			'title_en'     => 'required|string',
			'title_ar'     => 'required|string',
		]);



		try {
			$currency = new Currency;
			$currency->code = $request->input('code');
			$currency->symbol = $request->input('symbol');
			$currency->rate = $request->input('rate');
			$currency->title_en = $request->input('title_en');
			$currency->title_ar = $request->input('title_ar');
			$currency->display_order = $request->input('display_order');
			$currency->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';

			$currency->save();

			//save logs
			$key_name   = "Currency";
			$key_id     = $currency->id;
			$message    = "New Currency record is added as (" . $request->input('name') . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/currencies')->with('message-success', 'Currency is added successfully');
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
		$editCurrency = Currency::find($id);
		return view('gwc.currencies.edit', compact('editCurrency'));
	}


	/**
	 * Show the details of the Currency.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$CurrencyDetails = Currency::find($id);
		$listCountries   = Country::where('parent_id', '0')->where('is_active', 1)->get();
		$listaddresss    = CurrencyAddress::where('Currency_id', $id)->get();
		return view('gwc.currencies.view', compact('CurrencyDetails', 'listCountries', 'listaddresss'));
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
			'code'         => 'required|string',
			'symbol'        => 'nullable|string',
			'rate'       	=> 'required',
			'title_en'     => 'required|string',
			'title_ar'     => 'required|string',
		]);



		try {
			$currency = Currency::find($id);

			$currency->code = $request->input('code');
			$currency->symbol = $request->input('symbol');
			$currency->rate = $request->input('rate');
			$currency->title_en = $request->input('title_en');
			$currency->title_ar = $request->input('title_ar');
			$currency->display_order = $request->input('display_order');
			$currency->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$currency->save();

			//save logs
			$key_name   = "Currency";
			$key_id     = $currency->id;
			$message    = "Currency details are updated for " . $request->input('name');
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs


			return redirect('/gwc/currencies')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}

	/**
	 * Delete Currency along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{

		//check param ID
		if (empty($id)) {
			return redirect('/gwc/currencies')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$currency = Currency::find($id);
		//check cat id exist or not
		if (empty($currency->id)) {
			return redirect('/gwc/currencies')->with('message-error', 'No record found');
		}

		//save logs
		$key_name   = "Currency";
		$key_id     = $currency->id;
		$message    = "Currency is removed for " . $currency->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		$currency->delete();
		return redirect()->back()->with('message-success', 'Currency is deleted successfully');
	}

	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Currency::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}
		//save logs
		$key_name   = "Currency";
		$key_id     = $recDetails->id;
		$message    = "Currency status is changed to " . $active . " for " . $recDetails->name;
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}
}
