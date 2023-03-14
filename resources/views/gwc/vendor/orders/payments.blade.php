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
		<title>{{__('adminMessage.gulfwebvendor')}}|{{__('adminMessage.payments')}}</title>
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
					<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

						<!-- begin:: Subheader -->
						<div class="kt-subheader   kt-grid__item" id="kt_subheader">
							<div class="kt-container  kt-container--fluid ">
								<div class="kt-subheader__main">
									<h3 class="kt-subheader__title">{{__('adminMessage.payments')}}</h3>
								</div>
                                <div class="btn-group mt-2">
										
										<select name="vpayment_customers" id="vpayment_customers" class="form-control">
										<option value="0">{{__('adminMessage.allcustomers')}}</option>
										@if(!empty($customersLists))
											@foreach($customersLists as $customersList)
										    <option value="{{$customersList->id}}" @if(!empty(Session::get('vpayment_customers')) && Session::get('vpayment_customers')==$customersList->id) selected @endif>{{$customersList->name}}</option>
										    @endforeach
										@endif
										</select>
										
									</div>
                                 <div class="btn-group mt-2">
										
										<select name="pay_mode" id="vpay_mode" class="form-control">
										<option value="0">{{__('adminMessage.all')}}</option>
										@if(!empty($paymodelist))
											@foreach($paymodelist as $paymode)
                                            @if(!empty($paymode->pay_mode)){
										    <option value="{{$paymode->pay_mode}}" @if(!empty(Session::get('vpay_mode')) && Session::get('vpay_mode')==$paymode->pay_mode) selected @endif>{{$paymode->pay_mode}}</option>
                                            @endif
										    @endforeach
										@endif
										</select>
										
									</div>   
								<div class="kt-subheader__toolbar">
                                
                                @if(Session::get('vpayment_filter_dates'))
                                <button type="button" class="btn btn-danger btn-bold resetorderdaterange">{{__('adminMessage.reset')}}</button>
                                @endif
                                <div class="kt-subheader__wrapper">
										<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
										<input type="text" class="form-control"  name="kt_daterangepicker_range" id="kt_daterangepicker_range"  placeholder="Select Date Range" value="@if(Session::get('vpayment_filter_dates')){{Session::get('vpayment_filter_dates')}}@endif">
                                        <button id="vpaymentfilterBydatesId" style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
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
									<form class="kt-margin-l-20" method="get" id="kt_subheader_search_form" action="{{url('vendor/payments')}}">
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
										<button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">@if(Session::get('vpay_status')){{strtoupper(Session::get('vpay_status'))}}@else{{strtoupper(__('adminMessage.all'))}}@endif</button>
										<div class="dropdown-menu dropdown-menu-right">
                                            
											<ul class="kt-nav">
												<li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link vpaystatus" id="all">{{__('adminMessage.all')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link vpaystatus" id="paid">{{__('adminMessage.paid')}}</a></li>
                                                <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link vpaystatus" id="notpaid">{{__('adminMessage.notpaid')}}</a></li>
                                                
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
								
                              
									<!--begin: Datatable -->
									<table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
										<thead>
											<tr>
												<th width="10">#</th>
												<th>{{trans('adminMessage.orderid')}}</th>
                                                <th>{{trans('adminMessage.total')}}</th>
                                                <th>{{trans('adminMessage.orderstatus')}}</th>
                                                <th>{{trans('adminMessage.paymode_status')}}</th>
                                                <th>{{trans('adminMessage.name')}}</th>
                                                <th>{{trans('adminMessage.mobile')}}</th>
                                                <th>{{trans('adminMessage.createdon')}}</th>
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
                                        
                                        if(!empty($orderList->order_status) && $orderList->order_status=="pending"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--warning">'.$orderList->order_status.'</span>';
                                        }elseif(!empty($orderList->order_status) && $orderList->order_status=="completed"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--success">'.$orderList->order_status.'</span>';
                                        }elseif(!empty($orderList->order_status) && $orderList->order_status=="canceled"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--danger">'.$orderList->order_status.'</span>';
                                        }elseif(!empty($orderList->order_status) && $orderList->order_status=="returned"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">'.$orderList->order_status.'</span>';
                                        }elseif(!empty($orderList->order_status) && $orderList->order_status=="outfordelivery"){
                                        $orderStatus ='<span class="kt-badge kt-badge--inline kt-badge--info">'.$orderList->order_status.'</span>';
                                        }
                                        
                                        $totalAmounts = App\Http\Controllers\AdminCustomersController::vendorTotalAmount($orderList->order_id);
                                        
                                        $sellerDetails = App\Http\Controllers\AdminCustomersController::getCustomerDetails($orderList->customer_id);
                                        
                                        @endphp
											<tr class="search-body">
												<td align="center">{{$orderLists->firstItem() + $key}}
                                                <br><br>
                                            
                                                 
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
                                                <br>ORDER ID : {{$orderList->order_id}}<br>NAME : {{$orderList->name}}<br>@if(!empty($orderList->area->name_en)){{$orderList->area->name_en}}@endif @if(!empty($orderList->block)) Block : {{$orderList->block}},@endif @if(!empty($orderList->street)) Street : {{$orderList->street}} ,@endif @if(!empty($orderList->block)) House : {{$orderList->house}} ,@endif @if(!empty($orderList->floor)) Foor : {{$orderList->floor}} ,@endif @if(!empty($orderList->delivery_time_en)) <br> DELIVERY TIME : {{$orderList->delivery_time_en}}@endif <br> MOBILE : {{$orderList->mobile}} <br> AMOUNT : {{\App\Currency::default().' '.number_format($totalAmounts,3)}} </div>
                                                
                                           </div> 
										</div>
									</div>
								</div>
							    </div>
                                
                                                
                           
                                                </td>
												<td>{{$orderList->order_id}}</td>
                                                <td>{{number_format($totalAmounts,3)}}</td>
												<td>{!!$orderStatus!!}</td>
                                                <td>{{$orderList->pay_mode}}{!!$ispaid!!}</td>
                                                <td>{{$orderList->name}}</td>
                                                <td>{{$orderList->mobile}}</td>
												<td>{{$orderList->cdate}}</td>
											</tr>
                                        
                                        @php $p++; @endphp
                                        @endforeach   
                                        <tr><td colspan="10" class="text-center">{{ $orderLists->links() }}</td></tr> 
                                        @else
                                        <tr><td colspan="10" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                        @endif    
										</tbody>
									</table>
                            
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
</script>
	</body>
	<!-- end::Body -->
</html>