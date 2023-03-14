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
	<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.orders')}}</title>
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
							<h3 class="kt-subheader__title">{{__('adminMessage.orders')}}</h3>
						</div>
						<div class="btn-group mt-2">

							{{--										<select name="order_customers" id="order_customers" class="form-control">--}}
							{{--										<option value="0">{{__('adminMessage.allcustomers')}}</option>--}}
							{{--										@if(!empty($customersLists))--}}
							{{--											@foreach($customersLists as $customersList)--}}
							{{--										    <option value="{{$customersList->id}}" @if(!empty(Session::get('order_customers')) && Session::get('order_customers')==$customersList->id) selected @endif>{{$customersList->name}}</option>--}}
							{{--										    @endforeach--}}
							{{--										@endif--}}
							{{--										</select>--}}


							<select name="order_countries" id="order_countries" class="form-control">
								<option value="0">{{__('adminMessage.all')}} Country</option>
								@forelse(\App\Country::where('is_active' , 1 )->where('parent_id', 0)->orderBy('display_order')->get() as $country)
									<option value="{{$country->id}}" @if(!empty(Session::get('order_countries')) && Session::get('order_countries')==$country->id) selected @endif>{{$country->name_en}}</option>
								@empty
								@endforelse
							</select>

						</div>
						<div class="btn-group mt-2">

							<select name="pay_mode" id="order_pay_mode" class="form-control">
								<option value="0">{{__('adminMessage.all')}}</option>
								@if(!empty($paymodelist))
									@foreach($paymodelist as $paymode)
										@if(!empty($paymode->pay_mode)){
										<option value="{{$paymode->pay_mode}}" @if(!empty(Session::get('pay_mode')) && Session::get('pay_mode')==$paymode->pay_mode) selected @endif>{{$paymode->pay_mode}}</option>
										@endif
									@endforeach
								@endif
							</select>

						</div>
						<div class="kt-subheader__toolbar">
							@if(Session::get('order_filter_dates'))
								<button type="button" class="btn btn-danger btn-bold resetorderdaterange">{{__('adminMessage.reset')}}</button>
							@endif
							<div class="kt-subheader__wrapper">
								<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
									<input type="text" class="form-control"  name="kt_daterangepicker_range" id="kt_daterangepicker_range"  placeholder="Select Date Range" value="@if(Session::get('order_filter_dates')){{Session::get('order_filter_dates')}}@endif">
									<button id="filterBydatesId" style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
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
							<form class="kt-margin-l-20" method="get" id="kt_subheader_search_form" action="{{url('gwc/orders')}}">
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
								<button type="button" class="btn btn-warning btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">@if(Session::get('order_filter_status')){{strtoupper(Session::get('order_filter_status'))}}@else{{strtoupper(__('adminMessage.all'))}}@endif</button>
								<div class="dropdown-menu dropdown-menu-right">

									<ul class="kt-nav">
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="all">{{__('adminMessage.all')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="completed">{{__('adminMessage.completed')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="pending">{{__('adminMessage.pending')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="received">Received</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="outfordelivery">Out For Delivery</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="canceled">{{__('adminMessage.canceled')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link orderstatus" id="returned">{{__('adminMessage.returned')}}</a></li>
									</ul>
								</div>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">@if(Session::get('pay_filter_status')){{strtoupper(Session::get('pay_filter_status'))}}@else{{strtoupper(__('adminMessage.all'))}}@endif</button>
								<div class="dropdown-menu dropdown-menu-right">

									<ul class="kt-nav">
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paystatus" id="all">{{__('adminMessage.all')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paystatus" id="paid">{{__('adminMessage.paid')}}</a></li>
										<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link paystatus" id="notpaid">{{__('adminMessage.notpaid')}}</a></li>

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

						@if(auth()->guard('admin')->user()->can('order-list'))
							@if(Session::get('order_filter_dates'))
								<a href="{{ route('printOrdersInRole') }}" class="btn btn-info btn-bold">{{__('adminMessage.print')}}</a>
							@endif
						<!--begin: Datatable -->
							<table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
								<thead>
								<tr>
									<th width="10">#</th>
									<th>{{__('adminMessage.orderdetails')}}</th>
									<th>{{__('adminMessage.customerdetails')}}</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								@if(count($orderLists))
									@php $p=1; $orderStatus='';@endphp
									@foreach($orderLists as $key=>$orderList)
										@php
											if(!empty($orderList->is_paid)){
                                            $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success">'.__('adminMessage.yes').'</span>';
                                            }else{
                                            $ispaid ='<span class="kt-pull-right kt-badge kt-badge--inline kt-badge--danger">'.__('adminMessage.no').'</span>';
                                            }


                                            $stylePending = $styleCompleted = $styleCanceled = $styleReturned = $styleOutfordelivery = $styleReceived = 'none';
                                            if(!empty($orderList->order_status) && $orderList->order_status=="pending"){
                                                $stylePending = 'block';
                                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="received"){
                                                $styleReceived = 'block';
                                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="completed"){
                                                $styleCompleted = 'block';
                                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="canceled"){
                                                $styleCanceled = 'block';
                                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="returned"){
                                                $styleReturned = 'block';
                                            }elseif(!empty($orderList->order_status) && $orderList->order_status=="outfordelivery"){
                                                $styleOutfordelivery = 'block';
                                            }
                                            $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning orderStatus_'.$orderList->id.' orderStatus_pending_'.$orderList->id.'" style="display:'.$stylePending.';">'.trans('adminMessage.pending').'</span>';
                                            $orderStatus .='<span class="kt-badge kt-badge--inline kt-badge--warning orderStatus_'.$orderList->id.' orderStatus_received_'.$orderList->id.'" style="display:'.$styleReceived.';">Received</span>';
                                            $orderStatus .='<span class="kt-badge kt-badge--inline kt-badge--success orderStatus_'.$orderList->id.' orderStatus_completed_'.$orderList->id.'" style="display:'.$styleCompleted.';">'.trans('adminMessage.completed').'</span>';
                                            $orderStatus .='<span class="kt-badge kt-badge--inline kt-badge--danger orderStatus_'.$orderList->id.' orderStatus_canceled_'.$orderList->id.'" style="display:'.$styleCanceled.';">'.trans('adminMessage.canceled').'</span>';
                                            $orderStatus .='<span class="kt-badge kt-badge--inline kt-badge--info orderStatus_'.$orderList->id.' orderStatus_returned_'.$orderList->id.'" style="display:'.$styleReturned.';">'.trans('adminMessage.returned').'</span>';
                                            $orderStatus .='<span class="kt-badge kt-badge--inline kt-badge--info orderStatus_'.$orderList->id.' orderStatus_outfordelivery_'.$orderList->id.'" style="display:'.$styleOutfordelivery.';">'.trans('adminMessage.outfordelivery').'</span>';


                                            $totalAmounts = App\Http\Controllers\AdminCustomersController::getOrderAmounts($orderList->id);

                                            $sellerDetails = App\Http\Controllers\AdminCustomersController::getCustomerDetails($orderList->customer_id);

										@endphp
										<tr class="search-body">
											<td align="center">{{$orderLists->firstItem() + $key}}
												<br><br>

												<span style="overflow: visible; position: relative; width: 80px;">
                                                 <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-left">
                                                 <ul class="kt-nav">


                                                 @if(auth()->guard('admin')->user()->can('order-view'))
													 <!--<li class="kt-nav__item"><a href="{{url('gwc/pos/'.$orderList->id)}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-edit"></i><span class="kt-nav__link-text">{{__('adminMessage.edit')}}</span></a></li>-->

														 <li class="kt-nav__item"><a href="{{url('gwc/orders/'.$orderList->id.'/view')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-eye"></i><span class="kt-nav__link-text">{{__('adminMessage.view')}}</span></a></li>
													 @endif
													 @if(auth()->guard('admin')->user()->can('order-view'))
														 <li class="kt-nav__item"><a target="_blank" href="{{url(app()->getLocale().'/order-print/'.$orderList["order_id_md5"])}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-print"></i><span class="kt-nav__link-text">{{__('adminMessage.print')}}</span></a></li>
														 @if ( ! $orderList->is_paid )
															 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_pay_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-link"></i><span class="kt-nav__link-text">Payment links</span></a></li>
														@endif
														<li class="kt-nav__item"><a href="/gwc/orders/{{ $orderList->order_id }}/payment-status" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-info"></i><span class="kt-nav__link-text">{{ __("adminMessage.pay_status") }}</span></a></li>
													 @endif
													 @if(auth()->guard('admin')->user()->can('trackhistory-list'))
														 <li class="kt-nav__item"><a href="{{url('gwc/orders-track/'.$orderList->id)}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-clock"></i><span class="kt-nav__link-text">{{__('adminMessage.trackhistory')}}</span></a></li>
													 @endif

													 @if(auth()->guard('admin')->user()->can('order-edit-status'))
														 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_edit_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon-edit"></i><span class="kt-nav__link-text">{{__('adminMessage.changeorderstatus')}}</span></a></li>
													 @endif

													 @if(auth()->guard('admin')->user()->can('order-delete'))
														 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">{{__('adminMessage.delete')}}</span></a></li>
													 @endif

													 @if(!empty($settingDetailsMenu->is_dezorder_active))
														 <li class="kt-nav__item">
                                                 <a href="javascript:;" data-toggle="modal" data-target="#kt_modal_dezorder_{{$orderList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-delivery-truck"></i><span class="kt-nav__link-text">{{__('adminMessage.dezorder')}}</span>
                                                 @if(!empty($orderList->is_for_dezorder))
														 <span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success">{{__('adminMessage.sent')}}</span>
													 @endif
                                                 </a>
                                                 </li>
													 @endif

                                                 </ul>
                                                 </div>
                                                 </div>
                                                 </span>

												<!-- copy -->
												<div id="copy-{{$orderList->id}}" style="display:none;text-align:left;">
													{{!empty($settings->owner_name)?$settings->owner_name:$settings->name_en}} order - {{$orderList->pay_mode}}

													ORDER ID : {{$orderList->order_id}}

													NAME : {{$orderList->name}}

													@if(!empty($orderList->area->name_en)){{$orderList->area->name_en}}@endif @if(!empty($orderList->block)) Block : {{$orderList->block}},@endif @if(!empty($orderList->street)) Street : {{$orderList->street}} ,@endif @if(!empty($orderList->block)) House : {{$orderList->house}} ,@endif @if(!empty($orderList->floor)) Foor : {{$orderList->floor}} ,@endif

													@if(!empty($orderList->delivery_time_en))DELIVERY TIME : {{$orderList->delivery_time_en}}@endif

													MOBILE : {{$orderList->mobile}}

													AMOUNT : {{\App\Currency::default().' '.number_format($totalAmounts,3)}}

													@if($orderList->is_express_delivery)
														Express Delivery
													@endif
												</div>
												<!-- end copy -->

												<a href="javascript:;" data-toggle="modal" data-target="#kt_modal_short_{{$orderList->id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i class="flaticon2-copy"></i></a>


												<div class="modal fade" id="kt_modal_short_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title">Short Details</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																</button>
															</div>
															<div class="modal-body">
																<div class="form-group row">
																	<div class="col-lg-12">
																		{{!empty($settings->owner_name)?$settings->owner_name:$settings->name_en}} order - {{$orderList->pay_mode}}
																		<br>ORDER ID : {{$orderList->order_id}}<br>NAME : {{$orderList->name}}<br>@if(!empty($orderList->area->name_en)){{$orderList->area->name_en}}@endif @if(!empty($orderList->block)) Block : {{$orderList->block}},@endif @if(!empty($orderList->street)) Street : {{$orderList->street}} ,@endif @if(!empty($orderList->block)) House : {{$orderList->house}} ,@endif @if(!empty($orderList->floor)) Foor : {{$orderList->floor}} ,@endif @if(!empty($orderList->delivery_time_en)) <br> DELIVERY TIME : {{$orderList->delivery_time_en}}@endif <br> MOBILE : {{$orderList->mobile}} <br> AMOUNT : {{\App\Currency::default().' '.number_format($totalAmounts,3)}}
																		@if($orderList->is_express_delivery)
																			<br> Express Delivery
																		@endif
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="modal fade" id="kt_modal_pay_{{$orderList->id}}"
													 tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
													 aria-hidden="true">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title">Payment links</h5>
																<button type="button" class="close"
																		data-dismiss="modal" aria-label="Close">
																</button>
															</div>
															<div class="modal-body">
																<div class="form-group row">
																	<div class="col-lg-12">
																		@php
																			$payments = App\Country::getGateways($orderList->country_id);
                                                                            $msg = route('order.details.start.pay' , ['kw','ar',$orderList->order_id_md5,'']);
																		if(@$settingInfo->sms_text_paymentlink_sent_active){
																			$msg = $settingInfo->sms_text_paymentlink_sent_en . ' '.  $msg;
																		}
																		@endphp
																		@if(count($payments) > 0)

																			<div class="row">
                                                                                <div class="col-6">
                                                                                    <form action="/gwc/send-sms" method="post" class="d-flex align-items-end" onsubmit="sendPaymentLinkViaSMS(event)">
                                                                                        @csrf
                                                                                        <input name="sms_msg" value="{{$msg}}" type="hidden">
                                                                                        <div class="form-group mx-auto w-75" >
                                                                                            <label>{{ __('webMessage.send_sms')}}</label>
                                                                                            <input  class="form-control" name="to" value="{{ @$orderList->mobile }}">
                                                                                        </div>
                                                                                        <div class="form-group mx-auto w-25" >
                                                                                            <button type="submit" class="btn btn-success">Send SMS</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
																				<div class="form-group col-lg-6" onclick="copyToClipboardLink('copyToClipboard_all{{ $orderList["order_id_md5"] }}');">
																					<label>{{__('webMessage.all')}}</label>
																					<input id="copyToClipboard_all{{ $orderList["order_id_md5"] }}" class="form-control disabled"
																						value="{{route('order.details.start.pay' , [\App\Country::getIsoById($orderList->country_id),'en',$orderList["order_id_md5"],''])}}">
																				</div>
																				@php $paytxt=''; @endphp
																				@foreach($payments as $payment)
																					@php
																						if($payment=='COD'){
                                                                                            continue;
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
                                                                                        }else if($payment=='Q8LINK'){
                                                                                        $paytxt = trans('webMessage.payment_Q8LINK');
                                                                                        }else if($payment=='MasterCard'){
                                                                                        $paytxt = trans('webMessage.payment_MasterCard');
                                                                                        }
																					@endphp

																					<div class="form-group col-lg-6" onclick="copyToClipboardLink('copyToClipboard_{{$payment}}_{{$orderList["order_id_md5"]}}');">
																						<label><img
																									src="{{url('uploads/paymenticons/'.strtolower($payment).'.png')}}"
																									height="30" alt="{{__('webMessage.payment_'.$payment)}}">&nbsp;{{$paytxt}}</label>
																						<input id="copyToClipboard_{{$payment}}_{{$orderList["order_id_md5"]}}"  class="form-control disabled" value="{{route('order.details.start.pay' , [\App\Country::getIsoById($orderList->country_id),'en',$orderList["order_id_md5"],$payment])}}">
																					</div>
																				@endforeach
																			</div>

																		@endif
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>


												<div class="modal fade" id="kt_modal_dezorder_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title">{{__('adminMessage.editorderstatus')}}</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																</button>
															</div>
															<div class="modal-body">
																<div class="form-group row">
																	<div class="col-lg-8">
																		<label>{{__('adminMessage.movetodezorder')}}</label>
																	</div>
																	<div class="col-lg-4" align="right">
																		<span class="kt-switch"><label><input value="{{$orderList->id}}" {{!empty($orderList->is_for_dezorder)?'checked':''}} type="checkbox"  id="dezorder" class="change_status"><span></span></label></span>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

												<!--edit order status -->
												<div class="modal fade" id="kt_modal_edit_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
																		<select id="order_status{{$orderList->id}}" name="order_status" class="form-control">
																			<option value="pending"   @if($orderList->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>
																			<option value="received"   @if($orderList->order_status=='received') selected @endif>Received</option>
																			<option value="outfordelivery"   @if($orderList->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>
																			<option value="completed" @if($orderList->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option>
																			<option value="canceled" @if($orderList->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option>
																			<option value="returned" @if($orderList->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>
																		</select>
																	</div>
																	<div class="col-lg-6">
																		<label>{{__('adminMessage.pay_status')}}</label>
																		<select id="pay_status{{$orderList->id}}" name="pay_status" class="form-control">
																			<option value="1" @if(!empty($orderList->is_paid)) selected @endif>{{__('adminMessage.paid')}}</option>
																			<option value="0" @if(empty($orderList->is_paid)) selected @endif>{{__('adminMessage.notpaid')}}</option>
																		</select>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-lg-12"><textarea name="extra_comment" id="extra_comment{{$orderList->id}}" class="form-control">@if(!empty($orderList->extra_comment)){!!$orderList->extra_comment!!}@endif</textarea></div>
																</div>
															</div>
															<div class="modal-footer">
																<span id="OrderStatusMsg{{$orderList->id}}"></span>
																<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.cancel')}}</button>
																<button type="button" id="{{$orderList->id}}" class="btn btn-danger changeorderstatus">{{__('adminMessage.change')}}</button>


															</div>
														</div>
													</div>
												</div>
												<!--Delete modal -->
												<div class="modal fade" id="kt_modal_{{$orderList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
																<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('gwc/orders/delete/'.$orderList->id)}}'">{{__('adminMessage.yes')}}</button>
															</div>
														</div>
													</div>
												</div>

											</td>
											<td>
												<table width="100%">
													<tr><td width="150">{{trans('adminMessage.orderid')}}</td><td ><a href="{{url('gwc/orders/'.$orderList->id.'/view')}}">{{$orderList->order_id}}</a></td></tr>
													@if(!empty($orderList->is_removed))
														<tr><td>{{trans('adminMessage.removed')}}</td><td ><span class="kt-badge kt-badge--inline kt-badge--danger">{{__('adminMessage.removed')}}</span></td></tr>
													@endif
													@if(!empty($sellerDetails) && !empty($sellerDetails->name))
														<tr><td>{{trans('adminMessage.seller')}}</td><td >{{$sellerDetails->name}}</td></tr>
													@endif
													<tr><td>{{trans('adminMessage.total')}}</td><td >{{number_format($totalAmounts,3)}}</td></tr>
													<tr  class="kt-hidden-mobile">
														<td rowspan="2"  style="vertical-align:inherit;">{{trans('adminMessage.orderstatus')}}</td>
														<td>
															<select onchange="forceUpdateStatus({{$orderList->id}} , @if(empty($orderList->is_paid)) 0 @else 1 @endif , '{{ $orderList->extra_comment }}' , 'forceUpdateStatus_{{$orderList->id}}')" id="forceUpdateStatus_{{$orderList->id}}" class="form-control">
																<option value="pending"   @if($orderList->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>
																<option value="received"   @if($orderList->order_status=='received') selected @endif>Received</option>
																<option value="outfordelivery"   @if($orderList->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>
																<option value="completed" @if($orderList->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option>
																<option value="canceled" @if($orderList->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option>
																<option value="returned" @if($orderList->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>
															</select>
														</td>
													</tr>
													<tr  class="kt-hidden-mobile">
														<td>{!!$orderStatus!!}</td>
													</tr>
													<tr class="kt-hidden-desktop">
														<td colspan="2">{{trans('adminMessage.orderstatus')}}</td>
													</tr>
													<tr class="kt-hidden-desktop">
														<td colspan="2">
															<select onchange="forceUpdateStatus({{$orderList->id}} , @if(empty($orderList->is_paid)) 0 @else 1 @endif , '{{ $orderList->extra_comment }}' , 'forceUpdateStatus2_{{$orderList->id}}')" id="forceUpdateStatus2_{{$orderList->id}}" class="form-control">
																<option value="pending"   @if($orderList->order_status=='pending') selected @endif>{{__('adminMessage.pending')}}</option>
																<option value="received"   @if($orderList->order_status=='received') selected @endif>Received</option>
																<option value="outfordelivery"   @if($orderList->order_status=='outfordelivery') selected @endif>{{__('adminMessage.outfordelivery')}}</option>
																<option value="completed" @if($orderList->order_status=='completed') selected @endif>{{__('adminMessage.completed')}}</option>
																<option value="canceled" @if($orderList->order_status=='canceled') selected @endif>{{__('adminMessage.canceled')}}</option>
																<option value="returned" @if($orderList->order_status=='returned') selected @endif>{{__('adminMessage.returned')}}</option>
															</select>
															<div style="width: 100%;margin-top: 15px;">{!!$orderStatus!!}</div>
														</td>
													</tr>
													<tr><td>{{trans('adminMessage.paymode_status')}}</td><td >{{$orderList->pay_mode}}{!!$ispaid!!}</td></tr>
												</table>

											</td>
											<td>
												<table width="100%">
													@if(!empty($orderList->name))
														<tr><td width="100">{{trans('adminMessage.name')}}</td><td >{{$orderList->name}}</td></tr>
													@endif
													@if(!empty($orderList->mobile))
														<tr><td width="100">{{trans('adminMessage.mobile')}}</td><td >{{$orderList->mobile}}</td></tr>
													@endif
													@if(!empty($orderList->email))
														<tr><td width="100">{{trans('adminMessage.email')}}</td><td >{{$orderList->email}}</td></tr>
													@endif
													@if(!empty($orderList->area->name_en))
														<tr><td width="100">{{trans('adminMessage.area')}}</td><td ><font color="#3333FF">{{$orderList->area->name_en}}</font></td></tr>
													@endif
													@if($orderList->is_express_delivery)
														<tr><td width="100" colspan="2"><span class="kt-badge kt-badge--inline kt-badge--danger">Express Delivery</span></tr>
													@endif
												</table>


											</td>
											<td>
												<table width="100%">
													@if(!empty($orderList->created_at))
														<tr><td width="100">{{trans('adminMessage.createdon')}}</td><td >{{$orderList->created_at}}</td></tr>
													@endif
													@if(!empty($orderList->delivery_date) && $theme==1)
														<tr><td width="100">{{trans('adminMessage.delivery_date')}}</td><td ><font color="#ff0000">{{$orderList->delivery_date}}</font></td></tr>
													@endif
													@if ( $orderList->driver_id != null )
														<tr>
															<td>Driver</td>
															<td>
																@if($orderList->driver) {{$orderList->driver->full_name}} @else #{{ $orderList->driver_id }} @endif
															</td>
														</tr>
													@endif
													@if(!empty($orderList->device_type))
														<tr><td width="100">{{trans('adminMessage.device_type')}}</td><td >{{$orderList->device_type}}</td></tr>
													@endif
													<tr><td width="100">{{trans('adminMessage.ip')}}</td><td >{{!empty($orderList->order_ip)?$orderList->order_ip:'NA'}}</td></tr>
												</table>

											</td>

										</tr>

										@php $p++; @endphp
									@endforeach
									<tr>
										<td colspan="2" class="text-center">
											{{__('adminMessage.grandtotal')}}: {{$totalPrice}}
										</td><td class="text-center">
											{{__('adminMessage.delivery_fees')}}: {{$totalDelivery}}
										</td><td class="text-center">
											{{__('adminMessage.totalproducts')}}: {{$totalOrders}}
										</td>
									</tr>
									<tr>
										<td colspan="10" class="text-center">{{ $orderLists->links() }}</td>
									</tr>
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

	function copyToClipboard(elementId) {

		// Create a "hidden" input
		var aux = document.createElement("input");

		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementId).innerHTML);

		// Append it to the body
		document.body.appendChild(aux);

		// Highlight its content
		aux.select();

		// Copy the highlighted text
		document.execCommand("copy");

		// Remove it from the body
		document.body.removeChild(aux);



		toastr.success("Order Text Message Has Been Coppied");
	}

	function copyToClipboardLink(id) {
		var textBox = document.getElementById(id);
		textBox.select();
		document.execCommand("copy");
		toastr.success("Patyment Link Has Been Coppied");
	}

	$("#order_countries").change(function () {
		var val = $(this).val();
		$.ajax({
			type: "GET",
			url: "/gwc/storetocookie/ajax",
			data: "key=order_countries&val=" + val,
			dataType: "json",
			contentType: false,
			cache: false,
			processData: false,
			success: function (msg) {
				window.location.reload();
			},
			error: function (msg) {
				//notification start
				var notify = $.notify({message: 'Error occurred while processing'});
				notify.update('type', 'danger');
				//notification end
			}
		});
	});

	function forceUpdateStatus(id ,pay_status , extra_comment , elId ) {
		var order_status = $("#" + elId).val();
		$.ajax({
			type: "GET",
			url: "/gwc/orders/status/ajax",
			data: "id=" + id + "&order_status=" + order_status + "&pay_status=" + pay_status + "&extra_comment=" + extra_comment,
			dataType: "json",
			contentType: false,
			cache: false,
			processData: false,
			success: function (msg) {
				if (msg.status == 200) {
					$(".orderStatus_" + id).hide();
					$(".orderStatus_"+order_status+"_" + id).show();
					var notify = $.notify({message: msg.message});
					notify.update('type', 'success');
				} else {
					var notify = $.notify({message: msg.message});
					notify.update('type', 'danger');
				}
			},
			error: function (msg) {
				//notification start
				var notify = $.notify({message: 'Error occurred while processing'});
				notify.update('type', 'danger');
				//notification end
			}
		});
	}

    function sendPaymentLinkViaSMS(e){
         e.preventDefault();

        let form = e.target;
        fetch(form.action,
	        {
	            method:'post',
	            body: new FormData(form)
	        },
        ).then(response => response.json())
        .then(result => {
          console.log('Success:', result);
          if(result?.status == 200){
               var notify = $.notify({message: '{{ __("webMessage.sms_sent") }}'});
               notify.update('type', 'success');
          }else{
              var notify = $.notify({message: result?.message ||  '{{ __("webMessage.invalidpayment") }}'});
            notify.update('type', 'danger');
          }
        })
        .catch(error => {
          console.error('Error:', error);
           var notify = $.notify({message: '{{ __("webMessage.invalidpayment") }}'});
           notify.update('type', 'danger');
        });
    }

</script>
</body>
<!-- end::Body -->
</html>
