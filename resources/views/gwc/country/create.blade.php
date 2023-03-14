@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.createcountry')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
@include('gwc.css.user')
<!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if(!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

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
                     src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40"/>
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
                            <h3 class="kt-subheader__title">{{__('adminMessage.country')}}</h3>
                            <span class="kt-subheader__separator kt-hidden"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;"
                                   class="kt-subheader__breadcrumbs-link">{{__('adminMessage.createcountry')}}</a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- end:: Subheader -->

                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                @include('gwc.includes.alert')

                <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
                                <h3 class="kt-portlet__head-title">{{__('adminMessage.createcountry')}}</h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">

                                        @if(auth()->guard('admin')->user()->can('country-list'))
                                            <a href="{{url('gwc/country')}}"
                                               class="btn btn-brand btn-elevate btn-icon-sm"><i
                                                        class="la la-list-ul"></i>{{__('adminMessage.listcountry')}}
                                            </a> @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--begin::Form-->
                        @if(auth()->guard('admin')->user()->can('country-create'))

                            <form name="tFrm" id="form_validation" method="post"
                                  class="kt-form" enctype="multipart/form-data" action="{{route('country.store')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="kt-portlet__body">

                                    <!--categories name -->
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label>{{__('adminMessage.name_en')}}</label>
                                            <input type="text"
                                                   class="form-control @if($errors->has('name_en')) is-invalid @endif"
                                                   name="name_en"
                                                   value="{{old('name_en')}}" autocomplete="off"
                                                   placeholder="{{__('adminMessage.enter_name_en')}}*"/>
                                            @if($errors->has('name_en'))
                                                <div class="invalid-feedback">{{ $errors->first('name_en') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-lg-4">
                                            <label>{{__('adminMessage.name_ar')}}</label>
                                            <input type="text"
                                                   class="form-control @if($errors->has('name_ar')) is-invalid @endif"
                                                   name="name_ar"
                                                   value="{{old('name_ar')}}" autocomplete="off"
                                                   placeholder="{{__('adminMessage.enter_name_ar')}}*"/>
                                            @if($errors->has('name_ar'))
                                                <div class="invalid-feedback">{{ $errors->first('name_ar') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-lg-2">
                                            <label>Currency Code</label>
                                            <input type="text" class="form-control @if ($errors->has('currency')) is-invalid @endif"
                                                   name="currency"
                                                   value="{{ old('currency' , 'USD') }}"
                                                   autocomplete="off"
                                                   placeholder="Currency Code*" />
                                            @if ($errors->has('currency'))
                                                <div class="invalid-feedback">{{ $errors->first('currency') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-2">
                                            <label>2 Letter ISO Code</label>
                                            <input type="text" class="form-control @if ($errors->has('code')) is-invalid @endif"
                                                   name="code"
                                                   value="{{ old('code') }}"
                                                   autocomplete="off"
                                                   placeholder="{{ __('adminMessage.code') }}*" />
                                            @if ($errors->has('code'))
                                                <div class="invalid-feedback">{{ $errors->first('code') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

									<div class="form-group row">
										<div class="col-lg-6">
											<label>{{__('adminMessage.zone')}}</label>
											<select name="zone" class="form-control">
												@foreach($zones as $zone)
													<option value="{{$zone->id}}">
														{{$zone->title_en}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-lg-6">
											<label>{{__('adminMessage.shipment_method')}}</label>
											<select name="shipment" class="form-control">
												<option value="flatrate">{{__('adminMessage.flatrate')}} @if($settings->is_express) & Express @endif</option>
                                                <option value="zoneprice">{{__('adminMessage.zoneprice')}}</option>
                                                <option value="dhl">DHL</option>
											</select>
										</div>
									</div>

                                    <div class="form-group row">
                                        <div class="col-lg-8">
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">{{__('adminMessage.isstate')}}</label>
                                                <div class="col-2">
														<span class="kt-switch">
															<label>
																<input type="checkbox" checked="checked" name="is_state"
                                                                       id="is_state" value="1"/>
																<span></span>
															</label>
														</span>
                                                </div>

                                                <label class="col-2 col-form-label">{{__('adminMessage.isactive')}}</label>
                                                <div class="col-2">
														<span class="kt-switch">
															<label>
																<input type="checkbox" checked="checked"
                                                                       name="is_active" id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
                                                </div>
                                                <label class="col-2 col-form-label">{{__('adminMessage.displayorder')}}</label>
                                                <div class="col-2">
                                                    <input type="text"
                                                           class="form-control @if($errors->has('display_order')) is-invalid @endif"
                                                           name="display_order"
                                                           value="{{old('display_order')?old('display_order'):$lastOrder}}"
                                                           autocomplete="off"/>
                                                    @if($errors->has('display_order'))
                                                        <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="custom-file @if($errors->has('image')) is-invalid @endif">
                                                <input type="file"
                                                       class="custom-file-input @if($errors->has('image')) is-invalid @endif"
                                                       id="image" name="image">
                                                <label class="custom-file-label"
                                                       for="image">{{__('adminMessage.flagimage')}}</label>
                                            </div>
                                            @if($errors->has('image'))
                                                <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $settingInfo = App\Http\Controllers\webController::settings();
                                    @endphp
                                    @if(!empty($settingInfo->payments))
                                        <div class="form-group row">
                                            <!-- payment start -->
                                            <div class="row" style="border:1px #CCCCCC solid;margin-top:20px;padding:20px;border-radius:5px;">
                                                <div class="col-lg-12">
                                                    <h4 class="tt-collapse-title mb-3">{{strtoupper(__('webMessage.paymentmethod'))}}</h4>
                                                    @php
                                                        $payments = explode(",",$settingInfo->payments);
                                                        $p=1;
                                                    @endphp

                                                    <div class="row">
                                                        @php $paytxt=''; @endphp
                                                        @foreach($payments as $payment)
                                                            @php
                                                                if($payment=='COD'){
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
                                                            <div class="col-xs-12 col-md-6 col-lg-4">
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <label for="gateway_{{$payment}}" class="input-group-prepend"><span class="input-group-text" style="width:370px;">{{$paytxt}}</span>
                                                                        </label>

                                                                        <input @if(in_array($payment , old('gateways' , [] ) ) )  checked="" @endif id="gateway_{{$payment}}" type="checkbox" class="form-control" name="gateways[]" value="{{$payment}}">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @php $p++;@endphp
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end payment end -->
                                        </div>
                                    @endif


                                </div>
                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <button type="submit"
                                                class="btn btn-success">{{__('adminMessage.save')}}</button>
                                        <button type="button"
                                                onClick="Javascript:window.location.href='{{url('gwc/country')}}'"
                                                class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
                                    </div>
                                </div>
                            </form>

                        @else
                            <div class="alert alert-light alert-warning" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                <div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
                            </div>
                    @endif
                    <!--end::Form-->
                    </div>

                    <!--end::Portlet-->


                </div>

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


<!-- js files -->
@include('gwc.js.user')


</body>

<!-- end::Body -->
</html>