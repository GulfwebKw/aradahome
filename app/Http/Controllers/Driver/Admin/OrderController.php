<?php

namespace App\Http\Controllers\Driver\Admin;

use App\Driver;
use App\OrdersDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function search(Request $request, $status = null){
        switch ($status) {
            case "pending":
                $status_show = "Pending";
                $fa_icon = "fa-cash-register";
                break;
            case "received":
                $status_show = "Received";
                $fa_icon = "fa-dolly";
                break;
            case "canceled":
                $status_show = "Canceled";
                $fa_icon = "fa-shopping-cart";
                break;
            case "completed":
                $status_show = "Completed";
                $fa_icon = "fa-handshake";
                break;
            case "returned":
                $status_show = "Returned";
                $fa_icon = "fa-thumbs-down";
                break;
            case "outfordelivery":
                $status_show = "Out For Delivery";
                $fa_icon = "fa-shipping-fast";
                break;
            default :
                $status_show = "All";
                $fa_icon = "fa-shopping-cart";
                break;
        }
        $driver = null ;
        if ($request->driver_id  )
            $driver = Driver::findOrFail($request->driver_id);
        $assignHistory = false ;
        if ( $request->has('print') ){
            $orders = $this->getOrders($request , false , false ,$status );
            return $this->print($request , $orders , $driver , false , $status_show   );
        } else
            $orders = $this->getOrders($request , true , false ,$status );
        return view('driver.order.index' , compact('orders' , 'driver' , 'assignHistory' , 'status' , 'fa_icon' , 'status_show' ));
    }


    public function assigned_history(Request $request){
        $driver = null ;
        if ($request->driver_id  )
            $driver = Driver::findOrFail($request->driver_id);
        $assignHistory = true ;
        if ( $request->has('print') ){
            $orders = $this->getOrders($request , false , true );
            return $this->print($request ,$orders , $driver , true  );
        } else
            $orders = $this->getOrders($request , true , true );
        return view('driver.order.index' , compact('orders' , 'assignHistory' , 'driver'));
    }

    private function print($request ,$orders , $driver ,$isAssign , $status = null ){
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
        @font-face {
            font-family: \'kufi\';
            src: url(\''.url('assets/css/kufi/kufi.eot?#iefix').'\') format(\'embedded-opentype\'),
            url(\''.url('assets/css/kufi/kufi.woff').'\') format(\'woff\'),
            url(\''.url('assets/css/kufi/kufi.ttf').'\')  format(\'truetype\'),
            url(\''.url('assets/css/kufi/kufi.svg#DroidArabicKufi').'\') format(\'svg\');
            font-weight: normal;
            font-style: normal;
        }
        .kufi {
            font-family: \'kufi\', sans-serif;
        }
</style>
';
        if ( $isAssign )
            $title = "Latest task ". ($driver != null ? " of ".$driver->fullname : ""  ) ;
        else
            $title = "List Of ".$status." Orders ". ($driver != null ? " of ".$driver->fullname : ""  ) ;
        $html .= "<div style='width:100%;text-align:center;font-size: 24px;margin-bottom: 15px;'><strong>".$title ."</strong></div>";
        $search = [];
//        if ( $driver  != null )
//            $search[] = "<strong>Driver:</strong> ".$driver->fullname;
//        if ( $status  != null )
//            $search[] = "<strong>Order Status:</strong> ".$status;
        if ( $request->q  != null )
            $search[] = "<strong>Search:</strong> ".$request->q;
        if ( $request->isPaid  != null )
            $search[] = "<strong>Payment Status:</strong> ".($request->isPaid ? "Paid" : "not Paid");
        if ( $request->between  != null )
            $search[] = "<strong>Date in:</strong> ".$request->between;
        $html .= "<div style='margin-bottom: 15px;'>".implode(",\t" , $search)."</div>";
        $html .= "<table style='width: 100%;margin-bottom: 15px;'><thead><tr style='text-align: left;'><th style='width: 35%'>Address</th><th style='width: 20%'>Order Id</th><th style='width: 5%'>Price</th><th style='width: 5%'>Payment Status</th><th style='width: 35%'>Comment</th></tr></thead><tbody>";
        $BRGenerator =  new \Picqer\Barcode\BarcodeGeneratorPNG;
        $TotalOrders = 0 ;
        $TotalAmount = 0 ;
        $TotalOnline  = 0 ;
        $TotalCOD  = 0 ;
        $TotalCODPaid  = 0 ;
        $TotalCODNotPaid  = 0 ;
        foreach ( $orders as $order) {
            $TotalOrders++;
            $address = "<strong class='".( $this->is_arabic($order->name) ? 'kufi' : '')."'>".$order->name."</strong><br>";
            if ( $order->area != null )
                $address .=  $order->area->name_en ."<br>";
            if ( $order->block != null )
                $address .=  "<strong>Block:</strong> <span class='".( $this->is_arabic($order->name) ? 'kufi' : '')."'>".$order->block ."</span>, ";
            if ( $order->street != null )
                $address .=  "<strong>Street:</strong> <span class='".( $this->is_arabic($order->name) ? 'kufi' : '')."'>".$order->street ."</span>, ";
            if ( $order->house != null )
                $address .=  "<strong>House/Apartment:</strong> <span class='".( $this->is_arabic($order->name) ? 'kufi' : '')."'>".$order->house ."</span>, ";
            if ( $order->mobile != null )
                $address .=  "<br><strong>Mobile:</strong> <span class='".( $this->is_arabic($order->name) ? 'kufi' : '')."'>".$order->mobile."</span>" ;
            if ( $order->is_express_delivery )
                $address .=  "<br><strong>Express Delivery</strong></span>" ;
            $totalAmounts = \App\Http\Controllers\AdminCustomersController::getOrderAmounts($order->id);
            $html .= "<tr><td>".$address."</td><td style='padding: 5px;text-align: center;'><img src=\"data:image/png;base64,".  base64_encode($BRGenerator->getBarcode( $order->order_id, $BRGenerator::TYPE_CODE_128 , 2 , 50))."\"><br><strong style='width: 100%; text-align: center;display: block;margin-top: 5px;'>".$order->order_id."</strong></td><td style='text-align: center;'>".\App\Currency::default().' '.number_format($totalAmounts,3)."</td><td style='text-align: center;'>".($order->is_paid ? 'PAID' : 'Not PAID').'<br>'.$order->pay_mode."</td><td>".$order->extra_comment."</td></tr>";
            $TotalAmount += $totalAmounts ;
            if ( $order->pay_mode != 'COD')
                $TotalOnline += $totalAmounts ;
            else {
                $TotalCOD += $totalAmounts;
                if( $order->is_paid )
                    $TotalCODPaid += $totalAmounts;
                else
                    $TotalCODNotPaid += $totalAmounts;
            }
        }
        $html .= "</tbody></table><div style='width : 100%'></div>";
        $html .= "<strong>Total Orders:</strong> ".$TotalOrders ." Order, <strong>Total Amount:</strong> ".$TotalAmount .' '. \App\Currency::default().",  <strong>Total Online:</strong> ".$TotalOnline.' '. \App\Currency::default();
        $html .= "<br><strong>Total COD:</strong> ".$TotalCOD.' '. \App\Currency::default().", <strong>Total COD (Paid):</strong> ".$TotalCODPaid .' '. \App\Currency::default().",  <strong>Total COD (Not paid):</strong> ".$TotalCODNotPaid.' '. \App\Currency::default();
        $html .="<script type=\"text/javascript\">
    setTimeout(function () { window.print(); }, 500);
//    setTimeout(function () { window.close(); }, 600);
</script>";
        return $html;
    }

    private function getOrders($request  , $paginate , $isAssign , $status = null ){
        $orders = OrdersDetails::when($request->q != null , function ($query) use ($request){
                $query->where(function ($searchQ) use ($request){
                    $searchQ->where('id' , $request->q)
                        ->orWhere('driver_id' , (int) filter_var($request->q, FILTER_SANITIZE_NUMBER_INT))
                        ->orWhere('order_id' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('name' , 'Like' , '%'.$request->q.'%')
                        ->orWhere('mobile' , 'Like' , '%'.$request->q.'%');
                });
            })->when($request->driver_id != null , function ($query) use ($request){
                $query->where('driver_id' , (int) filter_var($request->driver_id, FILTER_SANITIZE_NUMBER_INT));
            })->when($request->isPaid != null and $request->isPaid != "All" , function ($query) use ($request){
                $query->where('is_paid' , $request->isPaid);
            })->when($request->between != null , function ($query) use ($request , $isAssign){
                $explodeDates = explode("-",$request->between);
                if(!empty($explodeDates[0]) && !empty($explodeDates[1])){
                    $date1 = date("Y-m-d 00:00:00",strtotime($explodeDates[0]));
                    $date2 = date("Y-m-d 23:59:59",strtotime($explodeDates[1]));
                    $query->whereBetween( ( ($isAssign or $request->driver_id != null) ? 'assigned_at' : 'created_at' ) , [$date1, $date2]);
                }
            });
        if ( $isAssign ){
            $orders->whereIn('order_status' , ['pending' , 'outfordelivery', 'received'])
                    ->whereNotNull('assigned_at')
                    ->orderByDesc('assigned_at');
        } else {
            $orders->when($status != null , function ($query) use ($status , $request){
                $query->where('order_status' , $status);
                if ( $request->driver_id != null ){
                    $query->orderByDesc('assigned_at');
                    return ;
                }
                if ( $status == "pending" or $status == "received" or $status == "outfordelivery" )
                    $query->orderBy('delivery_date');
                else
                    $query->orderByDesc('updated_at');
            } , function ($query) use ( $request) {
                if ( $request->driver_id != null )
                    $query->orderByDesc('assigned_at');
                else
                    $query->orderByDesc('updated_at');
            });
        }
        if ( $paginate )
            return $orders->paginate();
        return $orders->get();
    }


    private function uniord($u) {
        // i just copied this function fron the php.net comments, but it should work fine!
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
    private function is_arabic($str) {
        if(mb_detect_encoding($str) !== 'UTF-8') {
            $str = mb_convert_encoding($str,mb_detect_encoding($str),'UTF-8');
        }

        /*
        $str = str_split($str); <- this function is not mb safe, it splits by bytes, not characters. we cannot use it
        $str = preg_split('//u',$str); <- this function woulrd probably work fine but there was a bug reported in some php version so it pslits by bytes and not chars as well
        */
        preg_match_all('/.|\n/u', $str, $matches);
        $chars = $matches[0];
        $arabic_count = 0;
        $latin_count = 0;
        $total_count = 0;
        foreach($chars as $char) {
            //$pos = ord($char); we cant use that, its not binary safe
            $pos = $this->uniord($char);
            //echo $char ." --> ".$pos.PHP_EOL;

            if($pos >= 1536 && $pos <= 1791) {
                $arabic_count++;
            } else if($pos > 123 && $pos < 123) {
                $latin_count++;
            }
            $total_count++;
        }
        if(($arabic_count/$total_count) > 0.6) {
            // 60% arabic chars, its probably arabic
            return true;
        }
        return false;
    }
}
