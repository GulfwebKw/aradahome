@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.categorydetails')}}</title>
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
				<a href="index.html">
					<img alt="Logo" src="{!! url('admin_assets/assets/media/logos/logo-light.png') !!}" />
				</a>
			</div>
			<div class="kt-header-mobile__toolbar">
				<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.categories')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.categorydetails')}}</a>
									</div>
								</div>
						<div class="kt-subheader__toolbar">
                        <a href="{{url('gwc/bundles/category')}}" class="btn btn-default btn-bold">{{__('adminMessage.back')}}</a>
                         @if(auth()->guard('admin')->user()->can('bundle-category-create'))
                        <a href="{{url('gwc/bundles/category/create')}}" class="btn btn-brand btn-bold"><i class="la la-plus"></i>&nbsp;{{__('adminMessage.createnew')}}</a>
                        @endif
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.categorydetails')}}</h3>
									</div>
								</div>	
                                			
										<!--begin::Form-->
					        @if(auth()->guard('admin')->user()->can('bundle-category-view'))
                    
                             <!--begin:: Portlet-->
							<div class="kt-portlet ">
								<div class="kt-portlet__body">
									<div class="kt-widget kt-widget--user-profile-3">
										<div class="kt-widget__top">
											<div class="kt-widget__media kt-hidden-">
                                                @if($categoryDetails->image)
												<img src="{!! url('uploads/bundle_category/thumb/'.$categoryDetails->image) !!}" alt="{{$categoryDetails->name_en}}">
                                                @else
                                                <img src="{!! url('uploads/no-image.png') !!}" alt="{{$categoryDetails->name_en}}">
                                                @endif
											</div>
											<div class="kt-widget__content">
												<div class="kt-widget__head">
													<a href="#" class="kt-widget__title">{{$categoryDetails->name_en}}</a>
												</div>
												<div class="kt-widget__info">
													<div class="kt-widget__desc">
														{!!$categoryDetails->details_en!!}
													</div>
													
													<div class="kt-widget__stats d-flex align-items-center flex-fill">
														<div class="kt-widget__item">
															<span class="kt-widget__date">
																{{__('adminMessage.createdon')}}
															</span>
															<div class="kt-widget__label">
																<span class="btn btn-label-brand btn-sm btn-bold btn-upper">{{$categoryDetails->created_at}}</span>
															</div>
														</div>
														<div class="kt-widget__item">
															<span class="kt-widget__date">
																{{__('adminMessage.subcategories')}}
															</span>
															<div class="kt-widget__label">
																<span class="btn btn-label-danger btn-sm btn-bold btn-upper">{{$categoryDetails->updated_at}}</span>
															</div>
														</div>
													
													</div>
												</div>
											</div>
										</div>
										<div class="kt-widget__bottom">
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-map"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title">{{__('adminMessage.subcategories')}}</span>
													<span class="kt-widget__value">{{$countCats}}</span>
												</div>
											</div>
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-squares-3"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title">{{__('adminMessage.products')}}</span>
													<span class="kt-widget__value">{{count($countProducts)}}</span>
												</div>
											</div>
									   </div>
									</div>
								</div>
							</div>
                       <!-- offer for category -->
                             
                      @if(auth()->guard('admin')->user()->can('bundle-category-add-offer'))
                      <div class="kt-portlet__body">
                      <h5>{{__('adminMessage.offer')}}</h5>
                      
                      </div>
                    <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('categoryoffer',$categoryDetails->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
										<!--parent categories dropdown -->	
                                           <div class="form-group row">
                                                <div class="col-lg-6">
                                                <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" value="1" {{$categoryDetails->is_offer==1?'checked':''}} name="is_offer"  id="is_offer"/>
																<span></span>
															</label>
														</span>
													</div>
												
												</div>
                                                </div>
                                            </div>
                                            													
                                       <!--categories name -->  
                                       <div class="form-group row">
                                                <div class="col-lg-6">
                                               <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_1_en')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_1_en')) is-invalid @endif" name="title_1_en"
                                                               value="{{$categoryDetails->title_1_en?$categoryDetails->title_1_en:old('title_1_en')}}" autocomplete="off" />
                                                               @if($errors->has('title_1_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_1_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_1_ar')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_1_ar')) is-invalid @endif" name="title_1_ar"
                                                               value="{{$categoryDetails->title_1_ar?$categoryDetails->title_1_ar:old('title_1_ar')}}" autocomplete="off"  />
                                                               @if($errors->has('title_1_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_1_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_2_en')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_2_en')) is-invalid @endif" name="title_2_en"
                                                               value="{{$categoryDetails->title_2_en?$categoryDetails->title_2_en:old('title_2_en')}}" autocomplete="off" />
                                                               @if($errors->has('title_2_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_2_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_2_ar')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_2_ar')) is-invalid @endif" name="title_2_ar"
                                                               value="{{$categoryDetails->title_2_ar?$categoryDetails->title_2_ar:old('title_2_ar')}}" autocomplete="off"  />
                                                               @if($errors->has('title_2_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_2_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_3_en')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_3_en')) is-invalid @endif" name="title_3_en"
                                                               value="{{$categoryDetails->title_3_en?$categoryDetails->title_3_en:old('title_3_en')}}" autocomplete="off" />
                                                               @if($errors->has('title_3_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_3_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_3_ar')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_3_ar')) is-invalid @endif" name="title_3_ar"
                                                               value="{{$categoryDetails->title_3_ar?$categoryDetails->title_3_ar:old('title_3_ar')}}" autocomplete="off"  />
                                                               @if($errors->has('title_3_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_3_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_4_en')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_4_en')) is-invalid @endif" name="title_4_en"
                                                               value="{{$categoryDetails->title_4_en?$categoryDetails->title_4_en:old('title_4_en')}}" autocomplete="off" />
                                                               @if($errors->has('title_4_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_4_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_4_ar')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('title_4_ar')) is-invalid @endif" name="title_4_ar"
                                                               value="{{$categoryDetails->title_4_ar?$categoryDetails->title_4_ar:old('title_4_ar')}}" autocomplete="off"  />
                                                               @if($errors->has('title_4_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_4_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                <label>{{__('adminMessage.link')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('offer_link')) is-invalid @endif" name="offer_link"
                                                               value="{{$categoryDetails->offer_link?$categoryDetails->offer_link:old('offer_link')}}" autocomplete="off" />
                                                               @if($errors->has('offer_link'))
                                                               <div class="invalid-feedback">{{ $errors->first('offer_link') }}</div>
                                                               @endif
                                                </div>
                                                
                                            </div>
                                            <div class="form-group row">
                                                
                                                <div class="col-lg-8">
                                                <label>{{__('adminMessage.promo_image')}}</label>
                                                        <div class="custom-file @if($errors->has('offer_image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('offer_image')) is-invalid @endif"  id="offer_image" name="offer_image">
														<label class="custom-file-label" for="offer_image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                        @if($errors->has('offer_image'))
                                                        <div class="invalid-feedback">{{ $errors->first('offer_image') }}</div>
                                                        @endif
                                                </div>
                                                <div class="col-lg-2">
                                                @if($categoryDetails->offer_image)
                                                <img src="{!! url('uploads/bundle_category/'.$categoryDetails->offer_image) !!}" width="150">
                                                @endif
                                                </div>
                                                
                                            </div>
                                                </div>
                                                 <div class="col-lg-6">
                                               <div class="form-group row">
                                                <div class="col-lg-6">
                                                 <div @if($categoryDetails->offer_image) style="background-image:url({!! url('uploads/bundle_category/'.$categoryDetails->offer_image) !!})" @endif class="kt-portlet kt-portlet--height-fluid btn btn-label-brand btn-bold btn-sm btn-label-success kt-text-center">
                                                 <p><h5>{{$categoryDetails->title_1_en?$categoryDetails->title_1_en:old('title_1_en')}}</h5></p>
                                                 <p><h1>{{$categoryDetails->title_2_en?$categoryDetails->title_2_en:old('title_2_en')}}</h1></p>
                                                 <p>{{$categoryDetails->title_3_en?$categoryDetails->title_3_en:old('title_3_en')}}</p>
                                                 <p><span class="btn btn-label-brand btn-bold btn-sm">{{$categoryDetails->title_4_en?$categoryDetails->title_4_en:old('title_4_en')}}</span></p>
                                                 </div>
                                                </div>
                                                <div class="col-lg-6">
                                                 <div @if($categoryDetails->offer_image) style="background-image:url({!! url('uploads/bundle_category/'.$categoryDetails->offer_image) !!})" @endif class="kt-portlet kt-portlet--height-fluid btn btn-label-brand btn-bold btn-sm kt-text-center">
                                                 <p><h5>{{$categoryDetails->title_1_ar?$categoryDetails->title_1_ar:old('title_1_ar')}}</h5></p>
                                                 <p><h1>{{$categoryDetails->title_2_ar?$categoryDetails->title_2_ar:old('title_2_ar')}}</h1></p>
                                                 <p>{{$categoryDetails->title_3_ar?$categoryDetails->title_3_ar:old('title_3_ar')}}</p>
                                                 <p><span class="btn btn-label-brand btn-bold btn-sm">{{$categoryDetails->title_4_ar?$categoryDetails->title_4_ar:old('title_4_ar')}}</span></p>
                                                 </div>
                                                 </div>
                                                 </div>
                                                </div>
                                            </div>
                                                   
                                        
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													
												</div>
											</div>
										</form>
                                        
                         
                            @endif 
							<!--end:: Portlet-->           
                         
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
	</body>

	<!-- end::Body -->
</html>