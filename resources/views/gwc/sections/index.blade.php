@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.sections')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.sections')}}</h3>
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.sectionlisting')}}</a>
                                        
									</div>
								</div>
								<div class="kt-subheader__toolbar">
									<form class="kt-margin-l-20" id="kt_subheader_search_form">
											<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
												<input type="text" class="form-control" placeholder="{{__('adminMessage.searchhere')}}" id="searchCat" name="searchCat">
												<span class="kt-input-icon__icon kt-input-icon__icon--right">
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
												</span>
											</div>
										</form>
									<div class="btn-group">
                                        @if(auth()->guard('admin')->user()->can('sections-create'))
										<a href="javascript:;" data-toggle="modal" data-target="#kt_modal_contact_1" class="btn btn-brand btn-bold"><i class="la la-plus"></i>&nbsp;{{__('adminMessage.createnew')}}</a>
                                        @endif
										
										
									</div>
								</div>
							</div>
						</div>

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                            @include('gwc.includes.alert') 
							<div class="kt-portlet kt-portlet--mobile">
								<div class="kt-portlet__head kt-portlet__head--lg">
									<div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
										<h3 class="kt-portlet__head-title">
											{{__('adminMessage.sectionlisting')}}
										</h3>
									</div>
								</div>
                      
								<div class="kt-portlet__body">
                                @if(auth()->guard('admin')->user()->can('sections-list'))
									<!--begin: Datatable -->
									<table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
										<thead>
											<tr>
												<th width="10">#</th>
												<th>{{__('adminMessage.title')}}</th>
												<th>{{__('adminMessage.link')}}</th>
                                                <th width="120">{{__('adminMessage.displayorder')}}</th>
												<th width="10">{{__('adminMessage.status')}}</th>
												<th width="100">{{__('adminMessage.type')}}</th>
												<th width="100">{{__('adminMessage.items')}}</th>
												<th width="100">{{__('adminMessage.banner')}}</th>
												<th width="10">{{__('adminMessage.actions')}}</th>
											</tr>
										</thead>
										<tbody>
                                        @if(count($SectionLists))
                                        @php $p=1; @endphp
                                        @foreach($SectionLists as $key=>$SectionList)
                                        @php
                                        $countItems = App\Http\Controllers\AdminProductController::countItemBySections($SectionList->id);
                                        @endphp
											<tr class="search-body">
												<td>{{$SectionLists->firstItem() + $key}}</td>
												<td>
                                                @if($SectionList->section_type=='regular')  
                                                <a href="javascript:;" class="filterBySectionsDirect" id="{{$SectionList->id}}">{!! $SectionList->title_en !!}</a>
                                                @else
                                                {!! $SectionList->title_en !!}
                                                @endif
                                                <br>
                                                @if($SectionList->section_type=='regular')
                                                <a href="javascript:;" class="filterBySectionsDirect" id="{{$SectionList->id}}">{!! $SectionList->title_ar !!}</a>
                                                @else
                                                {!! $SectionList->title_ar !!}
                                                @endif
                                                </td>
												<td>
                                                {!! $SectionList->link??'---' !!}
                                                </td>
												<td><input style="border:1px #CCCCCC solid; width:100px;padding:10px;" type="number" class="change_asorting" alt="sections" id="{{$SectionList->id}}" value="{{$SectionList->display_order}}"></td>
												<td>
                                                <span class="kt-switch"><label><input value="{{$SectionList->id}}" {{!empty($SectionList->is_active)?'checked':''}} type="checkbox"  id="sections" class="change_status"><span></span></label></span>
                                                </td>
												<td>{!! $SectionList->section_type !!}</td>
												<td>
                                                @if($SectionList->section_type=='regular')
                                                <a href="javascript:;" class="filterBySectionsDirect" id="{{$SectionList->id}}">{{__('adminMessage.items')}}({{$countItems}})</a>
                                                @endif</td>
												<td>
													<img src="/uploads/section/thumb/{{ @$SectionList->banner ?? 'no-image.png' }}" width="40">
												</td>
                                                <td class="kt-datatable__cell" align="center">
                                                @if(auth()->guard('admin')->user()->can('sections-delete'))
                                                 <a title="{{__('adminMessage.edit')}}" href="javascript:;" data-toggle="modal" data-target="#kt_modal_editsection_{{$SectionList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-edit"></i><span class="kt-nav__link-text"></span></a>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('sections-delete')  && $SectionList->section_type=='regular')
                                                 <a title="{{__('adminMessage.delete')}}" href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$SectionList->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text"></span></a>
                                                 @endif
                                                 
                                                 <!--Delete modal -->
                       <div class="modal fade" id="kt_modal_{{$SectionList->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body" align="center">
											<h6 class="modal-title">{!!__('adminMessage.alertDeleteMessage')!!}</h6>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
											<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('gwc/sections/delete/'.$SectionList->id)}}'">{{__('adminMessage.yes')}}</button>
										</div>
									</div>
								</div>
							</div>
                            <!--edit -->
                            <div class="modal fade" id="kt_modal_editsection_{{$SectionList->id}}" tabindex="-1" role="dialog" aria-labelledby="kt_modal_editsection_{{$SectionList->id}}" aria-hidden="true" align="left">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">{{__('adminMessage.manageSection')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<p><!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('sections-create'))
                    
                         <form name="tFrm"  id="form_validation_{{$SectionList->id}}"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('saveEditSection',$SectionList->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <input type="hidden" name="id" value="{{$SectionList->id}}">
											<div class="kt-portlet__body">
										<!--parent categories dropdown -->	
                                           <div class="form-group row">
                                                <div class="col-lg-12">
                                                <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" @if($SectionList->is_active) checked="checked" @endif name="is_active"  id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<label class="col-3 col-form-label">{{__('adminMessage.displayorder')}}</label>
													<div class="col-3">
														<input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"  value="{{$SectionList->display_order?$SectionList->display_order:$lastOrder}}" autocomplete="off" />
                                                               @if($errors->has('display_order'))
                                                               <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                               @endif
													</div>
													<label class="col-3 col-form-label">Slide show</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" @if($SectionList->slideShow) checked="checked" @endif name="slideShow"  id="slideShow" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<div class="col-6">
														<div class="form-group">
															<label>Item Ordering</label>
															<select name="ordering" class="form-control @if($errors->has('ordering')) is-invalid @endif">
																<option value="updated_at" @if(old('ordering',$SectionList->ordering) == "updated_at") selected @endif>Update</option>
																<option value="created_at" @if(old('ordering',$SectionList->ordering) == "created_at") selected @endif>Create</option>
																<option value="display_order" @if(old('ordering',$SectionList->ordering) == "display_order") selected @endif>Product order</option>
																<option value="random" @if(old('ordering',$SectionList->ordering) == "random") selected @endif>Random</option>
															</select>
															@if($errors->has('ordering'))
																<div class="invalid-feedback">{{ $errors->first('ordering') }}</div>
															@endif
														</div>
													</div>
												   </div>
                                                </div>
                                            </div>
                                            													
                                       <!--categories name -->         
                                                <div class="form-group">
                                            
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="@if($SectionList->title_en){{$SectionList->title_en}}@else{{old('title_en')}}@endif" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}*" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="form-group">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="@if($SectionList->title_ar){{$SectionList->title_ar}}@else{{old('title_ar')}}@endif" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}*" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                
                                                </div>
                                                
                                                <div class="form-group">
                                                <label>{{__('adminMessage.link')}}</label>
                                                <input type="text" class="form-control @if($errors->has('link')) is-invalid @endif" name="link"
                                                               value="@if($SectionList->link){{$SectionList->link}}@else{{old('link')}}@endif" autocomplete="off" placeholder="{{__('adminMessage.enter_link')}}" />
                                                               @if($errors->has('link'))
                                                               <div class="invalid-feedback">{{ $errors->first('link') }}</div>
                                                               @endif
                                                
                                                </div>
                                                <div class="form-group">
                                                <label>{{__('adminMessage.banner')}}(1200px X 400px)</label>
												@if (@$SectionList->banner) 
													<div class="d-flex flex-direction-column w-50">
														<img src="/uploads/section/thumb/{{ @$SectionList->banner }}" >
														<button class="btn btn-danger" type="button" onclick="document.querySelector('[name=banner]').style.display= 'block';this.parentElement.remove();">{{ __('adminMessage.remove') }}</button>
														<input type="hidden" name="default_banner" class="form-control" value="{{ @$SectionList->banner }}">
													</div>
												@endif
                                                <input type="file" class="form-control-file @if($errors->has('banner')) is-invalid @endif" name="banner" @if (@$SectionList->banner) style="display: none" @endif
                                                               value="@if($SectionList->banner){{$SectionList->banner}}@else{{old('banner')}}@endif" autocomplete="off"  />
                                                               @if($errors->has('banner'))
                                                               <div class="invalid-feedback">{{ $errors->first('banner') }}</div>
                                                               @endif
                                                
                                                </div>
                                             
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:;" data-dismiss="modal" aria-label="Close"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
												</div>
											</div>
										</form>
                                  
                            @else
                            <div class="alert alert-light alert-warning" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
								<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
							</div>
                            @endif
										<!--end::Form--></p>
										</div>
										
									</div>
								</div>
							</div>
                            <!--end-->
                                                </td>
											</tr>
                                        
                                        @php $p++; @endphp
                                        @endforeach   
                                        <tr><td colspan="8" class="text-center">{{ $SectionLists->links() }}</td></tr> 
                                        @else
                                        <tr><td colspan="8" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
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
		
        
        <!--begin::Modal-->
							<div class="modal fade" id="kt_modal_contact_1" tabindex="-1" role="dialog" aria-labelledby="kt_modal_contact_1" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">{{__('adminMessage.manageSection')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<p><!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('sections-create'))
                    
                         <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('saveSection')}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
										<!--parent categories dropdown -->	
                                           <div class="form-group row">
                                                <div class="col-lg-12">
                                                <div class="form-group row">
													<label class="col-3 col-form-label">{{__('adminMessage.isactive')}}</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" checked="checked" name="is_active"  id="is_active" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<label class="col-3 col-form-label">{{__('adminMessage.displayorder')}}</label>
													<div class="col-3">
														<input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"  value="{{old('display_order')?old('display_order'):$lastOrder}}" autocomplete="off" />
                                                               @if($errors->has('display_order'))
                                                               <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                               @endif
													</div>
													<label class="col-3 col-form-label">Slide show</label>
													<div class="col-3">
														<span class="kt-switch">
															<label>
																<input type="checkbox" checked="checked" name="slideShow"  id="slideShow" value="1"/>
																<span></span>
															</label>
														</span>
													</div>
													<div class="col-6">
														<div class="form-group">
															<label>Item Ordering</label>
															<select name="ordering" class="form-control @if($errors->has('ordering')) is-invalid @endif">
																<option value="updated_at">Update</option>
																<option value="created_at">Create</option>
																<option value="display_order" selected >Product order</option>
																<option value="random">Random</option>
															</select>
															@if($errors->has('ordering'))
																<div class="invalid-feedback">{{ $errors->first('ordering') }}</div>
															@endif
														</div>
													</div>
												   </div>
                                                </div>
                                            </div>
                                            													
                                       <!--categories name -->         
                                                <div class="form-group">
                                            
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{old('title_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}*" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="form-group">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{old('title_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}*" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                
                                                </div>
                                                <div class="form-group">
                                                <label>{{__('adminMessage.link')}}</label>
                                                <input type="text" class="form-control @if($errors->has('link')) is-invalid @endif" name="link"
                                                               value="{{old('link')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_link')}}" />
                                                               @if($errors->has('link'))
                                                               <div class="invalid-feedback">{{ $errors->first('link') }}</div>
                                                               @endif
                                                
                                                </div>
												<div class="form-group">
													<label>{{__('adminMessage.banner')}}(1200px X 400px)</label>
													<input type="file" class="form-control-file @if($errors->has('banner')) is-invalid @endif" name="banner"
																   value="@if($SectionList->banner){{$SectionList->banner}}@else{{old('banner')}}@endif" autocomplete="off"  />
																   @if($errors->has('banner'))
																   <div class="invalid-feedback">{{ $errors->first('banner') }}</div>
																   @endif
													
												</div>
                                             
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:;" data-dismiss="modal" aria-label="Close"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
												</div>
											</div>
										</form>
                                  
                            @else
                            <div class="alert alert-light alert-warning" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
								<div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
							</div>
                            @endif
										<!--end::Form--></p>
										</div>
										
									</div>
								</div>
							</div>

							<!--end::Modal-->

		<!-- end::Quick Panel -->

		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>

		<!-- end::Scrolltop -->

		<!-- js files -->
		@include('gwc.js.user')
        
  
    <script type="text/javascript">
	$(document).ready(function(){
	 $('#searchCat').keyup(function(){
	  // Search text
	  var text = $(this).val();
	  // Hide all content class element
	  $('.search-body').hide();
	  // Search 
	   $('.search-body').each(function(){
	 
		if($(this).text().indexOf(""+text+"") != -1 ){
		 $(this).closest('.search-body').show();
		 
		}
	  });
	 
	 });
	});
	</script>
	</body>
	<!-- end::Body -->
</html>
