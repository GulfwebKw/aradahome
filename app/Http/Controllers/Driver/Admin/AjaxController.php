<?php

namespace App\Http\Controllers\Driver\Admin;

use App\Driver;
use App\OrdersDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use PayPal\Api\Order;

class AjaxController extends Controller
{
    public function driver($driverId) {
        $driver = Driver::orderByDesc('created_at')
            ->where('id' , (int) filter_var($driverId, FILTER_SANITIZE_NUMBER_INT))
            ->firstOrFail();
        $driver->avatar = url('uploads/users/'.($driver->avatar ?? 'no-image.png'));
        $driver->fullname_en = $driver->fullname;
        $driver->fullname_ar = $driver->fullname_ar;
        $settingInfo = \App\Http\Controllers\webController::settings();
        $BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;
        $driver->barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$driver->id, $BRGenerator::TYPE_CODE_128 , 2 , 50)) ;
        $driver->DriverId = $settingInfo->prefix.'D'.$driver->id ;
        return response($driver, 200);
    }

    public function searchDriver(Request $request) {
        $drivers = Driver::orderByDesc('created_at')
            ->when($request->has('id') and ! is_null($request->id) , function ($query) use($request){
                $query->orWhere('id' , (int) filter_var($request->id, FILTER_SANITIZE_NUMBER_INT));
            })
            ->when($request->has('name') and ! is_null($request->name) , function ($query) use($request){
                $query->orWhere('first_name_en' , 'like' , '%'.$request->name.'%')
                    ->orWhere('first_name_ar' , 'like' , '%'.$request->name.'%')
                    ->orWhere('last_name_en' , 'like' , '%'.$request->name.'%')
                    ->orWhere('last_name_ar' , 'like' , '%'.$request->name.'%');
            })
            ->when($request->has('username') and ! is_null($request->username) , function ($query) use($request){
                $query->orWhere('username' , 'like' , '%'.$request->username.'%');
            })
            ->when($request->has('phone') and ! is_null($request->phone) , function ($query) use($request){
                $query->orWhere('phone' , 'like' , '%'.$request->phone.'%');
            })
            ->get();

        $settingInfo = \App\Http\Controllers\webController::settings();
        $BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;
        foreach ($drivers as $driver){
            $driver->avatar = url('uploads/users/'.($driver->avatar ?? 'no-image.png'));
            $driver->fullname_en = $driver->fullname;
            $driver->barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$driver->id, $BRGenerator::TYPE_CODE_128 , 2 , 50)) ;
            $driver->DriverId = $settingInfo->prefix.'D'.$driver->id ;
        }
        return response($drivers, 200);
    }

    public function order($orderId){
        $order = OrdersDetails::orderByDesc('created_at')
            ->where('order_id' , $orderId)
            ->whereIn('order_status' , ['pending','outfordelivery', 'received'] )
            ->with('area')
            ->firstOrFail();
        $settingInfo = \App\Http\Controllers\webController::settings();
        $BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;
        $order->hasLastDriver = (bool)$order->driver_id;
        $order->lastDriverId = $settingInfo->prefix.'D'.$order->driver_id ;
        $order->lastDriver = Driver::find($order->driver_id ) ;
        $order->invoiceUrl = url('en/order-print/'.$order->order_id_md5.'?driverSystem=1');
        $order->barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$order->id, $BRGenerator::TYPE_CODE_128 , 2 , 50)) ;
        return response($order, 200);

    }

    public function assign($orderID , $driverId = null ){
        if ( $driverId != null )
            $driver = Driver::orderByDesc('created_at')
                ->where('id' , (int) filter_var($driverId, FILTER_SANITIZE_NUMBER_INT))
                ->firstOrFail();

        $order = OrdersDetails::orderByDesc('created_at')
            ->where('order_id' ,$orderID)
            ->whereIn('order_status' , ['pending','outfordelivery', 'received'] )
            ->with('area')
            ->firstOrFail();


        $settingInfo = \App\Http\Controllers\webController::settings();
        $BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;

        $lastDriverId= $order->driver_id;
        $order->driver_id = $driverId != null ? $driver->id : null;
        $order->driver_assigner_id = Auth::id();
        $order->assigned_at = Carbon::now();
        $order->order_status = $driverId != null ? "outfordelivery" : "received";
        $order->save();

        $order->hasLastDriver = (bool)$lastDriverId;
        $order->lastDriverId = $settingInfo->prefix.'D'.$lastDriverId ;
        $order->lastDriver = Driver::find($lastDriverId) ;
        $order->invoiceUrl = url('en/order-print/'.$order->order_id_md5.'?driverSystem=1');
        $order->barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$order->id, $BRGenerator::TYPE_CODE_128 , 2 , 50)) ;

        if ( $order->driver_id != null )
            $driver = Driver::orderByDesc('created_at')->find($order->driver_id);
        else
            $driver = new Driver();
        $driver->avatar = url('uploads/users/'.($driver->avatar ?? 'no-image.png'));
        $driver->fullname_en = $driver->fullname;
        $driver->fullname_ar = $driver->fullname_ar;

        if ( $order->driver_id != null )
            $driver->barcode = 'data:image/png;base64,' . base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$driver->id, $BRGenerator::TYPE_CODE_128 , 2 , 50)) ;
        else
            $driver->barcode = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        $driver->DriverId = $settingInfo->prefix.'D'.$driver->id ;
        $driver->profile = route('driver.admin.driver.index' , ['q' => $driver->DriverId ]);

        $order->driver = $driver ;
        return response($order, 200);
    }
}
