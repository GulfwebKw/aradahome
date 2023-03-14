<?php

namespace App\Http\Controllers;



use App\OrdersDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use App\Settings;



class AdminShipmentController extends Controller
{
    public function index(Request $request)
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
        if (!empty(Session::get('shipment_filter_dates'))) {
            $explodeDates = explode("-", Session::get('shipment_filter_dates'));
            if (!empty($explodeDates[0]) && !empty($explodeDates[1])) {
                $date1 = date("Y-m-d", strtotime($explodeDates[0]));
                $date2 = date("Y-m-d", strtotime($explodeDates[1]));
                $orderLists = $orderLists->whereBetween('created_at', [$date1, $date2]);
            }
        }
        if (!empty(Session::get('shipment_filter_status')) && Session::get('shipment_filter_status') <> "all") {
            $orderLists = $orderLists->where('order_status', '=', Session::get('shipment_filter_status'));
        }

        $orderLists = $orderLists->orderBy('id', 'DESC')->paginate($settingInfo->item_per_page_back);

        $totalNumber = 0;
        $totalAmount = 0;
        $totalDelivery = 0;
        foreach ($orderLists as $orderList){
            $totalNumber++;
            $totalAmount += $orderList->total_amount;
            $totalDelivery += $orderList->delivery_charges;
        }

        return view('gwc.shipment.index', compact('orderLists', 'totalNumber', 'totalAmount', 'totalDelivery', 'settingInfo'));
    }


    //reset days time filtration
    public function resetDateRange()
    {
        Session::forget('shipment_filter_dates');
        Session::forget('shipment_filter_status');
        return ["status"=>200,"message"=>""];
    }


    //store days time filtration values in cookie by ajax
    public function storeValuesInCookies(Request $request)
    {
               //date range
        if(!empty($request->shipment_dates)){
            Session::put('shipment_filter_dates', $request->shipment_dates);
        }
        //order status
        if(!empty($request->shipment_status)){
            Session::put('shipment_filter_status', $request->shipment_status);
        }

        return ["status"=>200,"message"=>""];
    }


}