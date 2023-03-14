@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.createproduct')}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--css files -->
		@include('gwc.css.user')
        <link href="{{url('admin_assets/assets/css/pages/wizard/wizard-1.css')}}" rel="stylesheet" type="text/css" />
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
                $warrantyLists = App\Http\Controllers\AdminProductController::getWarrantLists();
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
									<h3 class="kt-subheader__title">{{__('adminMessage.product')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('vendor/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.createproduct')}}</a>
									</div>
								</div>
								<div class="kt-subheader__toolbar">
									<div class="btn-group">
                                       
												<a href="{{url('vendor/product')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listproduct')}}</a> 
										
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
						<div class="kt-grid  kt-wizard-v1 kt-wizard-v1--white" id="kt_projects_add" data-ktwizard-state="step-first">
									<div class="kt-grid__item">

											<!--begin: Form Wizard Nav -->
											<div class="kt-wizard-v1__nav">
												<div class="kt-wizard-v1__nav-items">

													<!--doc: Replace A tag with SPAN tag to disable the step link click -->
													<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" data-ktwizard-state="current">
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.info')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.details')}}
															</div>
														</div>
													</div>
                                                   
                                                    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.options')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.options')}}
															</div>
														</div>
													</div>
                                                    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.category')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.categories')}}
															</div>
														</div>
													</div>
													<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.gallery')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.gallery')}}
															</div>
														</div>
													</div>
                                                    
													<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.seo')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.seoandtags')}}
															</div>
														</div>
													</div>
													
                                                    <div class="kt-wizard-v1__nav-item" >
														<div class="kt-wizard-v1__nav-body">
															<div class="kt-wizard-v1__nav-icon">{!!__('svgicon.finish')!!}</div>
															<div class="kt-wizard-v1__nav-label">
																{{__('adminMessage.finish')}}
															</div>
														</div>
													</div>
												</div>
											</div>

											<!--end: Form Wizard Nav -->
										</div>
                                     </div>
												
										<!--begin::Form-->
					
                    
                         <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('product.store')}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
																					
                                           <div class="form-group row">
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.item_code')}}</label>
                                                <input type="text" class="form-control @if($errors->has('item_code')) is-invalid @endif" name="item_code"
                                                               value="{{old('item_code')?old('item_code'):$serialNumber}}" autocomplete="off" placeholder="{{__('adminMessage.enter_item_code')}}*" />
                                                               @if($errors->has('item_code'))
                                                               <div class="invalid-feedback">{{ $errors->first('item_code') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.sku_no')}}</label>
                                                <input type="text" class="form-control @if($errors->has('sku_no')) is-invalid @endif" name="sku_no"
                                                               value="{{old('sku_no')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_sku_no')}}" />
                                                               @if($errors->has('sku_no'))
                                                               <div class="invalid-feedback">{{ $errors->first('sku_no') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.weight')}}</label>
                                                <input type="text" class="form-control @if($errors->has('weight')) is-invalid @endif" name="weight"  value="{{old('weight')?old('weight'):''}}" autocomplete="off"   placeholder="{{__('adminMessage.enter_weight')}}"/>
                                                               @if($errors->has('weight'))
                                                               <div class="invalid-feedback">{{ $errors->first('weight') }}</div>
                                                               @endif
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.height')}}</label>
                                                <input type="text" class="form-control @if($errors->has('height')) is-invalid @endif" name="height"  value="{{old('height')?old('height'):''}}" autocomplete="off"   placeholder="{{__('adminMessage.enter_height')}}"/>
                                                               @if($errors->has('height'))
                                                               <div class="invalid-feedback">{{ $errors->first('height') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.width')}}</label>
                                                <input type="text" class="form-control @if($errors->has('width')) is-invalid @endif" name="width"  value="{{old('width')?old('width'):''}}" autocomplete="off"   placeholder="{{__('adminMessage.enter_width')}}"/>
                                                               @if($errors->has('width'))
                                                               <div class="invalid-feedback">{{ $errors->first('width') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.depth')}}</label>
                                                <input type="text" class="form-control @if($errors->has('depth')) is-invalid @endif" name="depth"  value="{{old('depth')?old('depth'):''}}" autocomplete="off"   placeholder="{{__('adminMessage.enter_depth')}}"/>
                                                               @if($errors->has('depth'))
                                                               <div class="invalid-feedback">{{ $errors->first('depth') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.displayorder')}}</label>
                                                <input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"  value="{{old('display_order')?old('display_order'):$lastOrder}}" autocomplete="off" />
                                                               @if($errors->has('display_order'))
                                                               <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                                 
                                                <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{old('title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}*" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{old('title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}*" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.extra_title_en')}}</label>
                                                <input  type="text" class="form-control @if($errors->has('extra_title_en')) is-invalid @endif" name="extra_title_en"
                                                               value="{{old('extra_title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}" />
                                                               @if($errors->has('extra_title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('extra_title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.extra_title_ar')}}</label>
                                                <input  type="text" class="form-control @if($errors->has('extra_title_ar')) is-invalid @endif" name="extra_title_ar"
                                                               value="{{old('extra_title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}" />
                                                               @if($errors->has('extra_title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('extra_title_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.sdetails_en')}}</label>
                                                        <textarea rows="3" id="sdetails_en" name="sdetails_en" class="form-control @if($errors->has('sdetails_en')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_sdetails_en')}}">{{old('sdetails_en')}}</textarea>
                                                               @if($errors->has('sdetails_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('sdetails_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.sdetails_ar')}}</label>
                                                        <textarea   rows="3" id="sdetails_ar" name="sdetails_ar" class="form-control @if($errors->has('sdetails_ar')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_sdetails_ar')}}">{{old('sdetails_ar')}}</textarea>
                                                               @if($errors->has('sdetails_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('sdetails_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                      <!--categories description -->          
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_en')}}</label>
                                                        <textarea rows="3" id="details_en" name="details_en" class="kt-tinymce-4 form-control @if($errors->has('details_en')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_en')}}">{{old('details_en')}}</textarea>
                                                               @if($errors->has('details_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_ar')}}</label>
                                                        <textarea   rows="3" id="details_ar" name="details_ar" class="kt-tinymce-4 form-control @if($errors->has('details_ar')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_ar')}}">{{old('details_ar')}}</textarea>
                                                               @if($errors->has('details_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                    
                                           <div class="form-group row">
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.retail_price')}}*</label>
                                                <input type="text" class="form-control @if($errors->has('retail_price')) is-invalid @endif" name="retail_price" id="retail_price"
                                                               value="{{old('retail_price')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_retail_price')}}" />
                                                               @if($errors->has('retail_price'))
                                                               <div class="invalid-feedback">{{ $errors->first('retail_price') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.old_price')}}(e.g : <s>{{\App\Currency::default()}} 000</s>)</label>
                                                <input type="text" class="form-control @if($errors->has('old_price')) is-invalid @endif" name="old_price"
                                                               value="{{old('old_price')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_old_price')}}" />
                                                               @if($errors->has('old_price'))
                                                               <div class="invalid-feedback">{{ $errors->first('old_price') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.cost_price')}}<span id="profitprice"></span></label>
                                                <input type="text" class="form-control @if($errors->has('cost_price')) is-invalid @endif" name="cost_price" id="cost_price"
                                                               value="{{old('cost_price')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_cost_price')}}" />
                                                               @if($errors->has('cost_price'))
                                                               <div class="invalid-feedback">{{ $errors->first('cost_price') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.wholesale_price')}}</label>
                                                <input type="text" class="form-control @if($errors->has('wholesale_price')) is-invalid @endif" name="wholesale_price"
                                                               value="{{old('wholesale_price')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_wholesale_price')}}" />
                                                               @if($errors->has('wholesale_price'))
                                                               <div class="invalid-feedback">{{ $errors->first('wholesale_price') }}</div>
                                                               @endif
                                                </div>
                                                
                                               
                                            </div>
                                          
                                            <div class="form-group row">
                                               
                                                <div class="col-lg-4">
                                                        <label>{{trans('theme')['theme'.$theme]['product_image']}}*</label>
                                                        <div class="custom-file @if($errors->has('image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('image')) is-invalid @endif"  id="image" name="image">
														<label class="custom-file-label" for="image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('image'))
                                                               <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                               @endif
                                                </div>
                                                
                                                <div class="col-lg-4">
                                                        <label>{{trans('theme')['theme'.$theme]['product_rollover_image']}}</label>
                                                        <div class="custom-file @if($errors->has('rollover_image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('rollover_image')) is-invalid @endif"  id="rollover_image" name="rollover_image">
														<label class="custom-file-label" for="rollover_image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('rollover_image'))
                                                               <div class="invalid-feedback">{{ $errors->first('rollover_image') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                        <label>{{trans('theme')['theme'.$theme]['attachfile']}}</label>
                                                        <div class="custom-file @if($errors->has('attachfile')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('attachfile')) is-invalid @endif"  id="attachfile" name="attachfile">
														<label class="custom-file-label" for="rollover_image">{{__('adminMessage.choosefile')}}</label>
													    </div>
                                                               @if($errors->has('attachfile'))
                                                               <div class="invalid-feedback">{{ $errors->first('attachfile') }}</div>
                                                               @endif
                                                </div>
                                                
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-8">
                                                <label>{{__('adminMessage.slug')}}</label>
                                                <input type="text" class="form-control @if($errors->has('slug')) is-invalid @endif" name="slug"
                                                               value="{{old('slug')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_slug')}}" />
                                                               @if($errors->has('slug'))
                                                               <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.warranty')}}</label>
                                                <select class="form-control @if($errors->has('warranty')) is-invalid @endif" name="warranty">
                                                <option value="0">{{__('adminMessage.choosewarranty')}}</option>
                                                @if(!empty($warrantyLists) && count($warrantyLists)>0)
                                                @foreach($warrantyLists as $warrantyList)
                                                <option value="{{$warrantyList->id}}">{{$warrantyList->title_en}}</option>
                                                @endforeach
                                                @endif
                                                </select>
                                                               @if($errors->has('warranty'))
                                                               <div class="invalid-feedback">{{ $errors->first('warranty') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                      
                                  <div class="form-group">
                                        <div class="input-group row">
                                            <label class="col-2">
                                                {{ __('adminMessage.is_customizable') }}
                                            </label>
                                            <div class="col-2" align="right">
                                                <span class="kt-switch">
                                                    <label>
                                                        <input value="1"
                                                               type="checkbox" id="customizable"
                                                               name="customizable">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>       
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i class="la la-angle-double-right"></i></button>
													<button type="button" onClick="Javascript:window.location.href='{{url('vendor/product')}}'"  class="btn btn-secondary cancelbtn kt-pull-right">{{__('adminMessage.cancel')}}</button>
                                                    
                                                    <label for="redirect_to_listing"><input type="checkbox" name="redirect_to_listing" id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}</label>
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

	
		<!-- js files -->
		@include('gwc.js.user')
        
       <script>
        jQuery(document).ready(function() {
		
		$(document).on("keyup","#cost_price",function(){
		var cost_price   = $(this).val();
		var retail_price = $("#retail_price").val();
		var profit = parseFloat(retail_price) - parseFloat(cost_price);
		$("#profitprice").html('&nbsp;(Profit : '+profit+' {{\App\Currency::default()}})');
		});
		
		
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
		<script src="{{url('admin_assets/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
        <script>
		$('#news_date').datepicker({format:"yyyy-mm-dd"});
		</script>
	</body>

	<!-- end::Body -->
</html>
