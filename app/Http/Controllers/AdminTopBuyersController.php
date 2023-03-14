<?php

namespace App\Http\Controllers;



use App\Customers;
use App\OrdersDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use App\Settings;
use App\Orders;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class AdminTopBuyersController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname","setting")->first();
        $totalOrders = 0;
        $totalPrice = 0;
		
		$customerOrders = [];

        $orders = Orders::all();

        //apply date range filter
        if(!empty(Session::get('topbuyers_filter_dates'))){
            $explodeDates = explode("-",Session::get('topbuyers_filter_dates'));
            if(!empty($explodeDates[0]) && !empty($explodeDates[1])){
                $date1 = date("Y-m-d",strtotime($explodeDates[0]));
                $date2 = date("Y-m-d",strtotime($explodeDates[1]));
                $orders = $orders->whereBetween('created_at', [$date1, $date2]);
            }
        }

        //calculating orders for each customer
        foreach($orders as $order){
            $order_id = $order->order_id;
            $orderDetails = OrdersDetails::where('order_id', $order_id)->first();
            if ( $orderDetails == null ) break;
            $customer_id = $orderDetails->customer_id;
            if ($customer_id){
                //calculating total number of orders for the customer
                if (isset($customerOrders[$customer_id])){
                    $customerOrders[$customer_id] += 1;
                }
                else{
                    $customerOrders[$customer_id] = 1;
                }
                //calculating total amount of orders for the customer
                if (isset($customerAmount[$customer_id])){
                    $customerAmount[$customer_id] += ($order->quantity * $order->unit_price);
                }
                else{
                    $customerAmount[$customer_id] = ($order->quantity * $order->unit_price);
                }
            }

            //$totalOrders += $order->count();
            //$totalPrice += $order->unit_price * $order->quantity;
        }

        //sorting customer orders descending according to their number of orders
        arsort($customerOrders);

        //making an array for customers and their order quantity
        $customers = [];
        foreach($customerOrders as $customer_id => $quantity){
            $customer = Customers::find($customer_id);
            if($customer){
                $customers[] = [
                    'customer' => $customer,
                    'orders' => $quantity,
                    'amount' => $customerAmount[$customer_id]
                ];
				
				$totalOrders += $quantity;
				$totalPrice += $customerAmount[$customer_id];
            }
        }

        //making a collection from the array and paginating the array
        $page = null;
        $perPage = $settings->item_per_page_back;
        $options = ['path' => $request->url(), 'query' => $request->query()];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $customers instanceof Collection ? $customers : Collection::make($customers);
        $customers = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        return view('gwc.topbuyers.index',['customers'=>$customers, 'totalOrders'=>$totalOrders, 'totalPrice'=>$totalPrice, 'settings'=>$settings]);
    }


    //reset mostsold filtration
    public function resetDateRange()
    {
        Session::forget('topbuyers_filter_dates');
        return ["status"=>200,"message"=>""];
    }


    //store most sold filtration values in cookie by ajax
    public function storeValuesInCookies(Request $request)
    {
              //date range
        if(!empty($request->topbuyers_dates)){
            Session::put('topbuyers_filter_dates', $request->topbuyers_dates);
        }

        return ["status"=>200,"message"=>""];
    }

}