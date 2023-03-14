@extends('driver.include.master')
@section('title' , ! $assignHistory ? 'List '.$status_show. 'Orders' : 'Latest task')
@section('content')
    @php
        function uniord($u) {
        // i just copied this function fron the php.net comments, but it should work fine!
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
    function is_arabic($str) {
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
            $pos = uniord($char);
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
    @endphp
    <!--Begin::Row-->
    <div class="row fullPage-print">
        <div class="col-xl-12 fullPage-print">
            <div class="kt-portlet kt-portlet--height-fluid fullPage-print">
                <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm no-print">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            @if (! $assignHistory )
                                <i class="fa {{ $fa_icon }}"></i> List Of {{ $status_show }} Orders @if ( $driver != null ) of {{ $driver->fullname }} @endif
                            @else
                                <i class="fa fa-history"></i> Latest task @if ( $driver != null ) of {{ $driver->fullname }} @endif
                            @endif
                             ( Total items: {{ $orders->total() }})
                        </h3>
                    </div>
                    <form class="kt-quick-search__form" id="searchFormID" style="margin-left: 15px;" action="{{ route( ! $assignHistory ? 'driver.admin.orders.search' : 'driver.admin.orders.assigned_history' , [$status ?? null ]) }}">
                        <div class="kt-portlet__head-toolbar pt-3">
                            <button type="button" onclick="window.print();" class="btn btn-label-brand btn-bold" style="width: 140px;">
                                <i class="fa fa-print"></i> Print Page
                            </button>
                            <a type="button" target="_blank" href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" class="btn btn-label-brand btn-bold  ml-3" style="width: 160px;">
                                <i class="fa fa-list"></i> Print As List
                            </a>
                            <div class="input-group ml-3" style="max-width: 220px;">
                                <div class="input-group-prepend"><span class="input-group-text" onclick="$('#searchFormID').submit()"><i class="flaticon2-search-1"></i></span></div>
                                <input name="q" type="text" autocomplete="off"  autofocus value="{{ request()->q }}" class="form-control kt-quick-search__input" placeholder="Search..." >
                            </div>
                            <div class="btn-group ml-3">
                                <select class="form-control" onchange="this.form.submit()" name="isPaid">
                                    <option value="">{{__('adminMessage.all')}}</option>
                                    <option {{ request()->isPaid === '1' ? 'selected' : '' }} value="1">{{__('adminMessage.paid')}}</option>
                                    <option {{ request()->isPaid === '0' ? 'selected' : '' }} value="0">{{__('adminMessage.notpaid')}}</option>
                                </select>
                            </div>
                            <!-- filter date -->
                            <div class="input-group ml-3" style="max-width: 220px;">
                                <input name="between" onchange="this.form.submit()"  autocomplete="off"  id="kt_daterangepicker_range" type="text" value="{{ request()->between }}" class="form-control kt-quick-search__input" placeholder="Select Date Range">
                            </div>
                            <input name="driver_id" type="hidden" value="{{ request()->driver_id }}">
                        </div>
                    </form>
                </div>
                <div class="kt-portlet__body">

                    <table class="table table-striped- table-bordered table-hover table-checkable fullPage-print" id="kt_table_1">
                        <thead>
                        <tr>
                            <th style="width: 2%"  class="font-weight-bold">#</th>
                            <th style="width: 30%"  class="font-weight-bold">{{__('adminMessage.orderdetails')}}</th>
                            <th style="width: 44%"  class="font-weight-bold">{{__('adminMessage.customerdetails')}}</th>
                            <th style="width: 24%" class="font-weight-bold"></th>
                        </tr>
                        </thead>
                        <tbody>
                    @forelse($orders as $orderList)
                        @php
                            if(!empty($orderList->is_paid)){
                            $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success">'.__('adminMessage.yes').'</span>';
                            }else{
                            $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--danger">'.__('adminMessage.no').'</span>';
                            }

                            if(!empty($orderList->order_status) && $orderList->order_status=="pending"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning">'.$orderList->order_status.'</span>';
                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="received"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning">Received</span>';
                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="completed"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--success">'.$orderList->order_status.'</span>';
                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="canceled"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--danger">'.$orderList->order_status.'</span>';
                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="returned"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">'.$orderList->order_status.'</span>';
                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="outfordelivery"){
                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">'.$orderList->order_status.'</span>';
                            }

                            $totalAmounts = App\Http\Controllers\AdminCustomersController::getOrderAmounts($orderList->id);

                            $sellerDetails = App\Http\Controllers\AdminCustomersController::getCustomerDetails($orderList->customer_id);

                        @endphp
                        <tr class="search-body"  id="{{$orderList->order_id}}">
                            <td align="center">{{$loop->iteration}}
                                <br><br>

{{--                                <span style="overflow: visible; position: relative; width: 80px;">--}}
{{--                                                 <div class="dropdown">--}}
{{--                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>--}}
{{--                                                 <div class="dropdown-menu dropdown-menu-left">--}}
{{--                                                 <ul class="kt-nav">--}}

{{--                                                     <li class="kt-nav__item"><a target="_blank" href="{{url(app()->getLocale().'/order-print/'.$orderList->order_id_md5)}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-print"></i><span class="kt-nav__link-text">{{__('adminMessage.print')}}</span></a></li>--}}
{{--                                                     @if ( ! $orderList->is_paid )--}}
{{--                                                         <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_pay_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-link"></i><span class="kt-nav__link-text">Payment links</span></a></li>--}}
{{--                                                     @endif--}}
{{--                                                     @if(auth()->guard('admin')->user()->can('order-edit-status'))--}}
{{--                                                         <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_edit_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-edit"></i><span class="kt-nav__link-text">{{__('adminMessage.changeorderstatus')}}</span></a></li>--}}
{{--                                                     @endif--}}
{{--                                                 </ul>--}}
{{--                                                 </div>--}}
{{--                                                 </div>--}}
{{--                                                 </span>--}}

                                <!-- copy -->
                                <div id="copy-{{$orderList->id}}" style="display:none;text-align:left;">
                                    {{!empty($settingInfo->owner_name)?$settingInfo->owner_name:$settingInfo->name_en}} order - {{$orderList->pay_mode}}

                                    ORDER ID : {{$orderList->order_id}}

                                    NAME : <span class=" @if ( is_arabic($orderList->name) ) rtl @endif">{{$orderList->name}}</span>

                                    @if(!empty($orderList->country->name_en)){{$orderList->country->name_en}}@endif @if(!empty($orderList->area->name_en)){{$orderList->area->name_en}}@endif @if(!empty($orderList->block)) Block : {{$orderList->block}},@endif @if(!empty($orderList->street)) Street : {{$orderList->street}} ,@endif @if(!empty($orderList->house)) House : {{$orderList->house}} ,@endif @if(!empty($orderList->floor)) Foor : {{$orderList->floor}} ,@endif

                                    DELIVERY TIME : {{$orderList->delivery_time_en}}

                                    MOBILE : {{$orderList->mobile}}

                                    AMOUNT : {{\App\Currency::default().' '.number_format($totalAmounts,3)}}
                                    @if($orderList->is_express_delivery)
                                        Express Delivery
                                    @endif
                                </div>
                                <!-- end copy -->

                                <a target="_blank" title="{{__('adminMessage.print')}}" href="{{url(app()->getLocale().'/order-print/'.$orderList->order_id_md5)}}" class="btn btn-sm btn-clean btn-icon btn-icon-md no-print"><i class="kt-nav__link-icon flaticon2-print"></i></a>
                                @if ( ! $orderList->is_paid )
                                    <a href="javascript:;" title="Payment links" data-toggle="modal" data-target="#kt_modal_pay_{{$orderList->id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md no-print"><i class="kt-nav__link-icon fa fa-link"></i></a>
                                @endif
                                @if(auth()->guard('admin')->user()->can('order-edit-status'))
                                    <a href="javascript:;" title="{{__('adminMessage.changeorderstatus')}}" data-toggle="modal" data-target="#kt_modal_edit_{{$orderList->id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md no-print"><i class="kt-nav__link-icon flaticon-edit"></i></a>
                                @endif
                                <a href="javascript:;" title="Short Details" data-toggle="modal" data-target="#kt_modal_short_{{$orderList->id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md no-print"><i class="flaticon2-copy"></i></a>


                                <div class="modal fade" id="kt_modal_short_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Short Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-12">
                                                        {{!empty($settingInfo->owner_name)?$settingInfo->owner_name:$settingInfo->name_en}} order - {{$orderList->pay_mode}}
                                                        <br>ORDER ID : {{$orderList->order_id}}<br>NAME : <span class=" @if ( is_arabic($orderList->name) ) rtl @endif">{{$orderList->name}}</span><br>@if(!empty($orderList->area->name_en)){{$orderList->area->name_en}}@endif @if(!empty($orderList->block)) Block : {{$orderList->block}},@endif @if(!empty($orderList->street)) Street : {{$orderList->street}} ,@endif @if(!empty($orderList->block)) House : {{$orderList->house}} ,@endif @if(!empty($orderList->floor)) Foor : {{$orderList->floor}} ,@endif @if(!empty($orderList->delivery_time_en)) <br> DELIVERY TIME : {{$orderList->delivery_time_en}}@endif <br> MOBILE : {{$orderList->mobile}} <br> AMOUNT : {{\App\Currency::default().' '.number_format($totalAmounts,3)}}
                                                        @if($orderList->is_express_delivery)
                                                            <br> Express Delivery
                                                        @endif
                                                    <br>
                                                    <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode( $orderList->order_id, $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="kt_modal_pay_{{$orderList->id}}"
                                     tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Payment links</h5>
                                                <button type="button" class="close"
                                                        data-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-12">
                                                        @php
                                                            $payments = App\Country::getGateways($orderList->country_id);

                                                        @endphp
                                                        @if(count($payments) > 0)

                                                            <div class="row">
                                                                @php $paytxt=''; @endphp
                                                                @foreach($payments as $payment)
                                                                    @php
                                                                        if($payment=='COD'){
                                                                            continue;
                                                                        $paytxt = trans('webMessage.payment_COD');
                                                                        }else if($payment=='KNET'){
                                                                        $paytxt = trans('webMessage.payment_KNET');
                                                                        }else if($payment=='TPAY'){
                                                                        $paytxt = trans('webMessage.payment_TPAY');
                                                                        }else if($payment=='GKNET'){
                                                                        $paytxt = trans('webMessage.payment_GKNET');
                                                                        }else if($payment=='GTPAY'){
                                                                        $paytxt = trans('webMessage.payment_GTPAY');
                                                                        }else if($payment=='TAH'){
                                                                        $paytxt = trans('webMessage.payment_TAH');
                                                                        }else if($payment=='MF'){
                                                                        $paytxt = trans('webMessage.payment_MF');
                                                                        }else if($payment=='PAYPAL'){
                                                                        $paytxt = trans('webMessage.payment_PAYPAL');
                                                                        }else if($payment=='POSTKNET'){
                                                                        $paytxt = trans('webMessage.payment_POSTKNET');
                                                                        }else if($payment=='CS'){
                                                                        $paytxt = trans('webMessage.payment_CS');
                                                                        }else if($orderList->pay_mode=='MasterCard'){
                                                                        $paytxt = trans('webMessage.payment_MasterCard');
                                                                        }else if($payment=='Q8LINK'){
                                                                        $paytxt = trans('webMessage.payment_Q8LINK');
                                                                        }
                                                                    @endphp

                                                                    <div class="form-group col-lg-6" onclick="copyToClipboardLink('copyToClipboard_{{$payment}}_{{$orderList->order_id_md5}}');">
                                                                        <label><img
                                                                                    src="{{url('uploads/paymenticons/'.strtolower($payment).'.png')}}"
                                                                                    height="30" alt="{{__('webMessage.payment_'.$payment)}}">&nbsp;{{$paytxt}}</label>
                                                                        <input id="copyToClipboard_{{$payment}}_{{$orderList->order_id_md5}}"  class="form-control disabled" value="{{route('order.details.start.pay' , [\App\Country::getIsoById($orderList->country_id),'en',$orderList->order_id_md5,$payment])}}">
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal fade" id="kt_modal_dezorder_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{__('adminMessage.editorderstatus')}}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-8">
                                                        <label>{{__('adminMessage.movetodezorder')}}</label>
                                                    </div>
                                                    <div class="col-lg-4" align="right">
                                                        <span class="kt-switch"><label><input value="{{$orderList->id}}" {{!empty($orderList->is_for_dezorder)?'checked':''}} type="checkbox"  id="dezorder" class="change_status"><span></span></label></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--edit order status -->
                                <div class="modal fade" id="kt_modal_edit_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{__('adminMessage.editorderstatus')}}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-6">
                                                        <label>{{__('adminMessage.order_status')}}</label>
                                                        <select id="order_status{{$orderList->id}}" name="order_status" class="form-control">
                                                            <option value="pending"   @if($orderList->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>
                                                            <option value="received"   @if($orderList->order_status=='received') selected @endif>Received</option>
                                                            <option value="outfordelivery"   @if($orderList->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>
                                                            <option value="completed" @if($orderList->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option>
                                                            <option value="canceled" @if($orderList->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option>
                                                            <option value="returned" @if($orderList->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label>{{__('adminMessage.pay_status')}}</label>
                                                        <select id="pay_status{{$orderList->id}}" name="pay_status" class="form-control">
                                                            <option value="1" @if(!empty($orderList->is_paid)) selected @endif>{{__('adminMessage.paid')}}</option>
                                                            <option value="0" @if(empty($orderList->is_paid)) selected @endif>{{__('adminMessage.notpaid')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-lg-12"><textarea name="extra_comment" id="extra_comment{{$orderList->id}}" class="form-control">@if(!empty($orderList->extra_comment)){!!$orderList->extra_comment!!}@endif</textarea></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <span id="OrderStatusMsg{{$orderList->id}}"></span>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.cancel')}}</button>
                                                <button type="button" id="{{$orderList->id}}" class="btn btn-danger changeorderstatus">{{__('adminMessage.change')}}</button>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <table width="100%" class="table-borderless">
                                    <tr>
                                        <td width="150" class="font-weight-bold">{{trans('adminMessage.orderid')}}</td>
                                        <td class="font-weight-bold text-info">
                                            {{$orderList->order_id}}
                                        </td>
                                    </tr>
                                    @if(!empty($orderList->is_removed))
                                        <tr>
                                            <td class="font-weight-bold">{{trans('adminMessage.removed')}}</td>
                                            <td>
                                                <span class="kt-badge kt-badge--inline kt-badge--danger">{{__('adminMessage.removed')}}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="font-weight-bold">{{trans('adminMessage.total')}}</td>
                                        <td>{{number_format($totalAmounts,3)}}</td>
                                    </tr>
                                    <tr>
                                        <td  class="font-weight-bold">Driver</td>
                                        <td>
                                            @if ( $orderList->driver )
                                                <a  class="text-info" href="{{ route('driver.admin.driver.index' , ['q' => $settingInfo->prefix.'D'.$orderList->driver_id ]) }}" id="driver_of_{{$orderList->order_id}}">{{$orderList->driver->full_name}}</a>
                                                @if ( $orderList->order_status == "pending" or $orderList->order_status == "received" or $orderList->order_status == "outfordelivery" )
                                                    <button class="btn btn-link kt-pull-right" onclick="changeDriverOfOrder('{{$orderList->order_id}}')" >Change driver</button>
                                                @endif
                                            @else
                                                <a  class="text-info" href="#{{$orderList->order_id}}" id="driver_of_{{$orderList->order_id}}">--</a>
                                                @if ( $orderList->order_status == "pending" or $orderList->order_status == "received" or $orderList->order_status == "outfordelivery" )
                                                    <button class="btn btn-link kt-pull-right"  onclick="changeDriverOfOrder('{{$orderList->order_id}}')">Assign to any driver</button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">{{trans('adminMessage.orderstatus')}}</td>
                                        <td>{!!$orderStatus!!}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">{{trans('adminMessage.paymode_status')}}</td>
                                        @php
                                            if($orderList->pay_mode =='COD'){
                                            $paytxt = '<span class="kt-badge kt-badge--inline kt-badge--danger">'.trans('webMessage.payment_COD').'</span>';
                                            }else if($orderList->pay_mode=='KNET'){
                                            $paytxt = trans('webMessage.payment_KNET');
                                            }else if($orderList->pay_mode=='TPAY'){
                                            $paytxt = trans('webMessage.payment_TPAY');
                                            }else if($orderList->pay_mode=='GKNET'){
                                            $paytxt = trans('webMessage.payment_GKNET');
                                            }else if($orderList->pay_mode=='GTPAY'){
                                            $paytxt = trans('webMessage.payment_GTPAY');
                                            }else if($orderList->pay_mode=='TAH'){
                                            $paytxt = trans('webMessage.payment_TAH');
                                            }else if($orderList->pay_mode=='MF'){
                                            $paytxt = trans('webMessage.payment_MF');
                                            }else if($orderList->pay_mode=='PAYPAL'){
                                            $paytxt = trans('webMessage.payment_PAYPAL');
                                            }else if($orderList->pay_mode=='POSTKNET'){
                                            $paytxt = trans('webMessage.payment_POSTKNET');
                                            }else if($orderList->pay_mode=='CS'){
                                            $paytxt = trans('webMessage.payment_CS');
                                            }else if($orderList->pay_mode=='MasterCard'){
                                            $paytxt = trans('webMessage.payment_MasterCard');
                                            }else if($orderList->pay_mode=='Q8LINK'){
                                            $paytxt = trans('webMessage.payment_Q8LINK');
                                            }
                                        @endphp
                                        <td>{!! $paytxt!!}{!!$ispaid!!}</td>
                                    </tr>
                                </table>

                            </td>
                            <td>
                                <table width="100%"  class="table-borderless">
                                        <tr>
                                            <td  class="font-weight-bold">{{trans('adminMessage.name')}}</td>
                                            <td  colspan="3"><span class=" @if ( is_arabic($orderList->name) ) rtl @endif">{{$orderList->name}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{trans('adminMessage.mobile')}}</td>
                                            <td  @if(! $orderList->is_express_delivery) colspan="3" @endif>{{$orderList->mobile}}</td>
                                            @if($orderList->is_express_delivery)
                                                <td colspan="2"  class="font-weight-bold"> Express Delivery </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold" style="width: 10%">{{trans('adminMessage.country')}}</td>
                                            <td  style="width: 40%">{{$orderList->country ? $orderList->country->name_en : ""}}</td>
                                            <td class="font-weight-bold"  style="width: 10%">{{trans('adminMessage.area')}}</td>
                                            <td  style="width: 40%">{{$orderList->area? $orderList->area->name_en: ""}}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{trans('adminMessage.block')}}</td>
                                            <td>{{$orderList->block}}</td>
                                            <td class="font-weight-bold">{{trans('adminMessage.street')}}</td>
                                            <td>{{$orderList->street}}</td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-bold">{{trans('adminMessage.house')}}</td>
                                            <td>{{$orderList->house}}</td>
                                            <td class="font-weight-bold">{{trans('adminMessage.floor')}}</td>
                                            <td>{{$orderList->floor}}</td>
                                        </tr>
                                </table>


                            </td>
                            <td>
                                <table width="100%"  class="table-borderless">
                                    <tr>
                                        <td
                                            class="font-weight-bold">{{trans('adminMessage.createdon')}}</td>
                                        <td>{{$orderList->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td
                                            class="font-weight-bold">{{trans('adminMessage.updatedon')}}</td>
                                        <td>{{$orderList->updated_at}}</td>
                                    </tr>

                                    <tr>
                                        <td
                                            class="font-weight-bold">{{trans('adminMessage.delivery_date')}}</td>
                                        <td><font color="#ff0000">{{$orderList->delivery_date}}  {{$orderList->delivery_time_en}}</font></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold" title="Assign to driver at">
                                            Assign at
                                        </td>
                                        <td><font color="#ff0000">{{$orderList->assigned_at}}</font></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">
                                            Barcode
                                        </td>
                                        <td>
                                            <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode( $orderList->order_id, $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    {{__('adminMessage.recordnotfound')}}
                                </td>
                            </tr>
                    @endforelse

                    </tbody>
                </table>
                    <div class=" no-print">
                        {{ $orders->appends($_GET)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--End::Row-->
@endsection

@section('js')

    <script>
        function barcodeRead(barcode){
            $('.kt-quick-search__input').val(barcode);
            $('.kt-quick-search__form').submit();
        }

        function copyToClipboardLink(id) {
            var textBox = document.getElementById(id);
            textBox.select();
            document.execCommand("copy");
            toastr.success("Patyment Link Has Been Coppied");
        }

        function changeDriverOfOrder(orderId){
            functionSearch = function(item, index, arr) {
                $('#modalSearchResult').append("<tr><td>"+item.DriverId+"</td><td><img src='"+item.avatar+"' style='max-height: 32px;border-radius: 50%;margin-right: 5px;'> "+item.fullname_en+"</td><td>"+item.username+"</td><td>"+item.phone+"</td><td><button class='btn btn-primary' onclick='assginDriver(\""+orderId+"\",\""+item.DriverId+"\");$(\"#search_modal\").modal(\"hide\");$(\"#driverSearchModal\").resetForm();$(\"#modalSearchResult\").html(\"\");' >Assign to this Driver</td></tr>")
            }
            $('#search_modal_label').html('Change Driver #'+orderId);
            $('#search_modal').modal('show');
        }

        function assginDriver(orderID,driverId){
            $('#driver_of_'+orderID).attr('href' , '#').addClass('fa fa-spinner fa-spin text-warning').html('').removeClass('fa-exclamation-triangle');
            curl('{{ route('driver.admin.ajax.assign' , ['','']) }}/' + orderID + '/' +driverId  , function (result, data, httpCode) {
                if (result) {
                    $('#driver_of_'+orderID).attr('href' , data.driver.profile).removeClass('fa fa-spinner fa-spin text-warning').html(data.driver.fullname_en).addClass('alert alert-solid-success');
                    setTimeout(function() {
                        $('#driver_of_'+orderID).removeClass('alert alert-solid-success');
                    }, 5000 );
                } else {
                    $('#driver_of_'+orderID).removeClass('fa-spinner fa-spin').addClass('fa-exclamation-triangle');
                    alert("Order #" + orderID + " Can not assign to Driver #"+driverId+"." );
                }
            });
        }


        $(function() {
            $('#kt_daterangepicker_range').daterangepicker({
                opens: 'left',
                maxDate: '{{ date('m/d/Y') }}',
                autoUpdateInput: false,
            }, function(start, end, label) {
                $("#kt_daterangepicker_range").val( start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                $("#searchFormID").submit();
            });
        });



    </script>
@endsection