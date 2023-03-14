@extends('website.include.master')
@section('title' , __('webMessage.orderdetails').' ( '.$orderDetails->order_id.' ) ' )
@section('breadcrumb' )
<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/myorders')}}">{{ __('webMessage.myorders') }}</a></li>
<li class="breadcrumb-item active">{{ __('webMessage.orderdetails') }} ( {{$orderDetails->order_id}} )</li>
@endsection
@section('breadcrumb_title2' )
<a target="_blank" href="{{url(app()->getLocale().'/order-print/'.Request()->orderid)}}"
	class="btn btn-fill-out float-right"><i class="fas fa-print"></i>{{__('webMessage.print')}}</a>
@endsection

@section('content')
<!-- START SECTION SHOP -->
<div class="section">
	<div class="container">
		@if(session('session_msg'))
		<div class="alert alert-success">{!!session('session_msg')!!}</div>
		@endif
		@if(session('session_msg_error'))
		<div class="alert alert-danger">{!! session('session_msg_error') !!}</div>
		@endif

		<!-- order history -->
		@if(!empty($trackLists) && count($trackLists)>0)
		<div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
			<div class="col-lg-12">
				<h4>{{strtoupper(__('webMessage.trackhistory'))}}</h4>
				@foreach($trackLists as $trackList)
				<div class="row" @if(empty($trackList->is_seen)) style="font-weight:bold; color:#0066FF;" @endif>
					<div class="col-xs-12 col-md-2 col-lg-2">
						<div class="form-group">
							<h4>{{$trackList->details_date}}</h4>
						</div>
					</div>
					<div class="col-xs-12 col-md-10 col-lg-10">
						<div class="form-group">
							{!!$trackList['details_'.app()->getLocale() ]!!}
						</div>
					</div>
				</div>
				@php
				App\Http\Controllers\webCartController::updateSeendStatus($trackList->id);
				@endphp
				@endforeach
			</div>
		</div>
		@endif
		<!--end order history -->



		<!-- order status details -->
		<div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
			<div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.orderid')}} :</strong>
				<div>
					<strong>
						@if(!empty($orderDetails->order_id)) {{$orderDetails->order_id}}@else -- @endif
					</strong>
				</div>
			</div>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.paymentmethod')}} :</strong>
				<div>
					@if(!empty($orderDetails->pay_mode)) {{__($orderDetails->pay_mode)}}@else -- @endif
				</div>
			</div>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.payment_status')}} :</strong>
				<div>
					@if(!empty($orderDetails->is_paid)) <font color="#009900">{{strtoupper(__('webMessage.paid'))}}
					</font>@else <font color="#FF0000">{{strtoupper(__('webMessage.notpaid'))}}</font> @endif
				</div>
			</div>
			@php
			if(!empty($orderDetails->order_status) &&
			$orderDetails->order_status=='completed'){$color='#009900';}else{$color='#ff0000';}
			@endphp
			<div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.order_status')}} :</strong>
				<div>
					<font color="{{$color}}">{{strtoupper(__('webMessage.'.$orderDetails->order_status))}}</font>
				</div>
			</div>
			{{-- <div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.date')}} :</strong>
				<div>
					{{$orderDetails->created_at}}
				</div>
			</div> --}}
			@if(!empty($orderDetails->delivery_time_en) && !empty($orderDetails->delivery_time_ar))
			<div class="col-xs-12 col-md-3 col-lg-2">
				<strong>{{__('webMessage.deliverytime')}} :</strong>
				<div>
					{{$orderDetails['delivery_time_'.app()->getLocale()]}}
				</div>
			</div>
			@endif
		</div>
		<!-- order status details end -->



		<!--shopping cart start -->
		<div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
			<div class="col-lg-12">

				<table class="table">
					<thead class="tt-hidden-mobile">
						<tr>
							<th style="border-top:1px solid #fff;">{{__('webMessage.image')}}</th>
							<th style="border-top:1px solid #fff;">{{__('webMessage.details')}}</th>
							<th style="border-top:1px solid #fff;">{{__('webMessage.unit_price')}}</th>
							<th style="border-top:1px solid #fff;">{{__('webMessage.quantity')}}</th>
							<th style="border-top:1px solid #fff;" class="text-center">{{__('webMessage.subtotal')}}
							</th>
						</tr>
					</thead>
					<tbody>
						@php
						$unitprice=0;
						$subtotalprice=0;
						$totalprice=0;
						@endphp
						@if(!empty($orderLists) && count($orderLists)>0)
						
						@foreach($orderLists as $orderList)
						@php
						$productDetails
						=App\Http\Controllers\webCartController::getProductDetails($orderList->product_id);

						if($productDetails->image){
						$prodImage = url('uploads/product/thumb/'.$productDetails->image);
						}else{
						$prodImage = url('uploads/no-image.png');
						}

						$warrantyTxt='';
						if(!empty($productDetails->warranty)){
						$warrantyDetails =
						App\Http\Controllers\webCartController::getWarrantyDetails($productDetails->warranty);
						$warrantyTxt = app()->getLocale()=="en"?$warrantyDetails->title_en:$warrantyDetails->title_ar;
						}


						if(!empty($orderList->size_id)){
						$sizeName
						=App\Http\Controllers\webCartController::sizeNameStatic($orderList->size_id,app()->getLocale());
						$sizeName = trans('webMessage.size').':'.$sizeName;
						}else{$sizeName='';}
						if(!empty($orderList->color_id)){
						$colorName
						=App\Http\Controllers\webCartController::colorNameStatic($orderList->color_id,app()->getLocale());
						$colorName = trans('webMessage.color').':'.$colorName;
						//color image
						$colorImageDetails =
						App\Http\Controllers\webCartController::getColorImage($orderList->product_id,$orderList->color_id);
						if(!empty($colorImageDetails->color_image)){
						$prodImage = url('uploads/color/thumb/'.$colorImageDetails->color_image);
						}
						}else{$colorName='';}
						$optionsdetails = App\Http\Controllers\webCartController::getOptionsDtailsOrder($orderList->id);

						$unitprice = $orderList->unit_price;
						$subtotalprice = $unitprice*$orderList->quantity;

						@endphp
						<tr id="cart-{{$orderList->id}}">
							<td class="product-thumbnail">
								@if(!empty($productDetails->id))
								<a
									href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">
									<img src="{{$prodImage}}"
										alt="@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif">
								</a>
								@else
								<img src="{{url('uploads/no-image.png')}}"
									alt="@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif">
								@endif
							</td>
							<td class="product-name" data-title="{{__('webMessage.details')}}">
								@if(!empty($productDetails->id))
								<a
									href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif</a>
								@endif
								<div>
									@if($sizeName)<sapn style="margin: 3px;">{!!$sizeName!!}</sapn>@endif
									@if($colorName)<sapn style="margin: 3px;">{!!$colorName!!}</sapn>@endif
									@if($optionsdetails)<sapn style="margin: 3px;">{!!$optionsdetails!!}</sapn>@endif
									@if($warrantyTxt)<sapn style="margin: 3px;">{!!$warrantyTxt!!}</sapn>@endif
								</div>
							</td>
							<td class="product-price" data-title="{{__('webMessage.unit_price')}}">
								{{\App\Currency::default()}} {{$unitprice}}
							</td>
							<td class="product-stock-status" data-title="{{__('webMessage.quantity')}}">
								{{$orderList->quantity}}
							</td>
							<td class="product-price text-center" data-title="{{__('webMessage.subtotal')}}">
								{{\App\Currency::default()}} <span
									class="subtotal_result{{$orderList->id}}">{{$subtotalprice}}</span>
							</td>
						</tr>
						@php
						$totalprice+=$subtotalprice;
						@endphp
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6 col-lg-8">
						@if(!empty($orderDetails->order_id))
						@php
						$TransDetails = App\Http\Controllers\webCartController::TransDetails($orderDetails->order_id);

						@endphp
						@if(!empty($TransDetails->id))
						<h4>{{strtoupper(trans('webMessage.transactiondetails'))}}</h4>
						<div class="row">
							@if(!empty($TransDetails['payment_id']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.paymentid')}} :</strong> {{$TransDetails['payment_id']}}
							</div>
							@endif
							@if(!empty($TransDetails['MfTrackId']))
								<div class="col-lg-6">
									<strong>{{trans('webMessage.trackid')}} : </strong> {{$TransDetails['MfTrackId']}}
								</div>
							@elseif(!empty($TransDetails['trackid']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.trackid')}} : </strong> {{$TransDetails['trackid']}}
							</div>
							@endif
							@if(!empty($TransDetails['tranid']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.transid')}} : </strong> {{$TransDetails['tranid']}}
							</div>
							@endif
							@if(!empty($TransDetails['paypal_cart']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.transid')}} : </strong> {{$TransDetails['paypal_cart']}}
							</div>
							@endif
							@php
							if(!empty($TransDetails['presult']) &&
							$TransDetails['presult']=='CAPTURED'){$color='#009900';}else{$color='#ff0000';}
							@endphp

							<div class="col-lg-6">
								<strong>{{trans('webMessage.result')}} : </strong> @if(!empty($TransDetails['presult']))
								<font color="{{$color}}"> {{$TransDetails['presult']}} </font> @endif
							</div>
							@if(!empty($TransDetails['udf2']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.amount')}} : </strong> {{$TransDetails['udf2'].'
								'.\App\Currency::defaultCMS()}}
							</div>
							@endif
							@if(!empty($TransDetails['amt_dollar']))
							<div class="col-lg-6">
								<strong>{{trans('webMessage.amount')}} : </strong>
								<font color="#009900">{{trans('webMessage.usd').''.$TransDetails['amt_dollar']}}</font>
							</div>
							@endif
							<div class="col-lg-6">
								<strong>{{trans('webMessage.date')}} : </strong>
								@if(!empty($TransDetails['created_at'])) {{$TransDetails['created_at']}} @endif
							</div>
						</div>
						@endif
						@endif
					</div>
					<div class="col-md-6 col-lg-4">
						<div class="tt-shopcart-box tt-boredr-large">

                                            @if($orderDetails->linkDescription == null)
                                            <table class="tt-shopcart-table01">
                                                <tbody>
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.subtotal'))}}</th>
                                                        <td>{{\App\Currency::default()}} {{$totalprice}}</td>
                                                    </tr>
                                                    <!--seller discount -->
                                                    @if(!empty($orderDetails->seller_discount))
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.seller_discount'))}}</th>
                                                        <td>
                                                            <font color="#FF0000">-{{\App\Currency::default()}}
                                                                {{$orderDetails->seller_discount}}</font>
                                                        </td>
                                                    </tr>
                                                    @php
                                                    $totalprice=$totalprice-$orderDetails->seller_discount;
                                                    @endphp
                                                    @endif
                                                    <!--end-->
                                                    @if(!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_amount))
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.coupon_discount'))}}</th>
                                                        <td>
                                                            <font color="#FF0000">-{{\App\Currency::default()}}
                                                                {{$orderDetails->coupon_amount}}</font>
                                                        </td>
                                                    </tr>
                                                    @php
                                                    $totalprice=$totalprice-$orderDetails->coupon_amount;
                                                    @endphp
                                                    @endif
                                                    @if(!empty($orderDetails->coupon_code) &&
                                                    !empty($orderDetails->coupon_free))
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.coupon_discount'))}}</th>
                                                        <td>
                                                            <font color="#FF0000">
                                                                {{strtoupper(__('webMessage.free_delivery'))}}</font>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @if(empty($orderDetails->delivery_charges))
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                                        <td>
                                                            <font color="#FF0000">
                                                                {{strtoupper(__('webMessage.free_delivery'))}}</font>
                                                        </td>
                                                    </tr>
                                                    @endif

                                                    @if(!empty($orderDetails['delivery_charges']) &&
                                                    empty($orderDetails['coupon_free']))
                                                    @php
                                                    $deliveryCharge = $orderDetails['delivery_charges'];
                                                    @endphp
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                                        <td>{{\App\Currency::default()}} {{$deliveryCharge}}</td>
                                                    </tr>
                                                    @php
                                                    $totalprice=$totalprice+$deliveryCharge;
                                                    @endphp
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.grandtotal'))}}</th>
                                                        <td>{{\App\Currency::default()}} <span
                                                                class="total_result">{{$totalprice}}</span></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            @else
                                            <table class="tt-shopcart-table01">
                                                <tfoot>
                                                    <tr>
                                                        <th>{{strtoupper(__('webMessage.grandtotal'))}}</th>
                                                        <td>{{\App\Currency::default()}} <span
                                                                class="total_result">{{number_format($orderDetails->total_amount
                                                                , 3)}}</span></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            @endif
                                        </div>
					</div>
				</div>
			</div>
		</div>

		<div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.name')}} :</strong> @if($orderDetails->name)
						{{$orderDetails->name}}@else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.email')}} :</strong> @if($orderDetails->email)
						{{$orderDetails->email}}@else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.mobile')}} :</strong> @if($orderDetails->mobile)
						{{$orderDetails->mobile}}@else -- @endif
					</div>
				</div>

				@php
				$countryInfo = App\Http\Controllers\webCartController::get_csa_info($orderDetails->country_id);
				$stateInfo = App\Http\Controllers\webCartController::get_csa_info($orderDetails->state_id);
				$areaInfo = App\Http\Controllers\webCartController::get_csa_info($orderDetails->area_id);
				@endphp
				<div class="row">
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.country')}} :</strong> @if($orderDetails->country_id)
						{{$countryInfo['name_'.app()->getLocale()]}}@else -- @endif
					</div>

					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.state')}} :</strong> @if($orderDetails->state_id)
						{{$stateInfo['name_'.app()->getLocale()]}}@else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.area')}} :</strong> @if($orderDetails->area_id)
						{{$areaInfo['name_'.app()->getLocale()]}} @endif
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.block')}} :</strong> @if($orderDetails->block)
						{{$orderDetails['block']}}@else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.street')}} :</strong> @if($orderDetails->street)
						{{$orderDetails['street']}}@else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.avenue')}} :</strong> @if($orderDetails->avenue)
						{{$orderDetails['avenue']}}@else -- @endif
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.house')}} :</strong> @if($orderDetails['house'])
						{{$orderDetails['house']}} @else -- @endif
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.floor')}} :</strong> @if($orderDetails['floor'])
						{{$orderDetails['floor']}} @else -- @endif
					</div>

					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.landmark')}} :</strong> @if($orderDetails->landmark)
						{{$orderDetails['landmark']}} @else -- @endif
					</div>

					@if($orderDetails->is_express_delivery)

					<div class="col-xs-12 col-md-6 col-lg-4">
						<strong>{{__('webMessage.isSendByExpress')}}</strong>
					</div>
					@endif
				</div>

			</div>
		</div>

		@if(! $orderDetails->is_paid)
		<!-- payment start -->
		<div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
			<div class="col-lg-12">
				<form
					action="{{ route('order.details.pay' , ['locale' => App()->getLocale() , 'orderid' => $orderDetails->order_id_md5 ]) }}"
					method="POST">
					@csrf
					<h4>{{strtoupper(__('webMessage.payonline'))}}</h4>
					<p class="mb-3">{{__('webMessage.payonlinedescription')}}</p>
					@php
					$payments = App\Country::getGateways($orderDetails->linkDescription == null ?
                                $orderDetails->country_id : $domainCountry->id);
					$p=1;
					@endphp
					@if(count($payments) > 0)
					<div class="payment_option">
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
						<div class="custome-radio">
							<input class="form-check-input" required="" type="radio" name="payment_method"
								id="{{$payment}}" value="{{$payment}}" @if($p==1) checked @endif>
							<label class="form-check-label" for="{{$payment}}">
								<img src="{{url('uploads/paymenticons/'.strtolower($payment).'.png')}}" height="30"
									alt="{{__('webMessage.payment_'.$payment)}}">&nbsp;{{$paytxt}}
							</label>
						</div>
						@php $p++;@endphp
						@endforeach
					</div>
					@endif
					<button type="submit" class="btn btn-fill-out">{{strtoupper(__('webMessage.paynow'))}}</button>
				</form>
			</div>
		</div>
		<!--end payment end -->
		@endif

	</div>
	<!-- end shopping cart -->
	{{-- @else
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="text-center order_complete">
				<i class="fas fa-times-circle"></i>
				<div class="heading_s1">
					<h3>{{__('webMessage.404')}}</h3>
				</div>
				<p>{{__('webMessage.norecordfound')}}</p>
				<a href="javascript:history.go(-1);" class="btn btn-fill-out">{{__('webMessage.goback')}}</a>
			</div>
		</div>
	</div>
	@endif --}}
</div>
</div>
<!-- END SECTION SHOP -->
@endsection


@section('js')

@if(!empty($orderDetails->pay_mode) && $orderDetails->pay_mode=='POSTKNET')
@php
if(app()->getLocale()=="en" && !empty($settingInfo->postknet_note_en)){
$postknetnote = $settingInfo->postknet_note_en;
}elseif(app()->getLocale()=="ar" && !empty($settingInfo->postknet_note_ar)){
$postknetnote = $settingInfo->postknet_note_ar;
}else{
$postknetnote = trans('webMessage.thankyoufordoingbusinesswithus');
}
@endphp
<script>
	$(document).ready(function () {
                $("#spancartbox").html("<h3 class='alert alert-default'>{{$postknetnote}}</h3>");
                $("#modalDefaultBox").modal("show");//show modal
            });

			
</script>
@endif
<script>
	dataLayer.push({ecommerce: null}); // Clear the previous ecommerce object.
	
	dataLayer.push({
	'event':'enhance_purchase',
	'order_value': '{{ number_format($orderDetails->total_amount, 3) }}', // Fetch total value dynamically
	'order_id': '{{ $orderDetails->order_id }}', // Fetch Order id dynamically
	'enhanced_conversion_data': {
	"email": '{{ $orderDetails->email }}' // Fetch user email dynamically
	}
	})
</script>
@endsection
