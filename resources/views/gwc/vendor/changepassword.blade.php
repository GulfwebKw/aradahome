@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.gulfwebvendor')}} | {{__('adminMessage.changepassword')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.changepassword')}}</h3>
								</div>
							</div>
						</div>

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

							<!--begin::Portlet-->
									<div class="kt-portlet">
										
										<!--begin::Form-->
						
@php
$userDetails = App\Http\Controllers\AdminUserController::getUserDetails(auth()->guard('admin')->user()->id);
@endphp  
                         <div class="kt-portlet__body">
                         
										<div class="tab-content">
											
                         <div class="tab-pane  active " id="kt_user_edit_tab_2" role="tabpanel">
                         <div class="kt-form kt-form--label-right">
                         <form name="tFrmpass" id="tFrmpass"  method="post"
                          class="uk-form-stacked" enctype="multipart/form-data" action="{{route('vendorChangePass')}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <input type="hidden" name="id" value="{{$userDetails->id}}" >
													<div class="kt-form__body">
														<div class="kt-section kt-section--first">
															<div class="kt-section__body">
																@include('gwc.includes.alert')
																
																<div class="form-group row">
																	<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.currentpassword')}}</label>
																	<div class="col-lg-9 col-xl-6">
																		<input type="password" name="current_password" class="form-control @if($errors->has('current_password')) is-invalid @endif" value="{{old('current_password')}}" placeholder="{{__('adminMessage.entercurrentpassword')}}">                                                               @if($errors->has('current_password'))
                                                               <div class="invalid-feedback">{{ $errors->first('current_password') }}</div>
                                                               @endif
																	</div>
																</div>
																<div class="form-group row">
																	<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.newpassword')}}</label>
																	<div class="col-lg-9 col-xl-6">
																		<input type="password" name="new_password" class="form-control @if($errors->has('new_password')) is-invalid @endif" value="{{old('new_password')}}" placeholder="{{__('adminMessage.enternewpassword')}}">                                                               @if($errors->has('new_password'))
                                                               <div class="invalid-feedback">{{ $errors->first('new_password') }}</div>
                                                               @endif
																	</div>
																</div>
																<div class="form-group form-group-last row">
																	<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.confirmpassword')}}</label>
																	<div class="col-lg-9 col-xl-6">
																		<input type="password" name="confirm_password" class="form-control @if($errors->has('confirm_password')) is-invalid @endif" value="{{old('confirm_password')}}" placeholder="{{__('adminMessage.enterconfirmpassword')}}">                                                               @if($errors->has('confirm_password'))
                                                               <div class="invalid-feedback">{{ $errors->first('confirm_password') }}</div>
                                                               @endif
																	</div>
																</div>
															</div>
														</div>
													</div>
                                                    <div class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>
													<div class="kt-form__actions">
														<div class="row">
															<div class="col-xl-3"></div>
															<div class="col-lg-9 col-xl-6">
																<button type="submit" class="btn btn-success  btn-bold">{{__('adminMessage.save')}}</button>
                                                                
															</div>
														</div>
													</div>
                                                    </form>
													
												</div>
											</div>
                                            
                                       
                         
                                            
                                                 
											
										</div>
									
                                    </div>
                         
                                  
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