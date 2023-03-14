<?php

namespace App\Http\Controllers\Driver\Panel;

use App\OrdersDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function history(Request $request){
        $orders = OrdersDetails::orderByDesc('updated_at')
            ->whereNotIn('order_status' , ['pending' , 'outfordelivery', 'received'])
            ->Where('driver_id' , Auth::guard('driver')->id() )
            ->when($request->q != null , function ($query) use ($request){
                $query->where(function ($searchQ) use ($request){
                    $searchQ->where('id' , $request->q)
                        ->orWhere('order_id' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('name' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('street' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('avenue' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('house' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('block' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('mobile' , 'Like' , '%'.$request->q.'%');

                });
            })
            ->paginate();
        return view('driver.panel.order.index' , compact('orders' ));
    }


    public function assigned_task(Request $request){
        $orders = OrdersDetails::when($request->q != null , function ($query) use ($request){
                $query->where(function ($searchQ) use ($request){
                    $searchQ->where('id' , $request->q)
                        ->orWhere('order_id' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('name' , 'Like' , '%'.$request->q.'%');
                });
            })->when($request->barcode != null , function ($query) use ($request){
                $query->Where('order_id' , $request->barcode);
            })->where('driver_id' , auth('driver')->id() )
            ->whereIn('order_status' , ['outfordelivery','pending', 'received'])
            ->orderByDesc('delivery_date')
            ->orderBy('delivery_date')
            ->orderBy('created_at')
            ->with('area')
            ->get();
        return view('driver.panel.dashboard' , compact('orders'  ));
    }


}
