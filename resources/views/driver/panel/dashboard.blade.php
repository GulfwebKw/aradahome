@extends('driver.include.master')
@section('title' , 'My task')

@section('header')
    <style>
        @media (max-width: 1024px) {
            .kt-menu__link i {
                margin-right: 5px;
                margin-top: 7px;
            }
            .kt-footer{
                padding: 0px;
            }
        }
        @media (min-width: 1025px) {
            .kt-menu__link i {
                margin-right: 5px;
                margin-top: 3px;
            }
        }
        @keyframes shake {
            0% { transform: translate(0px, 0px) rotate(313deg); }
            10% { transform: translate(0px, 0px) rotate(310deg); }
            20% { transform: translate(0px, 0px) rotate(305deg); }
            30% { transform: translate(0px, 0px) rotate(300deg); }
            40% { transform: translate(0px, 0px) rotate(305deg); }
            50% { transform: translate(0px, 0px) rotate(310deg); }
            60% { transform: translate(0px, 0px) rotate(313deg); }
            70% { transform: translate(0px, 0px) rotate(318deg); }
            80% { transform: translate(0px, 0px) rotate(325deg); }
            90% { transform: translate(0px, 0px) rotate(318deg); }
            100% { transform: translate(0px, 0px) rotate(313deg); }
        }
        @media (max-width: 1024px) {
            .lessImportantTD {
                display: none !important;
            }
        }


        #button-background {
            position: relative;
            background-color: rgba(85, 120, 234, 0.5);
            width: 100%;
            height: 50px;
            border: white;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #slider {
            transition: width 0.3s, border-radius 0.3s, height 0.3s;
            position: absolute;
            left: -10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #slider.unlocked {
            transition: all 0.3s;
            width: inherit;
            left: 0 !important;
            height: inherit;
            border-radius: inherit;
        }
        .material-icons {
            color: black;
            font-size: 50px;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: default;
        }
        .slide-text {
            color: #3a3d55;
            font-size: 24px;
            text-transform: uppercase;
            font-family: "Roboto", sans-serif;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: default;
        }

    </style>

@endsection


