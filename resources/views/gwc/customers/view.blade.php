@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.customerdetails')}}</title>
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
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.customerdetails')}}</a>
									</div>
								</div>
								<div class="kt-subheader__toolbar"><a href="{{url('gwc/customers')}}" class="btn btn-default btn-bold">{{__('adminMessage.back')}}</a>
								</div>
							</div>
						</div>

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
						<div class="row">
                        <div class="col-lg-6">
                        <div class="kt-portlet kt-portlet--mobile">
								<div class="kt-portlet__head kt-portlet__head--lg">
									<div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
										<h3 class="kt-portlet__head-title">
											{{__('adminMessage.customerdetails')}}
										</h3>
									</div>
								</div>
                      
								<div class="kt-portlet__body">
                            @if(auth()->guard('admin')->user()->can('customers-view'))
						    <!--Begin:: Portlet-->
							<div class="kt-widget kt-widget--user-profile-3">
										<div class="kt-widget__top">
                                            <div class="kt-widget__media">
                                                @if($customerDetails->image)
												<img src="{{url('uploads/customers/thumb/'.$customerDetails->image)}}" alt="{{$customerDetails->name}}">
                                                @else
                                                <img src="{{url('uploads/customers/no-image.png')}}" alt="{{$customerDetails->name}}">
                                                @endif
											</div>
											<div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-bolder kt-font-light kt-hidden">
												JM
											</div>
                                            
											<div class="kt-widget__content">
												<div class="kt-widget__head">
													<div class="kt-widget__user">
														<h2>{{$customerDetails->name}}</h2>
													</div>
												</div>
												<div class="kt-widget__subhead">
													@if($customerDetails->email)
                                                    <a href="javascript:;"><i class="flaticon2-new-email"></i>{{$customerDetails->email}}</a>
                                                    @endif
                                                    
                                                    @if($customerDetails->username)
                                                    <a href="javascript:;"><i class="flaticon-user"></i>{{$customerDetails->username}}</a>
                                                    @endif
                                                    
                                                    
                                                    @if($customerDetails->mobile)
													<a href="javascript:;"><i class="flaticon2-phone"></i>{{$customerDetails->mobile}}</a>
                                                    @endif
                                                   
													
												</div>
												<div class="kt-widget__info">
													<div class="kt-widget__desc">
														
													</div>
													
												</div>
											</div>
										</div>
										
									</div>

							<!--End:: Portlet-->	
                            @else
                            <div class="alert alert-light alert-warning" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
								<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
							</div>
                            @endif
									<!--end: Datatable -->
								</div>
							</div>
                     
                        
                                    
                        </div>
                        <div class="col-lg-6">
                        <div class="kt-portlet kt-portlet--mobile">
								<div class="kt-portlet__head kt-portlet__head--lg">
									<div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
										<h3 class="kt-portlet__head-title">
											{{__('adminMessage.address')}}
										</h3>
									</div>
								</div>
                      
								<div class="kt-portlet__body">
                                   <div class="kt-form kt-form--label-right">
                         <form name="tFrmpass" id="tFrmpass"  method="post"
                          class="uk-form-stacked" enctype="multipart/form-data" action="{{route('customersaddress',$customerDetails->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
													<div class="kt-form__body">
														<div class="kt-section kt-section--first">
															<div class="kt-section__body">
																@include('gwc.includes.alert')
																
																<div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.title')}}*</label>
																<div class="col-lg-9 col-xl-6">
