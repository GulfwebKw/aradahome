<?php

namespace App\Http\Controllers;



use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use App\Settings;
use App\Orders;



class AdminDaysTimeController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname","setting")->first();

        //days of the week
        for ($i=0; $i<=6; $i++){
            $day[$i] = 0;
        }

        //times
        for ($j=0; $j<=23; $j++){
            $time[$j] = 0;
        }

        $orders = Orders::all();

        //apply date range filter
        if(!empty(Session::get('daystime_filter_dates'))){
            $explodeDates = explode("-",Session::get('daystime_filter_dates'));
            if(!empty($explodeDates[0]) && !empty($explodeDates[1])){
                $date1 = date("Y-m-d",strtotime($explodeDates[0]));
                $date2 = date("Y-m-d",strtotime($explodeDates[1]));
                $orders = $orders->whereBetween('created_at', [$date1, $date2]);
            }
        }

        foreach ($orders as $order){

            //calculating the day
            $date = \Carbon\Carbon::parse($order->created_at);
            $date = CarbonImmutable::parse($date);
            $weekday = $date->weekday();
            $day[$weekday]++;

            //calculating the time
            $hour = \Carbon\Carbon::parse($order->created_at)->format('H');
            $hour = (int) $hour;
            $time[$hour]++;
        }

        return view('gwc.daystime.index',['day'=>$day,'time'=>$time,'settings'=>$settings]);
    }


    //reset days time filtration
    public function resetDateRange()
    {
        Session::forget('daystime_filter_dates');
        return ["status"=>200,"message"=>""];
    }


    //store days time filtration values in cookie by ajax
    public function storeValuesInCookies(Request $request)
    {
        $minutes=3600;

        //date range
        if(!empty($request->daystime_dates)){
            Session::put('daystime_filter_dates', $request->daystime_dates);
        }

        return ["status"=>200,"message"=>""];
    }


}