@php
	$settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
		<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
	<meta charset="utf-8" />
	<title>{{__('adminMessage.websiteName')}}| Add Payment Link</title>
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
							<h3 class="kt-subheader__title">Add Payment Link</h3>
							<span class="kt-subheader__separator kt-hidden"></span>
							<div class="kt-subheader__breadcrumbs">
								<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
								<span class="kt-subheader__breadcrumbs-separator"></span>
								<a href="javascript:;" class="kt-subheader__breadcrumbs-link">Add Payment Link</a>
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
								<h3 class="kt-portlet__head-title">Add Payment Link</h3>
							</div>
							<div class="kt-portlet__head-toolbar">
								<div class="kt-portlet__head-wrapper">
									<div class="kt-portlet__head-actions">
										<a href="{{url('gwc/payment-links')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>All generated link</a>
									</div>
								</div>
							</div>
						</div>
						<!--begin::Form-->

							<form name="tFrm"  id="form_validation"  method="post"
								  class="kt-form" enctype="multipart/form-data" action="{{route('payment-links.store')}}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<div class="kt-portlet__body">



									<div class="form-group row">
										<div class="col-lg-4">
											<label>User</label>
											<select id="customer_id" class="form-control  @if($errors->has('customer_id')) is-invalid @endif" name="customer_id">
												@foreach($customersLists as $customer)
												<option value="{{ $customer->id }}" @if(old('customer_id', request()->customer_id) == $customer->id ) selected @endif>{{ $customer->name }}</option>
												@endforeach
											</select>
											@if($errors->has('customer_id'))
												<div class="invalid-feedback">{{ $errors->first('customer_id') }}</div>
											@endif
										</div>
										<div class="col-lg-4 d-flex" style="padding-top:2rem">
										    <h3 class="pr-3">OR</h3>
										    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Add New Customer</button>
										</div>
										<div class="col-lg-4">
											<label>Price</label>
											<input type="number" step="0.001" class="form-control @if($errors->has('price')) is-invalid @endif" name="price"
												   value="{{old('price')}}" autocomplete="off" placeholder="Price (KD)" />
											@if($errors->has('price'))
												<div class="invalid-feedback">{{ $errors->first('price') }}</div>
											@endif
										</div>

									</div>

									<div class="form-group row">
										<div class="col-lg-12">
											<label>{{__('adminMessage.og_description')}}</label>
											<textarea
													class="form-control @if($errors->has('description')) is-invalid @endif"
													name="description"  autocomplete="off">{{old('description')}}</textarea>
											@if($errors->has('description'))
												<div class="invalid-feedback">{{ $errors->first('description') }}</div>
											@endif
										</div>
									</div>
									<div class="kt-portlet__foot">
										<div class="kt-form__actions">
											<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
										</div>
									</div>
								</div>
							</form>

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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('adminMessage.createnewcustomers')}}</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form name="tFrm" id="create_user"  method="post"
            class="kt-form" enctype="multipart/form-data" action="{{route('customers.store')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="send_sms_new_user" value="1">
           	<div class="form-group row">
    			<div class="col-lg-12">
    			<label>{{__('adminMessage.name')}}</label>
                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" name="name"
                   value="{{old('name')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_name')}}*" />
                   @if($errors->has('name'))
                   <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                   @endif
    			</div>
    			<div class="col-lg-12 pt-3">
    			    <label>{{__('adminMessage.mobile')}}</label>
                    <input type="text" class="form-control @if($errors->has('mobile1')) is-invalid @endif" name="mobile"
                        value="{{old('mobile')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_mobile')}}*" />
                    @if($errors->has('mobile'))
                        <div class="invalid-feedback">{{ $errors->first('mobile') }}</div>
                    @endif
    			</div>
    		</div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="document.querySelector('#create_user').submit()" class="btn btn-primary">Create Customer</button>
      </div>
    </div>
  </div>
</div>
<!-- js files -->
@include('gwc.js.user')
<script src="{{ asset('admin_assets/assets/js/pages/crud/forms/widgets/select2.js') }}" ></script>

<script>
	$(document).ready(function () {
		//change selectboxes to selectize mode to be searchable
		$("#customer_id").select2();
	});
</script>

</body>

<!-- end::Body -->
</html>