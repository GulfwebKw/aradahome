@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme    = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.editoptions')}}</title>
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
									<h3 class="kt-subheader__title">{{__('adminMessage.options')}}</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="javascript:;" class="kt-subheader__breadcrumbs-link">{{__('adminMessage.editoptions')}}</a>
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
										<h3 class="kt-portlet__head-title">{{__('adminMessage.editoptions')}}</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="kt-portlet__head-wrapper">
											<div class="kt-portlet__head-actions">
												
												@if(auth()->guard('admin')->user()->can('options-list'))
												<a href="{{url('gwc/options')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-list-ul"></i>{{__('adminMessage.listoptions')}}</a> @endif
											</div>
										</div>
									</div>
								</div>				
										<!--begin::Form-->
					@if(auth()->guard('admin')->user()->can('options-edit'))
                    <form name="tFrm"  id="form_validation"  method="post"
                          class="kt-form" enctype="multipart/form-data" action="{{route('options.update',$editoptions->id)}}">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="kt-portlet__body">
																					
                                           
                                                 
                                                <div class="form-group row">
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.option_name')}}(En)*</label>
                                                <input type="text" class="form-control @if($errors->has('option_name_en')) is-invalid @endif" name="option_name_en"
                                                               value="{{$editoptions->option_name_en?$editoptions->option_name_en:old('option_name_en')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_option_name')}}*" />
                                                @if($errors->has('option_name_en'))
                                                <div class="invalid-feedback">{{ $errors->first('option_name_en') }}</div>
                                                @endif
                                                </div>
                                                <div class="col-lg-4">
                                                <label>{{__('adminMessage.option_name')}}(Ar)*</label>
                                                <input type="text" class="form-control @if($errors->has('option_name_ar')) is-invalid @endif" name="option_name_ar"
                                                               value="{{$editoptions->option_name_ar?$editoptions->option_name_ar:old('option_name_ar')}}" autocomplete="off" placeholder="{{__('adminMessage.enter_option_name')}}*" />
                                                @if($errors->has('option_name_ar'))
                                                <div class="invalid-feedback">{{ $errors->first('option_name_ar') }}</div>
                                                @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.option_type')}}</label>
                                                <select id="option_type" class="form-control @if($errors->has('option_type')) is-invalid @endif" name="option_type" autocomplete="off">
                                                @if(!empty($optionsLists) && count($optionsLists)>0)
                                                @foreach($optionsLists as $key=>$optionsList)
                                                <optgroup label="{{$key}}">
                                                @foreach($optionsList as $optionsSubList)
                                                <option value="{{$optionsSubList}}" {{$editoptions->option_type==$optionsSubList?'selected':''}}>{{$optionsSubList}}</option>
                                                @endforeach
                                                </optgroup>
                                                @endforeach
                                                @endif
                                                </select>
                                                
                                                @if($errors->has('option_type'))
                                                <div class="invalid-feedback">{{ $errors->first('option_type') }}</div>
                                                @endif
                                                </div>
                                                <div class="col-lg-2">
                                                <label>{{__('adminMessage.display_order')}}</label>
                                                <input type="text" class="form-control @if($errors->has('display_order')) is-invalid @endif" name="display_order"
                                                               value="{{$lastOrder}}" autocomplete="off"  />
                                                 @if($errors->has('option_name'))
                                                  <div class="invalid-feedback">{{ $errors->first('option_name') }}</div>
                                                 @endif
                                                </div>
                                            </div>
                                            <div id="suboptionsids" @if($editoptions->option_type<>"select" && $editoptions->option_type<>"select for each order" && $editoptions->option_type<>"radio" && $editoptions->option_type<>"checkbox") style="display:none" @endif>
                                            <!-- sub option start -->
                                            <div class="form-group row"><h5>{{trans('adminMessage.optionvalues')}}</h5></div>
                                         
                                            <div id="kt_repeater_1" class="kt_repeater_1">
												<div class="form-group form-group-last row" id="kt_repeater_1">
													<div data-repeater-list="attach" class="col-lg-12">
                                                    @if(!empty($editoptionschlds) && count($editoptionschlds)>0)
                                                    @foreach($editoptionschlds as $editoptionschld)
                                                    
                                                      <div data-repeater-item class="form-group row align-items-center repeatbox">
                                                      <input type="hidden" name="editsuboptionhidden" value="{{$editoptionschld->id}}">
													  		<div class="col-lg-4">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
                                                 <input type="text" class="form-control" name="option_value_name_en" value="{{$editoptionschld->option_value_name_en?$editoptionschld->option_value_name_en:''}}" autocomplete="off" placeholder="{{__('adminMessage.option_value_name')}}(En)" />
                                                               							
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
                                                            <div class="col-lg-4">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
                                                 <input type="text" class="form-control" name="option_value_name_ar" value="{{$editoptionschld->option_value_name_ar?$editoptionschld->option_value_name_ar:''}}" autocomplete="off" placeholder="{{__('adminMessage.option_value_name')}}(Ar)" />
                                                               							
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
															<div class="col-lg-2">
																<div class="kt-form__group--inline">
																	
																	<div class="kt-form__control">
                                                  <input type="text" class="form-control" name="option_display_order" value="{{$editoptionschld->display_order?$editoptionschld->display_order:''}}" autocomplete="off" placeholder="{{__('adminMessage.option_display_order')}}" />
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
                                                            
															<div class="col-md-2">
																<a id="{{$editoptionschld->id}}" href="javascript:;" title="{{__('adminMessage.delete')}}" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold deletechildoption">
																	<i class="la la-trash-o"></i>
																</a>
															</div>
														</div>
                                                    @endforeach
                                                    @endif
                                                    <!-- sub option -->
														<div data-repeater-item class="form-group row align-items-center repeatbox">
															<div class="col-lg-4">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
                                                 <input type="text" class="form-control" name="option_value_name_en" value="" autocomplete="off" placeholder="{{__('adminMessage.option_value_name')}}(EN)" />
                                                               							
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
                                                            <div class="col-lg-4">
																<div class="kt-form__group--inline">
																	<div class="kt-form__control">
                                                 <input type="text" class="form-control" name="option_value_name_ar" value="" autocomplete="off" placeholder="{{__('adminMessage.option_value_name')}}(AR)" />
                                                               							
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
															<div class="col-lg-2">
																<div class="kt-form__group--inline">
																	
																	<div class="kt-form__control">
                                                  <input type="text" class="form-control" name="option_display_order" value="" autocomplete="off" placeholder="{{__('adminMessage.option_display_order')}}" />
																	</div>
																</div>
																<div class="d-md-none kt-margin-b-10"></div>
															</div>
                                                            
															<div class="col-md-2">
																<a href="javascript:;" title="{{__('adminMessage.delete')}}" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
																	<i class="la la-trash-o"></i>
																</a>
															</div>
														</div>
                                                  <!-- end sub option -->      
                                                        
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
                                          
                                            <!-- end sub option -->
                                            
                                            </div> 
                                            
                                            
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<button type="submit" class="btn btn-success">{{__('adminMessage.save')}}</button>
													<button type="button" onClick="Javascript:window.location.href='{{url('gwc/options')}}'"  class="btn btn-secondary cancelbtn">{{__('adminMessage.cancel')}}</button>
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
			
			//show/hide options
			$("#option_type").change(function(){
			var opt = $(this).val();
			if(opt=="select" || opt=="radio" || opt=="checkbox"|| opt=="select for each order"){
			$("#suboptionsids").show();
			}else{
			$("#suboptionsids").hide();
			}
			});
			
		});
       </script>
       
	</body>

	<!-- end::Body -->
</html>