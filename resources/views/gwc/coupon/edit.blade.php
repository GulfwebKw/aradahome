@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.editcoupon')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.coupon')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.editcoupon')}}</a>
									</div>
								</div>
								@if(auth()->guard('admin')->user()->can('coupon-list'))
												<a style="margin-top:10px;" href="{{url('gwc/coupon')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listcoupon')}}</a> @endif
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.editcoupon')}}</h3>
									</div>
									
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('coupon-edit'))
                    <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('coupon.update',$editcoupon->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
																							
                                                <div class="form-group row">
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{$editcoupon->title_en?$editcoupon->title_en:old('title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}*" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{$editcoupon->title_ar?$editcoupon->title_ar:old('title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}*" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <div class="form-group row">
													<label class="col-2 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-2">
														<span class="kt-switch">
															<label>
																<input type="checkbox" {{$editcoupon->is_active==1?'checked':''}} name="is_active"  id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
                                                    <label class="col-2 col-form-label">{{__('adminMessage.freeshipping')}}</label>
													<div class="col-2">
														<span class="kt-switch">
															<label>
																<input type="checkbox"  {{$editcoupon->is_free==1?'checked':''}} name="is_free"  id="is_free" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<label class="col-2 col-form-label zonePrice " style="{{$editcoupon->is_free==1 ?'':'display: none'}}" >Zone country</label>
													<div class="col-2">
														<span class="kt-switch zonePrice" style="{{$editcoupon->is_free==1 ?'':'display: none'}}" >
															<label>
																<input type="checkbox" {{($editcoupon->is_free==1 and $editcoupon->is_zone_free==1) ?'checked':''}}  name="is_zone_free"  id="is_zone_free" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													
												   </div>
                                                </div>
                                            </div>
                                            
                                            
                                         
                                         <div class="form-group row">
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.coupon_code')}}</label>
                                                <input type="text" class="form-control @if($errors->has('coupon_code')) is-invalid @endif" name="coupon_code" value="{{$editcoupon->coupon_code?$editcoupon->coupon_code:old('coupon_code')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_coupon_code')}}*" />
                                                               @if($errors->has('coupon_code'))
                                                               <div class="invalid-feedback">{{ $errors->first('coupon_code') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.coupon_type')}}</label>
                                                <select class="form-control @if($errors->has('coupon_type')) is-invalid @endif" name="coupon_type" >
                                                <option value="">{{__('adminMessage.choosetype')}}*</option>
                                                <option value="amt" @if($editcoupon->coupon_type=='amt') selected @endif>{{__('adminMessage.amountkd')}}</option>
                                                <option value="per" @if($editcoupon->coupon_type=='per') selected @endif>{{__('adminMessage.percentage')}}</option>
                                                </select>
                                                @if($errors->has('coupon_code'))
                                                <div class="invalid-feedback">{{ $errors->first('coupon_code') }}</div>
                                                @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.coupon_value')}}</label>
                                                <input type="text" class="form-control @if($errors->has('coupon_value')) is-invalid @endif" name="coupon_value"
                                                               value="{{$editcoupon->coupon_value?$editcoupon->coupon_value:old('coupon_value')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_coupon_value')}}*" />
                                                               @if($errors->has('coupon_value'))
                                                               <div class="invalid-feedback">{{ $errors->first('coupon_value') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.date_range')}}</label>
													<div class="input-group">
														<input type="text" class="datepick form-control @if($errors->has('start_date')) is-invalid @endif" name="start_date" value="{{$editcoupon->start_date?$editcoupon->start_date:old('start_date')}}" autocomplete="off" placeholder="{{__('adminMessage.start_date')}}*" />
														<input type="text" class=" datepick form-control @if($errors->has('end_date')) is-invalid @endif" name="end_date"
                                                               value="{{$editcoupon->end_date?$editcoupon->end_date:old('end_date')}}" autocomplete="off" placeholder="{{__('adminMessage.end_date')}}*" />
													</div>
                                                    @if($errors->has('start_date'))
                                                    <div class="invalid-feedback">{{ $errors->first('start_date')}}</div>
                                                    @endif
                                                    @if($errors->has('end_date'))
                                                    <div class="invalid-feedback">{{ $errors->first('end_date')}}</div>
                                                    @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.price_range')}}</label>
													<div class="input-group">
														<input type="text" class="form-control @if($errors->has('price_start')) is-invalid @endif" name="price_start" value="{{$editcoupon->price_start?$editcoupon->price_start:old('price_start')}}" autocomplete="off" placeholder="{{__('adminMessage.price_start')}}*" />
														<input type="text" class="form-control @if($errors->has('price_end')) is-invalid @endif" name="price_end"
                                                               value="{{$editcoupon->price_end?$editcoupon->price_end:old('price_end')}}" autocomplete="off" placeholder="{{__('adminMessage.price_end')}}*" />
													</div>
                                                   
                                                    @if($errors->has('price_start'))
                                                    <div class="invalid-feedback">{{ $errors->first('price_start')}}</div>
                                                    @endif
                                                    @if($errors->has('price_end'))
                                                    <div class="invalid-feedback">{{ $errors->first('price_end')}}</div>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                               
                                               <div class="form-group row">
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.usage_limit')}}</label>
                                                <input type="text" class="form-control @if($errors->has('usage_limit')) is-invalid @endif" name="usage_limit" value="{{$editcoupon->usage_limit?$editcoupon->usage_limit:old('usage_limit')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_usage_limit')}}*" />
                                                               @if($errors->has('usage_limit'))
                                                               <div class="invalid-feedback">{{ $errors->first('usage_limit') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.coupon_for')}}</label>
                                                <select class="form-control @if($errors->has('is_for')) is-invalid @endif" name="is_for" >
                                                <option value="web" @if($editcoupon->is_for=='web') selected @endif>{{__('adminMessage.web')}}</option>
                                                <option value="app" @if($editcoupon->is_for=='app') selected @endif>{{__('adminMessage.app')}}</option>
                                                </select>
                                                @if($errors->has('is_for'))
                                                <div class="invalid-feedback">{{ $errors->first('is_for') }}</div>
                                                @endif
                                                </div>
                                                                                              
                                                
                                            </div>
                                                  
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/coupon')}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
		<script>
			$('#is_free').change(function() {
				if ($(this).prop('checked')) {
					$('.zonePrice').show();
				}
				else {
					$('.zonePrice').hide();
				}
			});
		</script>
	</body>

	<!-- end::Body -->
</html>