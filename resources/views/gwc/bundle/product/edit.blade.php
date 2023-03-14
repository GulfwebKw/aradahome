@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.editproduct')}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--css files -->
		@include('gwc.css.user')
        <link href="{{url('admin_assets/assets/css/pages/wizard/wizard-1.css')}}" rel="stylesheet" type="text/css" />

		<!--mini color -->
        <link href="{{url('admin_assets/assets/plugins/minicolors/jquery.minicolors.css')}}" rel="stylesheet" type="text/css" />
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
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.bundles.selectCategory')}}</a>
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
						<div class="kt-grid  kt-wizard-v1 kt-wizard-v1--white" id="kt_projects_add" data-ktwizard-state="step-first">
									<div class="kt-grid__item">
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('product-edit'))




                                      <!-- product categories -->
                                       @if(Request::is('gwc/product/*/bundle')==true)
                                       <form name="tFrm"  id="form_validation"  method="post"
                                       class="kt-form" enctype="multipart/form-data" action="{{route('uploadBundleCategory',$editproduct->id)}}">
                                       <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
                                            <div class="form-group row "><div class="col-lg-12 alert" style="background-color:#DDEEFF;">{!!__('adminMessage.categories_notes')!!}</div></div>
                                             <!-- show existing data -->

                                             @if($listCategories)
                                             <div id="kt_repeater_1_exist">
												<div class="form-group form-group-last row">
													<div data-repeater-list="attach_exist" class="col-lg-12">
                                                     @foreach($listCategories as $listCategory)
														<div  class="form-group row align-items-center">
															<div class="col-md-10">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control  col-form-label">


                                        <select name="category-{{$listCategory->id}}" id="category-{{$listCategory->id}}" class="form-control">
                                        <option value="0" selected>{{__('adminMessage.chooseCategory')}}</option>
                                        @foreach($Categories as $category)
                                        <option style="font-size:20px;" value="{{$category->id}}" @if($category->id==$listCategory->category_id) selected @endif >{{$category->name_en}}</option>
                                        @if(count($category->childs))
                                        @include('gwc.product.dropdown_edit_childs',['ParentName'=>$category->name_en,'childs' => $category->childs,'level'=>0,'listCategory'=>$listCategory])
                                        @endif
                                        @endforeach
                                        </select>
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>


															<div class="col-md-2">
																<a href="{{url('gwc/product/'.$editproduct->id.'/deleteBundleCategory/'.$listCategory->id)}}"  title="{{__('adminMessage.delete')}}"  class="btn-sm btn btn-label-danger btn-bold">
																	<i class="la la-trash-o"></i>
																</a>

															</div>
														</div>
                                                        @endforeach
													</div>
												</div>

											</div>
                                            @endif
                                            <!--end showing existing data -->

                                            <div id="kt_repeater_1">
												<div class="form-group form-group-last row" id="kt_repeater_1">
													<div data-repeater-list="attach" class="col-lg-12">
														<div data-repeater-item class="form-group row align-items-center repeatbox">
															<div class="col-md-10">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
										<select name="category" id="category" class="form-control">
                                        <option value="0" selected>{{__('adminMessage.chooseCategory')}}</option>
                                        @foreach($Categories as $category)
                                        <option style="font-size:20px;" value="{{$category->id}}">{{$category->name_en}}</option>
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
                                            <div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.saveandexit')}}</button>
                                                </div>
											</div>
                                            </form>

                                       @endif
                                      <!-- product categories end -->


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
                        </div>
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
		//
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
		 @if(Request::is('gwc/product/*/edit')==true)

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


		 @endif
		@php
		$skuno='';
		if(!empty($editproduct->sku_no)){
        $skuno=$editproduct->sku_no;
        }
		@endphp
		 <!--multiple fileds-->
		@if(!empty($chosenCustomOptions) && count($chosenCustomOptions)>0)
		@foreach($chosenCustomOptions as $chosenCustomOption)
		$('#kt_repeater_{{$chosenCustomOption->id}}').repeater({
		initEmpty: false,
		defaultValues:{
		'sku_no': '@php echo $skuno; @endphp'
		},
		defaultName: {
		'text-input': 'MyInputs',
		},
		show: function () {
		$(this).slideDown();
		},
		hide: function (deleteElement) {
		  $(this).slideUp(deleteElement);
		 }
	    });
		@endforeach
		@endif

		$('#kt_repeater_gallery_1').repeater({
		initEmpty: false,
		defaultName: {
		'text-input': 'foo',
		},
		show: function () {
		$(this).slideDown();
		$('.doc_date').datepicker({format:"yyyy-mm-dd"});
		},
		hide: function (deleteElement) {
		  $(this).slideUp(deleteElement);
		 }
	    });

		$('#kt_repeater_1').repeater({
		initEmpty: false,
		defaultName: {
		'text-input': 'foo',
		},
		show: function () {
		$(this).slideDown();
		$('.doc_date').datepicker({format:"yyyy-mm-dd"});
		},
		hide: function (deleteElement) {
		  $(this).slideUp(deleteElement);
		 }
	    });


		$('#countdown_datetime').datepicker({format:"yyyy-mm-dd"});
		@if(empty($editproduct->countdown_datetime))
		$("#countdown_datetime").val('');
		@endif

		@if(Request::is('gwc/product/*/seo-tags')==true)
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
		  @endif
		});
       </script>

    <script src="{!! url('admin_assets/jquery.form.js') !!}" type="text/javascript"></script>
    <script type="text/javascript">

	$(document).ready(function(){


		$(document).on("keyup","#cost_price",function(){
		var cost_price   = $(this).val();
		var retail_price = $("#retail_price").val();
		var profit = parseFloat(retail_price) - parseFloat(cost_price);
		$("#profitprice").html('&nbsp;(Profit : '+profit+' {{\App\Currency::default()}})');
		});



	 $('#galleryImageForm').ajaxForm({
        beforeSend:function(){
            $('#success').empty();
            $('.progress-bar').text('0%');
            $('.progress-bar').css('width', '0%');
        },
        uploadProgress:function(event, position, total, percentComplete){
            $('.progress-bar').text(percentComplete + '0%');
            $('.progress-bar').css('width', percentComplete + '0%');
        },
        success:function(data)
        {
            if(data.success!='1')
            {
			    toastr.success(data.success);
                $('#success').html(data.image);
                $('.progress-bar').text('Uploaded');
                $('.progress-bar').css('width', '100%');
				$('#file').val('');
				window.setTimeout(function(){window.location.href=data.redirect;},5000);
            }else{
			    $('#success').hide();
                $('.progress-bar').hide();
				$('#file').val('');
			    window.setTimeout(function(){window.location.href=data.redirect;},1);
			}
        }
	  });
	});
	</script>

	</body>

	<!-- end::Body -->
</html>