<input type="text" name="title" class="form-control @if($errors->has('title')) is-invalid @endif" value="{{old('title')}}" placeholder="{{__('adminMessage.enter_title')}}">                                                               	                                                                @if($errors->has('title'))
                                                                <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                                                                @endif
																</div>
																</div>
																<div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.country')}}*</label>
																<div class="col-lg-9 col-xl-6">
                                                                <select name="country" id="country" class="form-control @if($errors->has('country')) is-invalid @endif">
                                                                <option value="-1">{{__('adminMessage.choosecountry')}}</option>
                                                                @foreach($listCountries as $listcountry)
                                                                <option value="{{$listcountry->id}}">{{$listcountry->name_en}}</option>
                                                                @endforeach
                                                                </select>
                                                                @if($errors->has('country'))
                                                                <div class="invalid-feedback">{{ $errors->first('country') }}</div>
                                                                @endif
															    </div>
																</div>
																<div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.state')}}*</label>
																<div class="col-lg-9 col-xl-6">
                                                                <select name="state" id="state" class="form-control @if($errors->has('state')) is-invalid @endif">
                                                                <option value="-1">{{__('adminMessage.choosestate')}}</option>
                                                                </select>
                                                                @if($errors->has('state'))
                                                                <div class="invalid-feedback">{{ $errors->first('state') }}</div>
                                                                @endif
															    </div>
																</div>
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.area')}}*</label>
																<div class="col-lg-9 col-xl-6">
                                                                <select name="area" id="area" class="form-control @if($errors->has('area')) is-invalid @endif">
                                                                <option value="-1">{{__('adminMessage.choosearea')}}</option>
                                                                </select>
                                                                @if($errors->has('area'))
                                                                <div class="invalid-feedback">{{ $errors->first('area') }}</div>
                                                                @endif
															    </div>
																</div>
                                                                
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.block')}}*</label>
																<div class="col-lg-9 col-xl-6">
                                                                <input type="text" name="block" class="form-control @if($errors->has('block')) is-invalid @endif" value="{{old('block')}}" placeholder="{{__('adminMessage.enter_block')}}">                                                               	                                                                @if($errors->has('block'))
                                                                <div class="invalid-feedback">{{ $errors->first('block') }}</div>
                                                                @endif
																</div>
																</div>
                                                                
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.street')}}</label>
																<div class="col-lg-9 col-xl-6">
                                                                <input type="text" name="street" class="form-control @if($errors->has('street')) is-invalid @endif" value="{{old('street')}}" placeholder="{{__('adminMessage.enter_street')}}">                                                               	                                                                @if($errors->has('street'))
                                                                <div class="invalid-feedback">{{ $errors->first('street') }}</div>
                                                                @endif
																</div>
																</div>
                                                                
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.avenue')}}</label>
																<div class="col-lg-9 col-xl-6">
                                                                <input type="text" name="avenue" class="form-control @if($errors->has('avenue')) is-invalid @endif" value="{{old('avenue')}}" placeholder="{{__('adminMessage.enter_avenue')}}">                                                               	                                                                @if($errors->has('avenue'))
                                                                <div class="invalid-feedback">{{ $errors->first('avenue') }}</div>
                                                                @endif
																</div>
																</div>
                                                                
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.house')}}</label>
																<div class="col-lg-9 col-xl-6">
                                                                <input type="text" name="house" class="form-control @if($errors->has('house')) is-invalid @endif" value="{{old('house')}}" placeholder="{{__('adminMessage.enter_house')}}">                                                               	                                                                @if($errors->has('house'))
                                                                <div class="invalid-feedback">{{ $errors->first('house') }}</div>
                                                                @endif
																</div>
																</div>
                                                                
                                                                <div class="form-group row">
																<label class="col-xl-3 col-lg-3 col-form-label">{{__('adminMessage.floor')}}</label>
																<div class="col-lg-9 col-xl-6">
                                                                <input type="text" name="floor" class="form-control @if($errors->has('floor')) is-invalid @endif" value="{{old('floor')}}" placeholder="{{__('adminMessage.enter_floor')}}">                                                               	                                                                @if($errors->has('floor'))
                                                                <div class="invalid-feedback">{{ $errors->first('floor') }}</div>
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
                                @if(!empty($listaddresss))   
                     @foreach($listaddresss as $listaddress)    
                     @php
                     $getAddress = App\Http\Controllers\AdminCustomersController::getCustAddress($listaddress->id);
                     @endphp
                            <!--begin::Portlet-->
									<div class="kt-portlet kt-portlet--collapsed" data-ktportlet="true" id="kt_portlet_tools_4">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													{{$listaddress->title}} @if($listaddress->is_default==1) <span class="kt-badge kt-badge--brand kt-badge--inline">Default</span> @endif
												</h3>
											</div>
											<div class="kt-portlet__head-toolbar">
												<div class="kt-portlet__head-group">
													<a href="javascript:;" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-brand btn-icon-md"><i class="la la-angle-down"></i></a>
													<a href="javascript:;"  class="btn btn-sm btn-icon @if($listaddress->is_default==1) btn-success @else btn-danger @endif btn-icon-md chooseDefault" id="{{$listaddress->id}}"><i class="la la-eye"></i></a>
													<a href="{{url('gwc/customers/deleteAddress/'.$listaddress->customer_id.'/'.$listaddress->id)}}"  class="btn btn-sm btn-icon btn-warning btn-icon-md"><i class="la la-close"></i></a>
												</div>
											</div>
										</div>
										<div class="kt-portlet__body">
											<div class="kt-portlet__content">
												{!!$getAddress!!}
											</div>
										</div>
									</div>

									<!--end::Portlet-->
                                    @endforeach
                              @endif   
							</div>
                        </div>
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
         
    
	</body>
	<!-- end::Body -->
</html>