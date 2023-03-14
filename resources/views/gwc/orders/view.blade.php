@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>

    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.orderdetails')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{!! url('admin_assets/assets/css/pages/invoices/invoice-1.css')!!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('theme6/css/style.css')!!}" rel="stylesheet" type="text/css" />
    <!--css files -->
    @include('gwc.css.user')
    <!--begin::Page Custom Styles(used by this page) -->


    <!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if(!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

    <!-- begin:: Page -->

    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <div class="kt-header-mobile__logo">
            @php
            $settingDetailsMenu = App\Http\Controllers\AdminDashboardController::getSettingsDetails();
            @endphp
            <a href="{{url('/gwc/home')}}">
                @if($settingDetailsMenu['logo'])
                <img alt="{{__('adminMessage.websiteName')}}"
                    src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40" />
                @endif
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler">
                <span></span></button>

            <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more"></i></button>
        </div>
    </div>

    <!-- end:: Header Mobile -->
    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

            <!-- begin:: Aside -->
            @include('gwc.includes.leftmenu')

            <!-- end:: Aside -->
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <!-- begin:: Header -->
                @include('gwc.includes.header')


                <!-- end:: Header -->
                <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                    <!-- begin:: Subheader -->
                    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                        <div class="kt-container  kt-container--fluid ">
                            <div class="kt-subheader__main">
                                <h3 class="kt-subheader__title">{{__('adminMessage.orderdetails')}}</h3>
                                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                <div class="kt-subheader__breadcrumbs">
                                    <a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                    <span class="kt-subheader__breadcrumbs-separator"></span>
                                    <a href="javascript:;"
                                        class="kt-subheader__breadcrumbs-link">{{__('adminMessage.orderdetails')}}</a>
                                </div>
                            </div>
                            <div class="kt-subheader__toolbar"><a href="{{url('gwc/orders')}}"
                                    class="btn btn-default btn-bold">{{__('adminMessage.back')}}</a>
                            </div>
                        </div>
                    </div>

                    <!-- end:: Subheader -->

                    <!-- begin:: Content -->
                    @if(auth()->guard('admin')->user()->can('order-view'))

                    @php
                    $addr=strtoupper($orderDetails->name).'<br>';
                    $addr.=strtoupper($orderDetails->mobile).'<br>';
                    if(!empty($orderDetails->area_id)){
                    $areaName =
                    App\Http\Controllers\AdminCustomersController::getCountryStatesArea($orderDetails->area_id);
                    $stateName =
                    App\Http\Controllers\AdminCustomersController::getCountryStatesArea($orderDetails->state_id);
                    $countryName =
                    App\Http\Controllers\AdminCustomersController::getCountryStatesArea($orderDetails->country_id);
                    $addr.='Country='.$countryName. ',&nbsp;'.'State='.$stateName. ',&nbsp;'.'Area='.$areaName.
                    ',&nbsp;';
                    }

                    if(!empty($orderDetails->block)){
                    $addr.='Block='.$orderDetails->block. ',&nbsp;';
                    }
                    if(!empty($orderDetails->street)){
                    $addr.='Street='.$orderDetails->street. ',&nbsp;';
                    }
                    if(!empty($orderDetails->avenue)){
                    $addr.='Avenue='.$orderDetails->avenue. ',&nbsp;';
                    }
                    if(!empty($orderDetails->house)){
                    $addr.='House='.$orderDetails->house. ', &nbsp;';
                    }
                    if(!empty($orderDetails->floor)){
                    $addr.='Floor='.$orderDetails->floor. ',&nbsp; ';
                    }
                    if(!empty($orderDetails->landmark)){
                    $addr.='Land Mrk='.$orderDetails->landmark. ', &nbsp;';
                    }

                    if(!empty($orderDetails->latitude) && !empty($orderDetails->longitude)){
                    $addr.='<br><a target="_blank"
                        href="https://www.google.com/maps/place/'.$orderDetails->latitude.','.$orderDetails->longitude.'"
                        class="btn btn-info btn-small"><i class="flaticon2-map"></i></a>';
                    }


                    @endphp
                    <!--Begin:: Portlet-->
                    <!-- begin:: Content -->
                    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                        <div class="kt-portlet">
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="kt-invoice-1">
                                    <div class="kt-invoice__head"
                                        style="background-image: url({{url('admin_assets/assets/media/bg/bg-6.jpg')}});">
                                        <div class="kt-invoice__container" style="width:100%;">
                                            <div class="kt-invoice__brand">
                                                <h1 class="kt-invoice__title">{{strtoupper(__('adminMessage.invoice'))}}
                                                </h1>
                                                <div class="kt-invoice__logo">
                                                    @if($settingInfo->logo)
                                                    <a href="javascript:;"><img style="max-width:190px;"
                                                            src="{{url('uploads/logo/'.$settingInfo->logo)}}"></a>
                                                    @endif
                                                    @if($settingInfo->address_en)
                                                    <div class="kt-invoice__desc">
                                                        {!!nl2br($settingInfo->address_en)!!}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="kt-invoice__items">
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__subtitle">{{strtoupper(__('adminMessage.date'))}}</span>
                                                    <span class="kt-invoice__text">{{$orderDetails->created_at}}
                                                        <br>
                                                        {{strtoupper(__('adminMessage.device_type'))}}:
                                                        {{$orderDetails->device_type}}
                                                        @if( $orderDetails->device_type == "pos" )
                                                        @php $adminPOS = \App\AdminPos::find($orderDetails->pos_id);
                                                        @endphp
                                                        <br>
                                                        {{strtoupper(__('adminMessage.username'))}}: {{ $adminPOS->name
                                                        }}
                                                        @endif
                                                        @if( $orderDetails->coupon_code )
                                                        <br>
                                                        {{strtoupper(__('adminMessage.coupon_code')) . ' (' .
                                                        $orderDetails->coupon_code . ')'}} :
                                                        {{$orderDetails->coupon_code}}
                                                        @endif
                                                        @if ( $orderDetails->driver_id != null )
                                                        <br>
                                                        Driver: @if($orderDetails->driver)
                                                        {{$orderDetails->driver->full_name}} @else #{{
                                                        $orderDetails->driver_id }} @endif
                                                        @endif
                                                        @if($orderDetails->is_express_delivery)
                                                        <br>
                                                        <b>Express Delivery</b>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__subtitle">{{strtoupper(__('adminMessage.orderid'))}}</span>
                                                    <span class="kt-invoice__text">{{$orderDetails->order_id}}</span>
                                                </div>
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__subtitle">{{strtoupper(__('adminMessage.invoiceto'))}}</span>
                                                    <span class="kt-invoice__text">{!!$addr!!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kt-invoice__body">
                                        <div class="kt-invoice__container" style="width:100%;">
                                            <div class="table-responsive">
                                                @if(!empty($orderLists) && count($orderLists)>0)
                                                <table class="table table-striped-  table-hover table-checkable">
                                                    <thead>
                                                        <tr>
                                                            <th>{{__('adminMessage.image')}}</th>
                                                            <th style="text-align:left;">{{__('adminMessage.details')}}
                                                            </th>
                                                            <th style="text-align:center;">
                                                                {{__('adminMessage.unit_price')}}</th>
                                                            <th style="text-align:center;">
                                                                {{__('adminMessage.quantty')}}</th>
                                                            <th style="text-align:center;">
                                                                {{__('adminMessage.inventory.inventory')}}</th>
                                                            <th style="text-align:center;">
                                                                {{__('adminMessage.subtotal')}}</th>
                                                            <th @if(count($orderLists)<2) style="display: none;" @else
                                                                style="text-align:center" @endif>
                                                                {{__('adminMessage.actions')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                        $unitprice=0;
                                                        $subtotalprice=0;
                                                        $totalprice=0;
                                                        @endphp
                                                        @foreach($orderLists as $orderList)
                                                        @php
                                                        $productDetails
                                                        =App\Http\Controllers\webCartController::getProductDetails($orderList->product_id);
                                                        if($productDetails->image){
                                                        $prodImage =
                                                        url('uploads/product/thumb/'.$productDetails->image);
                                                        }else{
                                                        $prodImage = url('uploads/no-image.png');
                                                        }
                                                        if(!empty($orderList->size_id)){
                                                        $sizeName
                                                        =App\Http\Controllers\webCartController::sizeNameStatic($orderList->size_id,'en');
                                                        $sizeName = '<br>'.trans('webMessage.size').':'.$sizeName.'';
                                                        }else{$sizeName='';}
                                                        if(!empty($orderList->color_id)){
                                                        $colorName
                                                        =App\Http\Controllers\webCartController::colorNameStatic($orderList->color_id,'en');
                                                        $colorName = '<br>'.trans('webMessage.color').':'.$colorName.'';
                                                        //color image
                                                        $colorImageDetails =
                                                        App\Http\Controllers\webCartController::getColorImage($orderList->product_id,$orderList->color_id);
                                                        if(!empty($colorImageDetails->color_image)){
                                                        $prodImage =
                                                        url('uploads/color/thumb/'.$colorImageDetails->color_image);
                                                        }
                                                        }else{$colorName='';}
                                                        $optionsdetails =
                                                        App\Http\Controllers\webCartController::getOptionsDtailsOrderBr($orderList->id);
                                                        $unitprice = $orderList->unit_price;
                                                        $subtotalprice = $unitprice*$orderList->quantity;

                                                        $warrantyTxt='';
                                                        if(!empty($productDetails->warranty)){
                                                        $warrantyDetails =
                                                        App\Http\Controllers\webCartController::getWarrantyDetails($productDetails->warranty);
                                                        $warrantyTxt = $warrantyDetails->title_en;
                                                        }

                                                        $vendortxt='';
                                                        if(!empty($orderList->vendor->shop_name_en)){
                                                        $vendortxt='<br>'.trans('webMessage.vendor').':<a
                                                            href="'.url('vendor/'.$orderList->vendor->slug).'">'.$orderList->vendor->shop_name_en.'</a>';
                                                        }

                                                        $inventories = (array) json_decode($orderList->inventory ,
                                                        true);

                                                        @endphp
                                                        <tr id="cart-{{$orderList->id}}">
                                                            <td>
                                                                <img src="{{$prodImage}}"
                                                                    width="50"><br><small>{{$productDetails['item_code']}}</small>
                                                            </td>
                                                            <td style="text-align:left;">
                                                                @if(!empty($productDetails['title_en'])){{$productDetails['title_en']}}@endif
                                                                <span
                                                                    style="font-size:12px; font-weight:normal;">{!!$sizeName!!}
                                                                    {!!$colorName!!} {!!$optionsdetails!!}
                                                                    <br>{!!$warrantyTxt!!} {!!$vendortxt!!}</span>
                                                            </td>
                                                            <td>
                                                                <div align="center">{{number_format($unitprice,3)}}
                                                                </div>
                                                            </td>
                                                            <td align="center">
                                                                <input type="number" id="{{'quantity-'.$orderList->id}}"
                                                                    value="{{$orderList->quantity}}" min="1"
                                                                    class="form-control"
                                                                    style="text-align:center;width:100%" />
                                                                <button type="button"
                                                                    class="btn  {!!($orderList->quantity>1?'btn-danger':'btn-primary')!!} updateqty"
                                                                    onclick="updateQuantity({{$orderList->id}})"
                                                                    style="width:100%">Update
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <div align="center">
                                                                    @foreach( $inventories as $inventory )
                                                                    {{
                                                                    \App\Inventory::findOrFail($inventory['IID'])->title
                                                                    .' → '. $inventory['q'] }}<br>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td align="center">
                                                                <div align="center">{{number_format($subtotalprice,3)}}
                                                                </div>
                                                            </td>
                                                            <td @if(count($orderLists)<2) style="display: none;" @else
                                                                style="text-align:center" @endif>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#kt_modal_{{$orderList->id}}"
                                                                    class="kt-nav__link" @if(count($orderLists)<2)
                                                                    style="pointer-events: none;" @endif>
                                                                    <i class="kt-nav__link-icon flaticon2-trash"></i>
                                                                    <span
                                                                        class="kt-nav__link-text">{{__('adminMessage.delete')}}</span>
                                                                </a>

                                                                <!--Delete modal -->
                                                                <div class="modal fade" id="kt_modal_{{$orderList->id}}"
                                                                    tabindex="-1" role="dialog"
                                                                    aria-labelledby="exampleModalLabel"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">
                                                                                    {{__('adminMessage.alert')}}</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal"
                                                                                    aria-label="Close">
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <h6 class="modal-title">
                                                                                    {!!__('adminMessage.alertDeleteMessage')!!}
                                                                                </h6>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-dismiss="modal">{{__('adminMessage.no')}}</button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger"
                                                                                    onClick="Javascript:window.location.href='{{url('gwc/orders/deleteproduct/'.$orderList->id.'/'.$orderList->product_id)}}'">{{__('adminMessage.yes')}}</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @php
                                                        $totalprice+=$subtotalprice;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <h4>{{__('adminMessage.add_new_product')}}</h4>
                                        <div class="row">
                                            <div class="col-10">
                                                <input type="text" name="searchItemCode" id="searchItemCode"
                                                    class="form-control"
                                                    placeholder="{{__('adminMessage.enter_item_code')}}" />
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-primary w-100"
                                                    onClick="searchItemCode()">Search</button>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="p-4" id="productResults">

                                        </div>
                                    </div>

                                    <div class="kt-invoice__footer">
                                        <div class="kt-invoice__container" style="width:100%;">
                                            <div class="kt-invoice__bank">
                                                @if(!empty($orderDetails->order_id))
                                                @php
                                                $TransDetails =
                                                App\Http\Controllers\webCartController::TransDetails($orderDetails->order_id);
                                                @endphp
                                                @if(!empty($TransDetails->id))
                                                <div class="kt-invoice__title">
                                                    {{strtoupper(__('adminMessage.transdetails'))}}</div>
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.result')}}:</span>
                                                    <span class="kt-invoice__value"
                                                        @if($TransDetails->presult=='CAPTURED')style="color:#009900"
                                                        @else style="color:#ff0000"
                                                        @endif>{{$TransDetails->presult}}</span>
                                                </div>
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.paymentid')}}:</span>
                                                    <span class="kt-invoice__value">{{$TransDetails->payment_id}}</span>
                                                </div>
                                                @if(!empty($TransDetails->tranid))
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.transid')}}:</span>
                                                    <span class="kt-invoice__value">{{$TransDetails->tranid}}</span>
                                                </div>
                                                @endif
                                                @if(!empty($TransDetails->paypal_cart))
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.transid')}}:</span>
                                                    <span
                                                        class="kt-invoice__value">{{$TransDetails->paypal_cart}}</span>
                                                </div>
                                                @endif
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.trackid')}}:</span>
                                                    <span class="kt-invoice__value">{{@$TransDetails->MfTrackId ??
                                                        $TransDetails->trackid}}</span>
                                                </div>
                                                <div class="kt-invoice__item">
                                                    <span
                                                        class="kt-invoice__label">{{__('adminMessage.amount')}}:</span>
                                                    <span class="kt-invoice__value">{{round($TransDetails->udf2,3)}}
                                                        {{\App\Currency::default()}}
                                                        @if(!empty($TransDetails->amt_dollar))
                                                        ({{trans('webMessage.usd')}}
                                                        {{round($TransDetails->amt_dollar,3)}}
                                                        )@endif
                                                    </span>
                                                </div>
                                                @endif
                                                @endif
                                            </div>

                                            <div class="kt-invoice__total">
                                    
                                                <select
                                                    onchange="forceUpdateStatus({{$orderDetails->id}} , @if(empty($orderDetails->is_paid)) 0 @else 1 @endif , '{{ $orderDetails->extra_comment }}' , 'forceUpdateStatus_{{$orderDetails->id}}')"
                                                    id="forceUpdateStatus_{{$orderDetails->id}}" class="form-control">
                                                    <option value="pending" @if($orderDetails->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}
                                                    </option>
                                                    <option value="received" @if($orderDetails->order_status=='received') selected @endif>Received</option>
                                                    <option value="outfordelivery" @if($orderDetails->order_status=='outfordelivery') selected
                                                        @endif>{{__('adminMessage.outfordelivery')}}</option>
                                                    <option value="completed" @if($orderDetails->order_status=='completed') selected
                                                        @endif>{{__('adminMessage.completed')}}</option>
                                                    <option value="canceled" @if($orderDetails->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}
                                                    </option>
                                                    <option value="returned" @if($orderDetails->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}
                                                    </option>
                                                </select>
                                                    <span
                                                    class="kt-invoice__title">{{strtoupper(__('adminMessage.total'))}}</span>
                                                <span class="kt-invoice__price">{{number_format($totalprice,3)}}
                                                    {{\App\Currency::default()}} </span>

                                                @if(!empty($orderDetails->bundle_discount))
                                                <span
                                                    class="kt-invoice__notice">{{strtoupper(__('webMessage.bundles.BundleDiscount'))}}</span>
                                                <span
                                                    class="kt-invoice__price">-{{number_format($orderDetails->bundle_discount,3)}}
                                                    {{\App\Currency::default()}} </span>
                                                @php
                                                $totalprice=$totalprice-$orderDetails->bundle_discount;
                                                @endphp
                                                @endif

                                                @if(!empty($orderDetails->seller_discount))
                                                <span
                                                    class="kt-invoice__notice">{{strtoupper(__('webMessage.seller_discount'))}}</span>
                                                <span
                                                    class="kt-invoice__price">-{{number_format($orderDetails->seller_discount,3)}}
                                                    {{\App\Currency::default()}} </span>
                                                @php
                                                $totalprice=$totalprice-$orderDetails->seller_discount;
                                                @endphp
                                                @endif


                                                @if(!empty($orderDetails->coupon_code) and $orderDetails->coupon_amount
                                                > 0 )
                                                <span
                                                    class="kt-invoice__notice">{{strtoupper(__('adminMessage.coupon_discount')).
                                                    ' (' . $orderDetails->coupon_code . ')'}}</span>
                                                <span
                                                    class="kt-invoice__price">-{{number_format($orderDetails->coupon_amount,3)}}
                                                    {{\App\Currency::default()}} </span>
                                                @php
                                                $totalprice=$totalprice-$orderDetails->coupon_amount;
                                                @endphp
                                                @endif

                                                @if(empty($orderDetails->delivery_charges))
                                                <span
                                                    class="kt-invoice__notice">{{strtoupper(__('adminMessage.delivery_charge'))}}</span>
                                                <span
                                                    class="kt-invoice__price">{{__('adminMessage.free_delivery')}}</span>
                                                @endif

                                                @if(!empty($orderDetails['delivery_charges']) &&
                                                empty($orderDetails['coupon_free']))
                                                <span
                                                    class="kt-invoice__notice">{{strtoupper(__('adminMessage.delivery_charge'))}}</span>
                                                <span
                                                    class="kt-invoice__price">{{number_format($orderDetails['delivery_charges'],3)}}
                                                    {{\App\Currency::default()}}</span>
                                                @php
                                                $totalprice=$totalprice+$orderDetails['delivery_charges'];
                                                @endphp
                                                @endif
                                                <span
                                                    class="kt-invoice__title">{{strtoupper(__('adminMessage.grandtotal'))}}</span>
                                                <span class="kt-invoice__price">{{number_format($totalprice,3)}}
                                                    {{\App\Currency::default()}} </span>

                                            </div>
                                        </div>
                                    </div>
                                    @php
                                    if(!empty($orderDetails->delivery_date)){
                                    $delivery_date = $orderDetails->delivery_date;
                                    }else{
                                    $delivery_date = null;
                                    }
                                    @endphp
                                    <div class="collapse m-2" id="collapseExample">
                                        <form action="/gwc/order/discountapply/ajax">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="mobile">{{__('webMessage.mobile')}} <span
                                                                style="color:#FF0000">{{$settingInfo->validate_cust_mob==1?'*':''}}</span></label>
                                                        <input type="text" name="mobile" class="form-control"
                                                            id="mobile" placeholder="{{__('webMessage.enter_mobile')}}"
                                                            autcomplete="off" required
                                                            value="{{old('mobile' , $orderDetails->mobile )}}">
                                                        @if($errors->has('mobile'))
                                                        <label id="mobile-error" class="error"
                                                            for="mobile">{{$errors->first('mobile')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php
                                                $areaid = $orderDetails->area_id;
                                                $areatxt =
                                                App\Http\Controllers\webCartController::get_Country_areas($areaid > 0 ?
                                                $areaid : $orderDetails->country_id , false);
                                                $countryLists = App\Http\Controllers\webCartController::get_country(0);
                                                //$countryListsSelected =
                                                App\Http\Controllers\webCartController::get_country_of_area($areaid);
                                                $countryListsSelected = $orderDetails->country_id;
                                                @endphp
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="area">{{__('webMessage.area')}}<font
                                                                color="#FF0000">*</font></label>
                                                        <div class="custom_select">
                                                            <select name="area" class="form-control area_checkout"
                                                                id="area" required>
                                                                {!!$areatxt!!}
                                                            </select>
                                                            @if($errors->has('area'))
                                                            <label id="area-error" class="error"
                                                                for="area">{{$errors->first('area')}}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="block">{{__('webMessage.block')}}</label>
                                                        <input type="text" name="block" class="form-control" id="block"
                                                            placeholder="{{__('webMessage.enter_block')}}"
                                                            autcomplete="off"
                                                            value="{{old('block' , $orderDetails->block )}}">
                                                        @if($errors->has('block'))
                                                        <label id="block-error" class="error"
                                                            for="block">{{$errors->first('block')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="street">{{__('webMessage.street')}}</label>
                                                        <input type="text" name="street" class="form-control"
                                                            id="street" placeholder="{{__('webMessage.enter_street')}}"
                                                            autcomplete="off"
                                                            value="{{old('street' ,$orderDetails->street )}}">
                                                        @if($errors->has('street'))
                                                        <label id="street-error" class="error"
                                                            for="street">{{$errors->first('street')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="avenue">{{__('webMessage.avenue')}}</label>
                                                        <input type="text" name="avenue" class="form-control"
                                                            id="avenue" placeholder="{{__('webMessage.enter_avenue')}}"
                                                            autcomplete="off"
                                                            value="{{old('avenue' , $orderDetails->avenue )}}">
                                                        @if($errors->has('avenue'))
                                                        <label id="avenue-error" class="error"
                                                            for="avenue">{{$errors->first('avenue')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="house">{{__('webMessage.house')}}</label>
                                                        <input type="text" name="house" class="form-control" id="house"
                                                            placeholder="{{__('webMessage.enter_house')}}"
                                                            autcomplete="off"
                                                            value="{{old('house' , $orderDetails->house)}}">
                                                        @if($errors->has('house'))
                                                        <label id="house-error" class="error"
                                                            for="house">{{$errors->first('house')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="floor">{{__('webMessage.floor')}}</label>
                                                        <input type="text" name="floor" class="form-control" id="floor"
                                                            placeholder="{{__('webMessage.enter_floor')}}"
                                                            autcomplete="off"
                                                            value="{{old('floor' ,  $orderDetails->floor )}}">
                                                        @if($errors->has('floor'))
                                                        <label id="floor-error" class="error"
                                                            for="floor">{{$errors->first('floor')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label for="landmark">{{__('webMessage.landmark')}}</label>
                                                        <input type="text" name="landmark" class="form-control"
                                                            id="landmark"
                                                            placeholder="{{__('webMessage.enter_landmark')}}"
                                                            autcomplete="off"
                                                            value="{{old('landmark' , $orderDetails->landmark )}}">
                                                        @if($errors->has('landmark'))
                                                        <label id="landmark-error" class="error"
                                                            for="landmark">{{$errors->first('landmark')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php
                                                $deliverytimeslists =
                                                App\Http\Controllers\webCartController::listDeliveryTimes();
                                                @endphp
                                                @if(!empty($deliverytimeslists) && count($deliverytimeslists)>0)
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label
                                                            for="delivery_time">{{__('webMessage.deliverytime')}}</label>
                                                        <div class="custom_select">
                                                            <select name="delivery_time" id="delivery_time"
                                                                class="form-control">
                                                                <option value="0">
                                                                    {{__('webMessage.choosedeliverytimes')}}</option>
                                                                @foreach($deliverytimeslists as $deliverytimeslist)
                                                                <option value="{{$deliverytimeslist->id}}" @if(
                                                                    old('delivery_time' , $orderDetails->
                                                                    delivery_time_id) == $deliverytimeslist->id )
                                                                    selected @endif>{{$deliverytimeslist->title_en}}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label>{{__('webMessage.delivery_date')}}</label>
                                                        <input type="text" name="delivery_date" class="form-control"
                                                            id="delivery_date"
                                                            placeholder="{{__('webMessage.enter_delivery_date')}}"
                                                            autcomplete="off"
                                                            value="{{ old('delivery_date' , $delivery_date )  }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label>{{__('webMessage.delivery_charge')}}</label>
                                                        <input placeholder="{{trans('webMessage.delivery_charge')}}"
                                                            class="form-control" type="text"
                                                            value="{{ old('delivery_charge' , $orderDetails['delivery_charges'])}}"
                                                            name="delivery_charges" id="delivery_charges">
                                                        @if($errors->has('delivery_charge'))
                                                        <label id="landmark-error" class="error"
                                                            for="delivery_charge">{{$errors->first('delivery_charge')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mb-3">
                                                        <label>{{__('webMessage.seller_discount')}}</label>
                                                        <input placeholder="{{trans('webMessage.seller_discount')}}"
                                                            class="form-control" type="text"
                                                            value="{{ old('seller_discount' , $orderDetails['seller_discount'])}}"
                                                            name="seller_discount" id="seller_discount">
                                                        @if($errors->has('seller_discount'))
                                                        <label id="landmark-error" class="error"
                                                            for="seller_discount">{{$errors->first('seller_discount')}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="oid" id="oid" value="{{$orderDetails->id}}">
                                            <button class="btn btn-danger mb-3 pull-right" type="submit">
                                                Save Details
                                            </button>
                                        </form>
                                    </div>
                                    <div class="kt-invoice__actions">
                                        <div class="kt-invoice__container" style="width:100%;">
                                            <div class="row" style="width:100%;">
                                                <div class="col-lg-11">
                                                    <a href="{{url(app()->getLocale().'/order-print/'.$orderDetails->order_id_md5)}}"
                                                        style="color:#FFFFFF;" target="_blank"
                                                        class="btn btn-warning btn-bold"
                                                        title="{{__('adminMessage.printinvoice')}}"><i
                                                            class="flaticon2-print"></i></a>
                                                    @if(empty($orderDetails->is_paid))
                                                    <!--<a href="javascript:void(0);" style="color:#FFFFFF;" target="_blank" class="btn btn-info btn-bold"  title="{{__('adminMessage.sendpaymentlinkbysms')}}"><i class="flaticon-paper-plane"></i></a>
                                            <a href="javascript:void(0);" style="color:#FFFFFF;" target="_blank" class="btn btn-success btn-bold"  title="{{__('adminMessage.sendpaymentlinkbyemail')}}"><i class="flaticon-mail-1"></i></a>-->
                                                    @endif
                                                </div>
                                                {{-- <input id="applydiscountamount" type="button" --}} {{--
                                                    value="{{trans('adminMessage.save')}}" --}} {{--
                                                    class="btn btn-brand btn-bold">
                                            </div>--}}

                                            <button class="btn btn-primary" type="button" data-toggle="collapse"
                                                data-target="#collapseExample" aria-expanded="false"
                                                aria-controls="collapseExample">
                                                Edit Details
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- end:: Content -->

                <!--End:: Portlet-->
                @else
                <div class="alert alert-light alert-warning" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                    <div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
                </div>
                @endif

                <!-- end:: Content -->
            </div>

            <!-- begin:: Footer -->
            @include('gwc.includes.footer');

            <!-- end:: Footer -->
        </div>
    </div>
    </div>

    <!-- end:: Page -->

    <!-- begin::Scrolltop -->
    <div id="kt_scrolltop" class="kt-scrolltop">
        <i class="fa fa-arrow-up"></i>
    </div>

    <!-- end::Scrolltop -->

    <!-- js files -->
    @include('gwc.js.user')

    <script>
        $(function () {
        $("#delivery_date").datepicker({format: "yyyy-mm-dd"  ,autoUpdateInput: false });
        @if(Session::get('message-success'))
            var notify = $.notify({message: '{{ Session::get('message-success') }}'});
            notify.update('type', 'success');
        @endif

    });

    function updateQuantity(orderId) {
        var id = 'quantity-' + orderId;
        var quantity = $("#" + id).val();

        $.ajax({
            type: "POST",
            url: "/gwc/orders/updateprodqty",
            data: {quantity: quantity, orderId: orderId},
            success: function (response) {
                if (response.status == 400){
                    var notify = $.notify({message: response.message});
                    notify.update('type', 'danger');
                }
                else{
                    window.location.reload();
                    var notify = $.notify({message: 'Quantity Updated Successfully'});
                    notify.update('type', 'success');
                }
            },
            error: function () {
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
            }
        });
    }

    function searchItemCode() {
        var itemCode = $("#searchItemCode").val();
        var orderId = '{{ $orderDetails->order_id }}';

        $.ajax({
            type: "POST",
            url: "/gwc/orders/searchitemcode",
            data: {itemCode:itemCode, orderId:orderId},
            success: function (response) {
                if (response.status == 400){
                    var notify = $.notify({message: 'Product is already added!'});
                    notify.update('type', 'danger');
                }
                else if (response.status == 404){
                    var notify = $.notify({message: 'Product Not Found!'});
                    notify.update('type', 'danger');
                }
                else{
                    $("#productResults").empty();
                    $("#productResults").append(response);
                    var notify = $.notify({message: 'Product Found Successfully'});
                    notify.update('type', 'success');
                }
            },
            error: function () {
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
            }
        });
    }

    function setColorAttr(colorId) {
        $(".active").attr("class", "");
        var id = '#li-' + colorId;
        $(id).attr("class", "active");
        $("#color_attr").attr("value", colorId);
    }

    function addProductToOrder(){
        var productId = $("#product-to-add-id").text();
        var price = $("#display_price").text();
        var orderId = '{{ $orderDetails->order_id }}';

        var optionSc = $("input[name='option_sc']");
        if (optionSc && optionSc.length){
            optionSc = optionSc[0].value;
            if (optionSc == 1){
                var sizeAttr = $("#size_attr_" + productId).val();
                var colorAttr = 0;
            }
            else if (optionSc == 2){
                var colorAttr = $("#color_attr").val();
                var sizeAttr = 0;
            }
            else if (optionSc == 3){
                var sizeAttr = $("#size_attr_" + productId).val();
                var colorAttr = $("#color_attr").val();
            }
        }
        else{
            optionSc = 0;
        }

        var quantity = $("#available-qty").text();
        if (quantity > 0) {
            $.ajax({
                url: "/gwc/orders/add-product-to-order",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    order_id: orderId,
                    optionSc: optionSc,
                    size_attr: sizeAttr,
                    color_attr: colorAttr,
                    product_id: productId,
                    price: price,
                },
                success: function (response) {
                    console.log(response);
                    if (response.status == 400){
                        var notify = $.notify({message: response.message});
                        notify.update('type', 'danger');
                    }
                    else{
                        window.location = window.location;
                        window.location.reload();
                        var notify = $.notify({message: 'Invoice Updated Successfully'});
                        notify.update('type', 'success');
                    }
                },
                error: function (response) {
                    console.log(response);
                    var notify = $.notify({message: response.message});
                    notify.update('type', 'danger');
                },
            });
        }
        else{
            var notify = $.notify({message: 'This product is not available in the store'});
            notify.update('type', 'danger');
        }
    }
    function forceUpdateStatus(id ,pay_status , extra_comment , elId ) {
    var order_status = $("#" + elId).val();
    $.ajax({
    type: "GET",
    url: "/gwc/orders/status/ajax",
    data: "id=" + id + "&order_status=" + order_status + "&pay_status=" + pay_status + "&extra_comment=" + extra_comment,
    dataType: "json",
    contentType: false,
    cache: false,
    processData: false,
    success: function (msg) {
    if (msg.status == 200) {
    $(".orderStatus_" + id).hide();
    $(".orderStatus_"+order_status+"_" + id).show();
    var notify = $.notify({message: msg.message});
    notify.update('type', 'success');
    } else {
    var notify = $.notify({message: msg.message});
    notify.update('type', 'danger');
    }
    },
    error: function (msg) {
    //notification start
    var notify = $.notify({message: 'Error occurred while processing'});
    notify.update('type', 'danger');
    //notification end
    }
    });
    }
    </script>

</body>
<!-- end::Body -->

</html>
