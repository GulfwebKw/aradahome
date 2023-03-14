@php
use Illuminate\Support\Facades\Cookie;

$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.payments')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.payments')}}</h3>
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.paymentlisting')}}</a>
                                        
									</div>
								</div>
								<div class="kt-subheader__toolbar">
                                @if(Session::get('payment_filter_dates'))
                                <button type="button" class="btn btn-danger btn-bold resetorderdaterange">{{__('adminMessage.reset')}}</button>
                                @endif
                                <div class="kt-subheader__wrapper">
										<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
										<input type="text" class="form-control"  name="kt_daterangepicker_range" id="kt_daterangepicker_range"  placeholder="Select Date Range" value="@if(Session::get('order_filter_dates')){{Session::get('order_filter_dates')}}@endif">
                                        <button id="filterBydatesPayent" style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
													<span>
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
															</g>
														</svg>

														<!--<i class="flaticon2-search-1"></i>-->
													</span>
												</button>
                                        </div>
                                </div>        
									<form class="kt-margin-l-20" method="get" id="kt_subheader_search_form" action="{{url('gwc/payments')}}">
											<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
												<input value="{{Request()->q}}" type="text" class="form-control" placeholder="{{__('adminMessage.searchhere')}}" id="q" name="q">
												<button style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
													<span>
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
															</g>
														</svg>

														<!--<i class="flaticon2-search-1"></i>-->
													</span>
												</button>
											</div>
										</form>
									<div class="btn-group">
                                       
										<button type="button" class="btn btn-warning btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">@if(Session::get('payment_filter_status')){{strtoupper(Session::get('payment_filter_status'))}}@else{{strtoupper(__('adminMessage.all'))}}@endif</button>
										<div class="dropdown-menu dropdown-menu-right">
                                            
											<ul class="kt-nav">
												<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paymentstatus" id="all">{{__('adminMessage.all')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paymentstatus" id="paid">{{__('adminMessage.paid')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paymentstatus" id="notpaid">{{__('adminMessage.notpaid')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paymentstatus" id="release">{{__('adminMessage.released')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paymentstatus" id="nrelease">{{__('adminMessage.unreleased')}}</a></li>
											</ul>
										</div>
									</div>
                                    
								</div>
							</div>
						</div>

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                            @include('gwc.includes.alert') 
							<div class="kt-portlet kt-portlet--mobile">
								
                                @if(auth()->guard('admin')->user()->can('payment-list'))
									<!--begin: Datatable -->
									<table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
										<thead>
											<tr>
												<th width="10">#</th>
												<th >{{__('adminMessage.userdetails')}}</th>
                                                <th >{{__('adminMessage.paymentdetails')}}</th>
											
												<th width="10">{{__('adminMessage.actions')}}</th>
											</tr>
										</thead>
										<tbody>
                                        @if(count($paymentLists))
                                        @php $p=1; $orderStatus='';@endphp
                                        @foreach($paymentLists as $key=>$paymentList)
                                        @php
                                        if(!empty($paymentList->presult) && $paymentList->presult=='CAPTURED'){
                                        $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success">'.$paymentList->presult.'</span>';
                                        }else{
                                        $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--danger">'.$paymentList->presult.'</span>';
                                        }
										if(!empty($paymentList->release_pay)){
                                        $isreleased ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success">'.trans('adminMessage.yes').'</span>';
                                        }else{
                                        $isreleased ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--danger">'.trans('adminMessage.no').'</span>';
                                        }
                                        $areaName = App\Http\Controllers\AdminCustomersController::getCountryStatesArea($paymentList->area_id);
                                        @endphp
											<tr class="search-body">
												<td>{{$paymentLists->firstItem() + $key}}</td>
                                                
                                                <td>
                                                <table width="100%">
                                                @if(!empty($paymentList->name))
                                                <tr><td>{{trans('adminMessage.name')}}</td><td>{{$paymentList->name}}</td></tr>
                                                @endif
                                                @if(!empty($paymentList->mobile))
                                                <tr><td>{{trans('adminMessage.mobile')}}</td><td>{{$paymentList->mobile}}</td></tr>
                                                @endif
                                                @if(!empty($paymentList->email))
                                                <tr><td>{{trans('adminMessage.email')}}</td><td>{{$paymentList->email}}</td></tr>
                                                @endif
                                                @if(!empty($areaName))
                                                <tr><td>{{trans('adminMessage.area')}}</td><td>{{$areaName}}</td></tr>
                                                @endif
                                                <tr><td>{{trans('adminMessage.date')}}</td><td>{{$paymentList->created_at}}</td></tr>
                                                </table>
                                                </td>
                                                <td>
                                                <table width="100%">
                                                @if(!empty($paymentList->order_id))
                                                <tr><td>{{trans('adminMessage.orderid')}}</td><td>{{$paymentList->order_id}}</td></tr>
                                                @endif
                                                @if(!empty($paymentList->udf2))
                                                <tr><td>{{trans('adminMessage.amount')}}</td><td>{{\App\Currency::default().' '.number_format($paymentList->udf2,3)}}
                                                
                                                <span class="pull-right">@if(!empty($paymentList->amt_dollar)){{trans('webMessage.usd').''.number_format($paymentList->amt_dollar,2)}}@endif</span>
                                                </td></tr>
                                                @endif
                                                <tr><td>{{trans('adminMessage.status')}}</td><td>{{$paymentList->pay_mode}}{!!$ispaid!!}</td></tr>
                                                <tr><td>{{trans('adminMessage.release_status')}}</td><td>{{!empty($paymentList->release_date)?$paymentList->release_date:'0000-00-00 00:00:00'}}{!!$isreleased!!}</td></tr>
                                                <tr><td>{{trans('adminMessage.paymentid')}}</td><td>{{$paymentList->payment_id}}</td></tr>
                                                </table>
                                                </td>
												
                                                <td class="kt-datatable__cell">
                                                 <span style="overflow: visible; position: relative; width: 80px;">
                                                 <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-right">
                                                 <ul class="kt-nav">
                                                 @if(auth()->guard('admin')->user()->can('payment-view'))
                                                 <li class="kt-nav__item"><a href="{{url('gwc/orders/'.$paymentList->oid.'/view')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-eye"></i><span class="kt-nav__link-text">{{__('adminMessage.view')}}</span></a></li>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('payment-view'))
                                                 <li class="kt-nav__item"><a target="_blank" href="{{url(app()->getLocale(). '/order-print/'.$paymentList->order_id_md5)}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-print"></i><span class="kt-nav__link-text">{{__('adminMessage.print')}}</span></a></li>
                                                 @endif
												 @if(auth()->guard('admin')->user()->can('payment-delete'))
                                                 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$paymentList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">{{__('adminMessage.delete')}}</span></a></li>
                                                 @endif
                                                 
                                                 </ul>
                                                 </div>
                                                 </div>
                                                 </span>
                          <!--edit order status -->
                          <div class="modal fade" id="kt_modal_edit_{{$paymentList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{__('adminMessage.editorderstatus')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.order_status')}}</label>
                                                <select id="order_status{{$paymentList->id}}" name="order_status" class="form-control">
                                                <option value="pending"   @if($paymentList->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>     
                                                <option value="received"   @if($paymentList->order_status=='received') selected @endif>Received</option> 
                                                <option value="outfordelivery"   @if($paymentList->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>      
                                                <option value="completed" @if($paymentList->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option> 
                                                <option value="canceled" @if($paymentList->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option> 
                                                <option value="returned" @if($paymentList->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>   
                                                </select>
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.pay_status')}}</label>
                                                <select id="pay_status{{$paymentList->id}}" name="pay_status" class="form-control">
                                                <option value="1" @if(!empty($paymentList->is_paid)) selected @endif>{{__('adminMessage.paid')}}</option>      
                                                <option value="0" @if(empty($paymentList->is_paid)) selected @endif>{{__('adminMessage.notpaid')}}</option>
                                                </select>
                                                </div>
                                            </div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.cancel')}}</button>
											<button type="button" id="{{$paymentList->id}}" class="btn btn-danger changeorderstatus">{{__('adminMessage.change')}}</button>
										</div>
									</div>
								</div>
							</div>                       
                                                 <!--Delete modal -->
                        <div class="modal fade" id="kt_modal_{{$paymentList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<h6 class="modal-title">{!!__('adminMessage.alertDeleteMessage')!!}</h6>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
											<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('gwc/payments/delete/'.$paymentList->id)}}'">{{__('adminMessage.yes')}}</button>
										</div>
									</div>
								</div>
							</div>
                                                </td>
											</tr>
                                        
                                        @php $p++; @endphp
                                        @endforeach   
                                        <tr><td colspan="10" class="text-center">{{ $paymentLists->links() }}</td></tr> 
                                        @else
                                        <tr><td colspan="10" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                        @endif    
										</tbody>
									</table>
                            @else
                            <div class="alert alert-light alert-warning" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
								<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
							</div>
                            @endif
									<!--end: Datatable -->
								
							</div>
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

		<!-- end::Scrolltop -->

		<!-- js files -->
		@include('gwc.js.user')
        

 <script>
$(function() {
  $('input[name="kt_daterangepicker_range"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>
	</body>
	<!-- end::Body -->
</html>