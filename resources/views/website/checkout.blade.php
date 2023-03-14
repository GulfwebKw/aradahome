@extends('website.include.master')
@php
    if(app()->getLocale()=="en"){$strLang="en";}else{$strLang="ar";}
    use Illuminate\Support\Facades\Cookie;
    if(!empty(Auth::guard('webs')->user()->is_seller)){
    $userType=1;
    }else{
    $userType=0;
    }
    $pixelids =[];
@endphp

@section('title' , __('webMessage.checkout') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/cart')}}">{{ __('webMessage.shoppingcart')  }}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.checkout')  }}</li>
@endsection

@section('header')
    <style>
        #checktotalbox a {
            display: none;
        }
        .custome-radio input[type="radio"] + .form-check-label::after {
            top: 13px !important;
        }
    </style>
@endsection
@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            @if(!empty($tempOrders) && count($tempOrders)>0)
                <div class="row">
                    @if ($errors->any())
                        <div class="col-lg-12">
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">{!!  $error !!}</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="col-lg-6">
                        @if( ! Auth::guard('webs')->check())
                            <div class="toggle_info">
                                <span>
                                    <i class="fas fa-user"></i>
                                    <a href="#loginform" data-bs-toggle="collapse" class="collapsed" aria-expanded="false">
                                        {{ __('webMessage.ifyouhaveanaccountwithus') }}
                                    </a>
                                </span>
                            </div>
                            <div class="panel-collapse collapse login_form" id="loginform">
                                <div class="panel-body">
                                    <form id="customer_login_form" method="post" action="{{route('loginform',['locale'=>app()->getLocale()])}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group mb-3">
                                            <input type="text" name="login_username"  class="form-control @if($errors->has('login_username')) error @endif" id="login_username" placeholder="{{__('webMessage.username_or_email')}}" autcomplete="off" value="@if(Cookie::get('xlogin_username')) {{Cookie::get('xlogin_username')}} @elseif(old('login_username')) {{old('login_username')}} @endif">
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="password" name="login_password"  class="form-control @if($errors->has('login_password')) error @endif" id="login_password" placeholder="{{__('webMessage.password')}}" autcomplete="off"  value="@if(Cookie::get('xlogin_password')) {{Cookie::get('xlogin_password')}} @elseif(old('login_password')) {{old('login_password')}} @endif">
                                        </div>
                                        <div class="login_footer form-group mb-3">
                                            <div class="chek-form">
                                                <div class="custome-checkbox">
                                                    <input type="checkbox" id="remember_me" name="remember_me" @if(Illuminate\Support\Facades\Cookie::get('xremember_me')) checked @endif  value="1">
                                                    <label for="remember_me"><span class="check"></span><span class="box"></span>&nbsp;{{__('webMessage.remember_me_txt')}}</label>
                                                </div>
                                            </div>
                                            <a href="{{url(app()->getLocale().'/password/reset')}}">{{__('webMessage.forgot_password_txt')}}</a>
                                        </div>
                                        <div class="form-group mb-3">
                                            <button type="submit" class="btn btn-fill-out btn-block" name="login" >{{__('webMessage.login')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($settingInfo->is_discount)
                        <div class="col-lg-6">
                            <div class="toggle_info">
                                <span><i class="fas fa-tag"></i><a href="#coupon" data-bs-toggle="collapse" class="collapsed" aria-expanded="false">{{ __('webMessage.applycoupon') }}</a></span>
                            </div>
                            <div class="panel-collapse coupon_form collapse" id="coupon">
                                <div class="panel-body">
                                    <div class="coupon field_form input-group">
                                        @if(!empty($userType))
                                            <input type="text" autcomplete="off" onkeydown="$('#replaySeller_discount').val($(this).val());" value="{{ old('seller_discount' , Cookie::get('gb_seller_discount') ?? '' ) }}" class="form-control" id="seller_discount" placeholder="{{__('webMessage.enter_seller_discount')}}">
                                            <div class="input-group-append">
                                                <button class="btn btn-fill-out btn-sm applyselletdiscountbtn" type="button">{{__('webMessage.apply')}}</button>
                                            </div>
                                        @else
                                            <input type="text" autcomplete="off" onkeydown="$('#replayCoupon_code').val($(this).val());" value="{{ old('coupon_code' , Cookie::get('gb_coupon_code') ?? '' ) }}" class="form-control" id="coupon_code" placeholder="{{__('webMessage.enter_coupon_code')}}">
                                            <div class="input-group-append">
                                                <button class="btn btn-fill-out btn-sm applycouponbtn" type="button">{{__('webMessage.apply')}}</button>
                                            </div>
                                        @endif
                                    </div>
                                    <span style="margin-top: 15px;border-radius: 3px;" id="result_coupon"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <form style="margin-top:0;" id="checkoutform" method="post" action="{{route('checkoutconfirmform',['locale'=>app()->getLocale()])}}">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="coupon_code" id="replayCoupon_code" value="{{ old('coupon_code' , Cookie::get('gb_coupon_code') ?? '' ) }}">
                    @if(!empty($userType))
                        <input type="hidden" name="seller_discount" id="replaySeller_discount" value="{{ old('seller_discount' , Cookie::get('gb_seller_discount') ?? '' ) }}">
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <div class="medium_divider"></div>
                            <div class="divider center_icon"><i class="linearicons-credit-card"></i></div>
                            <div class="medium_divider"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="heading_s1">
                                <h4>{{strtoupper(__('webMessage.deliveryaddress'))}}</h4>
                            </div>
                            @if(empty($userType))
                                @php
                                    $customerAddress = App\Http\Controllers\webCartController::customerAddress();
                                @endphp
                                @if(!empty($customerAddress) && count($customerAddress)>0)
                                    <div class="form-group mb-3">
                                        <div class="custom_select">
                                            <select name="myaddress" id="myaddress"
                                                    class="form-control">
                                                <option value="0">{{__('webMessage.chooseaddress')}}</option>
                                                @foreach($customerAddress as $custaddress)
                                                    {{--                                        @if($custaddress->country_id !=  $domainCountry->id)--}}
                                                    {{--                                            @continue--}}
                                                    {{--                                        @endif--}}
                                                    <option value="{{$custaddress->id}}"
                                                            @if((!empty(Cookie::get('address_id')) && $custaddress->id==Cookie::get('address_id')) || (!empty($address->id) && $custaddress->id==$address->id)) @if($custaddress->country_id ==  $domainCountry->id) selected
                                                            @endif @endif @if($custaddress->country_id !=  $domainCountry->id) disabled @endif >{{$custaddress->title}} @if($custaddress->country_id !=  $domainCountry->id)
                                                            ( {{ __('webMessage.OutOfCountry' , ['country' => $domainCountry['name_'.app()->getLocale()]]) }}
                                                            )
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @php
                                if(!empty($userType)){
                                $name = 'Customer';
                                }else if(old('name')){
                                $name = old('name');
                                }elseif(!empty($userDetailsCheckout->name)){
                                $name = $userDetailsCheckout->name;
                                }elseif(Cookie::get('name')){
                                $name = Cookie::get('name');
                                } else {
                                    $name ="";
                                }
                            @endphp
                            <div class="form-group mb-3">
                                <input type="text" name="name" class="form-control" id="name"
                                       placeholder="{{__('webMessage.name')}}{{$settingInfo->validate_cust_name==1?' *':''}}"
                                       autcomplete="off" value="{{$name}}" {{$settingInfo->validate_cust_name==1?'required':''}}>
                                @if($errors->has('name'))
                                    <label id="name-error" class="error"
                                           for="name">{{$errors->first('name')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="email" class="form-control" id="email"
                                       placeholder="{{__('webMessage.email')}}"
                                       autcomplete="off"
                                       value="{{old('email' , ( isset($userDetailsCheckout) and !empty($userDetailsCheckout->email)) ? $userDetailsCheckout->email : ( Cookie::get('email') ?? '' ) )}}">
                                @if($errors->has('email'))
                                    <label id="email-error" class="error"
                                           for="email">{{$errors->first('email')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="mobile" class="form-control" id="mobile"
                                       placeholder="{{__('webMessage.mobile')}}{{$settingInfo->validate_cust_mob==1?' *':''}}"
                                       autcomplete="off" {{$settingInfo->validate_cust_mob==1?'required':''}}
                                       value="{{old('mobile' , ( isset($userDetailsCheckout) and !empty($userDetailsCheckout->mobile)) ? $userDetailsCheckout->mobile : ( Cookie::get('mobile') ?? '' ))}}">
                                @if($errors->has('mobile'))
                                    <label id="mobile-error" class="error"
                                           for="mobile">{{$errors->first('mobile')}}</label>
                                @endif
                            </div>
                            @php
                                $areaid  = !empty(Cookie::get('area'))?Cookie::get('area'):(!empty($userDetailsCheckout->area)?$userDetailsCheckout->area:'0');
                                $areatxt = App\Http\Controllers\webCartController::get_Country_areas($areaid > 0 ? $areaid : $domainCountry->id , false);
                                $countryLists = App\Http\Controllers\webCartController::get_country(0);
                                //$countryListsSelected = App\Http\Controllers\webCartController::get_country_of_area($areaid);
                                $countryListsSelected =  $domainCountry->id;
                            @endphp
                            <div class="form-group mb-3">
                                <div class="custom_select">
                                        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                                        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
									<select name="area"  class="form-control area_checkout js-example-basic-single" id="area" {{$settingInfo->validate_cust_area==1?'required':''}}>
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
                                    @if($errors->has('area'))
                                        <label id="area-error" class="error"
                                               for="area">{{$errors->first('area')}}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="block" class="form-control" id="block"
                                       placeholder="{{__('webMessage.block')}}{{$settingInfo->validate_cust_block==1?' *':''}}"
                                       autcomplete="off" {{$settingInfo->validate_cust_block==1?'required':''}}
                                       value="{{old('block' , ( isset($address) and !empty($address->block)) ? $address->block : ( Cookie::get('block') ?? '' ))}}">
                                @if($errors->has('block'))
                                    <label id="block-error" class="error"
                                           for="block">{{$errors->first('block')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="street" class="form-control" id="street"
                                       placeholder="{{__('webMessage.street')}}{{$settingInfo->validate_cust_st==1?' *':''}}"
                                       autcomplete="off" {{$settingInfo->validate_cust_st==1?'required':''}}
                                       value="{{old('street' , ( isset($address) and !empty($address->street)) ? $address->street : ( Cookie::get('street') ?? '' ))}}">
                                @if($errors->has('street'))
                                    <label id="street-error" class="error"
                                           for="street">{{$errors->first('street')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="avenue" class="form-control" id="avenue"
                                       placeholder="{{__('webMessage.avenue')}}"
                                       autcomplete="off"
                                       value="{{old('avenue' , ( isset($address) and !empty($address->avenue)) ? $address->avenue : ( Cookie::get('avenue') ?? '' ))}}">
                                @if($errors->has('avenue'))
                                    <label id="avenue-error" class="error"
                                           for="avenue">{{$errors->first('avenue')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="house" class="form-control" id="house"
                                       placeholder="{{__('webMessage.house')}}{{$settingInfo->validate_cust_hno==1?' *':''}}"
                                       autcomplete="off" {{$settingInfo->validate_cust_hno==1?'required':''}}
                                       value="{{old('house' , ( isset($address) and !empty($address->house)) ? $address->house : ( Cookie::get('house') ?? '' ))}}">
                                @if($errors->has('house'))
                                    <label id="house-error" class="error"
                                           for="house">{{$errors->first('house')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="floor" class="form-control" id="floor"
                                       placeholder="{{__('webMessage.floor')}}"
                                       autcomplete="off"
                                       value="{{old('floor' , ( isset($address) and !empty($address->floor)) ? $address->floor : ( Cookie::get('floor') ?? '' ))}}">
                                @if($errors->has('floor'))
                                    <label id="floor-error" class="error"
                                           for="floor">{{$errors->first('floor')}}</label>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="landmark" class="form-control"
                                       id="landmark"
                                       placeholder="{{__('webMessage.landmark')}}"
                                       autcomplete="off"
                                       value="{{old('landmark' , ( isset($address) and !empty($address->landmark)) ? $address->landmark : ( Cookie::get('landmark') ?? '' ))}}">
                                @if($errors->has('landmark'))
                                    <label id="landmark-error" class="error"
                                           for="landmark">{{$errors->first('landmark')}}</label>
                                @endif
                            </div>
                            @php
                                $deliverytimeslists = App\Http\Controllers\webCartController::listDeliveryTimes();
                            @endphp
                            @if(!empty($deliverytimeslists) && count($deliverytimeslists)>0)
                                <div class="form-group mb-3">
                                    <div class="custom_select">
                                        <select name="delivery_time" id="delivery_time"
                                                class="form-control">
                                            <option value="0">{{__('webMessage.choosedeliverytimes')}}</option>
                                            @foreach($deliverytimeslists as $deliverytimeslist)
                                                <option value="{{$deliverytimeslist->id}}">{{$strLang=="en"?$deliverytimeslist->title_en:$deliverytimeslist->title_ar}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($userType))
                                @php
                                    $cdate = date("Y-m-d");
                                    $defaultDeliveryDate = date("Y-m-d",strtotime($cdate.'+1 day'));
                                @endphp
                                <div class="form-group mb-3">
                                    <input type="text" name="delivery_date" class="form-control"
                                           id="delivery_date"
                                           placeholder="{{__('webMessage.delivery_date')}}"
                                           autcomplete="off"
                                           value="{{ old('delivery_date' ,  Cookie::get('gb_delivery_date') ?? Cookie::get('gb_delivery_date') )  }}">
                                </div>
                            @endif
                            
                            @if ( $domainCountry->shipment_method == "flatrate" and $settingInfo->is_express)
                            <div class="form-group mb-3">
                                <div class="chek-form">
                                    <div class="custome-checkbox">
                                        <input @if(Cookie::get('is_express_delivery' , 0)) checked @endif type="checkbox" id="is_express_delivery" value="1" name="is_express_delivery">
                                        <label for="is_express_delivery"><span class="check"></span><span class="box"></span>&nbsp;{{ __('webMessage.isSendByExpress') }}</label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(empty(Auth::guard('webs')->user()))
                            <div class="form-group mb-3">
                                <div class="chek-form">
                                    <div class="custome-checkbox">
                                        <input class="form-check-input" type="checkbox" name="register_me" value="1" id="register_me" @if(old('register_me')) checked @endif onchange="if (this.checked) $('.create-account').fadeIn('slow'); else  $('.create-account').fadeOut('slow');">
                                        <label class="form-check-label label_info" for="register_me"><span>{{__('webMessage.createanaccount')}}</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="create-account" @if(old('register_me')) style="display: block;" @endif>
                                <div class="form-group  mb-3">
                                    <input type="text" name="username" class="form-control"
                                           id="username" placeholder="{{__('webMessage.username')}}"
                                           autcomplete="off"
                                           value="{{old('username')}}">
                                    @if($errors->has('username'))
                                        <label id="username-error" class="error"
                                               for="username">{{$errors->first('username')}}</label>
                                    @endif
                                </div>
                                <div class="form-group  mb-3">
                                    <input type="password" name="password" class="form-control"
                                           id="password" placeholder="{{__('webMessage.password')}}"
                                           autcomplete="off"
                                           value="{{old('password')}}">
                                    @if($errors->has('password'))
                                        <label id="password-error" class="error"
                                               for="password">{{$errors->first('password')}}</label>
                                    @endif
                                </div>
                            </div>
{{--                            <div class="ship_detail">--}}
{{--                                <div class="form-group mb-3">--}}
{{--                                    <div class="chek-form">--}}
{{--                                        <div class="custome-checkbox">--}}
{{--                                            <input class="form-check-input cbx" required type="checkbox" name="cbx_tac" id="cbx_tac">--}}
{{--                                            <label class="form-check-label label_info" for="cbx_tac"><span>{{ __('webMessage.agree_to_terms_and_condition') }}</span></label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="order_review">
                                <div class="heading_s1">
                                    <h4>{{strtoupper(__('webMessage.shoppingcart'))}}</h4>
                                </div>
                                <div class="table-responsive order_table">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{__('webMessage.details')}}</th>
                                            <th>{{__('webMessage.subtotal')}}</th>
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
                                                $pixelids[]=$tempOrder->product_id;
                                                if($productDetails->image){
                                                $prodImage = url('uploads/product/thumb/'.$productDetails->image);
                                                }else{
                                                $prodImage = url('uploads/no-image.png');
                                                }
                                                $warrantyTxt='';
                                                if(!empty($productDetails->warranty)){
                                                $warrantyDetails = App\Http\Controllers\webCartController::getWarrantyDetails($productDetails->warranty);
                                                $warrantyTxt     = $strLang=="en"?"<br>".$warrantyDetails->title_en:"<br>".$warrantyDetails->title_ar;
                                                }

                                                $optionsDetails = App\Http\Controllers\webCartController::getOptionsDtails($tempOrder->id);

                                                if(!empty($tempOrder->size_id)){
                                                $sizeName =App\Http\Controllers\webCartController::sizeNameStatic($tempOrder->size_id,$strLang);
                                                $sizeName = '<li>'.trans('webMessage.size').':'.$sizeName.'</li>';
                                                }else{$sizeName='';}
                                                if(!empty($tempOrder->color_id)){
                                                $colorName =App\Http\Controllers\webCartController::colorNameStatic($tempOrder->color_id,$strLang);
                                                $colorName = '<li>'.trans('webMessage.color').':'.$colorName.'</li>';
                                                //color image
                                                $colorImageDetails = App\Http\Controllers\webCartController::getColorImage($tempOrder->product_id,$tempOrder->color_id);
                                                if(!empty($colorImageDetails->color_image)){
                                                $prodImage = url('uploads/color/thumb/'.$colorImageDetails->color_image);
                                                }
                                                }else{$colorName='';}
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
                                                        "item_variant"  => "",
                                                        "item_list_name"=> "",
                                                        "item_list_id"  => "",
                                                        "index"         => $tempOrder->id,
                                                        "quantity"      => $tempOrder->quantity??'1',
                                                        "currency"      => "KWD",
                                                        ];


                                            @endphp
                                            <tr>
                                                <td>
                                                    @if(!empty($productDetails->id))
                                                        <a href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">@if(!empty($productDetails['title_'.app()->getLocale()])){{$productDetails['title_'.app()->getLocale()]}}@endif</a>
                                                    @endif
                                                    @if(isset($sizeName) and $sizeName)<sapn style="margin-right: 3px;margin-left: 3px;">{!!$sizeName!!}</sapn>@endif
                                                    @if(isset($colorName) and $colorName)<sapn style="margin-right: 3px;margin-left: 3px;">{!!$colorName!!}</sapn>@endif
                                                    @if(isset($optionsdetails) and $optionsdetails)<sapn style="margin-right: 3px;margin-left: 3px;">{!!$optionsdetails!!}</sapn>@endif
                                                    @if(isset($warrantyTxt) and $warrantyTxt)<sapn style="margin-right: 3px;margin-left: 3px;">{!!$warrantyTxt!!}</sapn>@endif
                                                    <span class="product-qty">x {{$tempOrder->quantity}}</span>
                                                </td>
                                                <td>{{\App\Currency::default()}} {{$subtotalprice}}</td>
                                            </tr>
                                            @php
                                                $totalprice+=$subtotalprice;
                                            @endphp
                                        @endforeach
                                        @php
                                            $bundleDiscount =  App\Http\Controllers\webCartController::loadTempOrdersBundleDiscount($tempOrders);
                                            if ( $bundleDiscount > 0 ){
                                                $totalprice = $totalprice - $bundleDiscount;
                                            }
                                            $checktotal = $totalprice;
                                        @endphp
                                        </tbody>
                                        <tfoot id='checktotalbox'>
                                        <tr>
                                            <th>{{strtoupper(__('webMessage.subtotal'))}}</th>
                                            <td>{{\App\Currency::default()}} {{$totalprice}}</td>
                                        </tr>
                                        @if ( $bundleDiscount > 0 )
                                            <tr>
                                                <th>{{strtoupper(__('webMessage.bundles.BundleDiscount'))}}</th>
                                                <td>{{\App\Currency::default()}} -{{$bundleDiscount}}</td>
                                            </tr>
                                        @endif
                                        @if(!empty(Cookie::get('gb_seller_discount')))
                                            @php
                                                $totalprice=$totalprice-Cookie::get('gb_seller_discount');
                                            @endphp
                                            <tr>
                                                <th>{{strtoupper(__('webMessage.seller_discount'))}}</th>
                                                <td>
                                                    <font color="#FF0000">-{{Cookie::get('gb_seller_discount')}}</font>
                                                </td>
                                            </tr>
                                        @endif
                                        @if(!empty(Cookie::get('gb_coupon_code')) &&  floatval(preg_replace("/[^-0-9\.]/","",Cookie::get('gb_coupon_discount_text'))) > 0  )
                                            <tr>
                                                <th>{{strtoupper(__('webMessage.coupon_discount'))}}</th>
                                                <td>
                                                    <font color="#FF0000">-{{Cookie::get('gb_coupon_discount_text')}}</font>
                                                </td>
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
                                             @if(empty(Cookie::get('gb_coupon_free')))
                                                @php
                                                    if ( (!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free')) ) {
                                                        if(!empty(Cookie::get('area'))){ $areaid = Cookie::get('area'); }else if(!empty($userAddress->area_id)){ $areaid = $userAddress->area_id; }
                                                        $deliveryCharge = App\Http\Controllers\webCartController::get_delivery_charge($areaid);
                                                    }
                                                @endphp
                                                <tr>
                                                    <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                                    <td>@if((!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free'))) {{\App\Currency::default()}} {{$deliveryCharge}} @else -- @endif</td>
                                                </tr>
                                                @php
                                                    $totalprice=$totalprice+ ( (!empty(Cookie::get('area')) || !empty($userAddress->area_id)) && empty(Cookie::get('gb_coupon_free')) ? $deliveryCharge : 0 );
                                                @endphp
                                            @else
                                                <tr>
                                                    <th>{{strtoupper(__('webMessage.delivery_charge'))}}</th>
                                                    <td><font color="#FF0000">{{strtoupper(__('webMessage.free_delivery'))}}</font></td>
                                                </tr>
                                            @endif
                                        @endif
                                        <tr>
                                            <th>{{strtoupper(__('webMessage.grandtotal'))}}</th>
                                            <td>{{\App\Currency::default()}} {{$totalprice}}</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <input type="hidden" name="checkout_totalprice" id="checkout_totalprice"
                                           value="{{$checktotal}}">
                                    <input type="hidden" name="checkout_totalprice" id="checkout_bundleDiscount"
                                           value="{{$bundleDiscount}}">
                                </div>
                                @if(!empty($settingInfo->min_order_amount) && $settingInfo->min_order_amount>$checktotal && empty($userType))
                                    <div class="alert alert-danger">
                                        <p>{!!trans('webMessage.minimumordermessage')!!} <font color="#FF6600">{{number_format($settingInfo->min_order_amount,3)}} {{\App\Currency::default()}}</font></p>
                                        <a href="{{url(app()->getLocale().'/cart')}}" class="btn btn-fill-out"><i
                                                    class="fas fa-check-circle"></i>{{strtoupper(__('webMessage.backtoshoppingcart'))}}
                                        </a>
                                    </div>
                                @else
                                    @php
                                        $payments = App\Country::getGateways($countryListsSelected ?? 2);
                                        $p=1;
                                    @endphp
                                    @if(count($payments) > 0)
                                        <div class="payment_method">
                                            <div class="heading_s1">
                                                <h4>{{strtoupper(__('webMessage.paymentmethod'))}}</h4>
                                            </div>
                                            <div class="payment_option" id="PaymentGayteways">
                                                @php $paytxt=''; @endphp
                                                @foreach($payments as $payment)
                                                    @php
                                                        if($payment=='COD'){
                                                        $paytxt = trans('webMessage.payment_COD');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/money_b_ar.gif')."\">";
                                                        }else if($payment=='KNET' || $payment=='KNET'){
                                                        $paytxt = trans('webMessage.payment_KNET');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/knet_b_ar.gif')."\">";
                                                        }else if($payment=='TPAY' || $payment=='TPAY'){
                                                        $paytxt = trans('webMessage.payment_TPAY');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/tpay_ar.png')."\">";
                                                        }else if($payment=='CBK_KNET' || $payment=='CBK_KNET'){
                                                        $paytxt = trans('webMessage.payment_CBK_KNET');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/knet_b_ar.gif')."\">";
                                                        }else if($payment=='CBK_TPAY' || $payment=='CBK_TPAY'){
                                                        $paytxt = trans('webMessage.payment_TPAY');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/tpay_ar.png')."\">";
                                                        }else if($payment=='GKNET'){
                                                        $paytxt = trans('webMessage.payment_GKNET');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/knet_b_ar.gif')."\">";
                                                        }else if($payment=='GTPAY'){
                                                        $paytxt = trans('webMessage.payment_GTPAY');
                                                        $paytxt = "<img src=\"".url('assets/images/pay/tpay_ar.png')."\">";
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
                                                        $paytxt = "<img src=\"".url('assets/images/pay/master_ico.png')."\">";
                                                        }else if($payment=='Q8LINK'){
                                                        $paytxt = trans('webMessage.payment_Q8LINK');
                                                        }
                                                    @endphp
                                                        <div class="custome-radio">
                                                            <input class="form-check-input" required="" type="radio" name="payment_method" id="{{$payment}}" value="{{$payment}}" @if($p==1) checked @endif>
                                                            <label class="form-check-label" for="{{$payment}}">{!! $paytxt !!}</label>
                                                        </div>

                                                    @php $p++;@endphp
                                                @endforeach
                                            </div>
                                            
                @if ( $settingInfo['check_out_note_'.app()->getLocale()] )
                    <div class="alert alert-info">{!! nl2br(e($settingInfo['check_out_note_'.app()->getLocale()])) !!}</div>
                @endif
                                        </div>
                                        <button type="submit" class="btn btn-fill-out btn-block">{{strtoupper(__('webMessage.orderconfirm'))}}</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="text-center order_complete">
                            <i class="fas fa-times-circle"></i>
                            <p>{{__('webMessage.yourcartisempty')}}</p>
                            <a href="javascript:history.go(-1);" class="btn btn-fill-out">{{__('webMessage.goback')}}</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection

@section('js')

<script>
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
                    var areaid = $(".area_checkout").val();
                    var stateid = $("#state").val();
                    var countryid = $("#country").val();
                    var totalprice = $("#checkout_totalprice").val();
                    var bundleDiscount = $("#checkout_bundleDiscount").val();
                    $.ajax({
                        type: "GET",
                        url: "/ajax_get_area_delivery",
                        data: "t=check&areaid=" + areaid + "&totalprice=" + totalprice + "&stateid=" + stateid + "&countryid=" + countryid + "&bundleDiscount=" + bundleDiscount,
                        dataType: "json",
                        cache: false,
                        processData: false,
                        success: function (msg) {
                            $("#checktotalbox").html(msg.message);
                        },
                        error: function (msg) {
                            $("#checktotalbox").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                        }
                    });
                }else{
                    toastr.error(msg.message);
                }
            },
            error: function(msg){
                toastr.error('Oops! Something went wrong while processing');
            }
        });
    });
    gtag('event', 'begin_checkout', {
        value: '{{ @$totalprice }}',
        currency: '{{ @$dataLayer[0]["currency"] }}',
        items: @json($dataLayer)
    })
</script>

@endsection
