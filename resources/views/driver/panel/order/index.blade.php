@extends('driver.include.master')
@section('title' , 'History')

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
        @media only screen and (max-width: 1024px) {
            /* Force table to not be like tables anymore */
            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            .table-responsive thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            .table-responsive tr { border: 1px solid #ccc; }

            .table-responsive td {
                /* Behave like a "row" */
                border: none;
                border-bottom: 1px solid rgba(238, 238, 238, 0.59) !important;
                position: relative;
                padding-left: 50% !important;
                white-space: normal;
                text-align:left;
            }

            .table-responsive td:before {
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align:left;
                font-weight: bold;
            }

            /*
            Label the data
            */
            .table-responsive td:before { content: attr(data-title); }

            .kt-widget11 .table tbody > tr {
                border-bottom: 1px dashed #ebedf2 !important;
            }
            .kt-widget11 .table tbody > tr:last-child {
                border-bottom: 1px dashed #ebedf2 !important;
            }
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
                            <i class="fa fa-handshake"></i> History
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <form class="kt-quick-search__form" style="margin-left: 15px;" action="{{ route('driver.panel.orders.history') }}">
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
                            <table class="table table-bordered table-condensed table-striped">
                                <thead>
                                <tr>
                                    <td>Order Id</td>
                                    <td>Customer</td>
                                    <td>Status</td>
                                    <td>Payment</td>
                                    <td>Updated at</td>
                                    <td>Area</td>
                                    <td>Block</td>
                                    <td>Street</td>
                                    <td>Avenue</td>
                                    <td>House</td>
                                    <td>Floor</td>
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
                                        <td data-title="Order Id" onclick="openShortView( {{ ($order->order_status=="pending" or $order->order_status=="received" or $order->order_status=="outfordelivery") ? '1' : '0' }} , '{{$order->order_id}}','{{ $orderStatus }}', '{{$totalAmounts}}','','{{ $order->name ?? 'Unknown' }}',{{ (int) !empty($order->is_paid) }},'{{ $order->delivery_date }}','{{$order->delivery_time_en}}','{{$order->area ? $order->area->name_en : '--'}}','{{$order->block}}','{{$order->street}}','{{$order->avenue}}','{{$order->house}}','{{$order->floor}}' , '{{ url('en/order-print/'.$order->order_id_md5.'?driverSystem=1') }}' , '{{ $order->extra_comment }}');" class="text-primary" style="cursor: pointer" ><strong>#{{$order->order_id}}</strong></td>
                                        <td data-title="Customer" onclick="openShortView( {{ ($order->order_status=="pending" or $order->order_status=="received" or $order->order_status=="outfordelivery") ? '1' : '0' }} , '{{$order->order_id}}','{{ $orderStatus }}', '{{$totalAmounts}}','','{{ $order->name ?? 'Unknown' }}',{{ (int) !empty($order->is_paid) }},'{{ $order->delivery_date }}','{{$order->delivery_time_en}}','{{$order->area ? $order->area->name_en : '--'}}','{{$order->block}}','{{$order->street}}','{{$order->avenue}}','{{$order->house}}','{{$order->floor}}' , '{{ url('en/order-print/'.$order->order_id_md5.'?driverSystem=1') }}' , '{{ $order->extra_comment }}');" class="text-primary" style="cursor: pointer" ><strong>{{ $order->name ?? 'Unknown' }}</strong></td>
                                        <td data-title="Status">{!! $orderStatus !!}</td>
                                        <td data-title="Payment">
                                            {{ $totalAmounts  }}
                                            @if(!empty($order->is_paid))
                                                <span class="ml-3 kt-badge kt-badge--inline kt-badge--success">{{__('adminMessage.yes')}}</span>
                                            @else
                                                <span class="ml-3 kt-badge kt-badge--inline kt-badge--danger">{{__('adminMessage.no')}}</span>
                                            @endif
                                        </td>
                                        <td data-title="Updated at">{{$order->updated_at}}</td>
                                        <td data-title="Area">{{$order->area ? $order->area->name_en : '--'}}</td>
                                        <td data-title="Block">{{$order->block}}</td>
                                        <td data-title="Street">{{$order->street}}</td>
                                        <td data-title="Avenue">{{$order->avenue}}</td>
                                        <td data-title="House">{{$order->house}}</td>
                                        <td data-title="Floor">{{$order->floor}}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="11" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="kt-widget11__action kt-align-right">
                            {{ $orders->appends($_GET)->links() }}
                        </div>
                    </div>

                    <!--end::Widget 11-->

                </div>
            </div>
        </div>
    </div>

    <!--End::Row-->
@endsection