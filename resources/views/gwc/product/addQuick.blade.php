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
									<h3 class="kt-subheader__title">{{__('adminMessage.product')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.createproduct')}}</a>
									</div>
								</div>
								<div class="kt-subheader__toolbar">
									<div class="btn-group">
                                        @if(auth()->guard('admin')->user()->can('product-list'))
												<a href="{{url('gwc/product')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listproduct')}}</a> @endif
										
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
						
												
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('product-create'))
                    
                         <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('product.addQuick')}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
                                            <div class="form-group row">
											<h5>{{trans('adminMessage.details')}}</h5>
                                            </div>										
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
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_en')}}*</label>
                                                <input required type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{old('title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_ar')}}*</label>
                                                <input required type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{old('title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}" />
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
                                                <label>{{__('adminMessage.details_en')}}*</label>
                                                        <textarea rows="3" id="details_en" name="details_en" class="kt-tinymce-4 form-control @if($errors->has('details_en')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_en')}}">{{old('details_en')}}</textarea>
                                                               @if($errors->has('details_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_ar')}}*</label>
                                                        <textarea   rows="3" id="details_ar" name="details_ar" class="kt-tinymce-4 form-control @if($errors->has('details_ar')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_ar')}}">{{old('details_ar')}}</textarea>
                                                               @if($errors->has('details_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('details_ar') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                    
                                          
                                            
                                            
                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.item_has_an_attribute')}}</label>
                                                <select class="form-control @if($errors->has('is_attribute')) is-invalid @endif" name="is_attribute" id="is_attribute">
                                                <option value="1" @if(old('is_attribute')==1) selected @endif >{{__('adminMessage.yes')}}</option>
                                                <option value="0" @if(old('is_attribute')==0) selected @endif >{{__('adminMessage.no')}}</option>
                                                </select>
                                                               @if($errors->has('is_attribute'))
                                                               <div class="invalid-feedback">{{ $errors->first('is_attribute') }}</div>
                                                               @endif
                                                </div>
                                                
                                                <div class="col-lg-2"  id="box-quantity" @if(!empty(old('is_attribute'))) style="display:none;" @else  style="display:block;" @endif>
                                                <label>{{__('adminMessage.quantity')}}</label>
                                                <input type="number" class="form-control @if($errors->has('squantity')) is-invalid @endif" name="squantity"
                                                               value="{{old('squantity')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_quantity')}}" />
                                                               @if($errors->has('squantity'))
                                                               <div class="invalid-feedback">{{ $errors->first('squantity') }}</div>
                                                               @endif
                                                </div>
                                                
                                                <div class="col-lg-4">
                                                 <label>{{__('adminMessage.youtube_url')}}</label>
                                                <input type="text" class="form-control @if($errors->has('youtube_url')) is-invalid @endif" name="youtube_url" value="{{old('youtube_url')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_youtube_url')}}" />
                                                               @if($errors->has('youtube_url'))
                                                               <div class="invalid-feedback">{{ $errors->first('youtube_url') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-3">
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
                                              
                                              <div class="form-group row">
                                                 <div class="col-lg-3">
                                                <label>{{__('adminMessage.brand')}}</label>
                                                <select class="form-control" name="brand">
                                                <option value="0">{{__('adminMessage.none')}}</option>
                                                @if(!empty($brandLists) && count($brandLists)>0) 
                                                @foreach($brandLists as $brandList)
                                                <option value="{{$brandList->id}}" @if(old('brand')==$brandList->id) selected @endif>{{$brandList->title_en}}</option>
                                                @endforeach
                                                @endif
                                                </select>
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.sectionhome')}}</label>
                                                <select class="form-control" name="homesection">
                                                <option value="0" @if(old('homesection')==0) selected @endif>{{__('adminMessage.none')}}</option>
                                                @if(!empty($listSections))
                                                @foreach($listSections as $listSection)
                                                <option value="{{$listSection->id}}" @if(old('homesection')==$listSection->id) selected @endif>{{$listSection->title_en}}</option>
                                                @endforeach
                                                @endif
                                                
                                                </select>
                                                </div>
                                                <div class="col-lg-3">
                                                <label>{{__('adminMessage.status')}}</label>
                                                <select class="form-control" name="prodstatus">
                                                <option value="0" @if(old('is_active')==0) selected @endif>{{__('adminMessage.notpublished')}}</option>
                                                <option value="1" @if(old('is_active')==1) selected @endif>{{__('adminMessage.published')}}</option>
                                                <option value="2" @if(old('is_active')==2) selected @endif>{{__('adminMessage.publishedpreorder')}}</option>
                                                </select>
                                                </div>
                                                @if(!empty($manufacturerLists) && count($manufacturerLists)>0) 
                                                 <div class="col-lg-3">
                                                <label>{{__('adminMessage.manufacturer')}}</label>
                                                <select class="form-control" name="manufacturer">
                                                <option value="0" selected>{{__('adminMessage.none')}}</option>
                                                @foreach($manufacturerLists as $manufacturerList)
                                                <option value="{{$manufacturerList->id}}" @if(old('manufacturer')==$manufacturerList->id) selected @endif>{{$manufacturerList->title_en}}</option>
                                                @endforeach
                                                </select>
                                                </div>
                                                @endif
                                                
                                              </div>
                                              
                                              <div class="form-group">
											<h5>{{trans('adminMessage.defaultimage')}}</h5>
                                            </div>
                                              
                                              <div class="form-group row">
                                                <div class="col-lg-6">
                                                        <label>{{trans('theme')['theme'.$theme]['product_image']}}*</label>
                                                        <div class="custom-file @if($errors->has('image')) is-invalid @endif">
														<input required type="file" class="custom-file-input @if($errors->has('image')) is-invalid @endif"  id="image" name="image">
														<label class="custom-file-label" for="image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('image'))
                                                               <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                               @endif
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                        <label>{{trans('theme')['theme'.$theme]['product_rollover_image']}}</label>
                                                        <div class="custom-file @if($errors->has('rollover_image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('rollover_image')) is-invalid @endif"  id="rollover_image" name="rollover_image">
														<label class="custom-file-label" for="rollover_image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('rollover_image'))
                                                               <div class="invalid-feedback">{{ $errors->first('rollover_image') }}</div>
                                                               @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
											<h5>{{trans('adminMessage.gallery')}}</h5>
                                            </div>
                                            <div class="form-group">
                                              <div id="kt_repeater_gallery_1">
												<div class="form-group form-group-last row">
													<div data-repeater-list="attach" class="col-lg-12">
														<div data-repeater-item class="form-group row align-items-center repeatbox">
															<div class="col-md-3">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
																	<input type="text" class="form-control" name="atitle_en" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}" />
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
															<div class="col-md-3">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
																		<input type="text" class="form-control" name="atitle_ar" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}" />
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
                                                            
															<div class="col-md-5">
																<div>
														       <input  type="file" class="form-control  @if($errors->has('attach_file')) is-invalid @endif"   name="attach_file" id="attach_file">
                                                               @if($errors->has('attach_file'))
                                                               <div class="invalid-feedback">{{ $errors->first('attach_file') }}</div>
                                                               @endif
													            </div>
															</div>
															<div class="col-md-1">
																<a href="javascript:;" title="{{__('adminMessage.delete')}}" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
																	<i class="la la-trash-o"></i>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="form-group form-group-last row">
													<div class="col-lg-4">
														<a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
															<i class="la la-plus"></i> {{__('adminMessage.add')}}
														</a>
                                                        
													</div>
                                                    
												</div>
											</div>
                                            </div>
                                            
                                            <div class="form-group">
											<h5>{{trans('adminMessage.categories')}}</h5>
                                            </div>
                                            <div class="form-group"> 
                                              <div id="kt_repeater_1">
												<div class="form-group form-group-last row">
													<div data-repeater-list="attach" class="col-lg-12">
														<div data-repeater-item class="form-group row align-items-center repeatbox">
															<div class="col-md-11">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
                                                                        <select name="category" class="form-control">
                                                                        <option value="0" selected>{{__('adminMessage.chooseCategory')}}</option>
                                                                        @foreach($categoryLists as $category)
                                                                        <option style="font-size:20px;"  value="{{$category->id}}">{{$category->name_en}}</option>
                                                                        @if(count($category->childs))
                                                                        @include('gwc.product.dropdown_childs',['ParentName'=>$category->name_en,'childs' => $category->childs,'level'=>0])
                                                                        @endif
                                                                        @endforeach
                                                                        </select>							
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
															
															<div class="col-md-1">
																<a href="javascript:;" title="{{__('adminMessage.delete')}}" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
																	<i class="la la-trash-o"></i>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="form-group form-group-last row">
													<div class="col-lg-4">
														<a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
															<i class="la la-plus"></i> {{__('adminMessage.add')}}
														</a>
													</div>
                                                    
												</div>
											  </div>
                                            </div> 
                                              
                                             <!--tags -->
                                             <div class="form-group"><h5>{{__('adminMessage.tags')}}</h5></div> 
                                               <div class="form-group row"><div class="col-lg-12 alert" style="background-color:#DDEEFF;">{!!trans('adminMessage.tags_note')!!}</div></div> 
                                                <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.tags_en')}}</label>
                                                <textarea name="tags_en" autofocus class=" @if($errors->has('tags_en')) is-invalid @endif" id="tags_en" placeholder="{{__('adminMessage.entertags_en')}}" autocomplete="off">{{old('tags_en')}}</textarea>
                                                @if($errors->has('tags_en'))
                                                <div class="invalid-feedback">{{ $errors->first('tags_en') }}</div>
                                                 @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.tags_ar')}}</label>
                                                <textarea name="tags_ar" id="tags_ar" class=" @if($errors->has('tags_ar')) is-invalid @endif" placeholder="{{__('adminMessage.entertags_ar')}}" autocomplete="off">{{old('tags_ar')}}</textarea>
                                                 @if($errors->has('tags_ar'))
                                                 <div class="invalid-feedback">{{ $errors->first('tags_ar') }}</div>
                                                 @endif
                                                </div>
                                                </div> 
                                                
                                                <div class="form-group"><h5>{{__('adminMessage.seo')}}</h5></div>
                                            <div class="form-group row"><div class="col-lg-12 alert" style="background-color:#DDEEFF;">{!!trans('adminMessage.seo_key_note')!!}</div></div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.seokeywords_en')}}</label>
                                                <textarea name="seokeywords_en" class="form-control @if($errors->has('seokeywords_en')) is-invalid @endif" placeholder="{{__('adminMessage.enterseokeywords_en')}}" autocomplete="off">{{old('seokeywords_en')}}</textarea>
                                                @if($errors->has('seokeywords_en'))
                                                <div class="invalid-feedback">{{ $errors->first('seokeywords_en') }}</div>
                                                 @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.seokeywords_ar')}}</label>
                                                <textarea name="seokeywords_ar" class="form-control @if($errors->has('seokeywords_ar')) is-invalid @endif" placeholder="{{__('adminMessage.enterseokeywords_ar')}}" autocomplete="off">{{old('seokeywords_ar')}}</textarea>
                                                 @if($errors->has('seokeywords_ar'))
                                                 <div class="invalid-feedback">{{ $errors->first('seokeywords_ar') }}</div>
                                                 @endif
                                                </div>
                                                </div>
                                             <div class="form-group row"><div class="col-lg-12 alert" style="background-color:#DDEEFF;">{!!trans('adminMessage.seo_details_note')!!}</div></div>   
                                                <div class="form-group row">
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.seodescription_en')}}</label>
                                                <textarea name="seodescription_en" class="form-control @if($errors->has('seodescription_en')) is-invalid @endif" placeholder="{{__('adminMessage.enterseodescription_en')}}" autocomplete="off">{{old('seodescription_en')}}</textarea>
                                                @if($errors->has('seodescription_en'))
                                                <div class="invalid-feedback">{{ $errors->first('seodescription_en') }}</div>
                                                 @endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.seodescription_ar')}}</label>
                                                <textarea name="seodescription_ar" class="form-control @if($errors->has('seodescription_ar')) is-invalid @endif" placeholder="{{__('adminMessage.enterseodescription_ar')}}" autocomplete="off">{{old('seodescription_ar')}}</textarea>
                                                 @if($errors->has('seodescription_ar'))
                                                 <div class="invalid-feedback">{{ $errors->first('seodescription_ar') }}</div>
                                                 @endif
                                                </div>
                                                </div>
                                              <div class="form-group row">
                                                <div class="col-lg-12">
                                                <label>{{__('adminMessage.slug')}}</label>
                                                <input type="text" class="form-control @if($errors->has('slug')) is-invalid @endif" name="slug"
                                                               value="{{old('slug')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_slug')}}" />
                                                               @if($errors->has('slug'))
                                                               <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                                                               @endif
                                                </div>
                                               </div>
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/product')}}'"  class="btn btn-secondary">{{__('adminMessage.cancel')}}</button>
                                                    
                                                    
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
        
       <script>
        jQuery(document).ready(function() {
		
		$(document).on("keyup","#cost_price",function(){
		var cost_price   = $(this).val();
		var retail_price = $("#retail_price").val();
		var profit = parseFloat(retail_price) - parseFloat(cost_price);
		$("#profitprice").html('&nbsp;(Profit : '+profit+' {{\App\Currency::default()}})');
		});
		
		<!--tags -->
		var tags_en = document.getElementById('tags_en');
		@php
		if(!empty($tags_en_js)){
		$tags_en_js_k = json_encode($tags_en_js,true);
		}else{
		$tags_en_js_k = "[]";
		}
		if(!empty($tags_ar_js)){
		$tags_ar_js_k = json_encode($tags_ar_js,true);
		}else{
		$tags_ar_js_k = "[]";
		}
		@endphp
		tagify_en = new Tagify(tags_en,{
                whitelist: {!!$tags_en_js_k!!},
                blacklist: []
            })
		var tags_ar = document.getElementById('tags_ar');
		tagify_ar = new Tagify(tags_ar,{
                whitelist: {!!$tags_ar_js_k!!},
                blacklist: []
            })
		<!--end tags -->
		
		$("#is_attribute").change(function(){
		var attribute_status = $(this).val();
		if(attribute_status==1){
		$("#box-options").show();
		$("#box-options-button").show();
		$("#box-quantity").hide();
		}else{
		$("#box-options").hide();
		$("#box-options-button").hide();
		$("#box-display-options").hide();
		$("#box-quantity").show();
		}
		});
		
		$('#kt_repeater_1').repeater({
		initEmpty: false,
		defaultValues:{
		'category': '0'
		},
		defaultName: {
		'text-input': 'foo',
		},
		show: function () {
		$(this).slideDown();
		},
		hide: function (deleteElement) {  
		  $(this).slideUp(deleteElement);   
		 }   
	    });
		
		$('#kt_repeater_gallery_1').repeater({
		initEmpty: false,
		defaultName: {
		'text-input': 'foo',
		},
		show: function () {
		$(this).slideDown();
		},
		hide: function (deleteElement) {  
		  $(this).slideUp(deleteElement);   
		 }   
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