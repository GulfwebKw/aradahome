@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.editbanner')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.banner')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.editbanner')}}</a>
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.editbanner')}}</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="kt-portlet__head-wrapper">
											<div class="kt-portlet__head-actions">
												
												@if(auth()->guard('admin')->user()->can('banner-list'))
												<a href="{{url('gwc/banner')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listbanner')}}</a> @endif
											</div>
										</div>
									</div>
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('banner-edit'))
                    <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('banner.update',$editbanner->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
										<!--parent categories dropdown -->	
                                       
                                            													
                                       <!--categories name -->         
                                                <div class="form-group row">
                                                
                                                
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{$editbanner->title_en?$editbanner->title_en:old('title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{$editbanner->title_ar?$editbanner->title_ar:old('title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.link')}}</label>
                                                <input type="text" class="form-control @if($errors->has('link')) is-invalid @endif" name="link"
                                                               value="{!!$editbanner->link?$editbanner->link:old('link')!!}" autocomplete="off" placeholder="{{__('adminMessage.enter_link')}}" />
                                                               @if($errors->has('link'))
                                                               <div class="invalid-feedback">{{ $errors->first('link') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                         <div class="form-group row">
											<div class="col-lg-4">
                                            <label>{{__('adminMessage.type')}}</label>
											<select class="form-control @if($errors->has('link_type')) is-invalid @endif" name="link_type">
											<option value="web" @if($editbanner->link_type=="web") selected @endif >Web</option>
											<option value="category" @if($editbanner->link_type=="category") selected @endif >Category</option>
											<option value="product" @if($editbanner->link_type=="product") selected @endif >Product</option>
											<option value="offer" @if($editbanner->link_type=="offer") selected @endif >Offer</option>
											</select>
                                            @if($errors->has('link_type'))
                                            <div class="invalid-feedback">{{ $errors->first('link_type') }}</div>
                                            @endif	
										    </div>		
											<div class="col-lg-4">
                                            <label>{{__('adminMessage.link')}} or ID</label>
                                            <input type="text" class="form-control @if($errors->has('link_id')) is-invalid @endif" name="link_id"
                                                               value="{!!$editbanner->link_id?$editbanner->link_id:old('link_id')!!}" autocomplete="off" placeholder="{{__('adminMessage.enter_link')}}" />
                                            @if($errors->has('link_id'))
                                            <div class="invalid-feedback">{{ $errors->first('link_id') }}</div>
                                            @endif
                                            </div>
                                           @if($theme==8 || $theme==10  || $theme==11 || $theme==15)      
                                            <div class="col-lg-2"> 
                                            <label>{{__('adminMessage.image_size')}}</label> 
                                            <select name="image_size" class="form-control">
                                            <option value="1" @if($editbanner->image_size==1) selected @endif>296 X 254</option>
                                            <option value="2" @if($editbanner->image_size==2) selected @endif>296 X 520</option>
                                            <option value="3" @if($editbanner->image_size==3) selected @endif>612 X 256</option>
                                            </select> 
                                            </div> 
                                            <div class="col-lg-2">   
                                            <label>{{__('adminMessage.location')}}</label>
                                            <select name="box" class="form-control">
                                            @for($i=1;$i<=6;$i++)
                                            <option value="{{$i}}" @if($editbanner->box==$i) selected @endif>{{$i}}</option>
                                            @endfor
                                            </select>
                                            </div>  
                                            @endif 
											</div>
                                       
                                         <!-- friendly url , status , sorting -->   
                                         <div class="form-group row">
                                                
                                                <div class="col-lg-4">
                                                <label>
                                                        @if($theme==8 || $theme==10  || $theme==11 || $theme==15) 
                                                        {{trans('adminMessage.image')}}
                                                        @else
                                                        {{trans('theme')['theme'.$theme]['banner_image']}}
                                                        @endif
                                                </label>
                                                        <div class="custom-file @if($errors->has('image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('image')) is-invalid @endif"  id="image" name="image">
														<label class="custom-file-label" for="image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('image'))
                                                               <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                @if($editbanner->image)
                                                <br>
                                                <img src="{!! url('uploads/banner/thumb/'.$editbanner->image) !!}" width="40">
                                                @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" {{$editbanner->is_active==1?'checked':''}} name="is_active"  id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<label class="col-3 col-form-label">{{__('adminMessage.displayorder')}}</label>
													<div class="col-3">
														<input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"  value="{{$editbanner->display_order?$editbanner->display_order:old('display_order')}}" autocomplete="off" />
                                                               @if($errors->has('display_order'))
                                                               <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                               @endif
													</div>
												   </div>
                                                </div>
                                            </div>
                                            
                                                     
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/banner')}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
        
        
	</body>

	<!-- end::Body -->
</html>