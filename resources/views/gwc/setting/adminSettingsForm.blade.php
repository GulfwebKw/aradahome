@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>
    <meta charset="utf-8"/>
    <title>{{ __('adminMessage.websiteName') }} | {{ __('adminMessage.generalsettings') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
@include('gwc.css.user')
<!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
        class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if (!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        @php
            $settingDetailsMenu = App\Http\Controllers\AdminDashboardController::getSettingsDetails();
        @endphp
        <a href="{{ url('/gwc/home') }}">
            @if ($settingDetailsMenu['logo'])
                <img alt="{{ __('adminMessage.websiteName') }}"
                     src="{!! url('uploads/logo/' . $settingDetailsMenu['logo']) !!}" height="40"/>
            @endif
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left"
                id="kt_aside_mobile_toggler"><span></span></button>

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
                            <h3 class="kt-subheader__title">{{ __('adminMessage.systems') }}</h3>
                            <span class="kt-subheader__separator kt-hidden"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{ url('gwc/home') }}" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;"
                                   class="kt-subheader__breadcrumbs-link">{{ __('adminMessage.generalsettings') }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- end:: Subheader -->

                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

                    @include('gwc.includes.alert')

                    @if (auth()->guard('admin')->user()->can('general-settings-edit'))
                        <form name="tFrm" id="form_validation" method="post" class="kt-form"
                              enctype="multipart/form-data"
                              action="{{ route('general-settings.update', $settingDetails->keyname) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-md-6">

                                    <!--begin::Portlet-->
                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    {{ __('adminMessage.storedetails') }}
                                                </h3>

                                            </div>
                                            <button type="submit" class="btn btn-success"
                                                    style="float:right;">{{ __('adminMessage.save') }}</button>
                                        </div>

                                        <!--begin::Form-->

                                        <div class="kt-portlet__body">
                                            <div class="form-group ">
                                                <h5>{{ __('adminMessage.websitesetting') }}</h5>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.auto_change_order_status') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.auto_change_order_status_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->auto_change_order_status) ? 'checked' : '' }}
                                                                           type="checkbox" id="auto_change_order_status"
                                                                           name="auto_change_order_status"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.supplier_registration') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_supplier_registration_allowed') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->supplier_registration) ? 'checked' : '' }}
                                                                           type="checkbox" id="supplier_registration"
                                                                           name="supplier_registration"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_admin_menu_minimize') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_admin_menu_minimize_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_admin_menu_minimize) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_admin_menu_minimize"
                                                                           name="is_admin_menu_minimize"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_show_tags') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_show_tags_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_show_tags) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_show_tags"
                                                                           name="is_show_tags"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label class="col-10">Active Discount</label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_discount) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_discount"
                                                                           name="is_discount"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">Active Express delivery
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="Active Express delivery for flat rate shipping method">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_express) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_express"
                                                                           name="is_express"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_review_active') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_review_active_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_review_active) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_review_active"
                                                                           name="is_review_active"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_more_menu') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_more_menu_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_more_menu) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_more_menu"
                                                                           name="is_more_menu"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_offer_menu') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_offer_menu_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_offer_menu) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_offer_menu"
                                                                           name="is_offer_menu"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_home_popup') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_home_popup_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_home_popup) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_home_popup"
                                                                           name="is_home_popup"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_cart_popup') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_cart_popup_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_cart_popup) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_cart_popup"
                                                                           name="is_cart_popup"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_float_whatsapp') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_float_whatsapp_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_float_whatsapp) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_float_whatsapp"
                                                                           name="is_float_whatsapp"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_float_store_location') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_float_store_location_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_float_store_location) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_float_store_location"
                                                                           name="is_float_store_location"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_new_item_active') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.is_new_item_active_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_new_item_active) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_new_item_active"
                                                                           name="is_new_item_active"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.new_item_badge') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}"
                                                           data-html="true"
                                                           data-content="{{ __('adminMessage.new_item_badge') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->new_item_badge) ? 'checked' : '' }}
                                                                           type="checkbox" id="new_item_badge"
                                                                           name="new_item_badge"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_language_active') }}<a
                                                                href="javascript:;" data-toggle="kt-popover"
                                                                data-trigger="focus"
                                                                title="{{ __('adminMessage.note') }}"
                                                                data-html="true"
                                                                data-content="{{ __('adminMessage.is_language_active_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_lang) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_lang"
                                                                           name="is_lang"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('adminMessage.is_brand_active') }}<a
                                                                href="javascript:;" data-toggle="kt-popover"
                                                                data-trigger="focus"
                                                                title="{{ __('adminMessage.note') }}"
                                                                data-html="true"
                                                                data-content="{{ __('adminMessage.is_brand_active_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch">
                                                                <label><input value="1"
                                                                              {{ !empty($settingDetails->is_brand_active) ? 'checked' : '' }}
                                                                              type="checkbox" id="is_brand_active"
                                                                              name="is_brand_active"><span></span></label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-8">{{ __('adminMessage.showbrandimagetitle') }}<a
                                                                href="javascript:;" data-toggle="kt-popover"
                                                                data-trigger="focus"
                                                                title="{{ __('adminMessage.note') }}"
                                                                data-html="true"
                                                                data-content="{{ __('adminMessage.showbrandimagetitle_note') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                        <label>Logo</label>
                                                        <span class="kt-switch">
                                                                <label><input value="image"
                                                                              {{ !empty($settingDetails->is_brand_image_name) && $settingDetails->is_brand_image_name == 'image' ? 'checked' : '' }}
                                                                              type="radio" id="is_brand_active"
                                                                              name="is_brand_image_name"><span></span></label>
                                                            </span>
                                                    </div>
                                                    <div class="col-2" align="right">
                                                        <label>Name</label>
                                                        <span class="kt-switch">
                                                                <label><input value="title"
                                                                              {{ !empty($settingDetails->is_brand_image_name) && $settingDetails->is_brand_image_name == 'title' ? 'checked' : '' }}
                                                                              type="radio" id="is_brand_active"
                                                                              name="is_brand_image_name"><span></span></label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.websitename_en') }}</label>
                                                        <input type="text"
                                                               class="form-control @if ($errors->has('name_en')) is-invalid @endif"
                                                               name="name_en"
                                                               placeholder="{{ __('adminMessage.enter_websitename_en') }}"
                                                               value="@if ($settingDetails->name_en){{ $settingDetails->name_en }}@endif">
                                                        @if ($errors->has('name_en'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('name_en') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.websitename_ar') }}</label>
                                                        <input dir="rtl" type="text"
                                                               class="form-control @if ($errors->has('name_ar')) is-invalid @endif"
                                                               name="name_ar"
                                                               placeholder="{{ __('adminMessage.enter_websitename_ar') }}"
                                                               value="@if ($settingDetails->name_ar){{ $settingDetails->name_ar }}@endif">
                                                        @if ($errors->has('name_ar'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('name_ar') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--website logo -->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ trans('theme')['theme' . $theme]['logo_image'] }}</label>
                                                        <div class="custom-file @if ($errors->has('logo')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('logo')) is-invalid @endif"
                                                                   id="logo" name="logo">
                                                            <label class="custom-file-label"
                                                                   for="logo">{{ __('adminMessage.chooseImage') }}</label>
                                                        </div>
                                                        @if ($errors->has('logo'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('logo') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->logo)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->logo) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deleteLogo/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ trans('theme')['theme' . $theme]['footer_logo_image'] }}</label>
                                                        <div class="custom-file @if ($errors->has('footerlogo')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('footerlogo')) is-invalid @endif"
                                                                   id="footerlogo" name="footerlogo">
                                                            <label class="custom-file-label"
                                                                   for="footerlogo">{{ __('adminMessage.chooseImage') }}</label>
                                                        </div>
                                                        @if ($errors->has('footerlogo'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('footerlogo') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->footerlogo)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->footerlogo) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deleteFooterLogo/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--email logo-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ trans('theme')['theme' . $theme]['email_logo_image'] }}</label>
                                                        <div class="custom-file @if ($errors->has('emaillogo')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('emaillogo')) is-invalid @endif"
                                                                   id="emaillogo" name="emaillogo">
                                                            <label class="custom-file-label"
                                                                   for="emaillogo">{{ __('adminMessage.chooseImage') }}</label>
                                                        </div>
                                                        @if ($errors->has('emaillogo'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('emaillogo') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->emaillogo)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->emaillogo) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deleteEmailLogo/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--favicon -->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ __('adminMessage.favicon') }}</label>
                                                        <div class="custom-file @if ($errors->has('favicon')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('favicon')) is-invalid @endif"
                                                                   id="favicon" name="favicon">
                                                            <label class="custom-file-label"
                                                                   for="image">{{ __('adminMessage.chooseImageFavicon') }}</label>
                                                        </div>
                                                        @if ($errors->has('favicon'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('favicon') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->favicon)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->favicon) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deletefavicon/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--default header image -->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ trans('theme')['theme' . $theme]['default_header_image'] }}</label>
                                                        <div class="custom-file @if ($errors->has('header_image')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('header_image')) is-invalid @endif"
                                                                   id="header_image" name="header_image">
                                                            <label class="custom-file-label"
                                                                   for="header_image">{{ __('adminMessage.chooseImageHeader') }}</label>
                                                        </div>
                                                        @if ($errors->has('header_image'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('header_image') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->header_image)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->header_image) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deleteheaderimg/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span
                                                                class="input-group-text"
                                                                style="width:300px;">{{ __('adminMessage.is_watermark') }}</span>
                                                    </div>
                                                    <div class="form-control" align="right" style="border:0;">
                                                            <span class="kt-switch"
                                                                  style="margin-top:-6px;"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->is_watermark) ? 'checked' : '' }}
                                                                           type="checkbox" id="is_watermark"
                                                                           name="is_watermark"><span></span></label></span>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>{{ trans('theme')['theme' . $theme]['watermarkimage'] }}</label>
                                                        <div class="custom-file @if ($errors->has('watermark_img')) is-invalid @endif">
                                                            <input type="file"
                                                                   class="custom-file-input @if ($errors->has('watermark_img')) is-invalid @endif"
                                                                   id="watermark_img" name="watermark_img"> <label
                                                                    class="custom-file-label"
                                                                    for="watermark_img">{{ __('adminMessage.chooseImagewatermark') }}</label>
                                                        </div>
                                                        @if ($errors->has('watermark_img'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('watermark_img') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($settingDetails->watermark_img)
                                                            <img src="{!! url('uploads/logo/' . $settingDetails->watermark_img) !!}"
                                                                 width="40">
                                                            <a href="javascript:;" data-toggle="kt-popover"
                                                               data-trigger="focus"
                                                               title="{{ __('adminMessage.alert') }}"
                                                               data-html="true"
                                                               data-content="{{ __('adminMessage.areyousuretodelete') }}<br><br><a href='{{ url('gwc/settings/deletewatermark/') }}' class='btn btn-brand btn-danger btn-icon-sm btn-sm'>{{ __('adminMessage.yes') }}</a>"
                                                               class="btn btn-brand btn-danger btn-icon-sm btn-sm"><i
                                                                        class="la la-trash"></i>{{ __('adminMessage.delete') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group ">
                                                <h5>{{ __('adminMessage.seo') }}</h5>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('adminMessage.seodescription_en') }}</label>
                                                <textarea rows="5" type="text"
                                                          class="form-control  @if ($errors->has('seo_description_en')) is-invalid @endif"
                                                          name="seo_description_en"
                                                          placeholder="{{ __('adminMessage.enterseodescription_en') }}">@if ($settingDetails->seo_description_en){!! $settingDetails->seo_description_en !!}@endif</textarea>
                                                @if ($errors->has('seo_description_en'))
                                                    <div class="
                                                        invalid-feedback">{{ $errors->first('seo_description_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('adminMessage.seodescription_ar') }}</label>
                                                <textarea rows="5" dir="rtl" type="text"
                                                          class="form-control  @if ($errors->has('seo_description_ar')) is-invalid @endif"
                                                          name="seo_description_ar"
                                                          placeholder="{{ __('adminMessage.enterseodescription_ar') }}">@if ($settingDetails->seo_description_ar){!! $settingDetails->seo_description_ar !!}@endif</textarea>
                                                @if ($errors->has('seo_description_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('seo_description_ar') }}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('adminMessage.seokeywords_en') }}</label>
                                                <input type="text"
                                                       class="form-control  @if ($errors->has('seo_keywords_en')) is-invalid @endif"
                                                       name="seo_keywords_en"
                                                       placeholder="{{ __('adminMessage.enterseokeywords_en') }}"
                                                       value="@if ($settingDetails->seo_keywords_en){{ $settingDetails->seo_keywords_en }}@endif">
                                                @if ($errors->has('seo_keywords_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('seo_keywords_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('adminMessage.seokeywords_ar') }}</label>
                                                <input dir="rtl" type="text"
                                                       class="form-control  @if ($errors->has('seo_keywords_ar')) is-invalid @endif"
                                                       name="seo_keywords_ar"
                                                       placeholder="{{ __('adminMessage.enterseokeywords_ar') }}"
                                                       value="@if ($settingDetails->seo_keywords_ar){{ $settingDetails->seo_keywords_ar }}@endif">
                                                @if ($errors->has('seo_keywords_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('seo_keywords_ar') }}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>{{ __('adminMessage.extra_meta_tags') }}</label>
                                                        <textarea rows="10"
                                                                  class="form-control  @if ($errors->has('extra_meta_tags')) is-invalid @endif"
                                                                  rows="3" name="extra_meta_tags"
                                                                  placeholder="{{ __('adminMessage.enter_extra_meta_tags') }}">@if ($settingDetails->extra_meta_tags){!! $settingDetails->extra_meta_tags !!}@endif</textarea>
                                                        @if ($errors->has('extra_meta_tags'))
                                                            <div class="invalid-feedback">{{ $errors->first('extra_meta_tags') }}</div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="form-group "><h5>{{ __('adminMessage.address') }}</h5></div>
                                            <div class="form-group">
                                                <label>{{ __('adminMessage.owner_name') }}</label>
                                                <input type="text"
                                                       class="form-control  @if ($errors->has('owner_name')) is-invalid @endif"
                                                       name="owner_name"
                                                       placeholder="{{ __('adminMessage.enterowner_name') }}"
                                                       value="@if ($settingDetails->owner_name){{ $settingDetails->owner_name }}@endif">
                                                @if ($errors->has('owner_name'))
                                                    <div class="invalid-feedback">{{ $errors->first('owner_name') }}</div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('adminMessage.map_embed_url') }}</label>
                                                <input type="text"
                                                       class="form-control  @if ($errors->has('map_embed_url')) is-invalid @endif"
                                                       name="map_embed_url"
                                                       placeholder="{{ __('adminMessage.enter_map_embed_url') }}"
                                                       value="@if ($settingDetails->map_embed_url){{ $settingDetails->map_embed_url }}@endif">
                                                @if ($errors->has('map_embed_url'))
                                                    <div class="invalid-feedback">{{ $errors->first('map_embed_url') }}</div>
                                                @endif
                                            </div>


                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.address_en') }}</label>
                                                        <textarea
                                                                class="form-control  @if ($errors->has('address_en')) is-invalid @endif"
                                                                rows="3" name="address_en"
                                                                placeholder="{{ __('adminMessage.enter_address_en') }}">@if ($settingDetails->address_en){!! $settingDetails->address_en !!}@endif</textarea>
                                                        @if ($errors->has('address_en'))
                                                            <div class="invalid-feedback">{{ $errors->first('address_en') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.address_ar') }}</label>
                                                        <textarea dir="rtl"
                                                                  class="form-control  @if ($errors->has('address_ar')) is-invalid @endif"
                                                                  rows="3" name="address_ar"
                                                                  placeholder="{{ __('adminMessage.enter_address_ar') }}">@if ($settingDetails->address_ar){!! $settingDetails->address_ar !!}@endif</textarea>
                                                        @if ($errors->has('address_ar'))
                                                            <div class="invalid-feedback">{{ $errors->first('address_ar') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.email') }}</label>
                                                        <input type="text"
                                                               class="form-control  @if ($errors->has('email')) is-invalid @endif"
                                                               name="email"
                                                               placeholder="{{ __('adminMessage.enter_email') }}"
                                                               value="@if ($settingDetails->email){{ $settingDetails->email }}@endif">
                                                        @if ($errors->has('email'))
                                                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.mobile') }}</label>
                                                        <input type="text"
                                                               class="form-control  @if ($errors->has('mobile')) is-invalid @endif"
                                                               name="mobile"
                                                               placeholder="{{ __('adminMessage.enter_mobile') }}"
                                                               value="@if ($settingDetails->mobile){{ $settingDetails->mobile }}@endif">
                                                        @if ($errors->has('mobile'))
                                                            <div class="invalid-feedback">{{ $errors->first('mobile') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>{{ __('adminMessage.phone') }}</label>
                                                        <input type="text"
                                                               class="form-control  @if ($errors->has('phone')) is-invalid @endif"
                                                               name="phone"
                                                               placeholder="{{ __('adminMessage.enter_phone') }}"
                                                               value="@if ($settingDetails->phone){{ $settingDetails->phone }}@endif">
                                                        @if ($errors->has('phone'))
                                                            <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.office_hours_en') }}</label>
                                                        <input type="text"
                                                               class="form-control  @if ($errors->has('office_hours_en')) is-invalid @endif"
                                                               name="office_hours_en"
                                                               placeholder="{{ __('adminMessage.enter_office_hours_en') }}"
                                                               value="@if ($settingDetails->office_hours_en){{ $settingDetails->office_hours_en }}@endif">
                                                        @if ($errors->has('office_hours_en'))
                                                            <div class="invalid-feedback">{{ $errors->first('office_hours_en') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{ __('adminMessage.office_hours_ar') }}</label>
                                                        <input dir="rtl" type="text"
                                                               class="form-control  @if ($errors->has('office_hours_ar')) is-invalid @endif"
                                                               name="office_hours_ar"
                                                               placeholder="{{ __('adminMessage.enter_office_hours_ar') }}"
                                                               value="@if ($settingDetails->office_hours_ar){{ $settingDetails->office_hours_ar }}@endif">
                                                        @if ($errors->has('office_hours_ar'))
                                                            <div class="invalid-feedback">{{ $errors->first('office_hours_ar') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if (count($sociallinks))
                                                <input type="hidden" name="socialsfields"
                                                       value="{{ implode(',', $sociallinks) }}">
                                                <div class="form-group "><h5>{{ __('adminMessage.sociallinks') }}</h5>
                                                </div>
                                                @foreach ($sociallinks as $sociallinks)
                                                    <div class="form-group ">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span
                                                                        class="input-group-text"
                                                                        style="width:100px;">{{ __('adminMessage.' . $sociallinks) }}</span>
                                                            </div>

                                                            <input placeholder="{{ __('adminMessage.' . $sociallinks . '_place') }}"
                                                                   type="text" class="form-control"
                                                                   name="social_{{ $sociallinks }}"
                                                                   value="@if ($settingDetails->social_facebook && $sociallinks == 'facebook'){{ $settingDetails->social_facebook }}@elseif($settingDetails->social_twitter && $sociallinks=='twitter'){{ $settingDetails->social_twitter }}@elseif($settingDetails->social_instagram && $sociallinks=='instagram'){{ $settingDetails->social_instagram }}@elseif($settingDetails->social_linkedin && $sociallinks=='linkedin'){{ $settingDetails->social_linkedin }}@elseif($settingDetails->social_youtube && $sociallinks=='youtube'){{ $settingDetails->social_youtube }}@elseif($settingDetails->social_whatsapp && $sociallinks=='whatsapp'){{ $settingDetails->social_whatsapp }}@endif">

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div class="form-group "><h5>{{ __('adminMessage.googleanalytics') }}</h5>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>{{ __('adminMessage.googleanalyticseokbed') }}</label>
                                                        <textarea rows="15"
                                                                  class="form-control  @if ($errors->has('address_en')) is-invalid @endif"
                                                                  rows="3" name="google_analytics"
                                                                  placeholder="{{ __('adminMessage.enter_google_analytics') }}">@if ($settingDetails->google_analytics){!! $settingDetails->google_analytics !!}@endif</textarea>
                                                        @if ($errors->has('google_analytics'))
                                                            <div class="invalid-feedback">{{ $errors->first('google_analytics') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span
                                                                class="input-group-text">{{ __('adminMessage.google_profileid') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('google_profileid')) is-invalid @endif"
                                                           name="google_profileid"
                                                           value="@if ($settingDetails->google_profileid){{ $settingDetails->google_profileid }}@endif"
                                                           placeholder="Enter Google Analytics Profile ID">
                                                    @if ($errors->has('google_profileid'))
                                                        <div class="invalid-feedback">{{ $errors->first('google_profileid') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span
                                                                class="input-group-text">{{ __('adminMessage.google_analyticsemail') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('google_profileid')) is-invalid @endif"
                                                           name="google_analyticsemail"
                                                           value="@if ($settingDetails->google_analyticsemail){{ $settingDetails->google_analyticsemail }}@endif"
                                                           placeholder="Enter Google Analytics Email">
                                                    @if ($errors->has('google_analyticsemail'))
                                                        <div class="invalid-feedback">{{ $errors->first('google_analyticsemail') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span
                                                                class="input-group-text">{{ __('adminMessage.keysp12') }}</span>
                                                    </div>

                                                    <div class="custom-file @if ($errors->has('gakeys')) is-invalid @endif">
                                                        <input type="file"
                                                               class="custom-file-input @if ($errors->has('header_image')) is-invalid @endif"
                                                               id="gakeys" name="gakeys">
                                                        <label class="custom-file-label"
                                                               for="gakeys">{{ __('adminMessage.chooseFile') }}</label>
                                                    </div>
                                                    @if ($errors->has('gakeys'))
                                                        <div class="invalid-feedback">{{ $errors->first('gakeys') }}</div>
                                                    @endif
                                                </div>
                                                @if ($settingDetails->gakeys){{ $settingDetails->gakeys }}@endif
                                            </div>


                                            <div class="form-group "><h5>{{ __('adminMessage.appversion') }}</h5></div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.IOS') }}(OLD)</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('ios_old_version')) is-invalid @endif"
                                                           name="ios_old_version"
                                                           value="@if ($settingDetails->ios_old_version){{ $settingDetails->ios_old_version }}@endif"
                                                           placeholder="Enter Previous Version">
                                                    @if ($errors->has('ios_old_version'))
                                                        <div class="invalid-feedback">{{ $errors->first('ios_old_version') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.IOS') }}(NEW)</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('ios_new_version')) is-invalid @endif"
                                                           name="ios_new_version"
                                                           value="@if ($settingDetails->ios_new_version){{ $settingDetails->ios_new_version }}@endif"
                                                           placeholder="Enter Current Version">
                                                    @if ($errors->has('ios_new_version'))
                                                        <div class="invalid-feedback">{{ $errors->first('ios_new_version') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.Android') }}(OLD)</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('android_old_version')) is-invalid @endif"
                                                           name="android_old_version"
                                                           value="@if ($settingDetails->android_old_version){{ $settingDetails->android_old_version }}@endif"
                                                           placeholder="Enter Previous Version">
                                                    @if ($errors->has('android_old_version'))
                                                        <div class="invalid-feedback">{{ $errors->first('android_old_version') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.Android') }}(NEW)</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('android_new_version')) is-invalid @endif"
                                                           name="android_new_version"
                                                           value="@if ($settingDetails->android_new_version){{ $settingDetails->android_new_version }}@endif"
                                                           placeholder="Enter Current Version">
                                                    @if ($errors->has('android_new_version'))
                                                        <div class="invalid-feedback">{{ $errors->first('android_new_version') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:100px;">{{ __('adminMessage.ios_url') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('ios_url')) is-invalid @endif"
                                                           name="ios_url"
                                                           value="@if ($settingDetails->ios_url){{ $settingDetails->ios_url }}@endif"
                                                           placeholder="Enter IOS App Url">
                                                    @if ($errors->has('ios_url'))
                                                        <div class="invalid-feedback">{{ $errors->first('ios_url') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:100px;">{{ __('adminMessage.android_url') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('android_url')) is-invalid @endif"
                                                           name="android_url"
                                                           value="@if ($settingDetails->android_url){{ $settingDetails->android_url }}@endif"
                                                           placeholder="Enter Android App Url">
                                                    @if ($errors->has('android_url'))
                                                        <div class="invalid-feedback">{{ $errors->first('android_url') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:100px;">Huawei App</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('huawei_url')) is-invalid @endif"
                                                           name="huawei_url"
                                                           value="@if ($settingDetails->huawei_url){{ $settingDetails->huawei_url }}@endif"
                                                           placeholder="Enter Huawei App Gallery Url">
                                                    @if ($errors->has('huawei_url'))
                                                        <div class="invalid-feedback">{{ $errors->first('huawei_url') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group"><h5>{{ __('adminMessage.freedelivery') }}</h5>
                                                <small>{{ __('adminMessage.freedelivery_note') }}</small></div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:100px;height:40px;">{{ __('adminMessage.amount') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           style="width:200px;height:40px;padding:5px;border:1px #ccc solid;"
                                                           class=" @if ($errors->has('free_delivery_amount')) is-invalid @endif"
                                                           name="free_delivery_amount"
                                                           value="@if ($settingDetails->free_delivery_amount){{ $settingDetails->free_delivery_amount }}@endif"
                                                           placeholder="Enter Amount">
                                                    @if ($errors->has('free_delivery_amount'))
                                                        <div class="invalid-feedback">{{ $errors->first('free_delivery_amount') }}</div>
                                                    @endif
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:100px;height:40px;">Active&nbsp;<input
                                                                    @if (!empty($settingDetails->is_free_delivery)) checked
                                                                    @endif  type="checkbox" class="form-control"
                                                                    name="is_free_delivery" value="1"
                                                                    style="width:30px;padding:5px;border:0;"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group"><h5>{{ __('adminMessage.instafeed') }}</h5>
                                                <small>{{ __('adminMessage.instafeed_note') }}</small></div>
                                            <div class="form-group ">

                                                <input type="text"
                                                       class="form-control @if ($errors->has('instagram_token')) is-invalid @endif"
                                                       name="instagram_token"
                                                       value="@if ($settingDetails->instagram_token){{ $settingDetails->instagram_token }}@endif"
                                                       placeholder="Enter Instagram Token">
                                                @if ($errors->has('instagram_token'))
                                                    <div class="invalid-feedback">{{ $errors->first('instagram_token') }}</div>
                                                @endif

                                            </div>
                                            <div class="form-group ">

                                                <input type="text"
                                                       class="form-control @if ($errors->has('instagram_clientId')) is-invalid @endif"
                                                       name="instagram_clientId"
                                                       value="@if ($settingDetails->instagram_clientId){{ $settingDetails->instagram_clientId }}@endif"
                                                       placeholder="Enter Instagram Client Id">
                                                @if ($errors->has('instagram_clientId'))
                                                    <div class="invalid-feedback">{{ $errors->first('instagram_clientId') }}</div>
                                                @endif

                                            </div>
                                            <div class="form-group ">

                                                <input type="text"
                                                       class="form-control @if ($errors->has('instagram_userId')) is-invalid @endif"
                                                       name="instagram_userId"
                                                       value="@if ($settingDetails->instagram_userId){{ $settingDetails->instagram_userId }}@endif"
                                                       placeholder="Enter Instagram User id">
                                                @if ($errors->has('instagram_userId'))
                                                    <div class="invalid-feedback">{{ $errors->first('instagram_userId') }}</div>
                                                @endif

                                            </div>
                                            <div class="form-group"><h5>{{ __('adminMessage.item_list_box') }}</h5>
                                                <small>{{ __('adminMessage.item_list_box_note') }}</small></div>
                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:138px;height:40px;">Columns(3)&nbsp;<input
                                                                    @if (!empty($settingDetails->column_list) && $settingDetails->column_list == 1) checked
                                                                    @endif  type="radio" class="form-control"
                                                                    name="column_list" value="1"
                                                                    style="width:30px;padding:5px;border:0;"></span>
                                                    </div>
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:138px;height:40px;">Columns(4)&nbsp;<input
                                                                    @if (!empty($settingDetails->column_list) && $settingDetails->column_list == 2) checked
                                                                    @endif  type="radio" class="form-control"
                                                                    name="column_list" value="2"
                                                                    style="width:30px;padding:5px;border:0;"></span>
                                                    </div>
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:138px;height:40px;">Columns(6)&nbsp;<input
                                                                    @if (!empty($settingDetails->column_list) && $settingDetails->column_list == 3) checked
                                                                    @endif  type="radio" class="form-control"
                                                                    name="column_list" value="3"
                                                                    style="width:30px;padding:5px;border:0;"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group "><h5>{{ __('adminMessage.postknet_note') }}</h5>
                                            </div>
                                            <div class="form-group">
                                                <textarea rows="5" type="text"
                                                          class="form-control  @if ($errors->has('postknet_note_en')) is-invalid @endif"
                                                          name="postknet_note_en"
                                                          placeholder="{{ __('adminMessage.enterpostknet_note_en') }}">@if ($settingDetails->postknet_note_en){!! $settingDetails->postknet_note_en !!}@endif</textarea>
                                                @if ($errors->has('postknet_note_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('postknet_note_en') }}</div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <textarea rows="5" dir="rtl" type="text"
                                                          class="form-control  @if ($errors->has('postknet_note_ar')) is-invalid @endif"
                                                          name="postknet_note_ar"
                                                          placeholder="{{ __('adminMessage.enterpostknet_note_ar') }}">@if ($settingDetails->postknet_note_ar){!! $settingDetails->postknet_note_ar !!}@endif</textarea>
                                                @if ($errors->has('postknet_note_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('postknet_note_ar') }}</div>
                                                @endif
                                            </div>


                                            <div class="form-group ">
                                                <h5>Note in checkout page</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <textarea
                                                                    class="form-control  @if ($errors->has('check_out_note_en')) is-invalid @endif"
                                                                    rows="3" name="check_out_note_en">@if ($settingDetails->check_out_note_en){!! $settingDetails->check_out_note_en !!}@endif</textarea>
                                                            @if ($errors->has('check_out_note_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('check_out_note_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <textarea dir="rtl"
                                                                      class="form-control  @if ($errors->has('check_out_note_ar')) is-invalid @endif"
                                                                      rows="3" name="check_out_note_ar">@if ($settingDetails->check_out_note_ar){!! $settingDetails->check_out_note_ar !!}@endif</textarea>
                                                            @if ($errors->has('check_out_note_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('check_out_note_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group ">
                                                <h5>Note for POS Factor</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <textarea
                                                                    class="form-control  @if ($errors->has('pos_note_en')) is-invalid @endif"
                                                                    rows="3" name="pos_note_en">@if ($settingDetails->pos_note_en){!! $settingDetails->pos_note_en !!}@endif</textarea>
                                                            @if ($errors->has('pos_note_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('pos_note_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <textarea dir="rtl"
                                                                      class="form-control  @if ($errors->has('pos_note_ar')) is-invalid @endif"
                                                                      rows="3" name="pos_note_ar">@if ($settingDetails->pos_note_ar){!! $settingDetails->pos_note_ar !!}@endif</textarea>
                                                            @if ($errors->has('pos_note_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('pos_note_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Supervisor Password:</label>
                                                            <input type="password"
                                                                   class="form-control  @if ($errors->has('pos_supervisor_password')) is-invalid @endif"
                                                                   name="pos_supervisor_password"
                                                                   placeholder="If you dont want to change this, leave it empty.">
                                                            @if ($errors->has('pos_supervisor_password'))
                                                                <div class="invalid-feedback">{{ $errors->first('pos_supervisor_password') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="kt-portlet__foot">
                                            <div class="kt-form__actions">
                                                <button type="submit"
                                                        class="btn btn-success">{{ __('adminMessage.save') }}</button>
                                            </div>
                                        </div>


                                        <!--end::Form-->
                                    </div>

                                    <!--end::Portlet-->

                                </div>
                                <div class="col-md-6">

                                    <!--begin::Portlet-->
                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    {{ __('adminMessage.basicsettings') }}
                                                </h3>
                                            </div>
                                            <button type="submit" class="btn btn-success"
                                                    style="float:right;">{{ __('adminMessage.save') }}</button>
                                        </div>

                                        <!--begin::Form-->

                                        <div class="kt-portlet__body">

                                            <div class="form-group "><h5>{{ __('adminMessage.productsettings') }}</h5>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.prefix') }}(XYZ)</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('prefix')) is-invalid @endif"
                                                           name="prefix"
                                                           value="@if ($settingDetails->prefix){{ $settingDetails->prefix }}@endif"
                                                           placeholder="XYZ">
                                                    @if ($errors->has('prefix'))
                                                        <div class="invalid-feedback">{{ $errors->first('prefix') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.item_code_digits') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('item_code_digits')) is-invalid @endif"
                                                           name="item_code_digits"
                                                           value="@if ($settingDetails->item_code_digits){{ $settingDetails->item_code_digits }}@endif">
                                                    @if ($errors->has('item_code_digits'))
                                                        <div class="invalid-feedback">{{ $errors->first('item_code_digits') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.min_order_amount') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('min_order_amount')) is-invalid @endif"
                                                           name="min_order_amount"
                                                           value="@if ($settingDetails->min_order_amount){{ $settingDetails->min_order_amount }}@endif">
                                                    @if ($errors->has('min_order_amount'))
                                                        <div class="invalid-feedback">{{ $errors->first('min_order_amount') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.flat_rate') }}</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control @if ($errors->has('flat_rate')) is-invalid @endif"
                                                           name="flat_rate"
                                                           value="@if ($settingDetails->flat_rate){{ $settingDetails->flat_rate }}@endif">
                                                    @if ($errors->has('flat_rate'))
                                                        <div class="invalid-feedback">{{ $errors->first('flat_rate') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.base_currency') }}</span>
                                                    </div>

                                                    <select name="base_currency"
                                                            class="form-control @if ($errors->has('base_currency')) is-invalid @endif">
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->code }}" {{ $settingDetails->base_currency == $currency->code  ? 'selected' : '' }}>{{ $currency->title_en }} - {{ $currency->symbol }}</option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('default_sort'))
                                                        <div class="invalid-feedback">{{ $errors->first('default_sort') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">Show All Currencies
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                           data-content="Show All Currencies in product details page.">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->show_all_currencies) ? 'checked' : '' }}
                                                                           type="checkbox" id="show_all_currencies"
                                                                           name="show_all_currencies"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.item_per_page_front') }}</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('item_per_page_front')) is-invalid @endif"
                                                           name="item_per_page_front"
                                                           value="@if ($settingDetails->item_per_page_front){{ $settingDetails->item_per_page_front }}@endif">
                                                    @if ($errors->has('item_per_page_front'))
                                                        <div class="invalid-feedback">{{ $errors->first('item_per_page_front') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.item_per_page_back') }}</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('item_per_page_back')) is-invalid @endif"
                                                           name="item_per_page_back"
                                                           value="@if ($settingDetails->item_per_page_back){{ $settingDetails->item_per_page_back }}@endif">
                                                    @if ($errors->has('item_per_page_back'))
                                                        <div class="invalid-feedback">{{ $errors->first('item_per_page_back') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">{{ __('adminMessage.default_sort') }}</span>
                                                    </div>

                                                    <select name="default_sort"
                                                            class="form-control @if ($errors->has('default_sort')) is-invalid @endif">
                                                        @foreach ($sortings as $sorting)
                                                            <option value="{{ $sorting }}" {{ $settingDetails->default_sort == $sorting ? 'selected' : '' }}>{{ $sorting }}</option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('default_sort'))
                                                        <div class="invalid-feedback">{{ $errors->first('default_sort') }}</div>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:200px;">{{ __('adminMessage.image_thumb_wh') }}</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('image_thumb_w')) is-invalid @endif"
                                                           name="image_thumb_w"
                                                           value="@if ($settingDetails->image_thumb_w){{ $settingDetails->image_thumb_w }}@endif">
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('image_thumb_h')) is-invalid @endif"
                                                           name="image_thumb_h"
                                                           value="@if ($settingDetails->image_thumb_h){{ $settingDetails->image_thumb_h }}@endif">

                                                    @if ($errors->has('image_thumb_w'))
                                                        <div class="invalid-feedback">{{ $errors->first('image_thumb_w') }}</div>
                                                    @endif
                                                    @if ($errors->has('image_thumb_h'))
                                                        <div class="invalid-feedback">{{ $errors->first('image_thumb_h') }}</div>
                                                    @endif

                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:200px;">{{ __('adminMessage.image_big_wh') }}</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('image_big_w')) is-invalid @endif"
                                                           name="image_big_w"
                                                           value="@if ($settingDetails->image_big_w){{ $settingDetails->image_big_w }}@endif">
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('image_big_h')) is-invalid @endif"
                                                           name="image_big_h"
                                                           value="@if ($settingDetails->image_big_h){{ $settingDetails->image_big_h }}@endif">

                                                    @if ($errors->has('image_big_w'))
                                                        <div class="invalid-feedback">{{ $errors->first('image_big_w') }}</div>
                                                    @endif
                                                    @if ($errors->has('image_big_h'))
                                                        <div class="invalid-feedback">{{ $errors->first('image_big_h') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">{{ __('Show Out of Stock Products') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                           data-content="{{ __('adminMessage.show_out_of_stock') }}">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->show_out_of_stock) ? 'checked' : '' }}
                                                                           type="checkbox" id="show_out_of_stock"
                                                                           name="show_out_of_stock"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group row">
                                                    <label
                                                            class="col-10">Show Auto New Tag on product
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-trigger="focus"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                           data-content="Calculate difference Date between create item and now , then show 'NEW' Tag. ">{!! trans('svgicon.help') !!}</a></label>
                                                    <div class="col-2" align="right">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->show_new_tag) ? 'checked' : '' }}
                                                                           type="checkbox" id="show_new_tag"
                                                                           name="show_new_tag"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"
                                                                                           style="width:300px;">Days For 'NEW' Tag</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control @if ($errors->has('new_tag_days')) is-invalid @endif"
                                                           name="new_tag_days"
                                                           value="@if ($settingDetails->new_tag_days){{ $settingDetails->new_tag_days }}@endif">
                                                    @if ($errors->has('new_tag_days'))
                                                        <div class="invalid-feedback">{{ $errors->first('new_tag_days') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <h5>Checkout Settings</h5>
                                            </div>
                                            <div class="form-group ">
                                                <h6>Fields Required for Placing an Order</h6>
                                            </div>


                                            <div class="form-group row">
                                                <div class="input-group">
                                                    <label
                                                            class="col-2">{{ __('Name') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                                            <span class="kt-switch"><label>
                                                                    <input value="1"
                                                                           {{ !empty($settingDetails->validate_cust_name) ? 'checked' : '' }}
                                                                           type="checkbox" id="validate_cust_name"
                                                                           name="validate_cust_name"><span></span>
                                                                </label>
                                                            </span>
                                                    </div>
                                                    <label
                                                            class="col-2">{{ __('Mobile') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                                        <span class="kt-switch"><label>
                                                                <input value="1"
                                                                       {{ !empty($settingDetails->validate_cust_mob) ? 'checked' : '' }}
                                                                       type="checkbox" id="validate_cust_mob"
                                                                       name="validate_cust_mob"><span></span>
                                                            </label>
                                                        </span>
                                                    </div>


                                                    <label
                                                            class="col-2">{{ __('Block') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                                    <span class="kt-switch"><label>
                                                            <input value="1"
                                                                   {{ !empty($settingDetails->validate_cust_block) ? 'checked' : '' }}
                                                                   type="checkbox" id="validate_cust_block"
                                                                   name="validate_cust_block"><span></span>
                                                        </label>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="input-group">
                                                    <label
                                                            class="col-2">{{ __('Street') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                                    <span class="kt-switch"><label>
                                                            <input value="1"
                                                                   {{ !empty($settingDetails->validate_cust_st) ? 'checked' : '' }}
                                                                   type="checkbox" id="validate_cust_st"
                                                                   name="validate_cust_st"><span></span>
                                                        </label>
                                                    </span>
                                                    </div>

                                                    <label
                                                            class="col-2">{{ __('House No.') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           title="{{ __('adminMessage.note') }}" data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                                <span class="kt-switch"><label>
                                                        <input value="1"
                                                               {{ !empty($settingDetails->validate_cust_hno) ? 'checked' : '' }}
                                                               type="checkbox" id="validate_cust_hno"
                                                               name="validate_cust_hno"><span></span>
                                                    </label>
                                                </span>
                                                    </div>


                                                    <label
                                                            class="col-2">{{ __('Area') }}
                                                        <a href="javascript:;" data-toggle="kt-popover"
                                                           data-html="true"
                                                        ></a></label>
                                                    <div class="col-2">
                                            <span class="kt-switch"><label>
                                                    <input value="1"
                                                           {{ !empty($settingDetails->validate_cust_area) ? 'checked' : '' }}
                                                           type="checkbox" id="validate_cust_area"
                                                           name="validate_cust_area"><span></span>
                                                </label>
                                            </span>
                                                    </div>
                                                </div>
                                            </div>


                                            @if (count($paymentslink))
                                                @php
                                                    $payments = explode(',', $settingDetails->payments);
                                                @endphp
                                                <div class="form-group ">
                                                    <h5>{{ __('adminMessage.availablepayments') }}</h5></div>
                                                @foreach ($paymentslink as $paymentlink)
                                                    <div class="form-group ">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span
                                                                        class="input-group-text"
                                                                        style="width:370px;">{{ __('adminMessage.' . $paymentlink) }}</span>
                                                            </div>

                                                            <input @if (in_array($paymentlink, $payments)) checked
                                                                   @endif  type="checkbox" class="form-control"
                                                                   name="payments[]" value="{{ $paymentlink }}">

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div class="form-group ">
                                                <h5>{{ __('adminMessage.note_for_new_customer') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <textarea
                                                                    class="form-control  @if ($errors->has('note_for_new_customer_en')) is-invalid @endif"
                                                                    rows="3" name="note_for_new_customer_en"
                                                                    placeholder="{{ __('adminMessage.enter_note_for_new_customer_en') }}">@if ($settingDetails->note_for_new_customer_en){!! $settingDetails->note_for_new_customer_en !!}@endif</textarea>
                                                            @if ($errors->has('note_for_new_customer_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('note_for_new_customer_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <textarea dir="rtl"
                                                                      class="form-control  @if ($errors->has('note_for_new_customer_ar')) is-invalid @endif"
                                                                      rows="3" name="note_for_new_customer_ar"
                                                                      placeholder="{{ __('adminMessage.enter_note_for_new_customer_ar') }}">@if ($settingDetails->note_for_new_customer_ar){!! $settingDetails->note_for_new_customer_ar !!}@endif</textarea>
                                                            @if ($errors->has('note_for_new_customer_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('note_for_new_customer_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <h5>{{ __('adminMessage.note_for_newsletter') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <textarea
                                                                    class="form-control  @if ($errors->has('newsletter_note_en')) is-invalid @endif"
                                                                    rows="3" name="newsletter_note_en"
                                                                    placeholder="{{ __('adminMessage.enter_newsletter_note_en') }}">@if ($settingDetails->newsletter_note_en){!! $settingDetails->newsletter_note_en !!}@endif</textarea>
                                                            @if ($errors->has('newsletter_note_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('newsletter_note_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <textarea dir="rtl"
                                                                      class="form-control  @if ($errors->has('newsletter_note_ar')) is-invalid @endif"
                                                                      rows="3" name="newsletter_note_ar"
                                                                      placeholder="{{ __('adminMessage.enter_newsletter_note_ar') }}">@if ($settingDetails->newsletter_note_ar){!! $settingDetails->newsletter_note_ar !!}@endif</textarea>
                                                            @if ($errors->has('newsletter_note_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('newsletter_note_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group "><h5>{{ __('adminMessage.home_note_1') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note1_title_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note1_title_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_en') }}"
                                                                   value="@if ($settingDetails->home_note1_title_en){!! $settingDetails->home_note1_title_en !!}@endif">
                                                            @if ($errors->has('home_note1_title_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note1_title_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note1_title_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note1_title_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_ar') }}"
                                                                   value="@if ($settingDetails->home_note1_title_ar){!! $settingDetails->home_note1_title_ar !!}@endif">
                                                            @if ($errors->has('home_note1_title_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note1_title_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note1_details_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note1_details_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_en') }}"
                                                                   value="@if ($settingDetails->home_note1_details_en){!! $settingDetails->home_note1_details_en !!}@endif">
                                                            @if ($errors->has('home_note1_details_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note1_details_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note1_details_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note1_details_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_ar') }}"
                                                                   value="@if ($settingDetails->home_note1_details_ar){!! $settingDetails->home_note1_details_ar !!}@endif">
                                                            @if ($errors->has('home_note1_details_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note1_details_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group "><h5>{{ __('adminMessage.home_note_2') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note2_title_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note2_title_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_en') }}"
                                                                   value="@if ($settingDetails->home_note2_title_en){!! $settingDetails->home_note2_title_en !!}@endif">
                                                            @if ($errors->has('home_note2_title_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note2_title_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note2_title_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note2_title_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_ar') }}"
                                                                   value="@if ($settingDetails->home_note2_title_ar){!! $settingDetails->home_note2_title_ar !!}@endif">
                                                            @if ($errors->has('home_note2_title_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note2_title_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note2_details_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note2_details_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_en') }}"
                                                                   value="@if ($settingDetails->home_note2_details_en){!! $settingDetails->home_note2_details_en !!}@endif">
                                                            @if ($errors->has('home_note2_details_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note2_details_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note2_details_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note2_details_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_ar') }}"
                                                                   value="@if ($settingDetails->home_note2_details_ar){!! $settingDetails->home_note2_details_ar !!}@endif">
                                                            @if ($errors->has('home_note2_details_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note2_details_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group "><h5>{{ __('adminMessage.home_note_3') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note3_title_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note3_title_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_en') }}"
                                                                   value="@if ($settingDetails->home_note3_title_en){!! $settingDetails->home_note3_title_en !!}@endif">
                                                            @if ($errors->has('home_note3_title_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note3_title_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note3_title_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note3_title_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_ar') }}"
                                                                   value="@if ($settingDetails->home_note3_title_ar){!! $settingDetails->home_note3_title_ar !!}@endif">
                                                            @if ($errors->has('home_note3_title_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note3_title_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note3_details_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note3_details_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_en') }}"
                                                                   value="@if ($settingDetails->home_note3_details_en){!! $settingDetails->home_note3_details_en !!}@endif">
                                                            @if ($errors->has('home_note3_details_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note3_details_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note3_details_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note3_details_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_ar') }}"
                                                                   value="@if ($settingDetails->home_note3_details_ar){!! $settingDetails->home_note3_details_ar !!}@endif">
                                                            @if ($errors->has('home_note3_details_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note3_details_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group "><h5>{{ __('adminMessage.home_note_4') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note4_title_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note4_title_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_en') }}"
                                                                   value="@if ($settingDetails->home_note4_title_en){!! $settingDetails->home_note4_title_en !!}@endif">
                                                            @if ($errors->has('home_note4_title_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note4_title_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note4_title_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note4_title_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.title_ar') }}"
                                                                   value="@if ($settingDetails->home_note4_title_ar){!! $settingDetails->home_note4_title_ar !!}@endif">
                                                            @if ($errors->has('home_note4_title_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note4_title_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(En)</label>
                                                            <input name="home_note4_details_en" type="text"
                                                                   class="form-control  @if ($errors->has('home_note4_details_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_en') }}"
                                                                   value="@if ($settingDetails->home_note4_details_en){!! $settingDetails->home_note4_details_en !!}@endif">
                                                            @if ($errors->has('home_note4_details_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note4_details_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ trans('adminMessage.details') }}(Ar)</label>
                                                            <input dir="rtl" name="home_note4_details_ar" type="text"
                                                                   class="form-control  @if ($errors->has('home_note4_details_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_ar') }}"
                                                                   value="@if ($settingDetails->home_note4_details_ar){!! $settingDetails->home_note4_details_ar !!}@endif">
                                                            @if ($errors->has('home_note4_details_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('home_note4_details_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--top header note -->
                                            <div class="form-group"><h4>{{ __('adminMessage.top_header_note') }}</h4>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input name="top_header_note_en" type="text"
                                                                   class="form-control  @if ($errors->has('top_header_note_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_en') }}"
                                                                   value="@if ($settingDetails->top_header_note_en){!! $settingDetails->top_header_note_en !!}@endif">
                                                            @if ($errors->has('top_header_note_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('top_header_note_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input dir="rtl" name="top_header_note_ar" type="text"
                                                                   class="form-control  @if ($errors->has('top_header_note_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.details_ar') }}"
                                                                   value="@if ($settingDetails->top_header_note_ar){!! $settingDetails->top_header_note_ar !!}@endif">
                                                            @if ($errors->has('top_header_note_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('top_header_note_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--top header note -->
                                            <div class="form-group"><h4>{{ __('adminMessage.order_note') }}</h4></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea name="order_note_en" type="text"
                                                                      class="form-control  @if ($errors->has('order_note_en')) is-invalid @endif"
                                                                      placeholder="{{ __('adminMessage.details_en') }}">@if ($settingDetails->order_note_en){!! $settingDetails->order_note_en !!}@endif</textarea>
                                                            @if ($errors->has('order_note_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('order_note_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea dir="rtl" name="order_note_ar" type="text"
                                                                      class="form-control  @if ($errors->has('order_note_ar')) is-invalid @endif"
                                                                      placeholder="{{ __('adminMessage.details_ar') }}">@if ($settingDetails->order_note_ar){!! $settingDetails->order_note_ar !!}@endif</textarea>
                                                            @if ($errors->has('order_note_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('order_note_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group"><h4>{{ __('adminMessage.email_template') }}</h4>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                <span class="kt-switch"><label>
            <input value="1"
                   {{ !empty($settingDetails->invoice_template) && $settingDetails->invoice_template == 1 ? 'checked' : '' }} type="radio"
                   id="invoice_template1" name="invoice_template"><span></span></label></span>
                                                        <br>
                                                        <a href="{{ url('uploads/screen1.png') }}" target="_blank"><img
                                                                    width="200" src="{{ url('uploads/screen1.png') }}"></a>
                                                    </div>
                                                    <div class="col-md-6">
                                                <span class="kt-switch"><label>
            <input value="2"
                   {{ !empty($settingDetails->invoice_template) && $settingDetails->invoice_template == 2 ? 'checked' : '' }} type="radio"
                   id="invoice_template2" name="invoice_template"><span></span></label></span>
                                                        <br>
                                                        <a href="{{ url('uploads/screen2.png') }}" target="_blank"><img
                                                                    width="200" src="{{ url('uploads/screen2.png') }}"></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group"><h4>Show Qr-code in invoice</h4>
                                            </div>
                                            <div class="form-group">
                                                <span class="kt-switch"><label>
            <input value="1"
                   {{ !empty($settingDetails->invoice_qrcode) && $settingDetails->invoice_qrcode == 1 ? 'checked' : '' }} type="checkbox"
                   id="invoice_qrcode" name="invoice_qrcode"><span></span></label></span>

                                            </div>

                                            <div class="form-group ">
                                                <h5>{{ __('adminMessage.note_for_quantity_updating') }}</h5></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <textarea
                                                                class="form-control  @if ($errors->has('quantit_update_notification_en')) is-invalid @endif"
                                                                rows="3" name="quantit_update_notification_en"
                                                                placeholder="{{ __('adminMessage.enter_quantit_update_notification_en') }}">@if ($settingDetails->quantit_update_notification_en){!! $settingDetails->quantit_update_notification_en !!}@endif</textarea>
                                                        @if ($errors->has('quantit_update_notification_en'))
                                                            <div class="invalid-feedback">{{ $errors->first('quantit_update_notification_en') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <textarea dir="rtl"
                                                                  class="form-control  @if ($errors->has('quantit_update_notification_ar')) is-invalid @endif"
                                                                  rows="3" name="quantit_update_notification_ar"
                                                                  placeholder="{{ __('adminMessage.enter_quantit_update_notification_ar') }}">@if ($settingDetails->quantit_update_notification_ar){!! $settingDetails->quantit_update_notification_ar !!}@endif</textarea>
                                                        @if ($errors->has('quantit_update_notification_ar'))
                                                            <div class="invalid-feedback">{{ $errors->first('quantit_update_notification_ar') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!--top header note -->
                                            <div class="form-group"><h4>{{ __('adminMessage.fromemailandname') }}</h4>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input name="from_email" type="text"
                                                                   class="form-control  @if ($errors->has('from_email')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.fromemail') }}"
                                                                   value="@if ($settingDetails->from_email){!! $settingDetails->from_email !!}@endif">
                                                            @if ($errors->has('from_email'))
                                                                <div class="invalid-feedback">{{ $errors->first('from_email') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input name="from_name" type="text"
                                                                   class="form-control  @if ($errors->has('from_name')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.fromname') }}"
                                                                   value="@if ($settingDetails->from_name){!! $settingDetails->from_name !!}@endif">
                                                            @if ($errors->has('from_name'))
                                                                <div class="invalid-feedback">{{ $errors->first('from_name') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <h4>{{ __('adminMessage.pushnotificationsetting') }}</h4></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('adminMessage.web_server_key') }}</label>
                                                            <input name="web_server_key" type="text"
                                                                   class="form-control  @if ($errors->has('web_server_key')) is-invalid @endif"
                                                                   value="@if ($settingDetails->web_server_key){!! $settingDetails->web_server_key !!}@endif">
                                                            @if ($errors->has('web_server_key'))
                                                                <div class="invalid-feedback">{{ $errors->first('web_server_key') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('adminMessage.pushy_api_token') }}</label>
                                                            <input name="pushy_api_token" type="text"
                                                                   class="form-control  @if ($errors->has('pushy_api_token')) is-invalid @endif"
                                                                   value="@if ($settingDetails->pushy_api_token){!! $settingDetails->pushy_api_token !!}@endif">
                                                            @if ($errors->has('pushy_api_token'))
                                                                <div class="invalid-feedback">{{ $errors->first('pushy_api_token') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <h4>{{ __('adminMessage.websitecopyrightstext') }}</h4></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input name="copyrights_en" type="text"
                                                                   class="form-control  @if ($errors->has('copyrights_en')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.copyrights_enter_en') }}"
                                                                   value="@if ($settingDetails->copyrights_en){!! $settingDetails->copyrights_en !!}@endif">
                                                            @if ($errors->has('copyrights_en'))
                                                                <div class="invalid-feedback">{{ $errors->first('copyrights_en') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input dir="rtl" name="copyrights_ar" type="text"
                                                                   class="form-control  @if ($errors->has('copyrights_ar')) is-invalid @endif"
                                                                   placeholder="{{ __('adminMessage.copyrights_enter_ar') }}"
                                                                   value="@if ($settingDetails->copyrights_ar){!! $settingDetails->copyrights_ar !!}@endif">
                                                            @if ($errors->has('copyrights_ar'))
                                                                <div class="invalid-feedback">{{ $errors->first('copyrights_ar') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="kt-portlet__foot">
                                            <div class="kt-form__actions">
                                                <button type="submit"
                                                        class="btn btn-success">{{ __('adminMessage.save') }}</button>
                                            </div>
                                        </div>

                                        <!--end::Form-->
                                    </div>

                                    <!--end::Portlet-->

                                </div>

                            </div>
                        </form>

                    @else
                        <div class="alert alert-light alert-warning" role="alert">
                            <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                            <div class="alert-text">{{ __('adminMessage.youdonthavepermission') }}</div>
                        </div>
                    @endif
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
</body>

<!-- end::Body -->
</html>
