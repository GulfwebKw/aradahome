@php
$settingInfo = App\Http\Controllers\webController::settings();
if(app()->getLocale()=="en"){$strLang="en";}else{$strLang="ar";}
use Illuminate\Support\Facades\Cookie;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>@if(app()->getLocale()=="en") {{$settingInfo->name_en}} @else {{$settingInfo->name_ar}} @endif | {{__('webMessage.orderconfirm')}}</title>
<meta name="description" content="@if(app()->getLocale()=="en") {{$settingInfo->seo_description_en}} @else {{$settingInfo->seo_description_ar}} @endif" />
<meta name="abstract" content="@if(app()->getLocale()=="en") {{$settingInfo->seo_description_en}} @else {{$settingInfo->seo_description_ar}} @endif">
<meta name="keywords" content="@if(app()->getLocale()=="en") {{$settingInfo->seo_keywords_en}} @else {{$settingInfo->seo_keywords_ar}} @endif" />
<meta name="Copyright" content="{{$settingInfo->name_en}}, Kuwait Copyright 2020 - {{date('Y')}}" />
<META NAME="Geography" CONTENT="@if(app()->getLocale()=="en") {{$settingInfo->address_en}} @else {{$settingInfo->address_ar}} @endif">
@if($settingInfo->extra_meta_tags) {!!$settingInfo->extra_meta_tags!!} @endif
@if($settingInfo->favicon)
<link rel="icon" href="{{url('uploads/logo/'.$settingInfo->favicon)}}">
@endif
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
@include("website.includes.css")
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<!--preloader
    @include("website.includes.preloader")
    end preloader -->
<!--header -->
@include("website.includes.header")
<!--end header -->
<div class="tt-breadcrumb">
	<div class="container">
		<ul>
			<li><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
			<li>{{__('webMessage.orderconfirm')}}</li>
		</ul>
	</div>
