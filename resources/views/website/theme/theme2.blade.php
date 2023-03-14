@extends('website.include.master')
@section('homeSection')
@php
$numberOfDownHomeSecion = 3 ;
@endphp
@include('website.include.popup_modal')

<style>
    .erf-link {
        position: relative;
    }

    .erf-disc {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translate(-50%, -70%);
        color: #fff !important;
        background-color: #ffbb00b3;
        padding: 15px 35px;
        border-radius: 10px;
        font-size: large;
        transition: .5s ease;
    }

    .erf-overlay {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
    }

    .erf-link:hover .erf-disc {
        padding: 15px 45px;
        background-color: #ffbb00;
        transition: .5s ease;
    }

    .navbar li a {
        font-weight: 600;
    }

    .heading_tab_header .text_default {
        font-size: 1.2rem;
    }
     .select2 textarea { 
        direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
     } 

    .select2 textarea {
        height: 25px !important;
    }

    .select2-container img {
        width: 100px;
    }

    @media only screen and (max-width: 600px) {

        .banner_section:not(.full_screen),
        .banner_section:not(.full_screen) .carousel-item,
        .banner_section:not(.full_screen) .banner_content_wrap,
        .banner_section:not(.full_screen) .banner_content_wrap .carousel-item {
            height: auto;
        }
    }

    .owl-carousel .owl-item img {
        height: auto !important;
        object-fit: contain;
        aspect-ratio: 4/3;
    }

    .product_img.blog-img {
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

{{-- START HEADER --}}
<header class="header_wrap">
    <div class="top-header light_skin bg_dark d-none d-md-block">
        <div class="custom-container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-8">
                    <div class="header_topbar_info">
                        @if(!empty($settingInfo->is_free_delivery))
                        <div class="header_offer">
                            @php
                            $tprice = \App\Currency::convertTCountry($settingInfo->free_delivery_amount);
                            $free_delivery_amount = $tprice['price'] ?? $tprice->price ?? $tprice[0]->price ??
                            $settingInfo->free_delivery_amount;
                            @endphp
                            <span>{{ __('webMessage.free_delivery') }}: {{\App\Currency::default()}} {{
                                $free_delivery_amount }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-md-4">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                        {{-- @if ($settingInfo->is_lang == 1)--}}
                        {{-- <div class="lng_dropdown">--}}
                            {{-- <select class="custome_select" id="lng_changer">--}}
                                {{-- <option value='en'
                                    onclick="javascript:window.location.href='{{ url('en/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}'"
                                    @if(app()->getLocale() == "ar") selected @endif data-title="{{
                                    __('webMessage.english') }}">{{ __('webMessage.english') }}</option>--}}
                                {{-- <option value='ar'
                                    onclick="javascript:window.location.href='{{ url('ar/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}'"
                                    @if(app()->getLocale() == "en") selected @endif data-title="{{
                                    __('webMessage.arabic') }}">{{ __('webMessage.arabic') }}</option>--}}
                                {{-- </select>--}}
                            {{-- </div>--}}
                        {{-- @endif--}}

                        @if ( \App\Country::where('is_active' , 1 )->where('parent_id',
                        0)->orderBy('display_order')->count() > 1 )
                        <!-- currency start here-->
                        <div class="lng_dropdown">

                            <select class="custome_select" id="custome_locale_select">
                                @foreach( \App\Country::where('is_active' , 1 )->where('parent_id',
                                0)->orderBy('display_order')->get() as $co)
                                @php $tempCurrency = $co->getCurrency() ; @endphp
                                <option value="{{  'https://'.$co->code.'.'.config('app.url') }}" @if($domainCountry->id
                                    == $co->id) selected @endif data-image="{!!
                                    url('uploads/country/thumb/'.$co->image) !!}">{{ $co['name_'.app()->getLocale()] }}
                                    @if( $tempCurrency instanceof \App\Currency ) ( {{ $tempCurrency['symbol'] ??
                                    $tempCurrency['title_'.app()->getLocale()] }} )@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mx-3">
                            <a href="javascript:;" id="trackmyorder" class="trackorder icon-f-55"
                                title="{{ trans('webMessage.trackorder') }}">&nbsp;<span>{{ __('webMessage.trackorder')
                                    }}</span></a>
                        </div>
                        <!-- currency end here-->
                        @endif
                        @if ($settingInfo->is_lang == 1)
                        @if(app()->getLocale() == "ar")
                        <a href="{{ url('en/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}"
                            class="nav-link text-white">{{ __('webMessage.english') }}</a>
                        @else
                        <a href="{{ url('ar/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}"
                            class="nav-link text-white">{{ __('webMessage.arabic') }}</a>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="middle-header dark_skin">
        <div class="custom-container">
            <div class="nav_block">
                @if ($settingInfo->logo)
                <a class="navbar-brand" href="{{ url(app()->getLocale() . '/') }}">
                    {{-- <img class="logo_light" src="assets/images/logo_light.png" alt="logo" />--}}
                    <img class="logo_dark" width="150px" src="{{ url('uploads/logo/' . $settingInfo->logo) }}"
                        alt="{{ $settingInfo['name_'.app()->getLocale()] }}" />
                </a>
                @endif
                <div class="product_search_form rounded_input">
                    <form method="get" action="{{ url(app()->getLocale() . '/search') }}" id="pro-search-form">
                        <div class="input-group">
                            <select id="searchPro" class="form-control" multiple
                                placeholder="{{ __('webMessage.searchproducts') }}" required=""
                                value="@if (Request()->sq) {{ Request()->sq }} @endif" name="sq" type="text"
                                autocomplete="off">
                            </select>
                            <button type="submit" class="search_btn2"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                @if ($settingInfo->phone)
                <div class="contact_phone contact_support"><i class="linearicons-phone-wave"></i> <span dir="ltr">{{
                        $settingInfo->phone }}</span></div>
                @endif
            </div>
        </div>
    </div>
    @php
    $desktopMenusTrees = App\Categories::CategoriesTree();
    $brandMenus = App\Http\Controllers\webController::BrandsList();
    @endphp
    <div class="bottom_header dark_skin main_menu_uppercase border-top border-bottom">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 col-2">
                    <div class="categories_wrap">
                        <button type="button" data-bs-toggle="collapse" data-bs-target="#navCatContent"
                            aria-expanded="false" class="categories_btn">
                            <i class="linearicons-menu"></i><span>{{ __('webMessage.categories') }} </span>
                        </button>
                        <div id="navCatContent" class="nav_cat navbar collapse">
                            <ul>
                                @foreach ($desktopMenusTrees as $desktopMenusTree)
                                @if (!empty($desktopMenusTree->childs) && count($desktopMenusTree->childs) > 0)
                                <li class="dropdown dropdown-mega-menu">
                                    <a class="dropdown-item nav-link dropdown-toggler" data-bs-toggle="dropdown"
                                        href="#"
                                        onclick="window.location='{{ url(app()->getLocale() . '/products/' . $desktopMenusTree->id . '/' . $desktopMenusTree->friendly_url) }}'"><span>{{
                                            $desktopMenusTree['name_'.app()->getLocale()] }}</span></a>
                                    <div class="dropdown-menu">
                                        <ul class="mega-menu d-lg-flex">
                                            <li class="mega-menu-col col-lg-6">
                                                <ul>
                                                    @foreach ($desktopMenusTree->childs as $childCategory)
                                                    <li><a class="dropdown-item nav-link nav_item"
                                                            href="{{ url(app()->getLocale() . '/products/' . $childCategory->id . '/' . $childCategory->friendly_url) }}">{{
                                                            $childCategory['name_'.app()->getLocale()] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                @else
                                <li><a class="dropdown-item nav-link nav_item"
                                        href="{{ url(app()->getLocale() . '/products/' . $desktopMenusTree->id . '/' . $desktopMenusTree->friendly_url) }}"><span>{{
                                            $desktopMenusTree['name_'.app()->getLocale()] }}</span></a></li>
                                @endif
                                @endforeach
                            </ul>
                            @if (!empty($desktopMenusTrees) && count($desktopMenusTrees) > 10)
                            <div class="more_categories">{{ __('webMessage.more') }} {{ __('webMessage.categories') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-6 col-10">
                    <nav class="navbar navbar-expand-lg">
                        <button class="navbar-toggler side_navbar_toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSidetoggle" aria-expanded="false">
                            <span class="ion-android-menu"></span>
                        </button>
                        <div class="pr_search_icon">
                            <a href="javascript:void(0);" class="nav-link pr_search_trigger"><i
                                    class="linearicons-magnifier"></i></a>
                        </div>
                        <div class="collapse navbar-collapse mobile_side_menu" id="navbarSidetoggle">
                            <ul class="navbar-nav">
                                <li><a class="nav-link active" href="{{ url(app()->getLocale() . '/') }}">{{
                                        __('webMessage.home') }}</a></li>
                                @if (!empty($settingInfo->is_offer_menu))
                                <li>
                                    <a class="nav-link nav_item" href="{{ url(app()->getLocale() . '/offers') }}">{{
                                        __('webMessage.offers') }}</a>
                                </li>
                                @endif
                                @if (!empty($settingInfo->is_brand_active) && !empty($brandMenus) && count($brandMenus)
                                > 0)
                                <li class="dropdown">
                                    <a class="dropdown-toggle nav-link" href="#" data-bs-toggle="dropdown">{{
                                        __('webMessage.brands') }}</a>
                                    <div class="dropdown-menu dropdown-reverse">
                                        <ul>
                                            @foreach ($brandMenus as $brandMenu)
                                            <li><a class="dropdown-item nav-link nav_item"
                                                    href="{{ url(app()->getLocale() . '/brands/' . $brandMenu->slug) }}">{{
                                                    $brandMenu['title_'.app()->getLocale()] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                                @endif
                                @if ((new \App\bundleSetting())->is_active)
                                <li>
                                    <a class="nav-link nav_item"
                                        href="{{ route('webBundle', [app()->getLocale()]) }}">{{
                                        __('webMessage.bundles.Bundle') }}</a>
                                </li>
                                @endif
                                @php
                                $about_details = App\Http\Controllers\webController::singlePageDetails(1)
                                @endphp
                                @if (!empty(Auth::guard('webs')->user()->id))
                                <li class="hide_desk"><a href="{{ url(app()->getLocale() . '/account') }}"
                                        class="nav-link">{{ __('webMessage.myaccount') }}</a></li>
                                <li class="hide_desk"><a href="{{ url(app()->getLocale() . '/wishlist') }}"
                                        class="nav-link">{{ __('webMessage.wishlist') }}</a></li>
                                @else
                                <li class="hide_desk"><a href="{{ url(app()->getLocale() . '/register') }}"
                                        class="nav-link">{{ __('webMessage.signin') }}</a></li>
                                <li class="hide_desk"><a href="{{ url(app()->getLocale() . '/login') }}"
                                        class="nav-link">{{ __('webMessage.signup') }}</a></li>
                                @endif
                                <li><a class="nav-link nav_item"
                                        href="{{url(app()->getLocale().'/page/'.$about_details->slug)}}">{{__('webMessage.aboutus')}}</a>
                                </li>
                                <li><a class="nav-link nav_item"
                                        href="{{url(app()->getLocale().'/blog')}}">{{__('webMessage.BLOG')}}</a></li>
                                <li><a class="nav-link nav_item"
                                        href="{{url(app()->getLocale().'/contactus')}}">{{__('webMessage.contactus')}}</a>
                                </li>
                                <li><a class="nav-link nav_item d-lg-none trackmyorder-sm" href="javascript:;"
                                        id="trackmyorder" class="trackorder icon-f-55"
                                        title="{{ trans('webMessage.trackorder') }}">&nbsp;<span>{{
                                            __('webMessage.trackorder') }}</span></a></li>
                            </ul>
                        </div>

                        <ul class="navbar-nav attr-nav align-items-center">
                            @if ($settingInfo->is_lang == 1)
                            @if(app()->getLocale() == "ar")
                            <li class="hide_desk"><a
                                    href="{{ url('en/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}"
                                    class="nav-link">{{ __('webMessage.english') }}</a></li>
                            @else
                            <li class="hide_desk"><a
                                    href="{{ url('ar/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}"
                                    class="nav-link">{{ __('webMessage.arabic') }}</a></li>
                            @endif
                            @endif
                            @if ( \App\Country::where('is_active' , 1 )->where('parent_id',
                            0)->orderBy('display_order')->count() > 1 )
                            <!-- currency start here-->
                            <li class="dropdown cart_dropdown hide_desk">
                                <a class="nav-link cart_trigger" href="#" data-bs-toggle="dropdown">
                                    <img src="{{url('uploads/country/thumb/'.$domainCountry->image)}}"
                                        style="max-width: 33px;margin-top: 6px;">
                                </a>
                                <div class="cart_box cart_right dropdown-menu dropdown-menu-right"
                                    style="padding: 5px !important;left: -100px !important;max-width: 100px;">
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            onkeyup="CountrySearched = $(this).val().toLowerCase();if (CountrySearched ==='' ) { $('#listOfAllCountryForDomain li').show(); } else { $('#listOfAllCountryForDomain li').each(function () {if ( $(this).data('currencyen').toLowerCase().search(CountrySearched) > -1 || $(this).data('currencyar').toLowerCase().search(CountrySearched) > -1 || $(this).data('titlear').toLowerCase().search(CountrySearched) > -1 || $(this).data('titleen').toLowerCase().search(CountrySearched) > -1 ) { $(this).show(); } else {$(this).hide();} }); }">
                                    </div>
                                    <ul style="max-height: 400px;overflow-y: scroll;padding: 0;margin-top: 10px;border-width: 0;"
                                        id="listOfAllCountryForDomain">
                                        @foreach( \App\Country::where('is_active' , 1 )->where('parent_id',
                                        0)->orderBy('display_order')->get() as $co)
                                        @php $tempCurrency = $co->getCurrency() ; @endphp
                                        <li style="margin-top: 5px;width: 100%;"
                                            class="  @if($domainCountry->id == $co->id) active @endif "
                                            data-titleen="{{ $co['name_en'] }}" data-titlear="{{ $co['name_ar'] }}"
                                            data-currencyen="{{ $tempCurrency['title_en'] }}"
                                            data-currencyar="{{ $tempCurrency['title_ar'] }}"><img
                                                src="{!! url('uploads/country/thumb/'.$co->image) !!}"
                                                style="width: 40px;height:20px;float: left;margin-left: 5px;margin-right: 5px;"><a
                                                href="{{ url('https://'.$co->code.'.'.config('app.url'))}}">{{
                                                $co['name_'.app()->getLocale()] }} @if( $tempCurrency instanceof
                                                \App\Currency ) ( {{ $tempCurrency['symbol'] ??
                                                $tempCurrency['title_'.app()->getLocale()] }} )@endif</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <!-- currency end here-->
                            @endif

                            @if (!empty(Auth::guard('webs')->user()->id))
                            <li class="hide_mob"><a href="{{ url(app()->getLocale() . '/account') }}"
                                    class="nav-link"><i class="linearicons-user"></i></a></li>
                            <li class="hide_mob"><a href="{{ url(app()->getLocale() . '/wishlist') }}"
                                    class="nav-link"><i class="linearicons-heart"></i>
                                    <!-- <span class="wishlist_count">0</span> --> </a></li>
                            @else
                            <li class="">
                                <a href="{{ url(app()->getLocale() . '/login') }}" class="nav-link">
                                    <i class="linearicons-user"></i>
                                </a>
                            </li>
                            @endif
                            @include('website.include.cart')
                        </ul>

                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER -->

@php
$slideshows = App\Http\Controllers\webController::getSlideshow();
@endphp
<!-- START SECTION BANNER -->
<div class="mt-4 staggered-animation-wrap">
    <div class="custom-container">
        <div class="row">
            <div class="col-lg-7 offset-lg-3">
                @if(!empty($slideshows) && count($slideshows)>0)
                <div class="banner_section shop_el_slider">
                    <div id="carouselExampleControls" class="carousel slide carousel-fade light_arrow"
                        data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($slideshows as $slideshow)
                            @php
                            if(!empty($slideshow->link)){$lnks=$slideshow->link;}else{$lnks="";}
                            @endphp
                            <a href="{{ $lnks ?? '#' }}" class="carousel-item @if($loop->first) active @endif">
                                <img class="h-100" src="{{url('uploads/slideshow/'.$slideshow->image)}}"
                                    data-img-src="{{url('uploads/slideshow/'.$slideshow->image)}}">
                            </a>
                            @endforeach
                        </div>
                        <ol class="carousel-indicators indicators_style3">
                            @foreach($slideshows as $slideshow)
                            <li data-bs-target="#carouselExampleControls" data-bs-slide-to="{{ $loop->index }}"
                                class="@if($loop->first) active @endif"></li>
                            @endforeach
                        </ol>
                    </div>
                </div>
                @endif
            </div>
            @php
            $leftbanners = App\Http\Controllers\webController::banners();
            @endphp
            <div class="col-lg-2 d-none d-lg-block">

                @if(!empty($leftbanners) && count($leftbanners)>0)
                @php
                $leftbanner = $leftbanners[0];
                @endphp
                <div class="shop_banner2 el_banner1">
                    <a href="@if(!empty($leftbanner->link)) {{$leftbanner->link}} @else javascript:; @endif">
                        <div class="el_title text_white">
                            <h6>{{$leftbanner['title_'.app()->getLocale()]}}</h6>
                        </div>
                        @if(!empty($leftbanner->image))
                        <div class="el_img">
                            <img src="{{url('uploads/banner/'.$leftbanner->image)}}"
                                alt="{{$leftbanner['title_'.app()->getLocale()]}}">
                        </div>
                        @endif
                    </a>
                </div>
                @endif
                @if(!empty($leftbanners) && count($leftbanners)>1)
                @php
                $leftbanner = $leftbanners[1];
                @endphp
                <div class="shop_banner2 el_banner2">
                    <a href="@if(!empty($leftbanner->link)) {{$leftbanner->link}} @else javascript:; @endif">
                        <div class="el_title text_white">
                            <h6>{{$leftbanner['title_'.app()->getLocale()]}}</h6>
                        </div>
                        @if(!empty($leftbanner->image))
                        <div class="el_img">
                            <img src="{{url('uploads/banner/'.$leftbanner->image)}}"
                                alt="{{$leftbanner['title_'.app()->getLocale()]}}">
                        </div>
                        @endif
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- END SECTION BANNER -->
<!-- END MAIN CONTENT -->
<div class="main_content">

    @php
    $homesetions = App\Http\Controllers\webController::getSections();
    if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
    $showHomeSectionShortText = false;
    $showHomeSectionShopByCategory = false;
    @endphp

    @if(!empty($homesetions))
    <!-- START SECTION SHOP -->
    <div class="section small_pt pb-0">
        <div class="custom-container">
            <div class="row">
                <div class="col-12">
                    <div class="heading_tab_header">
                        <div class="heading_s2">
                            {{-- <h4>Exclusive Products</h4>--}}
                        </div>
                        <div class="tab-style2 hide_mob1">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#tabmenubar" aria-expanded="false">
                                <span class="ion-android-menu"></span>
                            </button>
                            <ul class="nav nav-tabs justify-content-center justify-content-md-end" id="tabmenubar"
                                role="tablist">
                                @php
                                $firstLoop = true;
                                $firstLoop2 = false;
                                $mobileHtml = "";
                                $indexIds = [];
                                @endphp
                                @foreach($homesetions as $homesetionIndex => $homesetion)

                                @if(@$homesetion->banner)
                                <img src="/uploads/section/{{ @$homesetion->banner }}" class="w-100">
                                @endif
                                @php
                                $showTitle = false;
                                if($homesetion->section_type=="static" && $homesetion->key_name=="shop_by_category"){
                                if(isset(Request()->catid)){$catidtop=Request()->catid;}else{$catidtop=0;}
                                $tempLists = App\Http\Controllers\webController::getProductCategories($catidtop);
                                if ( !empty($tempLists) && count($tempLists)>0 ) {
                                $showTitle = false;
                                $showHomeSectionShopByCategory = true;
                                }
                                }elseif($homesetion->section_type=="static" && $homesetion->key_name=="latest_product"){
                                $tempLists = App\Http\Controllers\webController::getNewProducts();
                                if ( !empty($tempLists) && count($tempLists)>0 ) {
                                $showTitle = true;
                                $firstLoop2 = $firstLoop2 === false ? $homesetionIndex : $firstLoop2;
                                }
                                }elseif($homesetion->section_type=="static" &&
                                $homesetion->key_name=="favorite_brands"){
                                $tempLists = App\Http\Controllers\webController::ShopByBrandsList();
                                if ( !empty($tempLists) && count($tempLists)>0 ) {
                                $showTitle = true;
                                $firstLoop2 = $firstLoop2 === false ? $homesetionIndex : $firstLoop2;
                                }
                                }elseif($homesetion->section_type=="static" && $homesetion->key_name=="shop_by_brands"){
                                $tempLists = App\Http\Controllers\webController::BestSellerBrandsList();
                                if ( !empty($tempLists) && count($tempLists)>0 ) {
                                $showTitle = true;
                                $firstLoop2 = $firstLoop2 === false ? $homesetionIndex : $firstLoop2;
                                }
                                }elseif($homesetion->section_type=="static" && $homesetion->key_name=="banner"){
                                $showTitle = false;
                                } elseif($homesetion->section_type=="static" &&
                                $homesetion->key_name=="short_text_boxes"){
                                $showTitle = false;
                                $showHomeSectionShortText = true;
                                } else {
                                $tempLists = App\Http\Controllers\webController::getSectionsProducts($homesetion->id);
                                if ( !empty($tempLists) && count($tempLists)>0 ) {
                                $showTitle = true;
                                $firstLoop2 = $firstLoop2 === false ? $homesetionIndex : $firstLoop2;
                                }
                                }
                                if ( $showTitle )
                                $indexIds[] = $homesetionIndex ;
                                @endphp
                                @endforeach
                                @php
                                $indexIdsCounter = count($indexIds) - $numberOfDownHomeSecion ;
                                @endphp
                                @foreach($homesetions as $homesetionIndex => $homesetion)
                                @if ( in_array($homesetionIndex,$indexIds) and $indexIdsCounter > 0 )
                                <li class="nav-item">
                                    <a class="nav-link @if($firstLoop) active @endif" id="arrival-tab-{{$loop->index}}"
                                        data-bs-toggle="tab" href="#arrival-{{$loop->index}}" role="tab"
                                        aria-controls="arrival"
                                        aria-selected="true">@if(app()->getLocale()=="en"){{strtoupper($homesetion->title_en)}}@else{{$homesetion->title_ar}}@endif</a>
                                </li>
                                @php
                                $firstLoop = false;
                                $indexIdsCounter--;
                                $mobileHtml .= "<option value=\"".($homesetion['title_'.app()->
                                    getLocale()])."\">".($homesetion['title_'.app()->getLocale()])."</option>";
                                @endphp
                                @endif
                                @endforeach
                            </ul>
                        </div>

                        {{-- <div class="custom_select hide_desk1 hide_mob">
                            <select class="form-control form-control-sm">
                                {!! $mobileHtml !!}
                            </select>
                        </div>--}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="tab_slider">
                        @php
                        $indexIdsCounter = count($indexIds) - 2 ;
                        @endphp
                        @foreach($homesetions as $homesetionIndex => $homesetion)
                        @if ( in_array($homesetionIndex,$indexIds) and $indexIdsCounter > 0 )
                        <div class="tab-pane fade @if($firstLoop2 == $homesetionIndex) show active @endif"
                            id="arrival-{{$loop->index}}" role="tabpanel"
                            aria-labelledby="arrival-tab-{{$loop->index}}">
                            <div class="@if($homesetion->slideShow) product_slider carousel_slider owl-carousel owl-theme dot_style1 @else row @endif"
                                data-loop="true" data-margin="20"
                                data-responsive='{"0":{"items": "2"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "5"}}'>
                                @php
                                $tempLists = [] ;
                                $tempListsBrand = [];
                                $tempListsBrand2 = [];
                                if($homesetion->section_type=="static" && $homesetion->key_name=="latest_product"){
                                $tempLists = App\Http\Controllers\webController::getNewProducts();
                                }elseif($homesetion->section_type=="static" &&
                                $homesetion->key_name=="favorite_brands"){
                                $tempListsBrand2 = App\Http\Controllers\webController::ShopByBrandsList();
                                }elseif($homesetion->section_type=="static" && $homesetion->key_name=="shop_by_brands"){
                                $tempListsBrand = App\Http\Controllers\webController::BestSellerBrandsList();
                                } else {
                                $tempLists = App\Http\Controllers\webController::getSectionsProducts($homesetion->id);
                                }
                                @endphp
                                @foreach($tempLists as $tempList)
                                @if( ! $homesetion->slideShow) <div class="col-md-4 col-xs-1"> @endif
                                    @component('website.include.productV2' , ['productDetails' =>
                                    $tempList])@endcomponent
                                    @if( ! $homesetion->slideShow) </div>@endif
                                @endforeach
                                @foreach($tempListsBrand as $tempList)
                                @if( ! $homesetion->slideShow) <div class="col-md-4 col-xs-1"> @endif
                                    <div class="item">
                                        <div class="product_wrap">
                                            <div class="product_img">
                                                <a href="{{url(app()->getLocale().'/brands/'.$tempList->slug)}}">
                                                    <img src="@if ($tempList->image) {{ url('uploads/brand/thumb/'.$tempList->image) }} @else {{ url('uploads/no-image.png') }} @endif"
                                                        alt="{{ $tempList['title_'.app()->getLocale()] }}">
                                                </a>
                                            </div>
                                            <div class="product_info">
                                                <h6 class="product_title"><a
                                                        href="{{url(app()->getLocale().'/brands/'.$tempList->slug)}}">{{
                                                        $tempList['title_'.app()->getLocale()] }}</a></h6>
                                            </div>
                                        </div>
                                    </div>
                                    @if( ! $homesetion->slideShow)
                                </div>@endif
                                @endforeach
                                @foreach($tempListsBrand2 as $tempList)
                                @php
                                $homesetionsprods =
                                App\Http\Controllers\webController::getBrandsProducts($tempList->id,$homesetion->ordering);
                                @endphp
                                @if (!empty($homesetionsprods) && count($homesetionsprods) > 0)
                                @php $tagsDetails=''; @endphp
                                @foreach ($homesetionsprods as $homesetionsprod)
                                @if( ! $homesetion->slideShow) <div class="col-md-4 col-xs-1"> @endif
                                    @component('website.include.productV2' , ['productDetails' =>
                                    $homesetionsprod])@endcomponent
                                    @if( ! $homesetion->slideShow) </div>@endif
                                @endforeach
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
    @endif
    @if($showHomeSectionShopByCategory)
    @php
    $shopcategoriesLists = App\Http\Controllers\webController::getProductCategories(0);
    @endphp
    @if(!empty($shopcategoriesLists) && count($shopcategoriesLists)>0)
    <!-- START SECTION BANNER -->
    <div class="section pb_20 small_pt">
        <div class="custom-container">
            <div class="row justify-content-center">
                @foreach($shopcategoriesLists as $shopcategoriesList)
                <div class="col-md-4">
                    <div class="sale-banner mb-3 mb-md-4">
                        <a href="{{url(app()->getLocale().'/products/'.$shopcategoriesList->cid.'/'.$shopcategoriesList->friendly_url)}}"
                            class="erf-link">
                            @if($shopcategoriesList['name_'.app()->getLocale()] ) <div class="erf-disc">
                                {{$shopcategoriesList['name_'.app()->getLocale()]}}</div> @endif
                            <img src="{{$shopcategoriesList->cimage ? url('uploads/category/original/'.$shopcategoriesList->cimage) : url('uploads/category/no-image.png') }}"
                                alt="{{$shopcategoriesList['name_'.app()->getLocale()]}}">
                            <div class="erf-overlay"></div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- END SECTION BANNER -->
    @endif
    @endif
    @php
    $shopcategoriesLists = \App\Product::whereDate('countdown_datetime' , '>' , Date('Y-m-d'))
    ->where('gwc_products.is_active', '!=', 0)
    ->get();
    @endphp
    @if(!empty($shopcategoriesLists) && count($shopcategoriesLists)>0)
    <!-- START SECTION SHOP -->
    <div class="section pt-0 pb-0">
        <div class="custom-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading_tab_header">
                        <div class="heading_s2">
                            <h4>@if(app()->getLocale() == "en") Deal Of The Day @else صفقة اليوم @endif</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="product_slider carousel_slider owl-carousel owl-theme nav_style3" data-loop="true"
                        data-dots="false" data-nav="true" data-margin="30"
                        data-responsive='{"0":{"items": "1"}, "650":{"items": "2"}, "1199":{"items": "2"}}'>
                        @foreach($shopcategoriesLists as $shopcategoriesList)
                        @component('website.include.productV3' , ['productDetails' => $shopcategoriesList])@endcomponent
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
    @endif

    @if(!empty($leftbanners) && count($leftbanners)>2)
    <!-- START SECTION BANNER -->
    <div class="section pb_20 small_pt">
        <div class="custom-container">
            <div class="row">
                @foreach($leftbanners->slice(2)->take(8) as $leftbanner)
                <div class="col-md-3">
                    <div class="sale-banner mb-3 mb-md-4">
                        <a @if(!empty($leftbanner->link)) href="{{$leftbanner->link}}" @else href="javascript:;" @endif
                            class="erf-link">
                            @if($leftbanner['title_'.app()->getLocale()]) <div class="erf-disc">
                                {{$leftbanner['title_'.app()->getLocale()]}}</div> @endif
                            <img src="{{url('uploads/banner/'.$leftbanner->image)}}"
                                alt="{{$leftbanner['title_'.app()->getLocale()]}}">
                            <div class="erf-overlay"></div>
                        </a>
                    </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- END SECTION BANNER -->
@endif

@if ( !empty($settingInfo->is_brand_active) && !empty($brandMenus) && count($brandMenus) > 0 )
<!-- START SECTION CLIENT LOGO -->
<div class="section pt-0 small_pb">
    <div class="custom-container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading_tab_header">
                    <div class="heading_s2">
                        <h4>{{ __('webMessage.brands') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="client_logo carousel_slider owl-carousel owl-theme nav_style3" data-dots="false"
                    data-nav="true" data-margin="30" data-loop="true" data-autoplay="true"
                    data-responsive='{"0":{"items": "2"}, "480":{"items": "3"}, "767":{"items": "4"}, "991":{"items": "5"}, "1199":{"items": "6"}}'>
                    @foreach ($brandMenus as $brandMenu)
                    @if($brandMenu->image and $brandMenu->is_home)
                    <div class="item">
                        <div class="cl_logo">
                            <a href="{{ url(app()->getLocale() . '/brands/' . $brandMenu->slug) }}">
                                <img src="{{ url('uploads/brand/thumb/'.$brandMenu->image) }}"
                                    alt="{{ $brandMenu['title_'.app()->getLocale()] }}" />
                            </a>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION CLIENT LOGO -->
@endif


<!-- START SECTION SHOP -->
<div class="section pt-0 pb_20">
    <div class="custom-container">
        <div class="row">
            @foreach($homesetions as $homesetionIndex => $homesetion)
            @if ( in_array($homesetionIndex, array_slice($indexIds, ($numberOfDownHomeSecion * -1 ),
            $numberOfDownHomeSecion, true) ) )
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12">
                        <div class="heading_tab_header">
                            <div class="heading_s2">
                                <h4>@if(app()->getLocale()=="en"){{strtoupper($homesetion->title_en)}}@else{{$homesetion->title_ar}}@endif
                                </h4>
                            </div>
                            <div class="view_all">
                                <a href="{{ @$homesetion->link ?? url(app()->getLocale() .'/allsections/'.$homesetion->id.'/'.$homesetion->slug)}}"
                                    class="text_default"><span>{{ __('webMessage.viewall') }}</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="product_slider carousel_slider product_list owl-carousel owl-theme nav_style5"
                            data-nav="true" data-dots="false" data-loop="true" data-margin="20"
                            data-responsive='{"0":{"items": "1"}, "380":{"items": "1"}, "640":{"items": "2"}, "991":{"items": "1"}}'>
                            @php
                            $tempLists = [] ;
                            $tempListsBrand2 = [] ;
                            if($homesetion->section_type=="static" && $homesetion->key_name=="latest_product"){
                            $tempLists = App\Http\Controllers\webController::getNewProducts();
                            }elseif($homesetion->section_type=="static" && $homesetion->key_name=="favorite_brands"){
                            }elseif($homesetion->section_type=="static" && $homesetion->key_name=="shop_by_brands"){
                            $tempListsBrand2 = App\Http\Controllers\webController::BestSellerBrandsList();
                            } else {
                            $tempLists = App\Http\Controllers\webController::getSectionsProducts($homesetion->id);
                            }
                            $indxTTT = 0 ;
                            @endphp
                            @foreach($tempLists as $tempList)
                            @if($indxTTT % 3 == 0 )
                            <div class="item">
                                @endif
                                @component('website.include.productV4' , ['productDetails' => $tempList])@endcomponent
                                @if($indxTTT % 3 == 2 )
                            </div>
                            @endif
                            @php
                            $indxTTT++;
                            @endphp
                            @endforeach
                            @foreach($tempListsBrand2 as $tempList)
                            @php
                            $homesetionsprods = App\Http\Controllers\webController::getBrandsProducts($tempList->id);
                            @endphp
                            @if (!empty($homesetionsprods) && count($homesetionsprods) > 0)
                            @foreach ($homesetionsprods as $homesetionsprod)
                            @if($indxTTT % 3 == 0 )
                            <div class="item">
                                @endif
                                @component('website.include.productV4' , ['productDetails' =>
                                $homesetionsprod])@endcomponent
                                @if($indxTTT % 3 == 2 )
                            </div>
                            @endif
                            @php
                            $indxTTT++;
                            @endphp
                            @endforeach
                            @endif
                            @endforeach
                            @php
                            $indxTTT--;
                            @endphp
                            @if($indxTTT % 3 != 2 and $indxTTT != -1 )
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
</div>
<!-- END SECTION SHOP -->


<!-- START SECTION SELLERS -->
@include("website.include.shop_by_sellers_scrolling")
<!-- END SECTION SELLERS -->

<!-- END SECTION POPUPLAR PRODUCTS -->
<section class="section pt-0 pb_20">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div class="heading_tab_header">
                    <div class="heading_s2">
                        <h4>{{ __('webMessage.mostpopular') }}</h4>
                    </div>
                </div>
                <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1"
                    data-loop="true" data-margin="20"
                    data-responsive='{"0":{"items": "2"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "5"}}'>
                    @php
                        $pItems = App\Http\Controllers\webController::getPopularProducts(20);
                    @endphp
                    @foreach($pItems as $tempList)
                            @component('website.include.productV2' , [
                                'productDetails' => $tempList
                                ])@endcomponent
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- END SECTION POPUPLAR PRODUCTS -->


@if(!empty($leftbanners) && count($leftbanners))
<!-- START SECTION BANNER -->
<div class="section pb_20 small_pt">
    <div class="custom-container">
        <div class="row">
            @foreach($leftbanners->slice(10)->take(4) as $leftbanner)
                <div class="col-md-3 testt">
                    <div class="sale-banner mb-3 mb-md-4">
                        <a @if(!empty($leftbanner->link)) href="{{$leftbanner->link}}" @else href="javascript:;" @endif
                            class="erf-link">
                            @if($leftbanner['title_'.app()->getLocale()]) <div class="erf-disc">
                                {{$leftbanner['title_'.app()->getLocale()]}}</div> @endif
                            <img src="{{url('uploads/banner/'.$leftbanner->image)}}"
                                alt="{{$leftbanner['title_'.app()->getLocale()]}}">
                            <div class="erf-overlay"></div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- END SECTION BANNER -->
    @endif

    <!-- START SECTION CLIENT LOGO -->
    <div class="section pt-0 small_pb l-sec">
        <div class="custom-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading_tab_header">
                        <div class="heading_s2">
                            <h4>{{ __('webMessage.latest_news') }}</h4>
                        </div>
                        <div class="view_all">
                            <a href="{{url(app()->getLocale().'/blog')}}" class="text_default"><span>{{
                                    __('webMessage.viewall') }}</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach(\App\BlogPost::where('status' , 'published')->latest()->with('comments')->limit(4)->get() as
                $post)
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="product_wrap">
                        @if ($post->created_at > now()->subDay(7))
                        <span class="pr_flash"
                            style="background-color:#008cff;color:#fff;border-radius:5px;font-size:12px;padding:3px;">NEW</span>
                        @endif
                        <div class="product_img blog-img">
                            <a
                                href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug]) }}">
                                <img src="{!! url('uploads/blog/'.$post->image) !!}" alt="iPad 12.9inch">
                                <img class="product_hover_img" src="{!! url('uploads/blog/'.$post->image) !!}"
                                    alt=" iPad 12.9inch ">
                            </a>
                        </div>
                        <div class="product_info">
                            {{-- <span id="responseMsg-1141"></span> --}}
                            <h6 class="product_title"><a
                                    href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug]) }}">{{
                                    $post['title_'.app()->getLocale()] }}</a></h6>

                            <ul class="list_none blog_meta">
                                <li><a href="#"><i class="ti-calendar"></i> {{ $post->created_at->format('F d, Y')
                                        }}</a></li>
                                <li><a href="#"><i class="ti-comments"></i> {{ $post->publsihComments->count() }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- END SECTION CLIENT LOGO -->

    <!-- START SECTION SUBSCRIBE NEWSLETTER -->
    <div class="section bg_default small_pt small_pb">
        <div class="custom-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="newsletter_text text_white">
                        <h3>{{ strtoupper(__('webMessage.newslettersignup')) }}</h3>
                        @if (app()->getLocale() == 'en' && $settingInfo->newsletter_note_en)
                        <p>{!! $settingInfo->newsletter_note_en !!}</p>
                        @endif
                        @if (app()->getLocale() == 'ar' && $settingInfo->newsletter_note_ar)
                        <p>{!! $settingInfo->newsletter_note_ar !!}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="newsletter_form2 rounded_input">
                        <form id="newsletterformtxt" name="newsletterformtxt" method="post" novalidate="novalidate">
                            <input type="text" name="newsletter_email" id="newsletter_email" class="form-control"
                                placeholder="{{ __('webMessage.enter_email') }}"><span id="newslettermsg"></span>

                            <button id="subscribeBtn" class="btn btn-dark btn-radius" type="button">{{
                                __('webMessage.subscribe') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- START SECTION SUBSCRIBE NEWSLETTER -->

</div>
<!-- END MAIN CONTENT -->

<!-- START FOOTER -->
<footer class="bg_gray">
    <div class="footer_top small_pt pb_20">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="widget">
                        <div class="footer_logo">
                            @if ($settingInfo->logo)
                            <a href="{{ url(app()->getLocale() . '/') }}">
                                <img src="{{ url('uploads/logo/' . $settingInfo->logo) }}" width="120px"
                                    alt="{{ $settingInfo['name_'.app()->getLocale()] }}" />
                            </a>
                            @endif
                        </div>
                        <ul class="contact_info">
                            @if ( (app()->getLocale() == 'ar' && $settingInfo->address_ar) OR (app()->getLocale() ==
                            'en' && $settingInfo->address_en) )
                            <li>
                                <i class="ti-location-pin"></i>
                                <p>{{$settingInfo['address_'.app()->getLocale()]}}</p>
                            </li>
                            @endif
                            @if( $settingInfo->email)
                            <li>
                                <i class="ti-email"></i>
                                <a href="mailto:{{ $settingInfo->email }}">{{ $settingInfo->email }}</a>
                            </li>
                            @endif
                            @if( $settingInfo->phone)
                            <li>
                                <i class="ti-mobile"></i>
                                <p dir="ltr">{{ $settingInfo->phone }}</p>
                            </li>
                            @endif
                            @if ( ( app()->getLocale() == 'en' && $settingInfo->office_hours_en) or ( app()->getLocale()
                            == 'ar' && $settingInfo->office_hours_ar))
                            <li>
                                <i class="ti-time"></i>
                                <p>{{ $settingInfo['office_hours_'.app()->getLocale()] }}</p>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                @php
                $footerMenusTrees = App\Categories::CategoriesTree();
                $singlePageLinks = App\Http\Controllers\webController::allSinglePagesLinks();
                @endphp
                @if (!empty($footerMenusTrees) && count($footerMenusTrees) > 0)
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">{{ __('webMessage.categories') }}</h6>
                        <ul class="widget_links">
                            @foreach ($footerMenusTrees as $footerMenusTree)
                            <li><a
                                    href="{{ url(app()->getLocale() . '/products/' . $footerMenusTree->id . '/' . $footerMenusTree->friendly_url) }}">{{
                                    $footerMenusTree['name_'.app()->getLocale()] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">{{ strtoupper(__('webMessage.importantlinks')) }}</h6>
                        <ul class="widget_links">
                            <!--<li>-->
                            <!--    <a href="{{ url(app()->getLocale() . '/faq') }}">{{ __('webMessage.faq') }}</a>-->
                            <!--</li>-->
                            @if(($settingInfo->ios_url != null or $settingInfo->android_url != null or
                            $settingInfo->huawei_url != null ) and ! empty($settingInfo->invoice_qrcode) )
                            <li>
                                <a href="{{route('downloadApp')}}">{{ __('webMessage.DownloadApp') }}</a>
                            </li>
                            @endif
                            @if (!empty(Auth::guard('webs')->user()->id))
                            <li>
                                <a href="{{ url(app()->getLocale() . '/dashboard') }}">{{ __('webMessage.myaccount')
                                    }}</a>
                            </li>
                            @else
                            <li>
                                <a href="{{ url(app()->getLocale() . '/register') }}">{{ __('webMessage.signup') }}</a>
                            </li>
                            <li>
                                <a href="{{ url(app()->getLocale() . '/login') }}">{{ __('webMessage.signin') }}</a>
                            </li>
                            @endif
                            @if ($settingInfo->supplier_registration == 1)
                            <li>
                                <a href="{{ url(app()->getLocale() . '/supplier-registration') }}">{{
                                    __('webMessage.supplier_registration') }}</a>
                            </li>
                            @endif
                            @foreach ($singlePageLinks as $links)
                            <li>
                                <a
                                    href="{{ url(app()->getLocale().'/page/' .$links->slug ) }}">{{app()->getLocale()=='en'?$links->title_en:$links->title_ar}}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @if(!empty($settingInfo->instagram_token))
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="widget">
                        <h6 class="widget_title">{{ strtoupper(__('webMessage.instagram')) }}</h6>
                        <ul class="widget_instafeed instafeed_col4">
                            @foreach (\App\Http\Controllers\Common::instagramFeed(6) as $feeds)
                            <li><a href="{{ $feeds->permalink }}"><img src="{{ $feeds->image }}" alt="insta_img"><span
                                        class="insta_icon"><i class="ti-instagram"></i></span></a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @if($showHomeSectionShortText)
    <div class="middle_footer">
        <div class="custom-container">
            <div class="row">
                <div class="col-12">
                    <div class="shopping_info">
                        <div class="row justify-content-center">
                            @if(!empty($settingInfo['home_note1_title_'.$strLang]))
                            <div class="col-md-3">
                                <div class="icon_box icon_box_style2">
                                    <div class="icon">
                                        <i class="flaticon-shipped"></i>
                                    </div>
                                    <div class="icon_box_content">
                                        <h5>{{$settingInfo['home_note1_title_'.$strLang]}}</h5>
                                        <p>{{$settingInfo['home_note1_details_'.$strLang]}}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($settingInfo['home_note2_title_'.$strLang]))
                            <div class="col-md-3">
                                <div class="icon_box icon_box_style2">
                                    <div class="icon">
                                        <img src="{{ url('assets\images\24support.png') }}" style="max-width: 40px;">
                                    </div>
                                    <div class="icon_box_content">
                                        <h5>{{$settingInfo['home_note2_title_'.$strLang]}}</h5>
                                        <p>{{$settingInfo['home_note2_details_'.$strLang]}}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($settingInfo['home_note3_title_'.$strLang]))
                            <div class="col-md-3">
                                <div class="icon_box icon_box_style2">
                                    <div class="icon">
                                        <img src="{{ url('assets\images\whatsapp-footer.png') }}"
                                            style="max-width: 33px;">
                                    </div>
                                    <div class="icon_box_content">
                                        <h5>{{$settingInfo['home_note3_title_'.$strLang]}}</h5>
                                        <p>{{$settingInfo['home_note3_details_'.$strLang]}}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($settingInfo['home_note4_title_'.$strLang]))
                            <div class="col-md-3">
                                <div class="icon_box icon_box_style2">
                                    <div class="icon">
                                        <i class="flaticon-money-back"></i>
                                    </div>
                                    <div class="icon_box_content">
                                        <h5>{{$settingInfo['home_note4_title_'.$strLang]}}</h5>
                                        <p>{{$settingInfo['home_note4_details_'.$strLang]}}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="bottom_footer border-top-tran">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-4">
                    <p class="mb-lg-0 text-center">
                        @if ($settingInfo->copyrights_en && app()->getLocale() == 'en'){!! $settingInfo->copyrights_en
                        !!}@endif
                        @if ($settingInfo->copyrights_ar && app()->getLocale() == 'ar'){!! $settingInfo->copyrights_ar
                        !!}@endif
                    </p>
                </div>
                <div class="col-lg-4 order-lg-first">
                    <div class="widget mb-lg-0">
                        <ul class="social_icons text-center text-lg-start">
                            @if ($settingInfo->social_facebook)
                            <li><a title="{{ __('webMessage.facebook') }}" target="_blank"
                                    href="{{ $settingInfo->social_facebook }}" class="sc_facebook"><i
                                        class="ion-social-facebook"></i></a></li>
                            @endif
                            @if ($settingInfo->social_twitter)
                            <li><a title="{{ __('webMessage.twitter') }}" target="_blank"
                                    href="{{ $settingInfo->social_twitter }}" class="sc_twitter"><i
                                        class="ion-social-twitter"></i></a></li>
                            @endif
                            @if ($settingInfo->social_instagram)
                            <li><a title="{{ __('webMessage.instagram') }}" target="_blank"
                                    href="{{ $settingInfo->social_instagram }}" class="sc_instagram"><i
                                        class="ion-social-instagram-outline"></i></a></li>
                            @endif
                            @if ($settingInfo->social_linkedin)
                            <li><a title="{{ __('webMessage.linkedin') }}" target="_blank"
                                    href="{{ $settingInfo->social_linkedin }}" class="sc_linkedin"><i
                                        class="ion-social-linkedin"></i></a></li>
                            @endif
                            @if ($settingInfo->social_youtube)
                            <li><a title="{{ __('webMessage.youtube') }}" target="_blank"
                                    href="{{ $settingInfo->social_youtube }}" class="sc_youtube"><i
                                        class="ion-social-youtube-outline"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if (!empty($settingInfo->payments))
                    @php
                    $payments = explode(',', $settingInfo->payments);
                    @endphp
                    <ul class="footer_payment text-center text-lg-end">
                        @foreach ($payments as $payment)
                        <li><a href="#"><img src="{{ url('uploads/paymenticons/' . strtolower($payment) . '.png') }}"
                                    height="30" alt=""></a></li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->

@if ($settingInfo->social_whatsapp)
@if (!empty($settingInfo->is_float_whatsapp))
<a href="https://api.whatsapp.com/send?phone={{ $settingInfo->social_whatsapp }}&text={{ __('webMessage.whatsappsharetext') }}"
    target="_blank" class="float">
    <img src="{{asset('assets/images/whatsapp.png')}}" alt=""></a>
@endif
@endif

<!--order tracking -->
<div class="modal  fade" id="modalPrderTrackBox" tabindex="-1" role="dialog" aria-label="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">

                <div type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="ion-ios-close-empty"></i></span>
                </div>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input type="text" name="trackorderid" class="form-control" id="trackorderid"
                                placeholder="{{ __('webMessage.enter_order_id') }}" autcomplete="off">

                        </div>
                    </div>
                    <div class="col-lg-2">
                        <input type="button" value="{{ __('webMessage.checknow') }}"
                            class="btn btn-border TrackMyOrders">
                    </div>
                </div>
                <span id="responseTrackOrder"></span>
            </div>
        </div>
    </div>
</div>
<a href="#" class="scrollup" style="display: none;"><i class="ion-ios-arrow-up"></i></a>
<script>
    screen.width <= 720 ? document.querySelector('#trackmyorder').remove() : false;
</script>
@endsection
