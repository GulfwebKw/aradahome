@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.createnewcustomers')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.customers')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.createnewcustomers')}}</a>
									</div>
								</div>
								@if(auth()->guard('admin')->user()->can('customers-list'))
								<a style="margin-top:10px;" href="{{url('gwc/customers')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listcustomers')}}</a>@endif
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.createnewcustomers')}}</h3>
									</div>
									
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('customers-create'))
                    
                         <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('customers.store')}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
										
                                            													
                                            <div class="form-group"><h5>{{__('adminMessage.basicinformation')}}</h5></div>
                                            <div class="form-group row">
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.name')}}</label>
                                                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" name="name"
                                                               value="{{old('name')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_name')}}*" />
                                                               @if($errors->has('name'))
                                                               <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.email')}}</label>
                                                <input type="text" class="form-control @if($errors->has('email')) is-invalid @endif" name="email"
                                                               value="{{old('email')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_email')}}*" />
                                                               @if($errors->has('email'))
                                                               <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                
                                                </div>
                                            </div>
                                          
                                          
                                         <div class="form-group row">
                                         <div class="col-lg-4">
                                                <label>{{__('adminMessage.mobile')}}</label>
                                                <input type="text" class="form-control @if($errors->has('mobile1')) is-invalid @endif" name="mobile"
                                                               value="{{old('mobile')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_mobile')}}*" />
                                                               @if($errors->has('mobile'))
                                                               <div class="invalid-feedback">{{ $errors->first('mobile') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{trans('theme')['theme'.$theme]['customer_profile_image']}}</label>
                                                        <div class="custom-file @if($errors->has('image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('image')) is-invalid @endif"  id="image" name="image">
														<label class="custom-file-label" for="image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('image'))
                                                               <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                
                                                </div>
                                                <div class="col-lg-4">
                                               
                                                </div>
                                            </div>
                                         <div class="form-group"><h5>{{__('adminMessage.logininformation')}}</h5></div>   
                                          <div class="form-group row">
                                             
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.username')}}</label>
                                                <input type="text" class="form-control @if($errors->has('username')) is-invalid @endif" name="username"
                                                               value="{{old('username')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_username')}}*" />
                                                               @if($errors->has('username'))
                                                               <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.password')}}</label>
                                                <input type="password" class="form-control @if($errors->has('password')) is-invalid @endif" name="password"
                                                               value="{{old('password')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_password')}}*" />
                                                               @if($errors->has('password'))
                                                               <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                                               @endif
                                                </div>
                                            </div>   
                                            <div class="form-group"><h5>{{__('adminMessage.status')}}</h5></div>         
                                             <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" checked="checked" name="is_active"  id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
												   </div>
                                                   
                                                           
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/customers')}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
        jQuery(document).ready(function() {
		$('.kt-tinymce-4').summernote({
		  toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic', 'underline', 'clear']],
['fontname', ['fontname']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']],
		    ['table', ['table']],
		    ['insert', ['link', 'picture', 'video']],
		    ['view', ['fullscreen', 'codeview', 'help']],
		  ],
		  height:200
		}); 
		});
       </script>
	</body>

	<!-- end::Body -->
</html>