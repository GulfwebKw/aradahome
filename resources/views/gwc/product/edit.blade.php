@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>{{__('adminMessage.websiteName')}}|{{__('adminMessage.editproduct')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
    @include('gwc.css.user')
    <link href="{{url('admin_assets/assets/css/pages/wizard/wizard-1.css')}}" rel="stylesheet" type="text/css"/>

    <!--mini color -->
    <link href="{{url('admin_assets/assets/plugins/minicolors/jquery.minicolors.css')}}" rel="stylesheet"
          type="text/css"/>
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
                <img alt="{{__('adminMessage.websiteName')}}"
                     src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40"/>
            @endif
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler">
            <span></span></button>

        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more"></i></button>
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
                                <a href="{{url('gwc/home')}}" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;"
                                   class="kt-subheader__breadcrumbs-link">{{__('adminMessage.editproduct')}}</a>
                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">
                            <div class="btn-group">
                                @if(auth()->guard('admin')->user()->can('product-list'))
                                    <a href="{{url('gwc/product')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i
                                                class="la la-list-ul"></i>{{__('adminMessage.listproduct')}}</a> @endif

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
                        <div class="kt-grid  kt-wizard-v1 kt-wizard-v1--white" id="kt_projects_add"
                             data-ktwizard-state="step-first">
                            <div class="kt-grid__item">

                                <!--begin: Form Wizard Nav -->
                                <div class="kt-wizard-v1__nav">
                                    <div class="kt-wizard-v1__nav-items">

                                        <!--doc: Replace A tag with SPAN tag to disable the step link click -->
                                        <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step"
                                             @if(Request::is('gwc/product/*/categories')==true || Request::is('gwc/product/*/seo-tags')==true || Request::is('gwc/product/*/edit')==true || Request::is('gwc/product/*/gallery')==true || Request::is('gwc/product/*/attributes')==true  || Request::is('gwc/product/*/finish')==true || Request::is('gwc/product/*/options')==true || Request::is('gwc/product/*/editoptions/*')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/edit')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.info')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.details')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step"
                                             @if(Request::is('gwc/product/*/categories')==true || Request::is('gwc/product/*/seo-tags')==true || Request::is('gwc/product/*/attributes')==true  || Request::is('gwc/product/*/finish')==true || Request::is('gwc/product/*/options')==true || Request::is('gwc/product/*/gallery')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/options')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.options')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.options')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="kt-wizard-v1__nav-item"
                                             @if(Request::is('gwc/product/*/categories')==true || Request::is('gwc/product/*/seo-tags')==true || Request::is('gwc/product/*/gallery')==true || Request::is('gwc/product/*/finish')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/categories')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.category')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.categories')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step"
                                             @if(Request::is('gwc/product/*/seo-tags')==true || Request::is('gwc/product/*/gallery')==true || Request::is('gwc/product/*/finish')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/gallery')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.gallery')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.gallery')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="kt-wizard-v1__nav-item"
                                             @if(Request::is('gwc/product/*/seo-tags')==true || Request::is('gwc/product/*/finish')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/seo-tags')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.seo')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.seoandtags')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="kt-wizard-v1__nav-item"
                                             @if(Request::is('gwc/product/*/finish')==true) data-ktwizard-state="current" @endif>
                                            <a href="{{url('gwc/product/'.$editproduct->id.'/finish')}}">
                                                <div class="kt-wizard-v1__nav-body">
                                                    <div class="kt-wizard-v1__nav-icon">{!!__('svgicon.finish')!!}</div>
                                                    <div class="kt-wizard-v1__nav-label">
                                                        {{__('adminMessage.finish')}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!--end: Form Wizard Nav -->
                            </div>
                        </div>
                        <!--begin::Form-->
                    @if(auth()->guard('admin')->user()->can('product-edit'))

                        <!-- product details start -->
                            @if(Request::is('gwc/product/*/edit')==true)
                                <form name="tFrm" id="form_validation" method="post"
                                      class="kt-form" enctype="multipart/form-data"
                                      action="{{route('product.update',$editproduct->id)}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="kt-portlet__body">
                                        <div class="form-group row">
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.item_code')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('item_code')) is-invalid @endif"
                                                       name="item_code"
                                                       value="{{$editproduct->item_code?$editproduct->item_code:old('item_code')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_item_code')}}*"/>
                                                @if($errors->has('item_code'))
                                                    <div class="invalid-feedback">{{ $errors->first('item_code') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.sku_no')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('sku_no')) is-invalid @endif"
                                                       name="sku_no"
                                                       value="{{$editproduct->sku_no?$editproduct->sku_no:old('sku_no')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_sku_no')}}"/>
                                                @if($errors->has('sku_no'))
                                                    <div class="invalid-feedback">{{ $errors->first('sku_no') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.weight')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('weight')) is-invalid @endif"
                                                       name="weight"
                                                       value="{{$editproduct->weight?$editproduct->weight:old('weight')}}"
                                                       autocomplete="off"/>
                                                @if($errors->has('weight'))
                                                    <div class="invalid-feedback">{{ $errors->first('weight') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.height')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('height')) is-invalid @endif"
                                                       name="height"
                                                       value="{{$editproduct->height?$editproduct->height:old('height')}}"
                                                       autocomplete="off"/>
                                                @if($errors->has('height'))
                                                    <div class="invalid-feedback">{{ $errors->first('height') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.width')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('width')) is-invalid @endif"
                                                       name="width"
                                                       value="{{$editproduct->width?$editproduct->width:old('width')}}"
                                                       autocomplete="off"/>
                                                @if($errors->has('width'))
                                                    <div class="invalid-feedback">{{ $errors->first('width') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.depth')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('depth')) is-invalid @endif"
                                                       name="depth"
                                                       value="{{$editproduct->depth?$editproduct->depth:old('depth')}}"
                                                       autocomplete="off"/>
                                                @if($errors->has('depth'))
                                                    <div class="invalid-feedback">{{ $errors->first('depth') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.displayorder')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('display_order')) is-invalid @endif"
                                                       name="display_order"
                                                       value="{{$editproduct->display_order?$editproduct->display_order:old('display_order')}}"
                                                       autocomplete="off"/>
                                                @if($errors->has('display_order'))
                                                    <div class="invalid-feedback">{{ $errors->first('display_order') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_en')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('title_en')) is-invalid @endif"
                                                       name="title_en"
                                                       value="{{$editproduct->title_en?$editproduct->title_en:old('title_en')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_title_en')}}*"/>
                                                @if($errors->has('title_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('title_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.title_ar')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('title_ar')) is-invalid @endif"
                                                       name="title_ar"
                                                       value="{{$editproduct->title_ar?$editproduct->title_ar:old('title_ar')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_title_ar')}}*"/>
                                                @if($errors->has('title_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('title_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.extra_title_en')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('extra_title_en')) is-invalid @endif"
                                                       name="extra_title_en"
                                                       value="{{$editproduct->extra_title_en?$editproduct->extra_title_en:old('extra_title_en')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_title_en')}}"/>
                                                @if($errors->has('extra_title_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('extra_title_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.extra_title_ar')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('extra_title_ar')) is-invalid @endif"
                                                       name="extra_title_ar"
                                                       value="{{$editproduct->extra_title_ar?$editproduct->extra_title_ar:old('extra_title_ar')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_title_ar')}}"/>
                                                @if($errors->has('extra_title_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('extra_title_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.sdetails_en')}}</label>
                                                <textarea rows="3" id="sdetails_en" name="sdetails_en"
                                                          class="form-control @if($errors->has('sdetails_en')) is-invalid @endif"
                                                          autocomplete="off"
                                                          placeholder="{{__('adminMessage.enter_sdetails_en')}}">{!!$editproduct->sdetails_en?$editproduct->sdetails_en:old('sdetails_en')!!}</textarea>
                                                @if($errors->has('sdetails_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('sdetails_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.sdetails_ar')}}</label>
                                                <textarea rows="3" id="sdetails_ar" name="sdetails_ar"
                                                          class="form-control @if($errors->has('sdetails_ar')) is-invalid @endif"
                                                          autocomplete="off"
                                                          placeholder="{{__('adminMessage.enter_sdetails_ar')}}">{!!$editproduct->sdetails_ar?$editproduct->sdetails_ar:old('sdetails_ar')!!}</textarea>
                                                @if($errors->has('sdetails_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('sdetails_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_en')}}</label>
                                                <textarea rows="3" id="details_en" name="details_en"
                                                          class="kt-tinymce-4 form-control @if($errors->has('details_en')) is-invalid @endif"
                                                          autocomplete="off"
                                                          placeholder="{{__('adminMessage.enter_details_en')}}">{!!$editproduct->details_en?$editproduct->details_en:old('details_en')!!}</textarea>
                                                @if($errors->has('details_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('details_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.details_ar')}}</label>
                                                <textarea rows="3" id="details_ar" name="details_ar"
                                                          class="kt-tinymce-4 form-control @if($errors->has('details_ar')) is-invalid @endif"
                                                          autocomplete="off"
                                                          placeholder="{{__('adminMessage.enter_details_ar')}}">{!!$editproduct->details_ar?$editproduct->details_ar:old('details_ar')!!}</textarea>
                                                @if($errors->has('details_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('details_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.retail_price')}}*</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('retail_price')) is-invalid @endif"
                                                       name="retail_price" id="retail_price"
                                                       value="{{$editproduct->retail_price?$editproduct->retail_price:old('retail_price')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_retail_price')}}"/>
                                                @if($errors->has('retail_price'))
                                                    <div class="invalid-feedback">{{ $errors->first('retail_price') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.old_price')}}(e.g : <s>{{\App\Currency::default()}} 000</s>)</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('old_price')) is-invalid @endif"
                                                       name="old_price"
                                                       value="{{$editproduct->old_price?$editproduct->old_price:old('old_price')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_old_price')}}"/>
                                                @if($errors->has('old_price'))
                                                    <div class="invalid-feedback">{{ $errors->first('old_price') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.cost_price')}}<span id="profitprice">
                                                @if(!empty($editproduct->cost_price))
                                                            (Profit: {{($editproduct->retail_price-$editproduct->cost_price)}}
                                                            )
                                                        @endif
                                                </span></label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('cost_price')) is-invalid @endif"
                                                       name="cost_price" id="cost_price"
                                                       value="{{$editproduct->cost_price?$editproduct->cost_price:old('cost_price')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_cost_price')}}"/>
                                                @if($errors->has('cost_price'))
                                                    <div class="invalid-feedback">{{ $errors->first('cost_price') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.wholesale_price')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('wholesale_price')) is-invalid @endif"
                                                       name="wholesale_price"
                                                       value="{{$editproduct->wholesale_price?$editproduct->wholesale_price:old('wholesale_price')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_wholesale_price')}}"/>
                                                @if($errors->has('wholesale_price'))
                                                    <div class="invalid-feedback">{{ $errors->first('wholesale_price') }}</div>
                                                @endif
                                            </div>


                                        </div>


                                        <div class="form-group row">
                                            <div class="col-lg-4">
                                                <label>{{trans('theme')['theme'.$theme]['product_image']}}</label>
                                                @if($editproduct->image)
                                                    <img class="pull-right"
                                                         src="{!! url('uploads/product/thumb/'.$editproduct->image) !!}"
                                                         width="35" height="35">
                                                    <a href="{{url('gwc/product/deleteImage/'.$editproduct->id)}}"
                                                       class="btn btn-brand btn-danger pull-right btn-icon-sm btn-sm "><i
                                                                class="la la-trash"></i>{{__('adminMessage.delete')}}
                                                    </a>
                                                @endif
                                                <div class="custom-file @if($errors->has('image')) is-invalid @endif">
                                                    <input type="file"
                                                           class="custom-file-input @if($errors->has('image')) is-invalid @endif"
                                                           id="image" name="image">
                                                    <label class="custom-file-label"
                                                           for="image">{{__('adminMessage.chooseImage')}}</label>
                                                </div>
                                                @if($errors->has('image'))
                                                    <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                                @endif
                                            </div>

                                            <div class="col-lg-4">
                                                <label>{{trans('theme')['theme'.$theme]['product_rollover_image']}}

                                                </label>
                                                @if($editproduct->rollover_image)
                                                    <img class="pull-right"
                                                         src="{!! url('uploads/product/thumb/'.$editproduct->rollover_image) !!}"
                                                         width="35" height="35">
                                                    <a href="{{url('gwc/product/deleteRolloverImage/'.$editproduct->id)}}"
                                                       class="btn btn-brand btn-danger pull-right btn-icon-sm btn-sm "><i
                                                                class="la la-trash"></i>{{__('adminMessage.delete')}}
                                                    </a>
                                                @endif
                                                <div class="custom-file @if($errors->has('rollover_image')) is-invalid @endif">

                                                    <input type="file"
                                                           class="custom-file-input @if($errors->has('rollover_image')) is-invalid @endif"
                                                           id="rollover_image" name="rollover_image">
                                                    <label class="custom-file-label"
                                                           for="rollover_image">{{__('adminMessage.chooseImage')}}</label>
                                                </div>
                                                @if($errors->has('rollover_image'))
                                                    <div class="invalid-feedback">{{ $errors->first('rollover_image') }}</div>
                                                @endif
                                            </div>

                                            <div class="col-lg-4">
                                                <label>{{trans('theme')['theme'.$theme]['attachfile']}}@if(!empty($editproduct->attachfile))
                                                        (<a href="{{url('uploads/product/'.$editproduct->attachfile)}}"
                                                            target="_blank">View</a>)@endif</label>
                                                @if(!empty($editproduct->attachfile))
                                                    <a href="{{url('gwc/product/deletePdf/'.$editproduct->id)}}"
                                                       class="btn btn-brand btn-danger pull-right btn-icon-sm btn-sm "><i
                                                                class="la la-trash"></i>{{__('adminMessage.delete')}}
                                                    </a>
                                                @endif
                                                <div class="custom-file @if($errors->has('attachfile')) is-invalid @endif">
                                                    <input type="file"
                                                           class="custom-file-input @if($errors->has('attachfile')) is-invalid @endif"
                                                           id="attachfile" name="attachfile">
                                                    <label class="custom-file-label"
                                                           for="rollover_image">{{__('adminMessage.choosefile')}}</label>
                                                </div>
                                                @if($errors->has('attachfile'))
                                                    <div class="invalid-feedback">{{ $errors->first('attachfile') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-8">
                                                <label>{{__('adminMessage.slug')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('slug')) is-invalid @endif"
                                                       name="slug"
                                                       value="{{$editproduct->slug?$editproduct->slug:old('slug')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_slug')}}"/>
                                                @if($errors->has('slug'))
                                                    <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-4">
                                                <label>{{__('adminMessage.warranty')}}</label>
                                                <select class="form-control @if($errors->has('warranty')) is-invalid @endif"
                                                        name="warranty">
                                                    <option value="0">{{__('adminMessage.choosewarranty')}}</option>
                                                    @if(!empty($warrantyLists) && count($warrantyLists)>0)
                                                        @foreach($warrantyLists as $warrantyList)
                                                            <option value="{{$warrantyList->id}}"
                                                                    @if($editproduct->warranty==$warrantyList->id) selected @endif>{{$warrantyList->title_en}}</option>
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
                                                               {{ !empty($editproduct->customizable) ? 'checked' : '' }}
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
                                            <button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i
                                                        class="la la-angle-double-right"></i></button>
                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtolisting')}}</button>

                                            <label for="redirect_to_listing"><input type="checkbox"
                                                                                    name="redirect_to_listing"
                                                                                    id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        <!-- product details end -->



                            <!-- product attributes -->
                            @if(Request::is('gwc/product/*/options')==true)


                                <form name="tFrm" id="form_validation" method="post" class="kt-form"
                                      enctype="multipart/form-data"
                                      action="{{route('uploadAttribute',$editproduct->id)}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="product_id" id="product_id" value="{{$editproduct->id}}">
                                    <div class="kt-portlet__body">
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.item_has_an_attribute')}}</label>
                                                <select class="form-control @if($errors->has('is_attribute')) is-invalid @endif"
                                                        name="is_attribute" id="is_attribute">
                                                    <option value="1"
                                                            @if($editproduct->is_attribute==1) selected @endif >{{__('adminMessage.yes')}}</option>
                                                    <option value="0"
                                                            @if($editproduct->is_attribute==0) selected @endif >{{__('adminMessage.no')}}</option>
                                                </select>
                                                @if($errors->has('is_attribute'))
                                                    <div class="invalid-feedback">{{ $errors->first('is_attribute') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-4">
                                                <label>{{__('adminMessage.inventory.select')}}</label>
                                                <select class="form-control" id="selectInventory">
                                                    @foreach(\App\Inventory::get(null , false) as $inventory)
                                                        <option value="{{$inventory->id}}">{{$inventory->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

{{--                                            <div class="col-lg-3" id="box-quantity"--}}
{{--                                                 @if($editproduct->is_attribute) style="display:none;" @endif>--}}
{{--                                                <label>{{__('adminMessage.quantity')}}</label>--}}
{{--                                                <input type="number"--}}
{{--                                                       class="form-control @if($errors->has('quantity')) is-invalid @endif"--}}
{{--                                                       name="squantity"--}}
{{--                                                       value="{{old('quantity' , $editproduct->quantity)}}"--}}
{{--                                                       autocomplete="off"--}}
{{--                                                       placeholder="{{__('adminMessage.enter_quantity')}}"/>--}}
{{--                                                @if($errors->has('quantity'))--}}
{{--                                                    <div class="invalid-feedback">{{ $errors->first('quantity') }}</div>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}

                                            <div class="col-lg-3" id="box-options"
                                                 @if( ! $editproduct->is_attribute) style="display:none;" @endif>
                                                @php
                                                    $custoid1 = App\Http\Controllers\AdminProductController::checkSizeColorDisable($editproduct->id,1);
                                                    $custoid2 = App\Http\Controllers\AdminProductController::checkSizeColorDisable($editproduct->id,2);
                                                    $custoid3 = App\Http\Controllers\AdminProductController::checkSizeColorDisable($editproduct->id,3);
                                                @endphp
                                                <label>{{__('adminMessage.options')}}</label>
                                                <select class="form-control" name="cust_options" id="cust_options">
                                                    <option value="">{{trans('adminMessage.chooseanoption')}}</option>
                                                    @if(!empty($customOptionsLists) && count($customOptionsLists)>0)
                                                        @foreach($customOptionsLists as $customOptionsList)
                                                            @php
                                                                $disableMe = '';
                                                                if($customOptionsList->id < 4 && (!empty($custoid1) || !empty($custoid2) || !empty($custoid3))){
                                                                    $custoid = App\Http\Controllers\AdminProductController::checkSizeColorDisable($editproduct->id,$customOptionsList->id);
                                                                    if(!empty($custoid) && !empty($custoid1)){
                                                                    }elseif(!empty($custoid) && !empty($custoid2)){
                                                                    }elseif(!empty($custoid) && !empty($custoid3)){
                                                                    }else{
                                                                        $disableMe = 'disabled';
                                                                    }
                                                                }
                                                            @endphp
                                                            <option value="{{$customOptionsList->id}}" {{ $disableMe }}>{{$customOptionsList->option_name_en}}</option>
                                                        @endforeach
                                                    @endif
                                                    <select>
                                            </div>

                                            <div class="col-lg-2" id="box-options-button">
                                                <label><br></label><br>
                                                <button type="button" class="btn btn-sm btn-info addcustomoption"><i
                                                            class="flaticon2-add-1"></i>{{__('adminMessage.add')}}
                                                </button>
                                            </div>
                                        </div>

                                        <div id="box-display-quantits"
                                             @if($editproduct->is_attribute) style="display:none;" @endif>

                                            @forelse($editproduct->getQuantity(-1,null,null, true,false,true) as $quantity)
                                                <div class="form-group row">
                                                    <div class="col-lg-5">
                                                        <label>{{__('adminMessage.inventory.quantity')}} {{ $quantity->inventory->title  }} @if ( ! $quantity->inventory->is_active ){{ __('adminMessage.inventory.deActiveInventory') }}@endif :</label>
                                                        <input type="number" min="0"
                                                               name="quantity_{{ $quantity->id }}"
                                                               class="form-control @if($errors->has('quantity_'.$quantity->id)) is-invalid @endif"
                                                               placeholder="{{__('adminMessage.quantity')}}"
                                                               value="{{ $quantity->quantity }}">
                                                        @if($errors->has('quantity_'.$quantity->id))
                                                            <div class="invalid-feedback">{{ $errors->first('quantity_'.$quantity->id) }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label><br></label><br>
                                                        <button type="button"  id="{{$quantity->id}}" class="btn btn-label-danger btn-sm btn-bold deleteParentInventory">{{trans('delete')}}</button>
                                                    </div>
                                                </div>
                                                @empty
                                                    @foreach(\App\Inventory::get(-1 , false) as $inventory)
                                                            <div class="form-group row">
                                                                <div class="col-lg-5">
                                                                    <label>{{__('adminMessage.inventory.quantity')}} {{ $inventory->title  }} : @if ( ! $inventory->is_active )({{ __('adminMessage.inventory.deActiveInventory') }})@endif</label>
                                                                    <input type="number" min="0"
                                                                           name="quantity_0"
                                                                           class="form-control @if($errors->has('quantity_0')) is-invalid @endif"
                                                                           placeholder="{{__('adminMessage.quantity')}}"
                                                                           value="">
                                                                    <input type="hidden"
                                                                           name="quantity_0_invenotyId"
                                                                           value="{{ $inventory->id  }}">
                                                                    @if($errors->has('quantity_0'))
                                                                        <div class="invalid-feedback">{{ $errors->first('quantity_0') }}</div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <label><br></label><br>
                                                                    <button type="button" class="btn btn-label-danger btn-sm btn-bold" onclick="$(this).parent().parent().remove()">{{trans('delete')}}</button>
                                                                </div>
                                                            </div>
                                                        @break
                                                    @endforeach

                                                @endforelse
                                        </div>

                                        <!-- show existing data -->

                                        @if(!empty($chosenCustomOptions) && count($chosenCustomOptions)>0)

                                            <div id="box-display-options"
                                                 @if(!$editproduct->is_attribute) style="display:none;" @endif>


                                            @foreach($chosenCustomOptions as $chosenCustomOption)
                                                @php
                                                    $isSelected = App\Http\Controllers\AdminProductController::getChoosenRequiredStatus($editproduct->id,$chosenCustomOption->id);
                                                    $invenotory = \App\Inventory::withTrashed()->findOrFail($chosenCustomOption->inventory_id);
                                                @endphp
                                                <!--layout for size color -->
                                                    @if($chosenCustomOption->id == 1 || $chosenCustomOption->id == 2 || $chosenCustomOption->id == 3)
                                                        <div class="kt-portlet__head kt-portlet__space-x">
                                                            <div class="kt-portlet__head-label">
                                                                <h5>
                                                                    {{$chosenCustomOption->option_name_en}} {{ '( '.__('adminMessage.inventory.inventory') .': '.$invenotory->title.' )'  }} @if ( ! $invenotory->is_active ){{ __('adminMessage.inventory.deActiveInventory') }}@endif
                                                                </h5>
                                                            </div>
                                                            <div class="kt-portlet__head-toolbar">
                                                                <div class="btn-label-danger">
                                                                    {{trans('adminMessage.required')}}
                                                                    <select style="border:1px #CCCCCC solid;padding:3px;margin-right:5px;"
                                                                            name="is_option_required{{$chosenCustomOption->id}}"
                                                                            id="is_option_required{{$chosenCustomOption->id}}">
                                                                        <option value="1"
                                                                                selected>{{trans('adminMessage.yes')}}</option>
                                                                        <option value="0"
                                                                                disabled>{{trans('adminMessage.no')}}</option>
                                                                    </select>
                                                                </div>
                                                                <a href="javascript:;" id="{{$chosenCustomOption->chosen_id}}"
                                                                   class="btn btn-label-danger btn-sm btn-bold deleteParentOptions">{{trans('delete')}}</a>
                                                            </div>
                                                        </div>
                                                        <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half"
                                                             style="border:1px #ccc solid;padding:10px;">
                                                            <div class="kt-portlet__body kt-portlet__body--fluid">
                                                                <div id="kt_repeater_{{$chosenCustomOption->chosen_id}}"
                                                                     class="kt_repeater_1">
                                                                    <div class="form-group form-group-last row">
                                                                        <div data-repeater-list="attach[{{$chosenCustomOption->chosen_id}}]"
                                                                             class="col-lg-12">
                                                                            @php
                                                                                $customSizeColorOptions = App\Http\Controllers\AdminProductController::getChosenCustomSizeColors($editproduct->id,$chosenCustomOption->id , $chosenCustomOption->chosen_id);
                                                                            @endphp
                                                                            @if(!empty($customSizeColorOptions) && count($customSizeColorOptions)>0)
                                                                                @foreach($customSizeColorOptions as $customSizeColorOption)
                                                                                    @php
                                                                                        $InventoryHave = $editproduct->getQuantity($invenotory->id,$customSizeColorOption->id,null,false,false,true);
                                                                                        $skuno='';
                                                                                        if(!empty($customSizeColorOption->sku_no)){
                                                                                            $skuno=$customSizeColorOption->sku_no;
                                                                                        }else if(!empty($editproduct->sku_no)){
                                                                                            $skuno=$editproduct->sku_no;
                                                                                        }
                                                                                    @endphp
                                                                                    <div data-repeater-item
                                                                                         class="form-group row align-items-center repeatbox"
                                                                                         id="optionChildId-{{$customSizeColorOption->id}}">
                                                                                        <input type="hidden"
                                                                                               name="hiddencustomattrid"
                                                                                               value="{{$customSizeColorOption->id}}">
                                                                                        <div class="col-md-2">
                                                                                            <div class="kt-form__group--inline">
                                                                                                <div class="kt-form__control">
                                                                                                    <input type="text"
                                                                                                           name="sku_no"
                                                                                                           id="sku_no"
                                                                                                           class="form-control"
                                                                                                           placeholder="{{__('adminMessage.sku_no')}}"
                                                                                                           value="{{$skuno}}">
                                                                                                    <input type="text"
                                                                                                           name="weight"
                                                                                                           id="weight"
                                                                                                           class="form-control"
                                                                                                           placeholder="{{__('adminMessage.weight')}}"
                                                                                                           value="{{!empty($customSizeColorOption->weight)?$customSizeColorOption->weight:''}}">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="d-md-none kt-margin-b-10"></div>
                                                                                        </div>


                                                                                        <div class="col-md-2">
                                                                                            <div class="kt-form__group--inline">

                                                                                                <div class="kt-form__control">

                                                                                                    @if($chosenCustomOption->id == 3 || $chosenCustomOption->id == 1)
                                                                                                        <select name="size"
                                                                                                                id="size"
                                                                                                                class="form-control">
                                                                                                            <option value="0"
                                                                                                                    selected>{{__('adminMessage.chooseSize')}}</option>
                                                                                                            @foreach($listSizes as $listSize)
                                                                                                                <option value="{{$listSize->id}}"
                                                                                                                        @if(!empty($customSizeColorOption->size_id) && $listSize->id==$customSizeColorOption->size_id) selected @endif>{{$listSize->title_en}}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    @endif

                                                                                                    @if($chosenCustomOption->id == 3 || $chosenCustomOption->id == 2)
                                                                                                        <select name="color"
                                                                                                                id="color"
                                                                                                                class="form-control">
                                                                                                            <option value="0"
                                                                                                                    selected>{{__('adminMessage.chooseColor')}}</option>
                                                                                                            @foreach($listColors as $listColor)
                                                                                                                <option value="{{$listColor->id}}"
                                                                                                                        @if(!empty($customSizeColorOption->color_id) && $listColor->id==$customSizeColorOption->color_id) selected @endif>{{$listColor->title_en}}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="d-md-none kt-margin-b-10"></div>
                                                                                        </div>

                                                                                        <div class="col-md-2">
                                                                                            <div class="kt-form__group--inline">
                                                                                                <div class="kt-form__control">
                                                                                                    <input name="quantity"
                                                                                                           id="quantity"
                                                                                                           type="number"
                                                                                                           min="0"
                                                                                                           class="form-control"
                                                                                                           placeholder="{{__('adminMessage.qty')}}"
                                                                                                           value="{{$InventoryHave->quantity ?? '0' }}">
                                                                                                    <select name="is_qty_deduct"
                                                                                                            id="is_qty_deduct"
                                                                                                            class="form-control">
                                                                                                        <option value="1"
                                                                                                                @if(!empty($InventoryHave->is_qty_deduct)) selected @endif >{{trans('adminMessage.deduct')}}</option>
                                                                                                        <option value="0"
                                                                                                                @if(empty($InventoryHave->is_qty_deduct)) selected @endif>{{trans('adminMessage.none')}}</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="d-md-none kt-margin-b-10"></div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <div class="kt-form__group--inline">
                                                                                                <div class="kt-form__control">
                                                                                                    <input type="text"
                                                                                                           name="retail_price"
                                                                                                           id="retail_price"
                                                                                                           class="form-control"
                                                                                                           placeholder="{{__('adminMessage.retail_price')}}"
                                                                                                           value="{{!empty($customSizeColorOption->retail_price)?$customSizeColorOption->retail_price:''}}">
                                                                                                    <input type="text"
                                                                                                           name="old_price"
                                                                                                           id="old_price"
                                                                                                           class="form-control"
                                                                                                           placeholder="{{__('adminMessage.old_price')}}"
                                                                                                           value="{{!empty($customSizeColorOption->old_price)?$customSizeColorOption->old_price:''}}">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="d-md-none kt-margin-b-10"></div>
                                                                                        </div>

                                                                                        <div class="col-md-3">
                                                                                            <div class="kt-form__group--inline">
                                                                                                <div class="kt-form__control">
                                                                                                    <input type="file"
                                                                                                           name="color_image"
                                                                                                           id="color_image"
                                                                                                           class="form-control">
                                                                                                    @php
                                                                                                        if(!empty($customSizeColorOption->color_image)){
                                                                                                        $colorimage = url('uploads/color/thumb/'.$customSizeColorOption->color_image);
                                                                                                        }else{
                                                                                                        $colorimage = url('uploads/no-image.png');
                                                                                                        }
                                                                                                    @endphp
                                                                                                    <img src="{{$colorimage}}"
                                                                                                         width="40"
                                                                                                         height="40"
                                                                                                         style="position:absolute;margin-top: -40px;margin-left:210px;">
                                                                                                </div>


                                                                                            </div>
                                                                                            <div class="d-md-none kt-margin-b-10"></div>
                                                                                        </div>

                                                                                        <div class="col-md-1">
                                                                                            <a href="javascript:;"
                                                                                               title="{{__('adminMessage.delete')}}"
                                                                                               class="btn-sm btn btn-label-danger btn-bold removeAttCustomOption"
                                                                                               id="{{$customSizeColorOption->id}}">
                                                                                                <i class="la la-trash-o"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif

                                                                            @php
                                                                                $skuno='';
                                                                                if(!empty($editproduct->sku_no)){
                                                                                $skuno=$editproduct->sku_no;
                                                                                }
                                                                            @endphp
                                                                            <div data-repeater-item
                                                                                 class="form-group row align-items-center repeatbox">

                                                                                <div class="col-md-2">
                                                                                    <div class="kt-form__group--inline">
                                                                                        <div class="kt-form__control">
                                                                                            <input type="text"
                                                                                                   name="sku_no"
                                                                                                   id="sku_no"
                                                                                                   class="form-control"
                                                                                                   placeholder="{{__('adminMessage.sku_no')}}"
                                                                                                   value="{{$skuno}}"><input
                                                                                                    type="text"
                                                                                                    name="weight"
                                                                                                    id="weight"
                                                                                                    class="form-control"
                                                                                                    placeholder="{{__('adminMessage.weight')}}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="d-md-none kt-margin-b-10"></div>
                                                                                </div>


                                                                                <div class="col-md-2">
                                                                                    <div class="kt-form__group--inline">

                                                                                        <div class="kt-form__control">

                                                                                            @if($chosenCustomOption->id == 3 || $chosenCustomOption->id == 1)
                                                                                                <select name="size"
                                                                                                        id="size"
                                                                                                        class="form-control">
                                                                                                    <option value="0"
                                                                                                            selected>{{__('adminMessage.chooseSize')}}</option>
                                                                                                    @foreach($listSizes as $listSize)
                                                                                                        <option value="{{$listSize->id}}">{{$listSize->title_en}}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            @endif

                                                                                            @if($chosenCustomOption->id == 3 || $chosenCustomOption->id == 2)
                                                                                                <select name="color"
                                                                                                        id="color"
                                                                                                        class="form-control">
                                                                                                    <option value="0"
                                                                                                            selected>{{__('adminMessage.chooseColor')}}</option>
                                                                                                    @foreach($listColors as $listColor)
                                                                                                        <option value="{{$listColor->id}}">{{$listColor->title_en}}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="d-md-none kt-margin-b-10"></div>
                                                                                </div>

                                                                                <div class="col-md-2">
                                                                                    <div class="kt-form__group--inline">
                                                                                        <div class="kt-form__control">
                                                                                            <input name="quantity"
                                                                                                   id="quantity"
                                                                                                   type="number"
                                                                                                   min="0"
                                                                                                   class="form-control"
                                                                                                   placeholder="{{__('adminMessage.qty')}}"
                                                                                                   value="">
                                                                                            <select name="is_qty_deduct"
                                                                                                    id="is_qty_deduct"
                                                                                                    class="form-control">
                                                                                                <option value="1"
                                                                                                        selected>{{trans('adminMessage.deduct')}}</option>
                                                                                                <option value="0">{{trans('adminMessage.none')}}</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="d-md-none kt-margin-b-10"></div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="kt-form__group--inline">
                                                                                        <div class="kt-form__control">
                                                                                            <input type="text"
                                                                                                   name="retail_price"
                                                                                                   id="retail_price"
                                                                                                   class="form-control"
                                                                                                   placeholder="{{__('adminMessage.retail_price')}}">
                                                                                            <input type="text"
                                                                                                   name="old_price"
                                                                                                   id="old_price"
                                                                                                   class="form-control"
                                                                                                   placeholder="{{__('adminMessage.old_price')}}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="d-md-none kt-margin-b-10"></div>
                                                                                </div>

                                                                                <div class="col-md-3">
                                                                                    <div class="kt-form__group--inline">
                                                                                        <div class="kt-form__control">
                                                                                            <input type="file"
                                                                                                   name="color_image"
                                                                                                   id="color_image"
                                                                                                   class="form-control">
                                                                                            <img src="{{url('uploads/no-image.png')}}"
                                                                                                 width="40" height="40"
                                                                                                 style="position:absolute;margin-top: -40px;margin-left:210px;">
                                                                                        </div>


                                                                                    </div>
                                                                                    <div class="d-md-none kt-margin-b-10"></div>
                                                                                </div>

                                                                                <div class="col-md-1">
                                                                                    <a href="javascript:;"
                                                                                       title="{{__('adminMessage.delete')}}"
                                                                                       data-repeater-delete=""
                                                                                       class="btn-sm btn btn-label-danger btn-bold">
                                                                                        <i class="la la-trash-o"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group form-group-last row">

                                                                        <div class="col-lg-4">
                                                                            <a href="javascript:;"
                                                                               data-repeater-create=""
                                                                               class="btn btn-bold btn-sm btn-label-brand">
                                                                                <i class="la la-plus"></i> {{__('adminMessage.add')}}
                                                                            </a>

                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <!--layout for size color -->
                                                    @else
                                                        @php
                                                            $optionvaluenamelists = App\Http\Controllers\AdminProductController::getOptionValueNames($chosenCustomOption->id);
                                                            $isSelected = App\Http\Controllers\AdminProductController::getChoosenRequiredStatus($editproduct->id,$chosenCustomOption->id);
                                                            $invenotory = \App\Inventory::withTrashed()->findOrFail($chosenCustomOption->inventory_id);
                                                        @endphp
                                                    <!-- other option start here -->
                                                        <div class="kt-portlet__head kt-portlet__space-x">
                                                            <div class="kt-portlet__head-label">
                                                                <h5>
                                                                    {{$chosenCustomOption->option_name_en}}
                                                                    {{ '( '.__('adminMessage.inventory.inventory') .': '.$invenotory->title.' )'  }} @if ( ! $invenotory->is_active ){{ __('adminMessage.inventory.deActiveInventory') }}@endif

                                                                </h5>
                                                            </div>
                                                            <div class="kt-portlet__head-toolbar">
                                                                <div class="btn-label-danger">
                                                                    {{trans('adminMessage.required')}}
                                                                    <select style="border:1px #CCCCCC solid;padding:3px;margin-right:5px;"
                                                                            name="is_option_required{{$chosenCustomOption->id}}"
                                                                            id="is_option_required{{$chosenCustomOption->id}}">
                                                                        <option value="1"
                                                                                @if($isSelected->is_required==1) selected @endif>{{trans('adminMessage.yes')}}</option>
                                                                        <option value="0"
                                                                                @if($isSelected->is_required!=1) selected @endif>{{trans('adminMessage.no')}}</option>
                                                                    </select>
                                                                </div>
                                                                <a href="javascript:;"
                                                                   class="btn btn-label-danger btn-sm btn-bold deleteParentOptions"
                                                                   id="{{$chosenCustomOption->chosen_id}}">{{trans('adminMessage.delete')}}</a>
                                                            </div>
                                                        </div>

                                                        @if($chosenCustomOption->option_type=="select" ||$chosenCustomOption->option_type=="select for each order" || $chosenCustomOption->option_type=="radio" || $chosenCustomOption->option_type=="checkbox")
                                                            <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half"
                                                                 style="border:1px #ccc solid;padding:10px;">

                                                                <div class="kt-portlet__body kt-portlet__body--fluid">

                                                                    <div id="kt_repeater_{{$chosenCustomOption->chosen_id}}"
                                                                         class="kt_repeater_1">
                                                                        <div class="form-group form-group-last row">
                                                                            <div data-repeater-list="attach[{{$chosenCustomOption->chosen_id}}]"
                                                                                 class="col-lg-12">


                                                                                @php
                                                                                    $customOtherOptions = App\Http\Controllers\AdminProductController::getChosenOtherOptions($editproduct->id,$chosenCustomOption->id, $chosenCustomOption->chosen_id);
                                                                                @endphp
                                                                                @if(!empty($customOtherOptions) && count($customOtherOptions)>0)
                                                                                    @foreach($customOtherOptions as $customOtherOption)
                                                                                        @php

                                                                                            $InventoryHave = $editproduct->getQuantity($invenotory->id,null,$customOtherOption->id,false,false,true);
                                                                                                    $skuno='';
                                                                                                    if(!empty($customOtherOption->sku_no)){
                                                                                                    $skuno=$customOtherOption->sku_no;
                                                                                                    }else if(!empty($editproduct->sku_no)){
                                                                                                    $skuno=$editproduct->sku_no;
                                                                                                    }
                                                                                        @endphp
                                                                                        <div data-repeater-item
                                                                                             class="form-group row align-items-center repeatbox">
                                                                                            <input type="hidden"
                                                                                                   name="hiddencustomattrid"
                                                                                                   value="{{$customOtherOption->id}}">

                                                                                            <div class="col-md-3">
                                                                                                <div class="kt-form__group--inline">

                                                                                                    <div class="kt-form__control">
                                                                                                        <select name="option_value_name"
                                                                                                                id="option_value_name_{{$chosenCustomOption->id}}"
                                                                                                                class="form-control">
                                                                                                            <option value="0"
                                                                                                                    selected>{{__('adminMessage.chooseoptionvalue')}}</option>
                                                                                                            @foreach($optionvaluenamelists as $optionvaluenamelist)
                                                                                                                <option value="{{$optionvaluenamelist->id}}"
                                                                                                                        @if($customOtherOption->option_value_id==$optionvaluenamelist->id) selected @endif >{{$optionvaluenamelist->option_value_name_en}}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>

                                                                                            <div class="col-md-2">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__control">
                                                                                                        <input type="text"
                                                                                                               name="sku_no"
                                                                                                               id="sku_no"
                                                                                                               class="form-control"
                                                                                                               placeholder="{{__('adminMessage.sku_no')}}"
                                                                                                               value="{{$skuno}}">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>
                                                                                            <div class="col-md-2">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__control">
                                                                                                        <input type="text"
                                                                                                               name="weight"
                                                                                                               id="weight"
                                                                                                               class="form-control"
                                                                                                               placeholder="{{__('adminMessage.weight')}}"
                                                                                                               value="{{$customOtherOption->weight}}">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>


                                                                                            <div class="col-md-2">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__control">
                                                                                                        <input name="quantity"
                                                                                                               id="quantity"
                                                                                                               type="number"
                                                                                                               min="0"
                                                                                                               class="form-control"
                                                                                                               placeholder="{{__('adminMessage.qty')}}"
                                                                                                               value="{{$InventoryHave->quantity ?? '0' }}">
                                                                                                        <select name="is_qty_deduct"
                                                                                                                id="is_qty_deduct"
                                                                                                                class="form-control">
                                                                                                            <option value="1"
                                                                                                                    @if(!empty($InventoryHave->is_qty_deduct)) selected @endif>{{trans('adminMessage.deduct')}}</option>
                                                                                                            <option value="0"
                                                                                                                    @if(empty($InventoryHave->is_qty_deduct)) selected @endif>{{trans('adminMessage.none')}}</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>
                                                                                            <div class="col-md-2">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__control">
                                                                                                        <input type="text"
                                                                                                               name="retail_price"
                                                                                                               id="retail_price"
                                                                                                               class="form-control"
                                                                                                               placeholder="{{__('adminMessage.retail_price')}}"
                                                                                                               value="{{$customOtherOption->retail_price}}">
                                                                                                        <select name="is_price_add"
                                                                                                                id="is_deduct"
                                                                                                                class="form-control">
                                                                                                            <option value="1"
                                                                                                                    @if(!empty($customOtherOption->is_price_add) && $customOtherOption->is_price_add==1) selected @endif>
                                                                                                                +
                                                                                                            </option>
                                                                                                            <option value="2"
                                                                                                                    @if(!empty($customOtherOption->is_price_add)  && $customOtherOption->is_price_add==2) selected @endif>
                                                                                                                -
                                                                                                            </option>
                                                                                                            <option value="0"
                                                                                                                    @if(empty($customOtherOption->is_price_add)) selected @endif>{{trans('adminMessage.none')}}</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>


                                                                                            <div class="col-md-1">
                                                                                                <a href="javascript:;"
                                                                                                   title="{{__('adminMessage.delete')}}"
                                                                                                   data-repeater-delete=""
                                                                                                   class="btn-sm btn btn-label-danger btn-bold deleteOtherChosenOption"
                                                                                                   id="{{$customOtherOption->id}}">
                                                                                                    <i class="la la-trash-o"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>


                                                                                    @endforeach
                                                                                @endif

                                                                                @php
                                                                                    $skuno='';
                                                                                    if(!empty($editproduct->sku_no)){
                                                                                    $skuno=$editproduct->sku_no;
                                                                                    }
                                                                                @endphp
                                                                                <div data-repeater-item
                                                                                     class="form-group row align-items-center repeatbox">


                                                                                    <div class="col-md-3">
                                                                                        <div class="kt-form__group--inline">

                                                                                            <div class="kt-form__control">
                                                                                                <select name="option_value_name"
                                                                                                        id="option_value_name_{{$chosenCustomOption->id}}"
                                                                                                        class="form-control">
                                                                                                    <option value="0"
                                                                                                            selected>{{__('adminMessage.chooseoptionvalue')}}</option>
                                                                                                    @foreach($optionvaluenamelists as $optionvaluenamelist)
                                                                                                        <option value="{{$optionvaluenamelist->id}}">{{$optionvaluenamelist->option_value_name_en}}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-md-none kt-margin-b-10"></div>
                                                                                    </div>

                                                                                    <div class="col-md-2">
                                                                                        <div class="kt-form__group--inline">
                                                                                            <div class="kt-form__control">
                                                                                                <input type="text"
                                                                                                       name="sku_no"
                                                                                                       id="sku_no"
                                                                                                       class="form-control"
                                                                                                       placeholder="{{__('adminMessage.sku_no')}}"
                                                                                                       value="{{$skuno}}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-md-none kt-margin-b-10"></div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="kt-form__group--inline">
                                                                                            <div class="kt-form__control">
                                                                                                <input type="text"
                                                                                                       name="weight"
                                                                                                       id="weight"
                                                                                                       class="form-control"
                                                                                                       placeholder="{{__('adminMessage.weight')}}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-md-none kt-margin-b-10"></div>
                                                                                    </div>


                                                                                    <div class="col-md-2">
                                                                                        <div class="kt-form__group--inline">
                                                                                            <div class="kt-form__control">
                                                                                                <input name="quantity"
                                                                                                       id="quantity"
                                                                                                       type="number"
                                                                                                       min="0"
                                                                                                       class="form-control"
                                                                                                       placeholder="{{__('adminMessage.qty')}}">
                                                                                                <select name="is_qty_deduct"
                                                                                                        id="is_qty_deduct"
                                                                                                        class="form-control">
                                                                                                    <option value="1"
                                                                                                            selected>{{trans('adminMessage.deduct')}}</option>
                                                                                                    <option value="0">{{trans('adminMessage.none')}}</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-md-none kt-margin-b-10"></div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="kt-form__group--inline">
                                                                                            <div class="kt-form__control">
                                                                                                <input type="text"
                                                                                                       name="retail_price"
                                                                                                       id="retail_price"
                                                                                                       class="form-control"
                                                                                                       placeholder="{{__('adminMessage.retail_price')}}">
                                                                                                <select name="is_price_add"
                                                                                                        id="is_deduct"
                                                                                                        class="form-control">
                                                                                                    <option value="1"
                                                                                                            selected>+
                                                                                                    </option>
                                                                                                    <option value="2">
                                                                                                        -
                                                                                                    </option>
                                                                                                    <option value="0">{{trans('adminMessage.none')}}</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-md-none kt-margin-b-10"></div>
                                                                                    </div>


                                                                                    <div class="col-md-1">
                                                                                        <a href="javascript:;"
                                                                                           title="{{__('adminMessage.delete')}}"
                                                                                           data-repeater-delete=""
                                                                                           class="btn-sm btn btn-label-danger btn-bold">
                                                                                            <i class="la la-trash-o"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group form-group-last row">

                                                                            <div class="col-lg-4">
                                                                                <a href="javascript:;"
                                                                                   data-repeater-create=""
                                                                                   class="btn btn-bold btn-sm btn-label-brand">
                                                                                    <i class="la la-plus"></i> {{__('adminMessage.add')}}
                                                                                </a>

                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        @endif


                                                    <!-- other option start here -->
                                                    @endif


                                                @endforeach
                                            </div>
                                    @endif

                                    <!--end showing existing data -->

                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions">
                                            <button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i
                                                        class="la la-angle-double-right"></i></button>
                                            <label for="redirect_to_listing"><input type="checkbox"
                                                                                    name="redirect_to_listing"
                                                                                    id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}
                                            </label>

                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product/'.request()->id.'/edit')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtodetails')}}</button>
                                        </div>
                                    </div>
                                </form>

                            @endif
                        <!-- product attributes end -->
                            <!-- product categories -->
                            @if(Request::is('gwc/product/*/categories')==true)
                                <form name="tFrm" id="form_validation" method="post"
                                      class="kt-form" enctype="multipart/form-data"
                                      action="{{route('uploadCategory',$editproduct->id)}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="kt-portlet__body">
                                        <div class="form-group row ">
                                            <div class="col-lg-12 alert"
                                                 style="background-color:#DDEEFF;">{!!__('adminMessage.categories_notes')!!}</div>
                                        </div>
                                        <!-- show existing data -->

                                        @if($listCategories)
                                            <div id="kt_repeater_1_exist">
                                                <div class="form-group form-group-last row">
                                                    <div data-repeater-list="attach_exist" class="col-lg-12">
                                                        @foreach($listCategories as $listCategory)
                                                            <div class="form-group row align-items-center">
                                                                <div class="col-md-10">
                                                                    <div class="kt-form__group--inline">
                                                                        <div class="kt-form__control  col-form-label">


                                                                            <select name="category-{{$listCategory->id}}"
                                                                                    id="category-{{$listCategory->id}}"
                                                                                    class="form-control">
                                                                                <option value="0"
                                                                                        selected>{{__('adminMessage.chooseCategory')}}</option>
                                                                                @foreach($Categories as $category)
                                                                                    <option style="font-size:20px;"
                                                                                            value="{{$category->id}}"
                                                                                            @if($category->id==$listCategory->category_id) selected @endif >{{$category->name_en}}</option>
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
                                                                    <a href="javascript:;"
                                                                       title="{{__('adminMessage.savechanges')}}"
                                                                       class="btn-sm btn btn-label-info btn-bold updateCategoryDetails"
                                                                       id="{{$listCategory->id}}">
                                                                        <i class="la la-check-circle"></i>
                                                                    </a>
                                                                    <a href="{{url('gwc/product/'.$editproduct->id.'/deleteprodcategory/'.$listCategory->id)}}"
                                                                       title="{{__('adminMessage.delete')}}"
                                                                       class="btn-sm btn btn-label-danger btn-bold">
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
                                                    <div data-repeater-item
                                                         class="form-group row align-items-center repeatbox">
                                                        <div class="col-md-10">
                                                            <div class="kt-form__group--inline">
                                                                <div class="kt-form__control">
                                                                    <select name="category" id="category"
                                                                            class="form-control">
                                                                        <option value="0"
                                                                                selected>{{__('adminMessage.chooseCategory')}}</option>
                                                                        @foreach($Categories as $category)
                                                                            <option style="font-size:20px;"
                                                                                    value="{{$category->id}}">{{$category->name_en}}</option>
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
                                                            <a href="javascript:;" title="{{__('adminMessage.delete')}}"
                                                               data-repeater-delete=""
                                                               class="btn-sm btn btn-label-danger btn-bold">
                                                                <i class="la la-trash-o"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-last row">
                                                <div class="col-lg-4">
                                                    <a href="javascript:;" data-repeater-create=""
                                                       class="btn btn-bold btn-sm btn-label-brand">
                                                        <i class="la la-plus"></i> {{__('adminMessage.add')}}
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions">
                                            <button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i
                                                        class="la la-angle-double-right"></i></button>
                                            <label for="redirect_to_listing"><input type="checkbox"
                                                                                    name="redirect_to_listing"
                                                                                    id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}
                                            </label>

                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product/'.request()->id.'/options')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtooptions')}}</button>
                                        </div>
                                    </div>
                                </form>

                            @endif
                        <!-- product categories end -->
                            <!-- product gallery start-->
                            @if(Request::is('gwc/product/*/gallery')==true)
                                <form id="galleryImageForm" method="post"
                                      action="{{route('uploadgalleryimages',$editproduct->id)}}"
                                      enctype="multipart/form-data">
                                    <div class="kt-portlet__body">
                                        <div class="form-group row">{!!__('adminMessage.browse_images')!!}</div>


                                        @csrf

                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <input placeholder="{{trans('adminMessage.enter_title_en')}}"
                                                       class="form-control" type="text" name="title_en" id="title_en"/>
                                            </div>
                                            <div class="col-md-6">
                                                <input placeholder="{{trans('adminMessage.enter_title_ar')}}"
                                                       class="form-control" type="text" name="title_ar" id="title_ar"/>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"
                                                                                       style="width:450px;">Browse images from your computer</span>
                                                </div>
                                                <input class="form-control" type="file" name="file[]" id="file"
                                                       accept="image/*" multiple/>
                                            </div>
                                        </div>


                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <div class="progress">
                                                    <div class="progress-bar" aria-valuenow="" aria-valuemin="0"
                                                         aria-valuemax="100" style="width: 0%">
                                                        0%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div id="success" class="row"></div>
                                        </div>
                                        <div class="row form-group">
                                            @if($listGalleries)
                                                @foreach($listGalleries as $listGallery)
                                                    @if($listGallery->image)
                                                        <div class="col-md-3" align="center"
                                                             style="border:1px #CCCCCC solid;">
                                                            <img src="{!! url('uploads/product/thumb/'.$listGallery->image) !!}"
                                                                 width="200" style="margin:3px;">
                                                            <a href="{{url('gwc/product/'.$editproduct->id.'/deletegallery/'.$listGallery->id)}}"
                                                               class="btn btn-danger btn-sm">Delete</a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions">
                                            <button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i
                                                        class="la la-angle-double-right"></i></button>
                                            <label for="redirect_to_listing"><input type="checkbox"
                                                                                    name="redirect_to_listing"
                                                                                    id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}
                                            </label>
                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product/'.request()->id.'/categories')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtocategory')}}</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        <!-- product gallery end -->
                            <!-- seo tags start -->
                            @if(Request::is('gwc/product/*/seo-tags')==true)
                                <form name="tFrm" id="form_validation" method="post"
                                      class="kt-form" enctype="multipart/form-data"
                                      action="{{route('seotags',$editproduct->id)}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="kt-portlet__body">
                                        <div class="form-group"><h5>{{__('adminMessage.seo')}}</h5></div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 alert"
                                                 style="background-color:#DDEEFF;">{!!trans('adminMessage.seo_key_note')!!}</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.seokeywords_en')}}</label>
                                                <textarea name="seokeywords_en"
                                                          class="form-control @if($errors->has('seokeywords_en')) is-invalid @endif"
                                                          placeholder="{{__('adminMessage.enterseokeywords_en')}}"
                                                          autocomplete="off">{{$editproduct->seokeywords_en?$editproduct->seokeywords_en:old('seokeywords_en')}}</textarea>
                                                @if($errors->has('seokeywords_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('seokeywords_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.seokeywords_ar')}}</label>
                                                <textarea name="seokeywords_ar"
                                                          class="form-control @if($errors->has('seokeywords_ar')) is-invalid @endif"
                                                          placeholder="{{__('adminMessage.enterseokeywords_ar')}}"
                                                          autocomplete="off">{{$editproduct->seokeywords_ar?$editproduct->seokeywords_ar:old('seokeywords_ar')}}</textarea>
                                                @if($errors->has('seokeywords_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('seokeywords_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 alert"
                                                 style="background-color:#DDEEFF;">{!!trans('adminMessage.seo_details_note')!!}</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.seodescription_en')}}</label>
                                                <textarea name="seodescription_en"
                                                          class="form-control @if($errors->has('seodescription_en')) is-invalid @endif"
                                                          placeholder="{{__('adminMessage.enterseodescription_en')}}"
                                                          autocomplete="off">@if(!empty($editproduct->details_en)){{strip_tags(html_entity_decode(preg_replace('/\s\s+/', ' ', $editproduct->details_en)))}}@else{{$editproduct->seodescription_en?$editproduct->seodescription_en:old('seodescription_en')}}@endif</textarea>
                                                @if($errors->has('seodescription_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('seodescription_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.seodescription_ar')}}</label>
                                                <textarea name="seodescription_ar"
                                                          class="form-control @if($errors->has('seodescription_ar')) is-invalid @endif"
                                                          placeholder="{{__('adminMessage.enterseodescription_ar')}}"
                                                          autocomplete="off">@if(!empty($editproduct->details_ar)){{strip_tags(html_entity_decode(preg_replace('/\s\s+/', ' ', $editproduct->details_ar)))}}@else{{$editproduct->seodescription_ar?$editproduct->seodescription_ar:old('seodescription_ar')}}@endif</textarea>
                                                @if($errors->has('seodescription_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('seodescription_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group"><h5>{{__('adminMessage.tags')}}</h5></div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 alert"
                                                 style="background-color:#DDEEFF;">{!!trans('adminMessage.tags_note')!!}</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.tags_en')}}</label>
                                                <textarea name="tags_en" autofocus
                                                          class=" @if($errors->has('tags_en')) is-invalid @endif"
                                                          id="tags_en" placeholder="{{__('adminMessage.entertags_en')}}"
                                                          autocomplete="off">{{$editproduct->tags_en?$editproduct->tags_en:old('tags_en')}}</textarea>
                                                @if($errors->has('tags_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('tags_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <label>{{__('adminMessage.tags_ar')}}</label>
                                                <textarea name="tags_ar" id="tags_ar"
                                                          class=" @if($errors->has('tags_ar')) is-invalid @endif"
                                                          placeholder="{{__('adminMessage.entertags_ar')}}"
                                                          autocomplete="off">{{$editproduct->tags_ar?$editproduct->tags_ar:old('tags_ar')}}</textarea>
                                                @if($errors->has('tags_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('tags_ar') }}</div>
                                                @endif
                                            </div>
                                        </div>


                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions">
                                            <button type="submit" class="btn btn-success">{{__('adminMessage.next')}}<i
                                                        class="la la-angle-double-right"></i></button>
                                            <label for="redirect_to_listing"><input type="checkbox"
                                                                                    name="redirect_to_listing"
                                                                                    id="redirect_to_listing" value="1">&nbsp;{{__('adminMessage.saveandredirecttolisting')}}
                                            </label>
                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product/'.request()->id.'/gallery')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtogallery')}}</button>
                                        </div>
                                    </div>
                                </form>

                            @endif
                        <!--seo tags end -->


                            <!-- product finish start -->
                            @if(Request::is('gwc/product/*/finish')==true)
                                <form name="tFrm" id="form_validation" method="post"
                                      class="kt-form" enctype="multipart/form-data"
                                      action="{{route('finishSave',$editproduct->id)}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="kt-portlet__body">

                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.status')}}</label>
                                                <select class="form-control" name="prodstatus">
                                                    <option value="0"
                                                            @if($editproduct->is_active==0) selected @endif>{{__('adminMessage.notpublished')}}</option>
                                                    <option value="1"
                                                            @if($editproduct->is_active==1) selected @endif>{{__('adminMessage.published')}}</option>
                                                    <option value="2"
                                                            @if($editproduct->is_active==2) selected @endif>{{__('adminMessage.publishedpreorder')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.min_purchase_qty')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('min_purchase_qty')) is-invalid @endif"
                                                       name="min_purchase_qty"
                                                       value="{{$editproduct->min_purchase_qty?$editproduct->min_purchase_qty:old('min_purchase_qty')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_min_purchase_qty')}}"/>
                                                @if($errors->has('min_purchase_qty'))
                                                    <div class="invalid-feedback">{{ $errors->first('min_purchase_qty') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.max_purchase_qty')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('max_purchase_qty')) is-invalid @endif"
                                                       name="max_purchase_qty"
                                                       value="{{$editproduct->max_purchase_qty?$editproduct->max_purchase_qty:old('max_purchase_qty')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_max_purchase_qty')}}"/>
                                                @if($errors->has('max_purchase_qty'))
                                                    <div class="invalid-feedback">{{ $errors->first('max_purchase_qty') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.alert_min_qty')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('alert_min_qty')) is-invalid @endif"
                                                       name="alert_min_qty"
                                                       value="{{$editproduct->alert_min_qty?$editproduct->alert_min_qty:old('alert_min_qty')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_alert_min_qty')}}"/>
                                                @if($errors->has('alert_min_qty'))
                                                    <div class="invalid-feedback">{{ $errors->first('alert_min_qty') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.sectionhome')}}</label>
                                                <select class="form-control" name="homesection">
                                                    <option value="0"
                                                            @if($editproduct->homesection==0) selected @endif>{{__('adminMessage.none')}}</option>
                                                    @if(!empty($listSections))
                                                        @foreach($listSections as $listSection)
                                                            <option value="{{$listSection->id}}"
                                                                    @if($editproduct->homesection==$listSection->id) selected @endif>{{$listSection->title_en}}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                            @if(!empty($manufacturerLists) && count($manufacturerLists)>0)
                                                <div class="col-lg-3">
                                                    <label>{{__('adminMessage.manufacturer')}}</label>
                                                    <select class="form-control" name="manufacturer">
                                                        <option value="0"
                                                                @if($editproduct->manufacturer_id==0) selected @endif>{{__('adminMessage.none')}}</option>
                                                        @foreach($manufacturerLists as $manufacturerList)
                                                            <option value="{{$manufacturerList->id}}"
                                                                    @if($editproduct->manufacturer_id==$manufacturerList->id) selected @endif>{{$manufacturerList->title_en}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            @if(!empty($brandLists) && count($brandLists)>0)
                                                <div class="col-lg-3">
                                                    <label>{{__('adminMessage.brand')}}</label>
                                                    <select class="form-control" name="brand">
                                                        <option value="0"
                                                                @if($editproduct->brand_id==0) selected @endif>{{__('adminMessage.none')}}</option>

                                                        @foreach($brandLists as $brandList)
                                                            <option value="{{$brandList->id}}"
                                                                    @if($editproduct->brand_id==$brandList->id) selected @endif>{{$brandList->title_en}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.youtube_url')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('youtube_url')) is-invalid @endif"
                                                       name="youtube_url"
                                                       value="{{$editproduct->youtube_url?$editproduct->youtube_url:old('youtube_url')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_youtube_url')}}"/>
                                                @if($errors->has('youtube_url'))
                                                    <div class="invalid-feedback">{{ $errors->first('youtube_url') }}</div>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.countdown_datetime')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('countdown_datetime')) is-invalid @endif"
                                                       name="countdown_datetime"
                                                       value="{{$editproduct->countdown_datetime?$editproduct->countdown_datetime:old('countdown_datetime')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_countdown_datetime')}}"
                                                       id="countdown_datetime"/>
                                                @if($errors->has('countdown_datetime'))
                                                    <div class="invalid-feedback">{{ $errors->first('countdown_datetime') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
                                                <label>{{__('adminMessage.countdown_price')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('countdown_price')) is-invalid @endif"
                                                       name="countdown_price"
                                                       value="{{$editproduct->countdown_price?$editproduct->countdown_price:old('countdown_price')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_countdown_price')}}"/>
                                                @if($errors->has('countdown_price'))
                                                    <div class="invalid-feedback">{{ $errors->first('countdown_price') }}</div>
                                                @endif
                                            </div>

                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.caption_name')}}(En)</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('caption_en')) is-invalid @endif"
                                                       name="caption_en"
                                                       value="{{$editproduct->caption_en?$editproduct->caption_en:old('caption_en')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_caption_en')}}"
                                                       id="caption_en"/>
                                                @if($errors->has('caption_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('caption_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.caption_name')}}(Ar)</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('caption_ar')) is-invalid @endif"
                                                       name="caption_ar"
                                                       value="{{$editproduct->caption_ar?$editproduct->caption_ar:old('caption_ar')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_caption_ar')}}"/>
                                                @if($errors->has('caption_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('caption_ar') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <label>{{__('adminMessage.caption_color')}}</label>
                                                <input type="text"
                                                       class="form-control @if($errors->has('caption_color')) is-invalid @endif demo"
                                                       name="caption_color"
                                                       value="{{$editproduct->caption_color?$editproduct->caption_color:old('caption_color')}}"
                                                       autocomplete="off"
                                                       placeholder="{{__('adminMessage.enter_caption_color')}}"
                                                       data-control="hue" id="hue-demo"/>
                                                @if($errors->has('caption_color'))
                                                    <div class="invalid-feedback">{{ $errors->first('caption_color') }}</div>
                                                @endif
                                            </div>

                                        </div>


                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions">
                                            <button type="submit"
                                                    class="btn btn-success">{{__('adminMessage.saveandfinish')}}</button>
                                            <button type="button"
                                                    onClick="Javascript:window.location.href='{{url('gwc/product/'.request()->id.'/seo-tags')}}'"
                                                    class="btn btn-secondary kt-pull-right cancelbtn">{{__('adminMessage.backtoseotags')}}</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        <!-- product finish end -->


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
@if(Request::is('gwc/product/*/edit')==true)
    <!--begin::Page Vendors(used by this page) -->
@endif
@if(Request::is('gwc/product/*/finish')==true)
    <!--begin::Page Vendors(used by this page) -->
    <script src="{{url('admin_assets/assets/plugins/minicolors/jquery.minicolors.min.js')}}"
            type="text/javascript"></script>
    <script src="{{url('admin_assets/assets/plugins/minicolors/components-color-pickers.min.js')}}"
            type="text/javascript"></script>
@endif
<!--end::Page Vendors -->


<script>
    jQuery(document).ready(function () {
        //
        $("#is_attribute").change(function () {
            var attribute_status = $(this).val();
            if (attribute_status == 1) {
                $("#box-options").show();
                // $("#box-options-button").show();
                $("#box-display-options").show();
                $("#box-display-quantits").hide();
            } else {
                $("#box-options").hide();
                // $("#box-options-button").hide();
                $("#box-display-options").hide();
                $("#box-display-quantits").show();
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
            height: 200
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
        $('#kt_repeater_{{$chosenCustomOption->chosen_id}}').repeater({
            initEmpty: false,
            defaultValues: {
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
                $('.doc_date').datepicker({format: "yyyy-mm-dd"});
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
                $('.doc_date').datepicker({format: "yyyy-mm-dd"});
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });


        $('#countdown_datetime').datepicker({format: "yyyy-mm-dd"});
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
            tagify_en = new Tagify(tags_en, {
            whitelist: {!!$tags_en_js_k!!},
            blacklist: []
        })
        var tags_ar = document.getElementById('tags_ar');
        tagify_ar = new Tagify(tags_ar, {
                whitelist: {!!$tags_ar_js_k!!},
                blacklist: []
            })
            <!--end tags -->
        @endif
    });
</script>

<script src="{!! url('admin_assets/jquery.form.js') !!}" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {


        $(document).on("keyup", "#cost_price", function () {
            var cost_price = $(this).val();
            var retail_price = $("#retail_price").val();
            var profit = parseFloat(retail_price) - parseFloat(cost_price);
            $("#profitprice").html('&nbsp;(Profit : ' + profit + ' {{\App\Currency::default()}})');
        });


        $('#galleryImageForm').ajaxForm({
            beforeSend: function () {
                $('#success').empty();
                $('.progress-bar').text('0%');
                $('.progress-bar').css('width', '0%');
            },
            uploadProgress: function (event, position, total, percentComplete) {
                $('.progress-bar').text(percentComplete + '0%');
                $('.progress-bar').css('width', percentComplete + '0%');
            },
            success: function (data) {
                if (data.success != '1') {
                    toastr.success(data.success);
                    $('#success').html(data.image);
                    $('.progress-bar').text('Uploaded');
                    $('.progress-bar').css('width', '100%');
                    $('#file').val('');
                    window.setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 5000);
                } else {
                    $('#success').hide();
                    $('.progress-bar').hide();
                    $('#file').val('');
                    window.setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 1);
                }
            }
        });
    });
</script>

</body>

<!-- end::Body -->
</html>