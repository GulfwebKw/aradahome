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
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.product')}}</title>
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
                $Categories = App\Http\Controllers\AdminProductController::getCategories();
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
									<h3 class="kt-subheader__title">{{__('adminMessage.productlisting')}}</h3>
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__breadcrumbs">
 @if(!empty($sectionsInfo->title_en)) for <a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{$sectionsInfo->title_en}}'</a> @endif
 @if(!empty(Cookie::get('item_status')) && Cookie::get('item_status')==1)><a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{trans('adminMessage.published')}}</a>@endif
 @if(!empty(Cookie::get('item_status')) && Cookie::get('item_status')==2)><a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{trans('adminMessage.publishedpreorder')}}</a>@endif
 @if(Cookie::get('item_status')==-1) > <a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{trans('adminMessage.notpublished')}}</a>@endif
									</div>
								</div>
								<div class="kt-subheader__toolbar">
                                
                                    <div class="btn-group">
                                    <select name="brand_id" id="brand_id" class="form-control" >  
                                                <option value="0">All Brands</option>  						
                                                @if(!empty($brandLists) && count($brandLists)>0)
                                                @foreach($brandLists as $brandList)
                                                <option value="{{$brandList->id}}" @if($brandList->id==Request()->brand_id) selected @endif>{{$brandList->title_en}}</option>  		
                                                @endforeach
                                                @endif   
                                    </select> 
                                    </div>
									<form class="kt-margin-l-20" id="kt_subheader_search_form" action="{{url('vendor/product')}}">
											<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
												<input value="@if(Request()->q){{Request()->q}}@endif" type="text" class="form-control" placeholder="{{__('adminMessage.searchhere')}}" id="searchCat" name="q">
                                                @if(!empty(Request()->category)) 
                                                <input type="hidden" name="category" value="{{Request()->category}}">
                                                @endif
                                                @if(!empty(Request()->tag)) 
                                                <input type="hidden" name="tag" value="{{Request()->tag}}">
                                                @endif
                                                
                                                @if(!empty(Request()->brand_id)) 
                                                <input type="hidden" name="brand_id" value="{{Request()->brand_id}}">
                                                @endif
                                                
                                                
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
                                        
										<a href="{{url('vendor/product/create')}}" class="btn btn-brand btn-info btn-bold" title="{{__('adminMessage.createnew')}}"><i class="la la-plus"></i></a>
                                        <a href="{{url('vendor/product/addQuick')}}" class="btn btn-brand btn-success btn-bold" title="{{__('adminMessage.createnew')}}(Quick)"><i class="la la-plus"></i></a>
                                      
										<button type="button" class="btn btn-brand btn-info btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										</button>
										<div class="dropdown-menu dropdown-menu-right">
											<ul class="kt-nav">
                                                
                                                
                                                @if(!empty($sectionsLists) && count($sectionsLists)>0)
                                                <li class="kt-nav__item"><hr></li>
                                                @foreach($sectionsLists as $sectionsList)
                                                <li class="kt-nav__item">
													<a href="javascript:;" class="kt-nav__link filterBySections" id="{{$sectionsList->id}}">
														<i class="kt-nav__link-icon flaticon-search"></i>
														<span class="kt-nav__link-text">{{$sectionsList->title_en}}</span>
													</a>
												</li>
                                                @endforeach
                                                @endif
                                                <li class="kt-nav__item"><hr></li>
                                                <li class="kt-nav__item">
												<a href="javascript:;" class="kt-nav__link filterByStatus"  id="1">
												<i class="kt-nav__link-icon flaticon-alert"></i>
												<span class="kt-nav__link-text">{{__('adminMessage.published')}}</span>
												</a>
                                                </li>
                                                <li class="kt-nav__item">
												<a href="javascript:;" class="kt-nav__link filterByStatus"  id="2">
												<i class="kt-nav__link-icon flaticon-alert"></i>
												<span class="kt-nav__link-text">{{__('adminMessage.publishedpreorder')}}</span>
												</a>
                                                </li>
                                                 <li class="kt-nav__item">
											     <a href="javascript:;" class="kt-nav__link filterByStatus" id="-1">
												 <i class="kt-nav__link-icon flaticon-alert-off"></i>
												 <span class="kt-nav__link-text">{{__('adminMessage.notpublished')}}</span>
												 </a>
												 </li>
                                                 <li class="kt-nav__item"><hr></li>
                                                 <li class="kt-nav__item">
											     <a href="javascript:;" class="kt-nav__link filterByOutofStock" id="1">
												 <i class="kt-nav__link-icon flaticon-search"></i>
												 <span class="kt-nav__link-text">{{__('adminMessage.outofstock')}}</span>
												 </a>
												 </li>
                                                 <li class="kt-nav__item"><hr></li>
                                                 <li class="kt-nav__item">
											     <a href="javascript:;" class="kt-nav__link btn-warning resetProductFilters" id="1">
												 <i class="kt-nav__link-icon flaticon-delete"></i>
												 <span class="kt-nav__link-text">{{__('adminMessage.resetfilyeration')}}</span>
												 </a>
												 </li>
                                                 <li class="kt-nav__item">
													<a href="{{url('vendor/product/createqrcode')}}" class="kt-nav__link">
														<i class="kt-nav__link-icon flaticon-file-2"></i>
														<span class="kt-nav__link-text">{{__('adminMessage.generateqrcode')}}</span>
													</a>
												</li>
                                                 
                                                 
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
                                                <th width="50">{{__('adminMessage.image')}}</th>
                                                <th width="350">{{__('adminMessage.details')}}</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
                                        @if(count($productLists))
                                        @php $p=1; $quantity=0; @endphp
                                        @foreach($productLists as $key=>$productList)
                                        @php
                                        $retail_price    = App\Http\Controllers\AdminProductController::getPriceFormat($productList->retail_price);
                                        $old_price       = App\Http\Controllers\AdminProductController::getPriceFormat($productList->old_price);
										$quantity        = App\Http\Controllers\AdminProductController::getQuantity($productList->id);
                                       
                                        @endphp
                                        
											<tr class="search-body">
												<td align="center">
                                                {{$productLists->firstItem() + $key}}
                                                 <br><br>
                                                 <span style="overflow: visible; position: relative; width: 80px;">
                                                 <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-left">
                                                 <ul class="kt-nav">
                                                
                                                 <li class="kt-nav__item"><a  href="javascript:;" data-toggle="modal" data-target="#kt_modal_duplicate_{{$productList->id}}"   class="kt-nav__link"><i class="kt-nav__link-icon flaticon-background"></i><span class="kt-nav__link-text">{{__('adminMessage.duplicate')}}</span></a></li>
                                                 
                                                 <li class="kt-nav__item"><a href="{{url('vendor/product/'.$productList->id.'/edit')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-contract"></i><span class="kt-nav__link-text">{{__('adminMessage.edit')}}</span></a></li>
                                                
													 <li class="kt-nav__item"><a href="{{url('vendor/product/'.$productList->id.'/quickedit')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-contract"></i><span class="kt-nav__link-text">{{__('adminMessage.edit')}}(Quick)</span></a></li>
												
                                                 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$productList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">{{__('adminMessage.delete')}}</span></a></li>
                                                 
                                                 </ul>
                                                 </div>
                                                 </div>
                                                 <br><br>
                                                 <a title="{{trans('adminMessage.openlink')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md mt-10" target="_blank" href="{{url('details/'.$productList->id.'/'.$productList->slug)}}"><i class="fa fa-link"></i></a>
                                                 
                                                 </span>
                                                 <!--duplicate item-->
                                                 <div class="modal fade" id="kt_modal_duplicate_{{$productList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelkt_modal_duplicate_{{$productList->id}}" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																</button>
															</div>
															<div class="modal-body">
																<h6 class="modal-title">{!!__('adminMessage.aresuretodplicateitem')!!}</h6>
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
																<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('vendor/product/duplicate/'.$productList->id)}}'">{{__('adminMessage.yes')}}</button>
															</div>
														</div>
													</div>
												</div>
                                                 
                                                 <!--Delete modal -->
												<div class="modal fade" id="kt_modal_{{$productList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelkt_modal_{{$productList->id}}" aria-hidden="true">
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
																<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('vendor/product/delete/'.$productList->id)}}'">{{__('adminMessage.yes')}}</button>
															</div>
														</div>
													</div>
												</div>
												<!--end delete -->
												<!--update qucik quantity-->
												<div class="modal fade" id="kt_modal_quantity_{{$productList->id}}" tabindex="-1" role="dialog" aria-labelledby="kt_modal_quantity_{{$productList->id}}" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title">{{__('adminMessage.editquantity')}}</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																</button>
															</div>
															<div class="modal-body">
															@if(empty($productList->is_attribute))
															<div class="form-group row">
															<div class="col-lg-6">
															<input type="text" class="form-control" name="quantity_{{$productList->id}}" id="quantity_{{$productList->id}}"
																		   value="{{$productList->quantity}}" autocomplete="off" />
															</div>
															<div class="col-lg-6"><input id="{{$productList->id}}" type="button" class="btn btn-brand btn-bold updatesingleqty" value="{{__('adminMessage.save')}}"> <img id="qty_gif_{{$productList->id}}" style="position:absolute;margin-top:-35px;display:none;" src="{{url('assets/images/loader.svg')}}" width="100"></div>
															</div>
															<span id="qtyedit-{{$productList->id}}"></span>
															
															@endif	
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.cancel')}}</button>
															</div>
														</div>
													</div>
												</div>
												<!--update end qty -->
                                                
                                                </td>
                                                <td align="center">
                                                @if($productList->image)
                                                <img style="border:1px #CCCCCC solid;" src="{!! url('uploads/product/thumb/'.$productList->image) !!}" width="40">
                                                @endif
                                                @if($productList->rollover_image)<br><br>
                                                <img style="border:1px #CCCCCC solid;" src="{!! url('uploads/product/thumb/'.$productList->rollover_image) !!}" width="40">
                                                @endif
                                                <br><br>
                                                <a target="_blank" href="{{url('uploads/product/qr/'.$productList->item_code.'.png')}}"><img width="40" src="{{url('uploads/product/qr/'.$productList->item_code.'.png')}}" style="border:1px #CCCCCC solid;"></a>
                                                
                                                <br><br>
                                                Views<br> {{$productList->most_visited_count}}
                                                </td>
                                                
                                                <td>
                                                <table width="100%">
                                                <tr><td width="120">{{__('adminMessage.item_code')}}:</td><td><b>{{$productList->item_code}}</b></td></tr>
                                                @if(!empty($productList->sku_no))
                                                <tr><td width="120">{{__('adminMessage.sku_no')}}:</td><td>{{$productList->sku_no}}</td></tr>
                                                @endif
                                                @if(!empty($productList->caption_en))
												<tr><td width="120">{{__('adminMessage.caption_name')}}:</td><td>{{$productList->caption_en}}</td></tr>
												@endif
                                               
												<tr><td width="120">{{__('adminMessage.quantity')}}:</td><td>
                                                <span id="q-{{$productList->id}}">{{$quantity}}</span>
												@if(empty($productList->is_attribute))
												<a title="Edit Quantity" href="javascript:;" data-toggle="modal" data-target="#kt_modal_quantity_{{$productList->id}}" class="float-right"><i class="kt-nav__link-icon flaticon2-contract"></i></span>
                                                </a>
											
                                                </td></tr>
												@endif
                                               
												<tr><td width="120">{{__('adminMessage.retail_price')}}:</td><td>{{$retail_price}}@if($old_price)<a class="pull-right"><s>{{$old_price}}</s></a>@endif</td></tr>
                                                @if(!empty($productList->cost_price))
                                                <tr><td width="120">{{__('adminMessage.cost_price')}}:</td><td>{{\App\Currency::default()}} {{$productList->cost_price}}</td></tr>
                                                <tr><td width="120" style="color:#990000;">{{__('adminMessage.profit')}}</td><td style="color:#990000;">
													{{\App\Currency::default()}} {{($productList->retail_price-$productList->cost_price)}}</td></tr>
                                                @endif
                                                @if($productList->wholesale_price)
                                                <tr><td width="120">{{__('adminMessage.wholesale_price')}}:</td><td>{{\App\Currency::default()}} {{$productList->wholesale_price}}</td></tr>
                                                @endif
                                                
                                                
                                                @if($productList->weight)
                                                <tr><td width="120">{{__('adminMessage.weight')}}:</td><td>{{$productList->weight}} Kilo</td></tr>
                                                @endif
                                                
                                                @if(!empty($productList->height) && !empty($productList->width) && !empty($productList->depth))
                                                <tr><td width="120">H x W x D</td><td>{{$productList->height}} x {{$productList->width}} x {{$productList->depth}}</td></tr>
                                                @endif
												
                                                @if(!empty($productList->brand->title_en))
												<tr><td width="120">{{__('adminMessage.brand')}}:</td><td>{{$productList->brand->title_en}}</td></tr>
												@endif
                                                <tr><td width="120">{{__('adminMessage.status')}}:</td><td><select style="width:100%;" class="form-control prodstatus" name="prodstatus" id="{{$productList->id}}">
                                                <option value="0" @if($productList->is_active==0) selected @endif>{{__('adminMessage.notpublished')}}</option>
                                                <option value="1" @if($productList->is_active==1) selected @endif>{{__('adminMessage.published')}}</option>
                                                <option value="2" @if($productList->is_active==2) selected @endif>{{__('adminMessage.publishedpreorder')}}</option>
                                                </select></td></tr>
                                                <tr><td width="120">{{__('adminMessage.export')}}:</td><td><span class="kt-switch"><label><input value="{{$productList->id}}" {{!empty($productList->is_export_active)?'checked':''}} type="checkbox"  id="productexport" class="change_status"><span></span></label></span></td></tr>
                                                
                                                
                                                </table>
												
												</td>
                                               
												<td>
                                                <table width="100%">
                                                <tr><td colspan="2">
                                                @if($productList->title_en)<b>{!!$productList->title_en!!}</b>@endif
                                                <br>
                                                @if(!empty($productList->productcat) && count($productList->productcat))
                                                @foreach($productList->productcat as $cattree)
                                                <span class="badge badge-warning">{{$cattree->name_en}}</span>
                                                @endforeach
                                                @endif
                                                </td>
                                                </tr>
                                                @if(!empty($productList->warranty))
                                                  @php
                                                  $warrantyDetails = App\Http\Controllers\webCartController::getWarrantyDetails($productList->warranty);
                                                  @endphp
                                                <tr><td width="150">{{__('adminMessage.warranty')}}:</td><td>{{!empty($warrantyDetails->title_en)?$warrantyDetails->title_en:'--'}}</td></tr>
                                                @endif 
                                                
                                                @if(!empty($productList->tags_en))
                                                <tr><td width="150">{{trans('adminMessage.tags')}}:</td><td>{{$productList->tags_en}}</td></tr>
                                                @endif
                                                
                                                  @if(!empty($productList->seokeywords_en))
                                                  <tr><td width="150">{{__('adminMessage.seokeywords_en')}}:</td><td>{{$productList->seokeywords_en}}</td></tr>
                                                  @endif
                                                  @if(!empty($productList->seodescription_en))
                                                  <tr><td width="150">{{__('adminMessage.seodescription_en')}}:</td><td>{{$productList->seodescription_en}}</td></tr>
                                                  @endif
                                                  @if(!empty($productList->youtube_url))
                                                  <tr><td width="150">{{trans('adminMessage.youtube')}}:</td><td>{{$productList->youtube_url}}</td></tr>
                                                  @endif
                                                  
                                                  @if(!empty($productList->homesection))
                                                  @php
                                                  $homeSectionDetails = App\Http\Controllers\AdminProductController::getSectionDetails($productList->homesection);
                                                  @endphp
                                                  <tr><td width="150">{{trans('adminMessage.sections')}}:</td><td>{{!empty($homeSectionDetails->title_en)?$homeSectionDetails->title_en:'--'}}</td></tr>
                                                  @endif
                                                  
                                                  @if(!empty($productList->manufacturer_id))
                                                  @php
                                                  $manufactDetails = App\Http\Controllers\AdminProductController::getManufactureDetails($productList->manufacturer_id);
                                                  @endphp
                                                  <tr><td width="150">{{trans('adminMessage.manufacturer')}}:</td><td>{{!empty($manufactDetails->title_en)?$manufactDetails->title_en:'--'}}</td></tr>
                                                  @endif
                                                  
                                                  
                                                  
                                                  
                                          
                                          
                                                </table>
                                                </td>
												
                                               
												
												
                                               
											</tr>
                                         
                                        @php $p++; @endphp
                                        @endforeach   
                                        <tr><td colspan="10" class="text-center">{{ $productLists->links() }}</td></tr> 
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

		<!-- begin::Quick Panel -->
		

		<!-- end::Quick Panel -->

		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>

		<!-- end::Scrolltop -->

		<!-- js files -->
		@include('gwc.js.user')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{!! url('admin_assets/assets/plugins/jstree/dist/jstree.min.js') !!}" type="text/javascript"></script>
       
  
    <script type="text/javascript">
	$(document).ready(function(){
	
	 $(document).on("change","#brand_id",function(){
	  var brand_id = $(this).val();
	 window.location.href = "/vendor/product?brand_id="+brand_id+"&tag={{Request()->tag}}&q={{Request()->q}}";
	 });
	 
	});
	</script>
	</body>
	<!-- end::Body -->
</html>