@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}| @if($post->exists ) Edit {{ $post->title_en }} @else Submit new post @endif </title>
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
									<h3 class="kt-subheader__title">@if($post->exists ) Edit {{ $post->title_en }} @else Submit new post @endif</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">@if($post->exists ) {{ $post->title_ar }}  @endif</a>
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
										<h3 class="kt-portlet__head-title">@if($post->exists ) Edit {{ $post->title_en }} @else Submit new post @endif</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="kt-portlet__head-wrapper">
											<div class="kt-portlet__head-actions">
												
												@if(auth()->guard('admin')->user()->can('post-list'))
												<a href="{{url('gwc/blog/post')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>List of posts</a> @endif
											</div>
										</div>
									</div>
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can($post->exists ? 'post-edit' : 'post-create'))
                    <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route($post->exists ? 'admin.blog.post.update' : 'admin.blog.post.store' , [$post->id])}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          @if($post->exists) @method('put') @endif
											<div class="kt-portlet__body">
										<!--parent categories dropdown -->	
                                       
                                            													
                                       <!--categories name -->         
                                                <div class="form-group row">
                                                
                                                
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.title_en')}}*</label>
                                                <input type="text" required="" class="form-control @if($errors->has('title_en')) is-invalid @endif" name="title_en"
                                                               value="{{old('title_en' , $post->title_en)}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_en')}}" />
                                                               @if($errors->has('title_en'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.title_ar')}}*</label>
                                                <input type="text" required="" class="form-control @if($errors->has('title_ar')) is-invalid @endif" name="title_ar"
                                                               value="{{old('title_ar', $post->title_ar)}}" autocomplete="off" placeholder="{{__('adminMessage.enter_title_ar')}}" />
                                                               @if($errors->has('title_ar'))
                                                               <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>slug</label>
                                                <input type="text" class="form-control @if($errors->has('slug')) is-invalid @endif" name="slug"
                                                               value="{{old('slug', $post->slug)}}" autocomplete="off" />
                                                               @if($errors->has('slug'))
                                                               <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                                                               @endif
                                                </div>
                                            </div>

											<div class="form-group row">


                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_en')}}*</label>
													<textarea rows="3" id="details_en" name="details_en" class="kt-tinymce-4 form-control @if($errors->has('details_en')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_en')}}">{!!old('details_en',$post->details_en)!!}</textarea>
													@if($errors->has('details_en'))
														<div class="invalid-feedback">{{ $errors->first('details_en') }}</div>
													@endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_ar')}}*</label>
													<textarea rows="3" id="details_ar" name="details_ar" class="kt-tinymce-4 form-control @if($errors->has('details_ar')) is-invalid @endif" autocomplete="off" placeholder="{{__('adminMessage.enter_details_ar')}}">{!!old('details_ar',$post->details_ar)!!}</textarea>
													@if($errors->has('details_ar'))
														<div class="invalid-feedback">{{ $errors->first('details_ar') }}</div>
													@endif
                                                </div>
                                            </div>

                                         <!-- friendly url , status , sorting -->   
                                         <div class="form-group row">
                                                
                                                <div class="col-lg-4">
                                                	<label>{{trans('adminMessage.status')}}*</label>
													<select name="status" class="form-control @if($errors->has('title_en')) is-invalid @endif">
														<option value="draft" @if(old('status' , $post->status ) == "draft" ) selected @endif>draft</option>
														<option value="published" @if(old('status' , $post->status ) == "published" ) selected @endif>published</option>
														<option value="hidden" @if(old('status' , $post->status ) == "hidden" ) selected @endif>hidden</option>
													</select>
													@if($errors->has('status'))
														<div class="invalid-feedback">{{ $errors->first('status') }}</div>
													@endif
                                                </div>
                                                <div class="col-lg-6">
                                                <label>{{trans('adminMessage.image')}}</label>
                                                        <div class="custom-file @if($errors->has('image')) is-invalid @endif">
														<input type="file" class="custom-file-input @if($errors->has('image')) is-invalid @endif"  id="image" name="image">
														<label class="custom-file-label" for="image">{{__('adminMessage.chooseImage')}}</label>
													    </div>
                                                               @if($errors->has('image'))
                                                               <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                               @endif
                                                </div>
                                                <div class="col-lg-2">
                                                @if($post->image)
                                                <br>
                                                <img src="{!! url('uploads/blog/thumb/'.$post->image) !!}" width="40">
                                                @endif
                                                </div>

                                            </div>
                                            
                                                     
                                                     
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/blog/post')}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
					height: 300
				});
			});


		</script>
        
	</body>

	<!-- end::Body -->
</html>