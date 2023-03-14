<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Settings;
use App\Product;
use App\Orders;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class AdminMostSoldController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname","setting")->first();
        $sales = [];
        $totalOrders = 0;
        $totalPrice = 0;

        $products = Orders::when(!empty(Session::get('mostsold_filter_dates')) , function ($query){
                $explodeDates = explode("-",Session::get('mostsold_filter_dates'));
                if(!empty($explodeDates[0]) && !empty($explodeDates[1])){
                    $date1 = date("Y-m-d",strtotime($explodeDates[0]));
                    $date2 = date("Y-m-d",strtotime($explodeDates[1]));
                    $query->whereBetween('gwc_orders.created_at', [$date1, $date2]);
                }
            })
            ->when( Session::has('payment_filter_status_Reports_MSO') , function ($query) {
                $query->join('gwc_orders_details' , 'gwc_orders.oid','gwc_orders_details.id');
                $query->where('gwc_orders_details.is_paid', Session::get('payment_filter_status_Reports_MSO') );

            })
            ->join('gwc_products' , 'gwc_orders.product_id','gwc_products.id')
            ->select('gwc_products.*' , DB::raw('SUM(gwc_orders.quantity) as soldQuantity') )
            ->groupBy('gwc_products.id')
            ->orderByDesc('soldQuantity')
            ->paginate($settings->item_per_page_back);

        $getAll = Orders::when(!empty(Session::get('mostsold_filter_dates')) , function ($query){
                $explodeDates = explode("-",Session::get('mostsold_filter_dates'));
                if(!empty($explodeDates[0]) && !empty($explodeDates[1])){
                    $date1 = date("Y-m-d",strtotime($explodeDates[0]));
                    $date2 = date("Y-m-d",strtotime($explodeDates[1]));
                    $query->whereBetween('gwc_orders.created_at', [$date1, $date2]);
                }
            })
            ->when( Session::has('payment_filter_status_Reports_MSO') , function ($query) {
                $query->join('gwc_orders_details' , 'gwc_orders.oid','gwc_orders_details.id');
                $query->where('gwc_orders_details.is_paid', Session::get('payment_filter_status_Reports_MSO') );

            })
            ->leftjoin('gwc_products' , 'gwc_orders.product_id','gwc_products.id')
            ->select( DB::raw('SUM(gwc_orders.quantity) as totalOrders , SUM(gwc_products.cost_price) as totalCost , SUM(gwc_orders.unit_price * gwc_orders.quantity) as totalPrice'))
            ->first();
        if ( $getAll != null ){
            $totalOrders = $getAll->totalOrders;
            $totalPrice = $getAll->totalPrice;
            $totalCost = $getAll->totalCost;
        }
        return view('gwc.mostsold.index',['products'=>$products, 'totalCost'=>$totalCost, 'totalOrders'=>$totalOrders, 'totalPrice'=>$totalPrice, 'settings'=>$settings]);
    }


    //reset mostsold filtration
    public function resetDateRange()
    {
        Session::forget('mostsold_filter_dates');
        return ["status"=>200,"message"=>""];
    }


    //store most sold filtration values in cookie by ajax
    public function storeValuesInCookies(Request $request)
    {
        $minutes=3600;

        //date range
        if(!empty($request->mostsold_dates)){
            Session::put('mostsold_filter_dates', $request->mostsold_dates);
        }

        return ["status"=>200,"message"=>""];
    }

}