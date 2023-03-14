@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.categories')}}</title>
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
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.categorylisting')}}</a>
                                        
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
                                        @if(auth()->guard('admin')->user()->can('bundle-category-create'))
										<a href="{{url('gwc/bundles/category/create')}}" class="btn btn-brand btn-bold"><i class="la la-plus"></i>&nbsp;{{__('adminMessage.createnew')}}</a>
                                        @endif
										<button type="button" class="btn btn-brand btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										</button>
										<div class="dropdown-menu dropdown-menu-right">
											<ul class="kt-nav">
												<li class="kt-nav__item">
													<a href="{{url('gwc/bundles/category/csv')}}" class="kt-nav__link">
														<i class="kt-nav__link-icon flaticon-file-2"></i>
														<span class="kt-nav__link-text">{{__('adminMessage.exportascsv')}}</span>
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
								<div class="kt-portlet__head kt-portlet__head--lg">
									<div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
										<h3 class="kt-portlet__head-title">
											{{__('adminMessage.categorylisting')}}
										</h3>
									</div>
								</div>

								<div class="kt-portlet__body">
                                @if(auth()->guard('admin')->user()->can('bundle-category-list'))

                                @if(count($categories))
                                <div class="kt-list-timeline">
												<div class="kt-list-timeline__items">
                                                   <div class="kt-list-timeline__item">
														<span class="kt-list-timeline__badge"></span>
														<span class="kt-list-timeline__text">{{__('adminMessage.categoryname')}}</span>

                                                        <span class="kt-list-timeline__time">{{__('adminMessage.image')}}</span>
                                                        
                                                        <span class="kt-list-timeline__time text-center">{{__('adminMessage.active')}}</span>
                                                        <span class="kt-list-timeline__time">{{__('adminMessage.sorting')}}</span>
														<span class="kt-list-timeline__time">{{__('adminMessage.actions')}}</span>
													</div>
                                                @foreach($categories as $category)
													<div class="kt-list-timeline__item">
														<span class="kt-list-timeline__badge"></span>
														<span class="kt-list-timeline__text"><h5><a href="{{url('gwc/product?bundle_category='.$category->id)}}">{{$category->name_en}}({{count($category->allproducts)}})</a></h5></span>

                                                        <span class="kt-list-timeline__time">
                                                        @if($category->image)
                                                        <img src="{!! url('uploads/bundle_category/thumb/'.$category->image) !!}" width="35">
                                                        @endif
                                                        </span>
                                                        <span class="kt-list-timeline__time">
                                                        <span class="kt-switch"><label><input value="{{$category->id}}" {{!empty($category->is_active)?'checked':''}} type="checkbox"  id="bundles/category" class="change_status"><span></span></label></span>
                                                        </span>
                                                        <span class="kt-list-timeline__time">{{$category->display_order}}</span>
														<span class="kt-list-timeline__time">
                                                         <span style="overflow: visible; position: relative; width: 80px;">
                                                 <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-right">
                                                 <ul class="kt-nav">
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-edit'))
                                                 <li class="kt-nav__item"><a href="{{url('gwc/bundles/category/'.$category->id.'/edit')}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-contract"></i><span class="kt-nav__link-text">Edit</span></a></li>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-view'))
                                                 <li class="kt-nav__item"><a href="{{url('gwc/bundles/category/'.$category->id.'/view')}}" class="kt-nav__link"><i class="kt-nav__link-icon la la-eye"></i><span class="kt-nav__link-text">{{__('adminMessage.view')}}</span></a></li>
                                                 @endif
                                                 @if(auth()->guard('admin')->user()->can('bundle-category-delete'))
                                                 <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$category->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">Delete</span></a></li>
                                                 @endif
                                                 </ul>
                                                 </div>
                                                 </div>
                                                 </span>

                                                 <!--Delete modal -->
 <div class="modal fade" id="kt_modal_{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											</button>
										</div>
										<div class="modal-body">
											<h6 class="modal-title text-left">{!!__('adminMessage.alertDeleteMessage')!!}</h6>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
											<button type="button" class="btn btn-danger"  onClick="Javascript:window.location.href='{{url('gwc/bundles/category/delete/'.$category->id)}}'">{{__('adminMessage.yes')}}</button>
										</div>
									</div>
								</div>
							</div>
                                                        </span>
													</div>
                                                    <div class="kt-separator kt-separator--space-sm kt-separator--border-dashed"></div>
                                                    @if(count($category->childs))
                                                    @include('gwc.bundle.category.childs',['childs' => $category->childs,'level'=>0])
                                                    @endif
                                                 @endforeach   
												</div>
											</div>
                                        @else
                                        <div class="text-center">No Record(s) Found</div>    	
                                        @endif    

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
		<div id="kt_quick_panel" class="kt-quick-panel">
			<a href="#" class="kt-quick-panel__close" id="kt_quick_panel_close_btn"><i class="flaticon2-delete"></i></a>
			<div class="kt-quick-panel__nav">
				<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_tab_notifications" role="tab">Notifications</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#kt_quick_panel_tab_logs" role="tab">Audit Logs</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#kt_quick_panel_tab_settings" role="tab">Settings</a>
					</li>
				</ul>
			</div>
			<div class="kt-quick-panel__content">
				<div class="tab-content">
					<div class="tab-pane fade show kt-scroll active" id="kt_quick_panel_tab_notifications" role="tabpanel">
						<div class="kt-notification">
							<a href="#" class="kt-notification__item">
								<div class="kt-notification__item-icon">
									<i class="flaticon2-line-chart kt-font-success"></i>
								</div>
								<div class="kt-notification__item-details">
									<div class="kt-notification__item-title">
										New order has been received
									</div>
									<div class="kt-notification__item-time">
										2 hrs ago
									</div>
								</div>
							</a>
							
						</div>
					</div>
					<div class="tab-pane fade kt-scroll" id="kt_quick_panel_tab_logs" role="tabpanel">
						<div class="kt-notification-v2">
							<a href="#" class="kt-notification-v2__item">
								<div class="kt-notification-v2__item-icon">
									<i class="flaticon-bell kt-font-brand"></i>
								</div>
								<div class="kt-notification-v2__itek-wrapper">
									<div class="kt-notification-v2__item-title">
										5 new user generated report
									</div>
									<div class="kt-notification-v2__item-desc">
										Reports based on sales
									</div>
								</div>
							</a>
							
						</div>
					</div>
					
				</div>
			</div>
		</div>

		<!-- end::Quick Panel -->

		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>

		<!-- end::Scrolltop -->

		<!-- js files -->
		@include('gwc.js.user')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
       
  
    <script type="text/javascript">
	$(document).ready(function(){
	 $('#searchCat').keyup(function(){
	  // Search text
	  var text = $(this).val();
	  // Hide all content class element
	  $('.kt-list-timeline__item').hide();
	  $('.kt-separator').hide();
	  // Search and show
	  // Search 
	   $('.kt-list-timeline__item').each(function(){
	 
		if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
		 $(this).closest('.kt-list-timeline__item').show();
		 
		}
	  });
	 
	 });
	});
	</script>
	</body>
	<!-- end::Body -->
</html>