<?php

namespace App\Http\Controllers;

use App\AdminPos;
use App\CashLog;
use App\OrdersDetails;
use App\Settings;
use App\Transaction;
use App\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminPOSController extends Controller
{
    protected $settingInfo ;

    protected function setting(){
        $this->settingInfo = Settings::where("keyname", "setting")->first();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function cash(Request $request)
    {
        self::setting();
        $cashs = CashLog::when(!empty($request->get('q')) , function ($query) use ($request){
                $query->where("description" , "like" , "%".$request->get('q')."%");
            })->when(!empty($request->type) , function ($query) use ($request){
                $query->where("type" , $request->type );
            })->when(!empty($request->shift_id) , function ($query) use ($request){
                $query->where("shift_id" , $request->shift_id );
            })->when(!empty($request->pos_id) , function ($query) use ($request){
                $query->where("pos_id" , $request->pos_id );
            })->when(!empty($request->filter_dates) , function ($query) use ($request){
                $explodeDates = explode("-", $request->filter_dates);
                if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
                    $date1 = date("Y-m-d", strtotime($explodeDates[0]));
                    $date2 = date("Y-m-d", strtotime($explodeDates[1]));
                    $query->whereBetween('created_at', [$date1, $date2]);
                }
            })
            ->with('pos:username,id' , 'shift:start,ended' , 'reference')
//            ->orderByDesc('created_at')
            ->orderByDesc('shift_id')
            ->paginate($this->settingInfo->item_per_page_back);
        $settings = $this->settingInfo;
        $poss = AdminPos::all();
        return view('gwc.pos.cashs' , compact('cashs' , 'settings', 'poss'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function days(Request $request)
    {
        self::setting();
        $days = WorkTime::when(!empty($request->pos_id) , function ($query) use ($request){
                $query->where("pos_id" , $request->pos_id );
            })->when(!empty($request->contradictionCashOfSystem) , function ($query) use ($request){
                if ( $request->contradictionCashOfSystem != "all")
                    $query->where(function ($query) use($request){
                        $query
    //                    ->where("contradictionCashOfSystem" , $request->contradictionCashOfSystem , 0 )
                        ->orWhere("contradictionCountCash" , $request->contradictionCashOfSystem , 0 )
                        ->orWhere("contradictionCountCard" , $request->contradictionCashOfSystem , 0 );
                    });
            })->when(!empty($request->filter_dates) , function ($query) use ($request){
                $explodeDates = explode("-", $request->filter_dates);
                if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
                    $date1 = date("Y-m-d", strtotime($explodeDates[0]));
                    $date2 = date("Y-m-d", strtotime($explodeDates[1]));
                    $query->where( function ($query) use($date1,$date2){
                        $query->whereBetween('start', [$date1, $date2])
                            ->orWhereBetween('ended', [$date1, $date2]);
                    });
                }
            })
            ->with('pos:username,id')
            ->with('lastCashRelation')
            ->orderByDesc('start')
            ->paginate($this->settingInfo->item_per_page_back);
        $settings = $this->settingInfo;
        $poss = AdminPos::all();
        return view('gwc.pos.days' , compact('days' , 'settings', 'poss'));
    }

    public function transactions(Request $request , $date1 = null  , $date2 = null ,$pos_id = null)
    {
        self::setting();
        $settingInfo = $this->settingInfo;
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
            ->when(!empty($request->get('q')) , function ($query) use($request) {
                $query->where(function ($sq) use ($request) {
                    $sq->where('gwc_orders_details.name', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('gwc_orders_details.email', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('gwc_orders_details.mobile', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('payment_id', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('trackid', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('tranid', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('ref', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('auth', 'LIKE', '%' . $request->input('q') . '%');
                    $sq->orwhere('presult', 'LIKE', '%' . $request->input('q') . '%');
                });
            })->when(!empty(Session::get('payment_filter_status')) , function ($query) use($request) {
                if ( Session::get('payment_filter_status') == 'paid') {
                    $query->where('presult', 'CAPTURED');
                } elseif ( Session::get('payment_filter_status') == 'notpaid') {
                    $query->where('presult', '!=', 'CAPTURED');
                } elseif ( Session::get('payment_filter_status') == 'release') {
                    $query->where('release_pay', '=', 1);
                } elseif ( Session::get('payment_filter_status') == 'nrelease') {
                    $query->where('release_pay', '!=', 1);
                }
            })
            ->when( $date1 and $date2 , function ($query) use ($date2,$date1){
                $query->whereBetween('gwc_transaction.created_at', [$date1 , $date2]);
            })
            ->when( $pos_id , function ($query) use ($pos_id) {
                $query->where('gwc_orders_details.pos_id', $pos_id);
            })->when( ! $date1 and ! $date2 and ! $pos_id , function ($query){
                    $query->whereNotNull('gwc_orders_details.pos_id');
            })->orderBy('gwc_transaction.created_at', 'DESC')
            ->paginate($settingInfo->item_per_page_back);
        return view('gwc.orders.payments', ['paymentLists' => $paymentsLists, 'settingInfo' => $settingInfo]);

    }




    public function orders (Request $request , $date1 = null  , $date2 = null ,$pos_id = null){

        $paymodelist = collect([
            (object) ['pay_mode' => 'split'],
            (object) ['pay_mode' => 'PCOD'],
            (object) ['pay_mode' => 'PCARD'],
            (object) ['pay_mode' => 'PKNET'],
            (object) ['pay_mode' => 'PVISA'],
        ]);

        $settingInfo = Settings::where("keyname", "setting")->first();

        //check search queries
        if (!empty($request->get('q'))) {
            $q = $request->get('q');
        } else {
            $q = $request->q;
        }

        $orderLists = OrdersDetails::with('area')->where('order_status', '!=', '')
            ->when( $date1 and $date2 , function ($query) use ($date2,$date1){
                $query->whereBetween('gwc_orders_details.created_at', [$date1 , $date2]);
            })
            ->when( $pos_id , function ($query) use ($pos_id){
                $query->where('pos_id',  $pos_id );
            })->when( ! $date1 and ! $date2 and ! $pos_id , function ($query){
                $query->whereNotNull('pos_id');
            });
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
            $orderLists = $orderLists->where('gwc_orders_details.pay_mode', Session::get('pay_mode'));
        }
        $orderLists2 = $orderLists;
        $orderLists = $orderLists->orderBy('gwc_orders_details.id', 'DESC')
            ->paginate($settingInfo->item_per_page_back);

        if (!empty($request->pmode)) {
            $orderLists->appends(['pmode' => $request->pmode]);
        }
        //collect customers listing for dropdown
        $customersLists = DB::table('gwc_orders_details')
            ->select('gwc_orders_details.customer_id', 'gwc_customers.id', 'gwc_orders_details.name')
            ->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_orders_details.customer_id')
            ->GroupBy('gwc_orders_details.customer_id')
            ->get();


        $orderLists2->join('gwc_orders' , 'gwc_orders.oid','gwc_orders_details.id');
        $orderLists2->groupby('gwc_orders_details.id');
        $orderLists2->select(\Illuminate\Support\Facades\DB::raw('SUM(gwc_orders_details.delivery_charges) / COUNT(`gwc_orders_details`.`id`) as delivery_charges , SUM(gwc_orders.unit_price * gwc_orders.quantity) as total_order, SUM(gwc_orders_details.total_amount) / COUNT(`gwc_orders_details`.`id`) as totalPrice'));
        $totals = DB::query()
            ->fromSub($orderLists2->getQuery() , 'a')
            ->select(\Illuminate\Support\Facades\DB::raw('SUM(delivery_charges) as delivery_charges , SUM(total_order) as total_order, SUM(totalPrice) as totalPrice'))
            ->forPage(1, 1)
            ->first();
        $totalOrders = $totalPrice = $totalDelivery = 0 ;
        if ( $totals != null ){
            $totalDelivery = $totals->delivery_charges;
            $totalOrders = $totals->total_order;
            $totalPrice = $totals->totalPrice;
        }
        return view('gwc.orders.index', compact('orderLists', 'settingInfo', 'customersLists', 'paymodelist' , 'totalDelivery' , 'totalOrders' , 'totalPrice' ));
    }
}
