<?php

namespace App\Http\Controllers;

use App\OrdersDetails;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PaymentsLinkController extends Controller
{
    public function index(Request $request){
        $paymodelist = DB::table('gwc_orders_details')->groupBy('pay_mode')->get();
        $settingInfo = Settings::where("keyname", "setting")->first();
        //check search queries
        if (!empty($request->get('q'))) {
            $q = $request->get('q');
        } else {
            $q = $request->q;
        }
        $orderLists = OrdersDetails::where('linkDescription', '!=', '');
        //search keywords
        if (!empty($q)) {
            $orderLists = $orderLists->where(function ($sq) use ($q) {
                $sq->where('gwc_orders_details.name', 'LIKE', '%' . $q . '%')
                    ->orwhere('gwc_orders_details.email', 'LIKE', '%' . $q . '%')
                    ->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $q . '%')
                    ->orwhere('gwc_orders_details.order_id', 'LIKE', '%' . $q . '%')
                    ->orwhere('gwc_orders_details.customer_id', 'LIKE', '%' . $q . '%')
                    ->orwhere('gwc_orders_details.order_id_md5', 'LIKE', '%' . $q . '%');
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
        if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'paid') {
            $orderLists = $orderLists->where('gwc_orders_details.is_paid', '=', 1);
        }
        if (!empty(Session::get('pay_filter_status')) && Session::get('pay_filter_status') == 'notpaid') {
            $orderLists = $orderLists->where('gwc_orders_details.is_paid', '!=', 1);
        }
        if (!empty(Session::get('order_customers')) and false) {
            $orderLists = $orderLists->where('gwc_orders_details.customer_id', '=', Session::get('order_customers'));
        }
        if (!empty(Session::get('pay_mode'))) {
            $orderLists = $orderLists->where('gwc_orders_details.pay_mode', '=', Session::get('pay_mode'));
        }
        $orderLists = $orderLists->orderBy('gwc_orders_details.id', 'DESC')->paginate($settingInfo->item_per_page_back);
        $customersLists = [];

        return view('gwc.payment-links.index', compact('orderLists', 'settingInfo', 'customersLists', 'paymodelist'  ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        //  collect customers listing for dropdown
		$customersLists = User::get();
        return view('gwc.payment-links.create' , compact('customersLists') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'description' => 'required|string',
            'customer_id' => 'required|numeric|exists:gwc_customers,id',
            'price' => 'required|numeric|min:0|not_in:0',
        ]);
        $settingInfo = Settings::where("keyname", "setting")->first();
        $userDetails = User::where('id', $request->customer_id)->first();
        $ordersid = webCartController::OrderSidNumber();
        $orderid = strtolower($settingInfo->prefix) .'PL' . $ordersid;
        $payments = explode(",",$settingInfo->payments);
        $details = new OrdersDetails();
        $details->sid = $ordersid ;
        $details->customer_id = $userDetails->id;
        $details->order_id = $orderid;
        $details->order_id_md5 = md5($orderid);
        $details->name = $userDetails->name;
        $details->email = $userDetails->email;
        $details->mobile = $userDetails->mobile;
        $details->country_id = 0;
        $details->state_id = 0;
        $details->area_id = 0;
        $details->pay_mode = isset($payments[0]) ? $payments[0] : 'COD';
        $details->is_coupon_used = 0 ;
        $details->total_amount = $request->price ;
        $details->total_amount_dollar = 0 ;
        $details->is_paid = 0 ;
        $details->is_express_delivery = 0 ;
        $details->order_status = 'completed' ;
        $details->is_qty_rollbacked = 0 ;
        $details->is_removed = 0 ;
        $details->strLang = 'en' ;
        $details->device_type = 'web' ;
        $details->is_push_sent = 0 ;
        $details->is_for_dezorder = 0 ;
        $details->linkDescription = $request->description ;
        $details->save();
        // Unique order id fix
        $ordersid = $details->id;
        $orderid  = strtolower($settingInfo->prefix) . $ordersid;
        $details->order_id     = $orderid;
        $details->sid          = $ordersid;
        $details->order_id_md5 = md5($orderid);
        $details->save();
        //save logs
        $key_name   = "payment-links";
        $key_id     = $details->id;
        $message    = "A new Payment Link is added. ( #" . $details->linkDescription . " )";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs
        return redirect('/gwc/payment-links')->with('success', 'Link generate successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->create();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $details = OrdersDetails::findOrFail($id);
        if (  $details->is_paid  )
            abort(403);
        return view('gwc.payment-links.edit' , compact('details') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request , [
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|not_in:0',
        ]);
        $details = OrdersDetails::findOrFail($id);
        if (  $details->is_paid  )
            abort(403);
        $details->total_amount = $request->price ;
        $details->linkDescription = $request->description ;
        $details->save();
        //save logs
        $key_name   = "payment-links";
        $key_id     = $details->id;
        $message    = "A Payment link updated. ( #" . $details->id . " , ".number_format($details->total_amount,2) ." KD )";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs
        return redirect('/gwc/payment-links')->with('success', 'Link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $details = OrdersDetails::findOrFail($id);
        if (  $details->is_paid  )
            abort(403);
        $details->delete();

        //save logs
        $key_name   = "payment-links";
        $key_id     = $details->id;
        $message    = "A Payment link deleted. ( #" . $details->id . " )";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/payment-links')->with('success', 'Link deleted successfully.');
    }
    
}