@section('content')
    <!--Begin::Row-->
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <i class="fa fa-shipping-fast"></i> My Task
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <form class="kt-quick-search__form" style="margin-left: 15px;" action="{{ route('driver.panel.dashboard') }}">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="flaticon2-search-1"></i></span></div>
                                <input name="q" type="text" autofocus value="{{ request()->q }}" class="form-control kt-quick-search__input" placeholder="Search...">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="kt-portlet__body">


                    <!--begin::Widget 11-->
                    <div class="kt-widget11">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped">
                                <thead>
                                <tr>
                                    <td>Order Id</td>
                                    <td class="lessImportantTD">Customer</td>
                                    <td class="lessImportantTD">Paid</td>
                                    <td class="lessImportantTD">Deliver at</td>
                                    <td>Area</td>
                                    <td class="lessImportantTD">Block</td>
                                    <td class="lessImportantTD">Street</td>
                                    <td class="lessImportantTD">Avenue</td>
                                    <td class="lessImportantTD">House</td>
                                    <td class="lessImportantTD">Floor</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    @php
                                        if(!empty($order->order_status) && $order->order_status=="pending"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning">Pending</span>';
                                        }elseif(!empty($order->order_status) && $order->order_status=="received"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning">Received</span>';
                                        }elseif(!empty($order->order_status) && $order->order_status=="completed"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--success">Completed</span>';
                                        }elseif(!empty($order->order_status) && $order->order_status=="canceled"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--danger">Canceled</span>';
                                        }elseif(!empty($order->order_status) && $order->order_status=="returned"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">Returned</span>';
                                        }elseif(!empty($order->order_status) && $order->order_status=="outfordelivery"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">Out For Delivery</span>';
                                        }

                                        $totalAmounts = App\Http\Controllers\AdminCustomersController::getOrderAmounts($order->id);
                                        $totalAmounts = number_format($totalAmounts,3). ' '. \App\Currency::default();
                                    @endphp
                                    <tr>
                                        <td>#{{$order->order_id}}</td>
{{--                                        <td class="lessImportantTD">@if($order->mobile) <a href="tel:{{$order->mobile}}"> <i class="fa fa-phone-volume" style="animation: shake 0.35s;animation-iteration-count: infinite;"></i> @endif {{ $order->name ?? 'Unknown' }} @if($order->mobile) </a> @endif</td>--}}
                                        <td class="lessImportantTD">
                                            @if($order->mobile)
                                                <a href="tel:{{$order->mobile}}">
                                                    <i class="fa fa-phone-volume" style="animation: shake 0.35s;animation-iteration-count: infinite;"></i>
                                            @endif
                                            {{ $order->mobile ?? 'Unknown' }}
                                            @if($order->mobile)
                                                </a>
                                                <span class="ml-5">
                                                    <a href="https://api.whatsapp.com/send?phone={{$order->mobile}}" target="_blank">
                                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAADe0lEQVQ4jb2UTWwbRRzF30x2vbuuXXuTuE6dOP0wtWtMg0rKhzggVIhUpYgeUFPEpYlQe0CqCAckkHJCIEDigIqAA4oEB4KEyKkUpKg9UKRiqUlKUsWJm4YkcmuSOrG9jr3r9X4MB8tL2mIQEmKO/3nzezPvPzPAfzzI300yxujs/NLDvOR6hACkptVu9MQjKUKI/a+As7Or8s5W4bwoCn073ELAxXEUAGqmaVdUPafp+mW1aJxLJML5fwSmVzL9Pq/nq4Df165YCpL5KWS0LAAgLIXwpNwLP+9DrqBslMqVoeieru+bAtMrmf5Aq/87r0eURlfHMJ69CMM27jHkKY+XQsfx6p5XsFWuahsFZWA71AFeX1727/b6b7a1egNvz72LyeJMs5gAAI/5e/BhYgSbhfLG5lo51jg+bQhkXjwfkH2B0dUxTBZnkPDG8OaB1+Ci/F8Cp4uzGF0dQ0D2tbv9/CeNOgUAxhiRJKGvZJUwnq3vfiT2BvqDz+PZ9qeb7nI8exGKqUAShOcYY9QBXr91K+4WxV3J/JSTmezyAwCinkhToGEbSOansEMSAjcWfks4QInyB3meo7e13x3x1fw1AMCFtYmmQADIaFm4eI62CFzcAQKkxhjAUc4Rfr78JYpGCcORs+C35Ujuu2kUFAwAZcxygIalp2qGYYalkCPM6Zt4Z+EjxL0H8GnP+zjoeQhD3S/j6yOf4YWOPkcXdodgGKat28ZC3RD1J7a+Wch4fEJo4NoZqJbmLEh4Y3greg5d28xK5hZOJE/DRXl8+/gXqJastWCbHCKE1DtDCLFVrfqD1CLhWPDoPUea20pjaHoY76U/xuXcz/glP4mR1AcAgIHOE/C2eKBW9QlCCAMAJzSbQQEAzao+ELzJTFzKXcGl3BWn9oR8GIPdp5DLF+8qRvX1Rt0BiqLrGUoIZpQ5POpLYLcYxE8bVx8wEKgLJztfxGD3KShbmqqU1cHD+/YV/2wagMXFRcHXFlhrk3f6DbsGgQpgAFRTRbJQ/xxsZqNT6sBTci+8LR7czRfXSxV1KLY3/ON2QwIAqaWV410duy5YllWr6sYd3TB+tW274haFo5IoBF08xxEQ1EzTUrXqeqWqTxR0dfhIJKLcHw8BgPml21FC0W+Ua98cOrR/vTHJGKPTqaX9gsTHqc041WLzvdG96UYD/pfxByXzbaDAW0V5AAAAAElFTkSuQmCC">
                                                    </a>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="lessImportantTD">
                                            {{ $totalAmounts  }}
                                            @if(!empty($order->is_paid))
                                                <span class="ml-5 kt-badge kt-badge--inline kt-badge--success">{{__('adminMessage.yes')}}</span>
                                            @else
                                                <span class="ml-5 kt-badge kt-badge--inline kt-badge--danger">{{__('adminMessage.no')}}</span>
                                            @endif
                                        </td>
                                        <td class="lessImportantTD">{{$order->delivery_date}} @if ($order->delivery_time_en) at {{$order->delivery_time_en}} @endif</td>
                                        <td>{{$order->area ? $order->area->name_en : '--'}}</td>
                                        <td class="lessImportantTD">{{$order->block}}</td>
                                        <td class="lessImportantTD">{{$order->street}}</td>
                                        <td class="lessImportantTD">{{$order->avenue}}</td>
                                        <td class="lessImportantTD">{{$order->house}}</td>
                                        <td class="lessImportantTD">{{$order->floor}}</td>
                                        <td class="kt-datatable__cell kt-align-right">
                                            <button onclick="openShortView( {{ ($order->order_status=="pending" or $order->order_status=="received" or $order->order_status=="outfordelivery") ? '1' : '0' }} , '{{$order->order_id}}','{{ $orderStatus }}', '{{$totalAmounts}}','{{$order->mobile}}','{{ $order->name ?? 'Unknown' }}',{{ (int) !empty($order->is_paid) }},'{{ $order->delivery_date }}','{{$order->delivery_time_en}}','{{$order->area ? $order->area->name_en : '--'}}','{{$order->block}}','{{$order->street}}','{{$order->avenue}}','{{$order->house}}','{{$order->floor}}' , '{{ url('en/order-print/'.$order->order_id_md5.'?driverSystem=1') }}' , '{{ $order->extra_comment }}');" class="btn btn-link kt-hidden-desktop1">View</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>

                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--end::Widget 11-->

                </div>
            </div>
        </div>
    </div>

    <!--End::Row-->
