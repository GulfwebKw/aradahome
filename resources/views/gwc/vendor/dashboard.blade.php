@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.gulfwebvendor')}}|{{__('adminMessage.dashboard')}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--css files -->
		@include('gwc.css.dashboard')
		<!-- token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed @if(!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

		<!-- begin:: Page -->

		<!-- begin:: Header Mobile -->
		<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
			<div class="kt-header-mobile__logo">
				@php
                $settingDetailsMenu = App\Http\Controllers\AdminDashboardController::getSettingsDetails();
                @endphp
                <a href="{{url('/vendor/home')}}">
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
					
                        @php
                        $gaAccesstoken = App\Http\Controllers\AdminDashboardController::gareport();
                        @endphp

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
 
							<!--Begin::Dashboard 6-->
                            <div class="kt-container ">
							<div class="row">
                       
                                @if(!empty($productsStats))
								<div class="col-lg-4">
									<a href="{{url('vendor/product')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-slow">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
												
												<div class="kt-iconbox__desc">
                                                <h3 class="kt-iconbox__title">{{__('adminMessage.products')}}</h3>
                                               <div class="row">
 <div class="col-lg-12">{{__('adminMessage.total')}}<span class="badge badge-success float-right" style="width:50px;">{{$productsStats['total']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.today')}}<span class="badge badge-info float-right" style="width:50px;">{{$productsStats['today']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastweek')}}<span class="badge badge-warning float-right" style="width:50px;">{{$productsStats['week']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastthritydays')}}<span class="badge badge-danger float-right" style="width:50px;">{{$productsStats['month']}}</span></div>
                                                </div>
                                                
												</div>
											</div>
										</div>
									</a>
								</div>
								@endif
								
                              
                                
                                @if(!empty($OrdersStats)) 
                                <div class="col-lg-4">
									<a href="{{url('vendor/orders')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
											
												<div class="kt-iconbox__desc">
												<h3 class="kt-iconbox__title">{{__('adminMessage.orders')}}({{trans('adminMessage.all')}})</h3>
                                                <div class="row">
 <div class="col-lg-12">{{__('adminMessage.total')}}<span class="badge badge-success float-right" style="width:50px;">{{$OrdersStats['total']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.today')}}<span class="badge badge-info float-right" style="width:50px;">{{$OrdersStats['today']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastweek')}}<span class="badge badge-warning float-right" style="width:50px;">{{$OrdersStats['week']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastthritydays')}}<span class="badge badge-danger float-right" style="width:50px;">{{$OrdersStats['month']}}</span></div>
                                                </div>
												</div>
											</div>
										</div>
									</a>
								</div>
                                @endif
                                
                                @if(!empty($SoldOutStats)) 
                                <div class="col-lg-4">
									<a href="{{url('vendor/mostsold')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
												
												<div class="kt-iconbox__desc">
												<h3 class="kt-iconbox__title">{{__('adminMessage.orders')}}({{trans('adminMessage.completed')}})</h3>
                                                <div class="row">
 <div class="col-lg-12">{{__('adminMessage.total')}}<span class="badge badge-success float-right" style="width:50px;">{{$SoldOutStats['total']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.today')}}<span class="badge badge-info float-right" style="width:50px;">{{$SoldOutStats['today']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastweek')}}<span class="badge badge-warning float-right" style="width:50px;">{{$SoldOutStats['week']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastthritydays')}}<span class="badge badge-danger float-right" style="width:50px;">{{$SoldOutStats['month']}}</span></div>
                                                </div>
												</div>
											</div>
										</div>
									</a>
								</div>
                                @endif
                                
                                @if(!empty($paymentStats)) 
                                <div class="col-lg-4">
									<a href="{{url('vendor/orders?pmode=KNET')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
											
												<div class="kt-iconbox__desc">
												<h3 class="kt-iconbox__title">{{__('adminMessage.payments')}}(Online)</h3>
                                                <div class="row">
 <div class="col-lg-12">{{__('adminMessage.total')}}<span class="badge badge-success float-right" style="width:50px;">{{$paymentStats['total']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.today')}}<span class="badge badge-info float-right" style="width:50px;">{{$paymentStats['today']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastweek')}}<span class="badge badge-warning float-right" style="width:50px;">{{$paymentStats['week']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastthritydays')}}<span class="badge badge-danger float-right" style="width:50px;">{{$paymentStats['month']}}</span></div>
                                                </div>
												</div>
											</div>
										</div>
									</a>
								</div>
                                @endif
                                
                                @if(!empty($codstats)) 
                                <div class="col-lg-4">
									<a href="{{url('vendor/orders?pmode=COD')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
												
												<div class="kt-iconbox__desc">
												<h3 class="kt-iconbox__title">{{__('adminMessage.payments')}}(Offline)</h3>
                                                <div class="row">
 <div class="col-lg-12">{{__('adminMessage.total')}}<span class="badge badge-success float-right" style="width:50px;">{{$codstats['total']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.today')}}<span class="badge badge-info float-right" style="width:50px;">{{$codstats['today']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastweek')}}<span class="badge badge-warning float-right" style="width:50px;">{{$codstats['week']}}</span></div>
 <div class="col-lg-12">{{__('adminMessage.lastthritydays')}}<span class="badge badge-danger float-right" style="width:50px;">{{$codstats['month']}}</span></div>
                                                </div>
												</div>
											</div>
										</div>
									</a>
								</div>
                                @endif
                                
                                
                                
                                <div class="col-lg-4">
									<a href="{{url('vendor/orders?pmode=COD_KNET')}}" class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
										<div class="kt-portlet__body">
											<div class="kt-iconbox__body">
												<div class="kt-iconbox__desc">
                                                <div class="row">
 <div class="col-lg-12"><h3 class="kt-iconbox__title">{{__('adminMessage.completed_order')}}</h3><span class="badge badge-success" style="width:100%;">{{$SoldOutStats['total']}}</span></div>
 <div class="col-lg-12 mt-3"><h3 class="kt-iconbox__title">{{__('adminMessage.amount_collected')}}</h3><span class="badge badge-info" style="width:100%;">{{number_format(($paymentStats['total']+$codstats['total']),3)}} {{\App\Currency::default()}}</span></div>
                                                </div>
												</div>
											</div>
										</div>
									</a>
								</div>
                                
							</div>
                         <!--sales state-->
                         
                        
                       
                        
						</div>

						<!-- end:: iconbox -->
					    <!--End::Dashboard 6-->
                            
						</div>
                        
                        

						<!-- end:: Content -->
					

					<!-- begin:: Footer -->
					@include('gwc.includes.footer');

					<!-- end:: Footer -->
				</div>
			</div>
		</div>

		<!-- end:: Page -->

		<!-- begin::Quick Panel -->
		

		<!-- end::Quick Panel -->

		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>

		<!-- end::Scrolltop -->
	
		<!-- js files -->
		@include('gwc.js.dashboard')
		
		<!--begin::Page Vendors(used by this page) -->
		<script src="{!!url('admin_assets/assets/plugins/amcharts/amcharts.js')!!}" type="text/javascript"></script>
     
		<script src="{!!url('admin_assets/assets/plugins/amcharts/serial.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/plugins/amcharts/radar.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/plugins/amcharts/pie.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/plugins/amcharts/polarScatter.min.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/plugins/amcharts/animate.min.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/js/pages/crud/metronic-datatable/base/html-table.js')!!}" type="text/javascript"></script>
		<script src="{!!url('admin_assets/assets/plugins/amcharts/light.js')!!}" type="text/javascript"></script>
        
        
       
	</body>

	<!-- end::Body -->
</html>