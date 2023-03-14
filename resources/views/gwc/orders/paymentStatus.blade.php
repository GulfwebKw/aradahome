@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>

	<meta charset="utf-8" />
	<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.orderdetails')}}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="{!! url('admin_assets/assets/css/pages/invoices/invoice-1.css')!!}" rel="stylesheet" type="text/css" />
	<link href="{!! url('theme6/css/style.css')!!}" rel="stylesheet" type="text/css" />
	<!--css files -->
	@include('gwc.css.user')
	<!--begin::Page Custom Styles(used by this page) -->


	<!-- token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
		tr,
		td {
			color: black !important;
		}
	</style>
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
	class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if(!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

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
					src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40" />
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
								<h3 class="kt-subheader__title">{{__('adminMessage.orderdetails')}}</h3>
								<span class="kt-subheader__separator kt-subheader__separator--v"></span>
								<div class="kt-subheader__breadcrumbs">
									<a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i
											class="flaticon2-shelter"></i></a>
									<span class="kt-subheader__breadcrumbs-separator"></span>
									<a href="javascript:;"
										class="kt-subheader__breadcrumbs-link">{{__('adminMessage.pay_status')}}</a>
								</div>
							</div>
							<div class="kt-subheader__toolbar"><a href="{{url('gwc/orders')}}"
									class="btn btn-default btn-bold">{{__('adminMessage.back')}}</a>
							</div>
						</div>
					</div>

					<!-- end:: Subheader -->

					<!-- begin:: Content -->
					@if(auth()->guard('admin')->user()->can('order-view'))

					<!--Begin:: Portlet-->
					<!-- begin:: Content -->
					<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
						<div class="kt-portlet">
							<div class="kt-portlet__body kt-portlet__body--fit">
								<div class="kt-invoice-1">
									<div class="kt-invoice__body">
										<div class="kt-invoice__container" style="width:100%;">
											<div class="table-responsive">
												<h2>{{__('adminMessage.pay_status')}}</h2>
												@if(!empty($transaction))
												@php
												$resultTxt = 'danger';
												if(@$transaction['presult'] == 'CAPTURED'){
												$resultTxt = 'success';
												}
												@endphp
												<table class="table table-striped-  table-hover table-checkable w-50">
													<tbody>
														<tr>
															<td>
																Order ID
															</td>
															<td>
																{{ @$transaction['trackid'] }}
															</td>
														</tr>
														<tr>
															<td>
																Payment ID
															</td>
															<td>
																{{ @$transaction['payment_id'] }}
															</td>
														</tr>
														<tr>
															<td>
																Result
															</td>
															<td class="text-{{ $resultTxt }}">
																{{ @$transaction['presult'] }}
															</td>
														</tr>
														<tr>
															<td>
																Trans ID
															</td>
															<td>
																{{ @$transaction['tranid'] }}
															</td>
														</tr>
														<tr>
															<td>
																Ref ID
															</td>
															<td>
																{{ @$transaction['ref'] }}
															</td>
														</tr>
														<tr>
															<td>
																Track ID
															</td>
															<td>
																{{ @$transaction['MfTrackId'] ??
																@$transaction['trackid'] }}
															</td>
														</tr>
														<tr>
															<td>
																Auth ID
															</td>
															<td>
																{{ @$transaction['auth'] }}
															</td>
														</tr>
														<tr>
															<td>
																Amount
															</td>
															<td>
																{{ @$transaction['amt'] ?? @$transaction->orderDetails->total_amount }} {{\App\Currency::default()}}
															</td>
														</tr>
														<tr>
															<td>
																Date
															</td>
															<td>
																{{ substr_replace($transaction['postdate'],"/", 2, 0) }}
															</td>
														</tr>
														<tr>
															<td>
																Payment Method
															</td>
															<td>
																{{ @$transaction['pay_mode'] }}
															</td>
														</tr>
													</tbody>
												</table>
												@else
												<h5>{{ __('adminMessage.recordnotfound') }}</h5>
												@endif
											</div>
											<br>
											<button onclick="printDiv('table.table')" style="color:#FFFFFF;"
												target="_blank" class="btn btn-warning btn-bold"
												title="{{__('adminMessage.printinvoice')}}"><i
													class="flaticon2-print"></i></button>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- end:: Content -->

				<!--End:: Portlet-->
				@else
				<div class="alert alert-light alert-warning" role="alert">
					<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
					<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
				</div>
				@endif

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

	<!-- end::Scrolltop -->

	<!-- js files -->
	@include('gwc.js.user')
	<script>
		function printDiv(el) {
            var divContents = document.querySelector(el).outerHTML;
            var a = window.open('', '', 'height=500, width=500');
            a.document.write('<html>');
            a.document.write('<head>');
			document.querySelectorAll('link').forEach(element => {
				a.document.write(element.outerHTML)
			});
            a.document.write();
            a.document.write('<style>');
            a.document.write(`
				table td{
					border: 1px solid black;
				}
			`);
            a.document.write('</style>');
            a.document.write('</head>');
            a.document.write('<body style="padding: 1rem; background-color: white" > <h1>'+'{{__('adminMessage.pay_status')}}'+'</h1><br>');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        }
	</script>

</body>
<!-- end::Body -->

</html>
