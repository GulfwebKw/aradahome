@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.createtrackhistory')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.trackhistory')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.createtrackhistory')}}</a>
                                        <span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">#{{$OrderInfo->order_id}}</a>
                                        
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.createtrackhistory')}} </h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="kt-portlet__head-wrapper">
											<div class="kt-portlet__head-actions">
												
												@if(auth()->guard('admin')->user()->can('trackhistory-list'))
												<a href="{{url('gwc/orders-track/'.Request()->oid)}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listtrackhistory')}}</a> @endif
											</div>
										</div>
									</div>
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('trackhistory-create'))
                    
                         <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('track-orders.postnewtrack',Request()->oid)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
																					
                                  
                                            <!--categories description -->          
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_en')}}*</label>
                                                        <textarea rows="3" id="details_en" name="details_en" class="form-control @if($errors->has('details_en')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_en')}}">@if(!empty($settingInfo->is_sms_active) && !empty($settingInfo->sms_text_track_order_active) && !empty($settingInfo->sms_text_track_order_en)){{$settingInfo->sms_text_track_order_en}}@else{{old('details_en')}}@endif</textarea>
                                                               @if($errors->has('details_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_ar')}}*</label>
                                                        <textarea   rows="3" id="details_ar" name="details_ar" class="form-control @if($errors->has('details_ar')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_ar')}}">@if(!empty($settingInfo->is_sms_active) && !empty($settingInfo->sms_text_track_order_active) && !empty($settingInfo->sms_text_track_order_ar)){{$settingInfo->sms_text_track_order_ar}}@else{{old('details_ar')}}@endif</textarea>
                                                               @if($errors->has('details_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                    
                                    
                                            
                                         <!-- friendly url , status , sorting -->   
                                         <div class="form-group row">
                                                <div class="col-lg-6">
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
													<label class="col-3 col-form-label">{{__('adminMessage.displayorder')}}</label>
													<div class="col-3">
														<input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"  value="{{old('display_order')?old('display_order'):$lastOrder}}" autocomplete="off" />
                                                               @if($errors->has('display_order'))
                                                               <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                               @endif
													</div>
												   </div>
                                                </div>
                                                <div class="col-lg-3">
                                                 <div class="input-group date @if($errors->has('details_date')) is-invalid @endif">
													<input type="text" class="form-control" placeholder="{{__('adminMessage.datetime')}}*"  readonly="" name="details_date" value="@if(old('details_date')){{old('details_date')}}@else{{date('Y-m-d H:i')}}@endif" id="details_date">
													<div class="input-group-append">
														<span class="input-group-text">
															<i class="la la-calendar glyphicon-th"></i>
														</span>
													</div>
												</div>              
                                                               
                                                @if($errors->has('details_date'))
                                                <div class="invalid-feedback">{{ $errors->first('details_date') }}</div>
                                                @endif
                                                </div>
                                                <div class="col-lg-3">
                                           
                                                <select id="order_status" name="order_status" class="form-control">
                                                <option disabled>{{__('adminMessage.order_status')}}</option>
                                                
                                                <option value="pending"   @if($OrderInfo->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>     
                                                <option value="received"   @if($OrderInfo->order_status=='received') selected @endif>Received</option> 
                                                <option value="outfordelivery"   @if($OrderInfo->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>      
                                                <option value="completed" @if($OrderInfo->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option> 
                                                <option value="canceled" @if($OrderInfo->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option> 
                                                <option value="returned" @if($OrderInfo->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>   
                                                </select>
                                                
                                                </div>
                                                
                                            </div>
                                            
                                            @if(!empty($settingInfo->is_sms_active))
                                                <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.sendsmsnotification')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" @if(!empty($settingInfo->sms_text_track_order_active)) checked="checked" @endif name="is_sms_active"  id="is_sms_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
												</div>
                                            @endif
                                                     
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/orders-track/'.Request()->oid)}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
       
       <!--begin::Page Scripts(used by this page) -->
		<script src="{{url('admin_assets/assets/js/pages/crud/forms/widgets/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>
        <script>
		$('#details_date').datetimepicker();
		</script>
	</body>

	<!-- end::Body -->
</html>