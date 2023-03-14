@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}} | {{__('adminMessage.facebooksettings')}}</title>
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
				<img alt="{{__('adminMessage.websiteName')}}" src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40" />
                @endif
			   </a>
			</div>
			<div class="kt-header-mobile__toolbar">
				<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
				
				<button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.systems')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.smssettings')}}</a>
									</div>
								</div>
								
							</div>
						</div>
                        

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                        
                          @include('gwc.includes.alert')
                          
                         @if(auth()->guard('admin')->user()->can('sms-setting-edit'))  
                           <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('smssettingpost',$settingDetails->keyname)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            
							<div class="row">
								<div class="col-md-12">
                       
									<!--begin::Portlet-->
									<div class="kt-portlet">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													{{__('adminMessage.smssettings')}}
												</h3>
											</div>
										</div>

										<!--begin::Form-->
                                       
											<div class="kt-portlet__body">
                                            @if(!empty($smsDetails['sms_points']))
                                            <div class="form-group">
                                            <div class="row">
                                             <div class="col-lg-4">Status : {{$smsDetails['status']}}</div>
                                             <div class="col-lg-4">SMS Points : {{$smsDetails['sms_points']}}</div>
                                             <div class="col-lg-4">SMS Points : {{$smsDetails['expiry_date']}}</div>
                                            </div>
                                            </div>
                                            @endif
                                            
                                            
                                            <div class="row">
                                             <div class="col-lg-3">
												<div class="form-group">
                                                <div class="input-group row">
												 <label class="col-6">{{__('adminMessage.enable_sms_notification')}}*</label>
													<div class="col-6">
														<span class="kt-switch"><label>
												<input value="1" {{(!empty(old('is_sms_active')) || !empty($settingDetails->is_sms_active))?'checked':''}} type="checkbox"  id="is_sms_active" name="is_sms_active"><span></span>
															</label>
														</span>
													</div>
                                                   </div>
                                                </div> 
                                               </div> 
                                               <div class="col-lg-3">
                                                 <div class="form-group">
                                                    <label for="sms_userid">{{__('adminMessage.sms_userid')}}*</label>
													<input id="sms_userid"   name="sms_userid" class="form-control  @if($errors->has('sms_userid')) is-invalid @endif" placeholder="{{__('adminMessage.enter_sms_userid')}}" value="@if(!empty(old('sms_userid'))){{old('sms_userid')}}@elseif($settingDetails->sms_userid){{$settingDetails->sms_userid}}@endif">
                                                    @if($errors->has('sms_userid'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_userid') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-3">
                                               <div class="form-group">
                                                    <label for="sms_sender_name">{{__('adminMessage.sms_sender_name')}}*</label>
													<input id="sms_sender_name"   name="sms_sender_name" class="form-control  @if($errors->has('sms_sender_name')) is-invalid @endif" placeholder="{{__('adminMessage.enter_sms_sender_name')}}" value="@if(!empty(old('sms_sender_name'))){{old('sms_sender_name')}}@elseif($settingDetails->sms_sender_name){{$settingDetails->sms_sender_name}}@endif">
                                                    @if($errors->has('sms_sender_name'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_sender_name') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-3">
                                                  <div class="form-group">
                                                    <label for="sms_api_key">{{__('adminMessage.sms_api_key')}}*</label>
													<input id="sms_api_key"   name="sms_api_key" class="form-control  @if($errors->has('sms_api_key')) is-invalid @endif" placeholder="{{__('adminMessage.enter_sms_api_key')}}" value="@if(!empty(old('sms_api_key'))){{old('sms_api_key')}}@elseif($settingDetails->sms_api_key){{$settingDetails->sms_api_key}}@endif">
                                                    @if($errors->has('sms_api_key'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_api_key') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               
                                               
                                          </div>                                                
                                         <div class="form-group"><i>{!!__('adminMessage.dezsmsnotes')!!}</i></div>
                
                                                
                                             <!-- sms box -->
                                               <div class="form-group">
                                               <h5>{{__('adminMessage.notificationafterorderplaced_cod')}}</h5>
                                               <div class="row">
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_cod_en" class="form-control  @if($errors->has('sms_text_cod_en')) is-invalid @endif" id="sms_text_cod_en" placeholder="{{__('adminMessage.sms_text')}}(En)*">@if(!empty(old('sms_text_cod_en'))){{old('sms_text_cod_en')}}@elseif($settingDetails->sms_text_cod_en){{$settingDetails->sms_text_cod_en}}@endif</textarea>
                                                    @if($errors->has('sms_text_cod_en'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_cod_en') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_cod_ar" class="form-control  @if($errors->has('sms_text_cod_ar')) is-invalid @endif" id="sms_text_cod_ar" placeholder="{{__('adminMessage.sms_text')}}(Ar)*">@if(!empty(old('sms_text_cod_ar'))){{old('sms_text_cod_ar')}}@elseif($settingDetails->sms_text_cod_ar){{$settingDetails->sms_text_cod_ar}}@endif</textarea>
                                                    @if($errors->has('sms_text_cod_ar'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_cod_ar') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                                <div class="col-lg-2" align="right">
                                                  <span class="kt-switch"><label>
												<input value="1" {{(!empty(old('sms_text_cod_active')) || !empty($settingDetails->sms_text_cod_active))?'checked':''}} type="checkbox"  id="sms_text_cod_active" name="sms_text_cod_active"><span></span>
															</label>
														</span>
                                                  </div>
                                                 </div>  
                                                </div>
                                               <!--end sms box --> 
                                               
                                               
                                               <!-- sms box -->
                                               <div class="form-group">
                                               <h5>{{__('adminMessage.notificationafterorderplaced_knet')}}</h5>
                                               <div class="row">
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_knet_en" class="form-control  @if($errors->has('sms_text_knet_en')) is-invalid @endif" id="sms_text_knet_en" placeholder="{{__('adminMessage.sms_text')}}(En)*">@if(!empty(old('sms_text_knet_en'))){{old('sms_text_knet_en')}}@elseif($settingDetails->sms_text_knet_en){{$settingDetails->sms_text_knet_en}}@endif</textarea>
                                                    @if($errors->has('sms_text_knet_en'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_knet_en') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_knet_ar" class="form-control  @if($errors->has('sms_text_knet_ar')) is-invalid @endif" id="sms_text_knet_ar" placeholder="{{__('adminMessage.sms_text')}}(Ar)*">@if(!empty(old('sms_text_knet_ar'))){{old('sms_text_knet_ar')}}@elseif($settingDetails->sms_text_knet_ar){{$settingDetails->sms_text_knet_ar}}@endif</textarea>
                                                    @if($errors->has('sms_text_knet_ar'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_knet_ar') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                                <div class="col-lg-2" align="right">
                                                  <span class="kt-switch"><label>
												<input value="1" {{(!empty(old('sms_text_knet_active')) || !empty($settingDetails->sms_text_knet_active))?'checked':''}} type="checkbox"  id="sms_text_knet_active" name="sms_text_knet_active"><span></span>
															</label>
														</span>
                                                  </div>
                                                 </div>  
                                                </div>
                                               <!--end sms box -->
                                               
                                               <!-- sms box -->
                                               <div class="form-group">
                                               <h5>{{__('adminMessage.notificationafterordertrack')}}</h5>
                                               <div class="row">
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_track_order_en" class="form-control  @if($errors->has('sms_text_track_order_en')) is-invalid @endif" id="sms_text_track_order_en" placeholder="{{__('adminMessage.sms_text')}}(En)*">@if(!empty(old('sms_text_track_order_en'))){{old('sms_text_track_order_en')}}@elseif($settingDetails->sms_text_track_order_en){{$settingDetails->sms_text_track_order_en}}@endif</textarea>
                                                    @if($errors->has('sms_text_track_order_en'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_track_order_en') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_track_order_ar" class="form-control  @if($errors->has('sms_text_track_order_ar')) is-invalid @endif" id="sms_text_track_order_ar" placeholder="{{__('adminMessage.sms_text')}}(Ar)*">@if(!empty(old('sms_userid'))){{old('sms_userid')}}@elseif($settingDetails->sms_text_track_order_ar){{$settingDetails->sms_text_track_order_ar}}@endif</textarea>
                                                    @if($errors->has('sms_text_track_order_ar'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_track_order_ar') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                                <div class="col-lg-2" align="right">
                                                  <span class="kt-switch"><label>
												<input value="1" {{(!empty(old('sms_text_track_order_active')) || !empty($settingDetails->sms_text_track_order_active))?'checked':''}} type="checkbox"  id="sms_text_track_order_active" name="sms_text_track_order_active"><span></span>
															</label>
														</span>
                                                  </div>
                                                 </div>  
                                                </div>
                                               <!--end sms box -->
                                               
                                               <!-- sms box -->
                                               <div class="form-group">
                                               <h5>{{__('adminMessage.notificationforoutofstock')}}</h5>
                                               <div class="row">
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_outofstock_en" class="form-control  @if($errors->has('sms_text_outofstock_en')) is-invalid @endif" id="sms_text_outofstock_en" placeholder="{{__('adminMessage.sms_text')}}(En)*">@if(!empty(old('sms_text_outofstock_en'))){{old('sms_text_outofstock_en')}}@elseif($settingDetails->sms_text_outofstock_en){{$settingDetails->sms_text_outofstock_en}}@endif</textarea>
                                                    @if($errors->has('sms_text_outofstock_en'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_outofstock_en') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_outofstock_ar" class="form-control  @if($errors->has('sms_text_outofstock_ar')) is-invalid @endif" id="sms_text_outofstock_ar" placeholder="{{__('adminMessage.sms_text')}}(Ar)*">@if(!empty(old('sms_text_outofstock_ar'))){{old('sms_text_outofstock_ar')}}@elseif($settingDetails->sms_text_outofstock_ar){{$settingDetails->sms_text_outofstock_ar}}@endif</textarea>
                                                    @if($errors->has('sms_text_outofstock_ar'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_outofstock_ar') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                                <div class="col-lg-2" align="right">
                                                  <span class="kt-switch"><label>
												<input value="1" {{(!empty(old('sms_text_outofstock_active')) || !empty($settingDetails->sms_text_outofstock_active))?'checked':''}} type="checkbox"  id="sms_text_outofstock_active" name="sms_text_outofstock_active"><span></span>
															</label>
														</span>
                                                  </div>
                                                 </div>  
                                                </div>
                                               <!--end sms box -->
                                                <!-- sms box -->
                                               <div class="form-group">
                                               <h5>{{__('adminMessage.notificationforpaymentlink_sent')}}</h5>
                                               <div class="row">
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_paymentlink_sent_en" class="form-control  @if($errors->has('sms_text_paymentlink_sent_en')) is-invalid @endif" id="sms_text_paymentlink_sent_en" placeholder="{{__('adminMessage.sms_text')}}(En)*">@if(!empty(old('sms_text_paymentlink_sent_en'))){{old('sms_text_paymentlink_sent_en')}}@elseif($settingDetails->sms_text_paymentlink_sent_en){{$settingDetails->sms_text_paymentlink_sent_en}}@endif</textarea>
                                                    @if($errors->has('sms_text_paymentlink_sent_en'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_paymentlink_sent_en') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                               <div class="col-lg-5">
                                                  <div class="form-group">
													<textarea name="sms_text_paymentlink_sent_ar" class="form-control  @if($errors->has('sms_text_paymentlink_sent_ar')) is-invalid @endif" id="sms_text_paymentlink_sent_ar" placeholder="{{__('adminMessage.sms_text')}}(Ar)*">@if(!empty(old('sms_text_paymentlink_sent_ar'))){{old('sms_text_paymentlink_sent_ar')}}@elseif($settingDetails->sms_text_paymentlink_sent_ar){{$settingDetails->sms_text_paymentlink_sent_ar}}@endif</textarea>
                                                    @if($errors->has('sms_text_paymentlink_sent_ar'))
                                                  <div class="invalid-feedback">{{ $errors->first('sms_text_paymentlink_sent_ar') }}</div>
                                                    @endif
												 </div>
                                               </div>
                                                <div class="col-lg-2" align="right">
                                                  <span class="kt-switch"><label>
												<input value="1" {{(!empty(old('sms_text_paymentlink_sent_active')) || !empty($settingDetails->sms_text_paymentlink_sent_active))?'checked':''}} type="checkbox"  id="sms_text_paymentlink_sent_active" name="sms_text_paymentlink_sent_active"><span></span>
															</label>
														</span>
                                                  </div>
                                                 </div>  
                                                </div>
                                               <!--end sms box -->
                                           
                                               
                                          	
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
												</div>
											</div>
										

										<!--end::Form-->
									</div>

									<!--end::Portlet-->

								</div>
								
                                
							</div>
                            </form>
                            @else
                            <div class="alert alert-light alert-warning" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
								<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
							</div>
                            @endif
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