@endsection


@section('js')

    @forelse($orders as $order)
    <!--begin::Modal-->
    <div class="modal fade" id="kt_modal_edit_{{$order->order_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="false">
        <div class="modal-dialog modal-lg" role="document"  style="height: 98%;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <h5 class="modal-title" >{{__('adminMessage.editorderstatus')}}</h5>
                    <button class="btn btn-outline-primary kt-pull-right" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>{{__('adminMessage.order_status')}}</label>
                            <select id="order_status{{$order->id}}" name="order_status" class="form-control">
                                <option value="outfordelivery"   @if($order->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>
                                <option value="completed" @if($order->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option>
                                @if( env('DRIVER_CAN_CANCEL' , true) )
                                <option value="canceled" @if($order->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option>
                                @endif
                                <option value="returned" @if($order->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>
                            </select>
                        </div>
                        @if( ( $order->is_paid and  $order->pay_mode == 'COD' ) or ( ! $order->is_paid ) )
                        <div class="col-lg-6">
                            <label>{{__('adminMessage.pay_status')}}</label>
                            <select id="pay_status{{$order->id}}" name="pay_status" class="form-control">
                                <option value="1" @if(!empty($order->is_paid)) selected @endif>{{__('adminMessage.paid')}}</option>
                                <option value="0" @if(empty($order->is_paid)) selected @endif>{{__('adminMessage.notpaid')}}</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12"><textarea name="extra_comment" id="extra_comment{{$order->id}}" class="form-control">@if(!empty($order->extra_comment)){!!$order->extra_comment!!}@endif</textarea></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div id="OrderStatusMsg{{$order->id}}" style="display: none;" class=" alert w-100 text-center alert-solid-success"></div>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.cancel')}}</button>
                    <button type="button" id="{{$order->id}}" class="btn btn-danger changeorderstatus">{{__('adminMessage.change')}}</button>


                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->
    <div class="modal fade" id="kt_modal_pay_{{$order->order_id}}"
         tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"  style="height: 98%;">
            <div class="modal-content" style="height: 100%;">
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
                                $payments = App\Country::getGateways($order->country_id);

                            @endphp
                            @if(count($payments) > 0)

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
                                            }else if($payment=='MasterCard'){
                                            $paytxt = trans('webMessage.payment_MasterCard');
                                            }else if($payment=='Q8LINK'){
                                            $paytxt = trans('webMessage.payment_Q8LINK');
                                            }
                                        @endphp

                                        <div class="row">
                                            <div class="form-group col-6 mt-5" onclick="copyToClipboardLink('copyToClipboard_{{$payment}}_{{$order->order_id_md5}}');">
                                                <label><img
                                                            src="{{url('uploads/paymenticons/'.strtolower($payment).'.png')}}"
                                                            height="30" alt="{{__('webMessage.payment_'.$payment)}}">&nbsp;{{$paytxt}}</label>
                                                <input id="copyToClipboard_{{$payment}}_{{$order->order_id_md5}}"  class="form-control disabled" value="{{route('order.details.start.pay' , [\App\Country::getIsoById($order->country_id),'en',$order->order_id_md5,$payment])}}">
                                            </div>
                                            <div class="form-group col-6" onclick="copyToClipboardLink('copyToClipboard_{{$payment}}_{{$order->order_id_md5}}');">
                                                <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{route('order.details.start.pay' , [\App\Country::getIsoById($order->country_id),'en',$order->order_id_md5,$payment])}}" height="150">
                                            </div>
                                        </div>
                                    @endforeach

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    @endforelse

@endsection