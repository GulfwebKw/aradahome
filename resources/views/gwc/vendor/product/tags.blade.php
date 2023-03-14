@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.tags')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.tags')}}</h3>
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
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
                                                <th width="100">{{__('adminMessage.tag_name_en')}}</th>
                                                <th width="50">{{__('adminMessage.items')}}</th>
                                                <th width="50">{{__('adminMessage.image')}}</th>
                                                <th width="100">(30 X 30)</th>
                                                <th width="10">{{__('adminMessage.actions')}}</th>
											</tr>
										</thead>
										<tbody>
                                        @if(count($tagslists_en)>0)
                                        @php $p=1; $tagImage='';$countItems='0'; @endphp
                                        @foreach($tagslists_en as $key=>$tag)
                                        @if(!empty($tag))
                                        @php
                                        $tagImage   = App\Http\Controllers\AdminProductController::getTagsDetails($tag);
                                        $countItems = App\Http\Controllers\AdminProductController::getItemCountsByTag($tag);
                                        @endphp
											<tr class="search-body">
												<td>{{$p}}</td>
                                                
                                                <td>@if(!empty($tag)){{$tag}}@endif</td>
                                                <td><a href="{{url('vendor/product?tag='.$tag)}}">{{$countItems}}</a></td>
                                                <td>
                                                @if(!empty($tagImage->image))<img src="{{url('uploads/product/'.$tagImage->image)}}" width="30">@endif</td>
                                                <td>
                                                <form method="post" name="tagsName" id="tagsName{{$p}}" enctype="multipart/form-data" action="{{route('tagsPost')}}">
                                                <input type="hidden" name="tag_name_en" value="@if(!empty($tag)){{$tag}}@endif">
                                                <input type="hidden" name="tag_name_ar" value="">
                                         
                                                
                                                <input class="form-control uploadsImage" type="file" name="tag_image" id="{{$p}}">
                                                </form>
                                                </td>
                                                <td align="center"><a href="{{url('vendor/product-delete-tags/'.$tag)}}"><i class="kt-nav__link-icon flaticon2-trash"></i></a></td>
												
											</tr>
                                        
                                        @php $p++; @endphp
                                        @endif
                                        @endforeach   
                                        
                                        @else
                                        <tr><td colspan="9" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
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
        
  
    <script type="text/javascript">
	$(document).ready(function(){
	 $('.uploadsImage').change(function(){
	 var id = $(this).attr('id');
	 $("#tagsName"+id).submit();
	 });
	});
	</script>
	</body>
	<!-- end::Body -->
</html>