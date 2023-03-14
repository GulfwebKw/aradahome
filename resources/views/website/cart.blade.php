@extends('website.include.master')
@section('title' , __('webMessage.shoppingcart') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.shoppingcart') }}</li>
@endsection
@section('header')
    <style>
        .tt-shopcart-table01 {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
            caption-side: bottom;
            border-collapse: collapse;
        }
        .tt-shopcart-table01>:not(caption)>*>* {
            padding: 0.5rem 0.5rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }
        .btn-lg {
            background-color: #ffbb00;
            border: 1px solid #ffbb00;
            color: #fff;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-lg.active:focus, .btn-lg:active:focus {
            box-shadow: none !important;
        }
        .btn-lg:hover {
            color: #ffbb00 !important;
            background-color: transparent;
        }
    </style>
@endsection
@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if(session('session_msg'))
                        <div class="alert-danger">{!!session('session_msg')!!}</div>
                    @endif
                    @if(!empty($tempOrders) && count($tempOrders)>0)
                        <div class="table-responsive shop_cart_table">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="product-thumbnail">&nbsp;</th>
                                    <th class="product-name">{{__('webMessage.details')}}</th>
                                    <th class="product-price">{{__('webMessage.unit_price')}}</th>
                                    <th class="product-quantity">{{__('webMessage.quantity')}}</th>
                                    <th class="product-subtotal">{{__('webMessage.subtotal')}}</th>
                                    <th class="product-remove">{{__('webMessage.remove')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $unitprice=0;
                                    $subtotalprice=0;
                                    $totalprice=0;
                                    $dataLayer = [];
                                @endphp
                                @foreach($tempOrders as $tempOrder)
                                    @php
                                        $productDetails =App\Http\Controllers\webCartController::getProductDetails($tempOrder->product_id);
                                        if($productDetails->image){
                                        $prodImage = url('uploads/product/thumb/'.$productDetails->image);
                                        }else{
                                        $prodImage = url('uploads/no-image.png');
                                        }

                                        $warrantyTxt='';
                                        if(!empty($productDetails->warranty)){
                                        $warrantyDetails = App\Http\Controllers\webCartController::getWarrantyDetails($productDetails->warranty);
                                        $warrantyTxt     = app()->getLocale()=="en"?"<br>".$warrantyDetails->title_en:"<br>".$warrantyDetails->title_ar;
                                        }

                                        if(!empty($tempOrder->size_id)){
                                        $sizeName =App\Http\Controllers\webCartController::sizeNameStatic($tempOrder->size_id,app()->getLocale());
                                        $sizeName = '<li>'.trans('webMessage.size').':'.$sizeName.'</li>';
                                        }else{$sizeName='';}
                                        if(!empty($tempOrder->color_id)){
                                        $colorName =App\Http\Controllers\webCartController::colorNameStatic($tempOrder->color_id,app()->getLocale());
                                        $colorName = '<li>'.trans('webMessage.color').':'.$colorName.'</li>';
                                        //color image
                                        $colorImageDetails = App\Http\Controllers\webCartController::getColorImage($tempOrder->product_id,$tempOrder->color_id);
                                        if(!empty($colorImageDetails->color_image)){
                                        $prodImage = url('uploads/color/thumb/'.$colorImageDetails->color_image);
                                        }

                                        }else{$colorName='';}
                                        $optionsDetailstxt = App\Http\Controllers\webCartController::getOptionsDtails($tempOrder->id);

                                        $unitprice = $tempOrder->unit_price;
                                        $subtotalprice = round(($unitprice*$tempOrder->quantity),3);
                                        $aquantity = App\Http\Controllers\webCartController::getProductQuantity($tempOrder->product_id,$tempOrder->size_id,$tempOrder->color_id,$tempOrder);

                                         $dataLayer[] = [
                                                "item_name"     => $productDetails->title_en??'no name',
                                                "item_id"       => $productDetails->id??'0',
                                                "price"         => $unitprice,
                                                "item_brand"    => "",
                                                "item_category" => "",
                                                "item_category2"=> "",
                                                "item_category3"=> "",
                                                "item_category4"=> "",
                                                "item_variant"  => $optionsDetailstxt,
                                                "item_list_name"=> "",
                                                "item_list_id"  => "",
                                                "index"         => $tempOrder->id,
                                                "quantity"      => $tempOrder->quantity??'1',
                                                "currency"      => "KWD",
                                                ];
                                    @endphp
                                    <tr id="cart-{{$tempOrder->id}}">
                                        <td  class="product-thumbnail">
                                            <a href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">
                                                <img src="{{$prodImage}}" alt="@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif">
                                            </a>
                                        </td>
                                        <td class="product-name" data-title="{{__('webMessage.details')}}" >
                                            <a href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif</a>
                                            <small>
                                                {!!$sizeName!!}
                                                {!!$colorName!!}
                                                {!!$optionsDetailstxt!!}
                                                {!!$warrantyTxt!!}
                                            </small>
                                        </td>
                                        <td class="product-price" data-title="{{__('webMessage.unit_price')}}">
                                            {{$unitprice}} {{\App\Currency::default()}}
                                        </td>
                                        <td  class="product-quantity" data-title="{{__('webMessage.quantity')}}">
                                            <div class="quantity">
                                                <input type="button" value="-" id="{{$tempOrder->id}}" class="minus minus-btn">
                                                <input size="{{$aquantity}}"  type="text" name="quantity{{$tempOrder->id}}" value="{{$tempOrder->quantity}}"  id="quantity{{$tempOrder->id}}" title="{{__('webMessage.quantity')}}" class="qty" >
                                                <input type="button" value="+" id="{{$tempOrder->id}}" class="plus plus-btn">
                                            </div>
                                        </td>
                                        <td class="product-subtotal" data-title="{{__('webMessage.subtotal')}}">
                                            <span class="subtotal_result{{$tempOrder->id}}">{{$subtotalprice}}</span> {{\App\Currency::default()}}
                                        </td>
                                        <td class="product-remove" data-title="{{__('webMessage.remove')}}">
                                            <a href="javascript:;" id="{{$tempOrder->id}}" class="deleteFromCart"><i class="ti-close"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $totalprice+=$subtotalprice;
                                        $productid = $tempOrder->product_id;
                                    @endphp
                                @endforeach
                                @php
                                    $lastshoppinglink=App\Http\Controllers\webCartController::getShoppingLink($productid ?? null);
                                @endphp
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6" class="px-0">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                                                @if(empty($userType) and $settingInfo->is_discount)
                                                <div class="coupon field_form input-group">
                                                    <input type="text" name="coupon_code"  class="form-control form-control-sm" id="coupon_code" placeholder="{{__('webMessage.enter_coupon_code')}}" autcomplete="off" value="@if(Cookie::get('gb_coupon_code')) {{Cookie::get('gb_coupon_code')}} @endif">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-fill-out btn-sm applycouponbtn" type="button">{{__('webMessage.apply')}}</button>
                                                    </div>
                                                </div>
                                                    <span id="result_coupon"></span>
                                                @endif
                                            </div>
                                            <div class="col-lg-8 col-md-6  text-start  text-md-end">
                                                <a class="btn btn-line-fill btn-sm" href="{{$lastshoppinglink}}">{{ strtoupper(__('webMessage.continueshopping')) }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            @php
                                $bundleDiscount =  App\Http\Controllers\webCartController::loadTempOrdersBundleDiscount($tempOrders);
                                if ( $bundleDiscount > 0 ){
                                    $totalprice = $totalprice - $bundleDiscount;
                                }
                            @endphp
                            <input type="hidden" name="checkout_totalprice" id="checkout_totalprice" value="{{$totalprice}}">
                            <input type="hidden" name="checkout_totalprice" id="checkout_bundleDiscount" value="{{$bundleDiscount}}">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="medium_divider"></div>
                        <div class="divider center_icon"><i class="ti-shopping-cart-full"></i></div>
                        <div class="medium_divider"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="heading_s1 mb-3">
                            <h6>{{strtoupper(__('webMessage.checkdeliverycharges'))}}</h6>
                        </div>
                        @php
                            $areaid=!empty(Cookie::get('area'))?Cookie::get('area'):'0';
                            $areatxt = App\Http\Controllers\webCartController::get_Country_areas($areaid > 0 ? $areaid : $domainCountry->id);
                            $countryLists = App\Http\Controllers\webCartController::get_country(0);
                            //$countryListsSelected = App\Http\Controllers\webCartController::get_country_of_area($areaid);
                            $countryListsSelected = $domainCountry->id ;
                        @endphp
                        <div class="form-row">
                            <div class="form-group col-lg-12 mb-3">
                                <div class="custom_select">
                                    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
									<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
									<select name="country"  class="form-control area_checkoutcart js-example-basic-single" id="country" >
										{!!$areatxt!!}
									</select>
									<script type="module">
										$(document).ready(function() {
											$('.js-example-basic-single').select2({
												@if(app()->getLocale() == "ar" ) dir: "rtl", @endif
												selectionCssClass: ":all:"
											});
										});
									</script>
									<style>
										span.form-control {
											border: 1px solid #ced4da !important;
										}
										.select2-container--default .select2-selection--single .select2-selection__rendered {
											line-height: 1.5;
										}
										.select2-container .select2-selection--single {
											height: auto;
										}
										.select2-selection__arrow {
											margin-top: 4px;
										}
										.select2-selection__rendered {
											margin-top: 2px;
										}
										.select2-search__field:focus-visible{
											outline-width: 0 !important;
										}
									</style>
                                </div>
                            </div>
                            @if ( $domainCountry->shipment_method == "flatrate" and $settingInfo->is_express)
                            <div class="form-group col-lg-12 mb-3">
                                <div class="chek-form">
                                    <div class="custome-checkbox">
                                        <input @if(Cookie::get('is_express_delivery' , 0)) checked @endif type="checkbox" id="is_express_delivery" value="1" name="is_express_delivery">
                                        <label for="is_express_delivery"><span class="check"></span><span class="box"></span>&nbsp;{{ __('webMessage.isSendByExpress') }}</label>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3 p-md-4">
                            <div class="heading_s1 mb-3">
                                <h6>{{strtoupper(__('webMessage.grandtotal'))}}</h6>
                            </div>
                            <div class="table-responsive" id='checktotalbox'>
                                <table class="tt-shopcart-table01">
                                    <tbody>
                                    <tr>
                                        <th>{{strtoupper(__('webMessage.subtotal'))}}</th>
                                        <td> <span class="total_result">{{$totalprice}}</span> {{\App\Currency::default()}}</td>
                                    </tr>

                                    @if ( $bundleDiscount > 0 )
                                        <tr>
                                            <th>{{strtoupper(__('webMessage.bundles.BundleDiscount'))}}</th>
                                            <td style="color: #FF0000;">-{{$bundleDiscount}} {{\App\Currency::default()}}</td>
                                        </tr>
                                    @endif
                                    @if(!empty(Cookie::get('gb_coupon_code')) &&  floatval(preg_replace("/[^-0-9\.]/","",Cookie::get('gb_coupon_discount_text'))) > 0   )
                                        <tr>
                                            <th>{{strtoupper(__('webMessage.coupon_discount'))}}</th>
                                            <td><font color="#FF0000">-{{Cookie::get('gb_coupon_discount_text')}}</font></td>
                                        </tr>
                                        @php
                                            $totalprice=$totalprice-Cookie::get('gb_coupon_discount');
                                        @endphp
                                    @endif

                                    @if(!empty($settingInfo->is_free_delivery) && $totalprice>=$settingInfo->free_delivery_amount)
                                        <tr>
                                            <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                            <td><font color="#FF0000">{{strtoupper(__('webMessage.free_delivery'))}}</font></td>
                                        </tr>
                                    @else

                                        @if(!empty(Cookie::get('gb_coupon_code')) && !empty(Cookie::get('gb_coupon_free')))
                                            <tr>
                                                <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                                <td><font color="#FF0000">{{strtoupper(__('webMessage.free_delivery'))}}</font></td>
                                            </tr>
                                        @endif
                                        @if(empty(Cookie::get('gb_coupon_free')))
                                           @php
        									   if( (!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free')) ){
        										  	if(!empty(Cookie::get('area'))){ $areaid = Cookie::get('area'); }else if(!empty($userAddress->area_id)){ $areaid = $userAddress->area_id; }
        											$deliveryCharge = App\Http\Controllers\webCartController::get_delivery_charge($areaid);
        									   }
                                           @endphp
                                            <tr>
        										<th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
        										<td>@if( (!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free')) ){{$deliveryCharge}} {{\App\Currency::default()}} @else -- @endif</td>
        									</tr>
                                            @php
                                           $totalprice=$totalprice+ ( (!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free')) ? $deliveryCharge : 0 );
                                           @endphp
                                        @endif
                                    @endif


                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>{{strtoupper(__('webMessage.grandtotal'))}}</th>
                                        <td><span class="total_result">{{$totalprice}}</span> {{\App\Currency::default()}}</td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <a href="{{url(app()->getLocale().'/checkout')}}" class="btn btn-lg"><span class="icon icon-check_circle"></span>{{strtoupper(__('webMessage.proceedtocheckout'))}}</a>
                            </div>
                        </div>
                    @else
                        <div class="pagination pagination_style1 justify-content-center">
                            <p>{{__('webMessage.yourcartisempty')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            BASE_URL='';
        	$( "#is_express_delivery" ).change(function() {
        		$.ajax({
        			type: "GET",
        			url: "/ajax_is_express_delivery",
        			data: "is_express_delivery="+ ( $('#is_express_delivery').is(':checked') ? "1" : "0" ) ,
        			dataType: "json",
        			cache: false,
        			processData:false,
        			success: function(msg){
        				if(msg.status=="200"){
        					cartAreaRefresh();
        				}else{
        					toastr.error(msg.message);
        				}
        			},
        			error: function(msg){
        				toastr.error('Oops! Something went wrong while processing');
        			}
        		});
        	});

            $(".plus-btn").click(function(){
                var id = $(this).attr("id");
                var quantity = parseInt($("#quantity"+id).val()) + 1;
                $.ajax({
                    type: "GET",
                    url: BASE_URL+"/ajax_change_cart_quantity",
                    data: "type=a&id="+id+"&quantity="+quantity,
                    dataType: "json",
                    cache: false,
                    processData:false,
                    success: function(msg){
                        if(msg.status=="200"){
                            $(".subtotal_result"+id).html(msg.subtotal);
                            //$(".total_result").html(msg.total);
                            $("#checkout_totalprice").val(msg.total);
                            $("#quantity"+id).val(quantity) 
                            //$("#result_reponse_cart").html(msg.message);
                            toastr.success(msg.message);
                            cartAreaRefresh();

                        }else{
                            //$("#result_reponse_cart").html(msg.message);
                            toastr.error(msg.message);
                        }
                    },
                    error: function(msg){
                        //$("#result_reponse_cart").html('<div class="alert-danger">Something was wrong</div>');
                        toastr.error('Oops! Something went wrong while processing');
                    }
                });
            });

            //
            $(".minus-btn").click(function(){
                var id = $(this).attr("id");
                if(parseInt($("#quantity"+id).val()) !== 1){
                    var quantity =  parseInt($("#quantity"+id).val()) - 1 
                }else{
                    $(".deleteFromCart").click()
                }
                $.ajax({
                    type: "GET",
                    url: BASE_URL+"/ajax_change_cart_quantity",
                    data: "type=m&id="+id+"&quantity="+quantity,
                    dataType: "json",
                    cache: false,
                    processData:false,
                    success: function(msg){
                        if(msg.status=="200"){
                            $(".subtotal_result"+id).html(msg.subtotal);
                            //$(".total_result").html(msg.total);
                            $("#checkout_totalprice").val(msg.total);
                            $("#quantity"+id).val(quantity) 
                            //$("#result_reponse_cart").html(msg.message);
                            toastr.success(msg.message);
                            cartAreaRefresh();

                        }else{
                            //$("#result_reponse_cart").html(msg.message);
                            toastr.error(msg.message);
                        }
                    },
                    error: function(msg){
                        //$("#result_reponse_cart").html('<div class="alert-danger">Something was wrong</div>');
                        toastr.error('Oops! Something went wrong while processing');
                    }
                });
            });

        });
        @if(!empty($dataLayer))
        <!--view cart -->
        gtag("event", "view_cart", {
            currency: "KWD",
            value: {{number_format($totalprice,2)}},
            items: {!! json_encode($dataLayer) !!}
        });
        @endif
    </script>
    <script>
        gtag('event', 'screen_view', {
            'screen_name' : 'Shopping Cart'
        });
    </script>
@endsection
