<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use App\Settings;
use App\Product;
use App\Orders;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class AdminMostViewedController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname", "setting")->first();
        $products = Product::orderBy('most_visited_count', 'desc')->paginate($settings->item_per_page_back);
        return view('gwc.mostviewed.index', compact('products'));
    }


    // //reset mostsold filtration
    // public function resetDateRange()
    // {
    //     Session::forget('mostso_filter_dates');
    //     return ["status" => 200, "message" => ""];
    // }



}