</div>
<div id="tt-pageContent">
	<div class="container-indent">
		<div class="container">
			<h1 class="tt-title-subpages noborder">{{__('webMessage.orderconfirm')}}</h1>
            @if(!empty($tempOrders) && count($tempOrders)>0)
            <div class="tt-collapse-block">

      <form id="checkoutform" class="contact-form form-default" method="post" novalidate action="{{route('checkoutconfirmform')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="row">
                            <div class="col-lg-12">
                            <a href="{{url(app()->getLocale().'/checkout')}}" class="btn btn-lg btn-info pull-{{__('webMessage.align')}}"><span class="icon icon-check_circle"></span>{{strtoupper(__('webMessage.backtocheckoutt'))}}</a>
                            <button type="submit" class="btn btn-lg float-{{__('webMessage.oalign')}}">{{strtoupper(__('webMessage.confirm'))}}</button>
                            </div>
                            </div>

                            <!--shopping cart start -->
							<div class="tt-item active">
								<div class="tt-collapse-title">{{strtoupper(__('webMessage.shoppingcart'))}}</div>
								<div class="tt-collapse-content">

            <span id="result_reponse_cart"></span>
			<div class="tt-shopcart-table-02">
				<table>
                    <thead class="tt-hidden-mobile">
                    <tr>
                    <th>{{__('webMessage.image')}}</th>
                    <th>{{__('webMessage.details')}}</th>
                    <th>{{__('webMessage.unit_price')}}</th>
                    <th>{{__('webMessage.quantity')}}</th>
                    <th>{{__('webMessage.subtotal')}}</th>
                    </tr>
                    </thead>
					<tbody>
                    @php
                    $unitprice=0;
                    $subtotalprice=0;
                    $totalprice=0;
                    @endphp
                    @foreach($tempOrders as $tempOrder)
                    @php
                    $productDetails =App\Http\Controllers\webCartController::getProductDetails($tempOrder->product_id);
                    if(!empty($tempOrder->size_id)){
                    $sizeName =App\Http\Controllers\webCartController::sizeNameStatic($tempOrder->size_id,$strLang);
                    $sizeName = '<li>'.trans('webMessage.size').':'.$sizeName.'</li>';
                    }else{$sizeName='';}
                    if(!empty($tempOrder->color_id)){
                    $colorName =App\Http\Controllers\webCartController::colorNameStatic($tempOrder->color_id,$strLang);
                    $colorName = '<li>'.trans('webMessage.color').':'.$colorName.'</li>';
                    }else{$colorName='';}
					$optionsDetails = App\Http\Controllers\webCartController::getOptionsDtails($tempOrder->id);

                    $unitprice = $tempOrder->unit_price;
                    $subtotalprice = $unitprice*$tempOrder->quantity;
                    $aquantity = App\Http\Controllers\webCartController::getProductQuantity($tempOrder->product_id,$tempOrder->size_id,$tempOrder->color_id);
                    @endphp
						<tr id="cart-{{$tempOrder->id}}">
							<td><a href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">
								<div class="tt-product-img">
									<img src="{{url('assets/images/loader.svg')}}" data-src="{{url('uploads/product/thumb/'.$productDetails['image'])}}" alt="@if(!empty($productDetails['title_'.$strLang])){{$productDetails['title_'.$strLang]}}@endif">
								</div></a>
							</td>
							<td>
								<h2 class="tt-title">
									<a href="{{url('directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">@if(!empty($productDetails['title_'.$strLang])){{$productDetails['title_'.$strLang]}}@endif</a>
								</h2>
								<ul class="tt-list-description">
									{!!$sizeName!!}
									{!!$colorName!!}
									{!!$optionsDetails['optionName']!!}
								</ul>
								<ul class="tt-list-parameters">
									<li>
										<div class="tt-price">
											{{\App\Currency::default()}} {{$unitprice}}
										</div>
									</li>
									<li>
										<div class="detach-quantity-mobile"></div>
									</li>
									<li>
										<div class="tt-price subtotal">
											{{\App\Currency::default()}} <span class="subtotal_result{{$tempOrder->id}}">{{$subtotalprice}}</span>
										</div>
									</li>
								</ul>
							</td>
							<td>
								<div class="tt-price">
									{{\App\Currency::default()}} {{$unitprice}}
								</div>
							</td>
							<td>
								<div class="detach-quantity-desctope">
									<div class="tt-input-counter style-01">
										{{$tempOrder->quantity}}
									</div>
								</div>
							</td>
							<td align="center">
								<div class="tt-price subtotal" align="center">
									{{\App\Currency::default()}} <span class="subtotal_result{{$tempOrder->id}}">{{$subtotalprice}}</span>
								</div>
							</td>

						</tr>
                        @php
                        $totalprice+=$subtotalprice;
                        @endphp
						@endforeach

					</tbody>
				</table>

			</div>
			<div class="tt-shopcart-col">
				<div class="row">
					<div class="col-md-6 col-lg-8">&nbsp;</div>
					<div class="col-md-6 col-lg-4">
						<div class="tt-shopcart-box tt-boredr-large">
							<table class="tt-shopcart-table01">
								<tbody>
									<tr>
										<th>{{strtoupper(__('webMessage.subtotal'))}}</th>
										<td>{{\App\Currency::default()}} <span class="total_result">{{$totalprice}}</span></td>
									</tr>
                                   @if(!empty(Cookie::get('gb_coupon_code')) && floatval(preg_replace("/[^-0-9\.]/","",Cookie::get('gb_coupon_free'))))
                                    <tr>
										<th>{{strtoupper(__('webMessage.coupon_discount'))}}</th>
										<td><font color="#FF0000">-{{Cookie::get('gb_coupon_discount_text')}}</font></td>
									</tr>
                                   @php
                                   $totalprice=$totalprice-Cookie::get('gb_coupon_discount');
                                   @endphp
                                   @endif
                                   @if(!empty(Cookie::get('gb_coupon_code')) && !empty(Cookie::get('gb_coupon_free')))
                                   <tr>
										<th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
										<td><font color="#FF0000">{{strtoupper(__('webMessage.free_delivery'))}}</font></td>
									</tr>
                                   @endif
                                   @if(!empty(Cookie::get('area')) && empty(Cookie::get('gb_coupon_free')))
                                   @php
                                   $deliveryCharge = App\Http\Controllers\webCartController::get_delivery_charge(Cookie::get('area'));
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
										<td>{{\App\Currency::default()}} <span class="total_result">{{$totalprice}}</span></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>

								</div>
							</div>
							<!--shopping end-->
                            <!--deliveryaddress start-->
							<div class="tt-item active">
								<div class="tt-collapse-title">{{strtoupper(__('webMessage.deliveryaddress'))}}</div>
								<div class="tt-collapse-content">

									<div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="name">{{__('webMessage.name')}}</label>
									<div  class="form-control">@if(Cookie::get('name')) {{Cookie::get('name')}}@else -- @endif</div>

									</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="email">{{__('webMessage.email')}}</label>
                                    <div  class="form-control">@if(Cookie::get('email')) {{Cookie::get('email')}}@else -- @endif</div>
                                    </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="mobile">{{__('webMessage.mobile')}}</label>
                                    <div  class="form-control">@if(Cookie::get('mobile')) {{Cookie::get('mobile')}}@else -- @endif</div>
                                    </div>
                                    </div>
                                    </div>
                                    @php
                                    $countryInfo = App\Http\Controllers\webCartController::get_csa_info(Cookie::get('country'));
                                    $stateInfo   = App\Http\Controllers\webCartController::get_csa_info(Cookie::get('state'));
                                    $areaInfo    = App\Http\Controllers\webCartController::get_csa_info(Cookie::get('area'));
                                    @endphp
                                    <div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="country">{{__('webMessage.country')}}</label>
									<div  class="form-control">@if(Cookie::get('country')) {{$countryInfo['name_'.$strLang]}}@else -- @endif</div>
									</div>
                                    </div>

                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="state">{{__('webMessage.state')}}</label>
									<div  class="form-control">@if(Cookie::get('state')) {{$stateInfo['name_'.$strLang]}}@else -- @endif</div>
									</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="area">{{__('webMessage.area')}}</label>
									<div  class="form-control">@if(Cookie::get('area')) {{$areaInfo['name_'.$strLang]}} @endif</div>
									</div>
                                    </div>
                                    </div>

                                    <div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="block">{{__('webMessage.block')}}</label>
									<div  class="form-control">@if(Cookie::get('block')) {{Cookie::get('street')}}@else -- @endif</div>
									</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="street">{{__('webMessage.street')}}</label>
                                    <div  class="form-control">@if(Cookie::get('street')) {{Cookie::get('street')}}@else -- @endif</div>
                                    </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="avenue">{{__('webMessage.avenue')}}</label>
                                    <div  class="form-control">@if(Cookie::get('avenue')) {{Cookie::get('avenue')}}@else -- @endif</div>
                                    </div>
                                    </div>
                                    </div>

                                    <div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="house">{{__('webMessage.house')}}</label>
                                    <div  class="form-control">@if(Cookie::get('house')) {{Cookie::get('house')}} @else -- @endif</div>
                                    </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="floor">{{__('webMessage.floor')}}</label>
									<div  class="form-control">@if(Cookie::get('floor')) {{Cookie::get('floor')}} @else -- @endif</div>
									</div>
                                    </div>

                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label for="landmark">{{__('webMessage.landmark')}}</label>
                                    <div  class="form-control">@if(Cookie::get('landmark')) {{Cookie::get('landmark')}} @else -- @endif</div>
                                    </div>
                                    </div>
                                    </div>



								</div>
							</div>
                            <!--deliveryaddress end-->
                            @if(!empty(Cookie::get('payment_method')))
                            <!--payment start-->
							<div class="tt-item active" >
								<div class="tt-collapse-title">{{strtoupper(__('webMessage.paymentmethod'))}}</div>
								<div class="tt-collapse-content">
									<div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    {{__('webMessage.payment_'.Cookie::get('payment_method'))}}
									</div>
                                    </div>
                                    </div>
								</div>
							</div>
                            <!--payment end-->
                            @endif

                            <div class="row">
                            <div class="col-lg-12">
                            <a href="{{url(app()->getLocale().'/checkout')}}" class="btn btn-lg btn-info float-{{__('webMessage.align')}}"><span class="icon icon-check_circle"></span>{{strtoupper(__('webMessage.backtocheckoutt'))}}</a>
                            <button type="submit" class="btn btn-lg float-{{__('webMessage.oalign')}}">{{strtoupper(__('webMessage.confirm'))}}</button>
                            </div>
							</div>

                            </form>
						</div>
            @else
            <div align="center"><p>{{__('webMessage.yourcartisempty')}}</p></div>
            @endif
		</div>
	</div>
</div>
<!--footer-->
@include("website.includes.footer")
<script src="{{url('assets/external/jquery/jquery.min.js')}}"></script>

<!--<script src="{{url('assets/external/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{url('assets/external/slick/slick.min.js')}}"></script>
<script src="{{url('assets/external/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{url('assets/external/panelmenu/panelmenu.js')}}"></script>
<script src="{{url('assets/external/instafeed/instafeed.min.js')}}"></script>
<script src="{{url('assets/external/countdown/jquery.plugin.min.js')}}"></script>
<script src="{{url('assets/external/countdown/jquery.countdown.min.js')}}"></script>
<script src="{{url('assets/external/rs-plugin/js/jquery.themepunch.tools.min.js')}}"></script>
<script src="{{url('assets/external/rs-plugin/js/jquery.themepunch.revolution.min.js')}}"></script>
<script src="{{url('assets/external/lazyLoad/lazyload.min.js')}}"></script>
<script src="{{url('assets/js/main.js')}}"></script>
--><script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script defer src="{{url('assets/js/bundle.js')}}"></script>
<script src="{{url('assets/js/gulfweb.js?v2')}}"></script>
<script>
gtag('event', 'screen_view', {
  'screen_name' : 'order confirmation'
});
</script>
</body>
</html>
