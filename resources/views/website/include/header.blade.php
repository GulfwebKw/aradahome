@if ( !isset($ShowDefaulrHaderBlade) )
<style>
     .navbar li a {
        font-weight: 600;
    }
    .heading_tab_header .text_default{
            font-size: 1.2rem;
    }
    @if(app()->getLocale() == 'ar')
        /*.collapse.navbar-collapse ul,*/
        .select2 textarea{
            direction: rtl;
        }
        
    @endif
    .select2 .select2-search,
    .select2 .selection{
        width: 100%
    }
    .select2 textarea{
        height: 25px !important;
    }
    .select2-container img{
        width: 100px;
    }
    
    @media only screen and (max-width: 991px){
        .product_search_form {
            bottom: -80px !important;
        }
    }
</style>
    <!-- START HEADER -->
    <header class="header_wrap fixed-top header_with_topbar">
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
{{--                            @if ($settingInfo->is_lang == 1)--}}
{{--                            <div class="lng_dropdown me-2">--}}
{{--                                <select class="custome_select"  id="lng_changer">--}}
{{--                                    <option value='en' onclick="javascript:window.location.href='{{ url('en/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}'" @if(app()->getLocale() == "ar") selected @endif data-title="{{ __('webMessage.english') }}">{{ __('webMessage.english') }}</option>--}}
{{--                                    <option value='ar' onclick="javascript:window.location.href='{{ url('ar/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}'" @if(app()->getLocale() == "en") selected @endif data-title="{{ __('webMessage.arabic') }}">{{ __('webMessage.arabic') }}</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            @endif--}}

                            @if ($settingInfo->is_lang == 1)
                                @if(app()->getLocale() == "ar")
                                    <a href="{{ url('en/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}" class="nav-link">{{ __('webMessage.english') }}</a>
                                @else
                                    <a href="{{ url('ar/' . substr(Request::getRequestUri(), 4, strlen(Request::getRequestUri()))) }}" class="nav-link">{{ __('webMessage.arabic') }}</a>
                                @endif
                            @endif
                            @if ( \App\Country::where('is_active' , 1 )->where('parent_id', 0)->orderBy('display_order')->count() > 1 )
                            <!-- currency start here-->
                                <div class="lng_dropdown me-2">

                                    <select class="custome_select" id="custome_locale_select">
                                            @foreach( \App\Country::where('is_active' , 1 )->where('parent_id', 0)->orderBy('display_order')->get() as $co)
                                                @php $tempCurrency = $co->getCurrency() ; @endphp
                                            <option value='{{ 'https://'.$co->code.'.'.config('app.url') }}'  @if($domainCountry->id == $co->id) selected @endif data-image="{!! url('uploads/country/thumb/'.$co->image) !!}" data-title="{{ $co['name_'.app()->getLocale()] }} @if( $tempCurrency instanceof \App\Currency ) ( {{ $tempCurrency['symbol'] ?? $tempCurrency['title_'.app()->getLocale()] }} )@endif">{{ $co['name_'.app()->getLocale()] }} @if( $tempCurrency instanceof \App\Currency ) ( {{ $tempCurrency['symbol'] ?? $tempCurrency['title_'.app()->getLocale()] }} )@endif</option>
                                            @endforeach
                                    </select>
                                </div>
                                <!-- currency end here-->
                            @endif
                            
                            
                            @if ($settingInfo->phone)
                                <ul class="contact_detail text-center text-lg-start">
                                    <li><i class="ti-mobile"></i>
                                        <span><a href="tel:{{ $settingInfo->phone }}" dir="ltr">{{ $settingInfo->phone }}</a></span>
                                    </li>
                                    @if (!empty(Auth::guard('webs')->user()->id))
                                        <li><a href="{{ url(app()->getLocale() . '/account') }}"  class="hide_desk"><i class="ti-user"></i><span>{{ __('webMessage.dashboard') }}</span></a></li>
                                    @else
                                        <li><a href="{{ url(app()->getLocale() . '/login') }}"  class="hide_desk"><i class="ti-user"></i><span>
                                            {{--{{ __('webMessage.signin') }}--}}
                                            </span></a></li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="product_search_form rounded_input">
                           <form method="get" action="{{ url(app()->getLocale() . '/search') }}" id="pro-search-form">
                                <div class="input-group">
                                            <select id="searchPro"  class="form-control" multiple placeholder="{{ __('webMessage.searchproducts') }}" required=""  value="@if (Request()->sq) {{ Request()->sq }} @endif" name="sq"  type="text" autocomplete="off">
                                            </select> 
                                    <button type="submit" class="search_btn2"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-2 hide_mob">
                        <div class="text-center text-md-end">
                            <ul class="header_list">
                                @if (!empty(Auth::guard('webs')->user()->id))
                                    <li><a href="{{ url(app()->getLocale() . '/wishlist') }}"><i class="ti-heart"></i><span>{{ __('webMessage.wishlists') }}</span></a></li>
                                    <li><a href="{{ url(app()->getLocale() . '/account') }}"><i class="ti-user"></i><span>{{ __('webMessage.dashboard') }}</span></a></li>
                                @else
                                    <li><a href="{{ url(app()->getLocale() . '/register') }}"><i class="ti-user"></i><span>{{ __('webMessage.signup') }}</span></a></li>
                                    <li><a href="{{ url(app()->getLocale() . '/login') }}"><i class="ti-user"></i><span>{{ __('webMessage.signin') }}</span></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom_header dark_skin main_menu_uppercase">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    @if ($settingInfo->logo)
                    <a class="navbar-brand" href="{{ url(app()->getLocale() . '/') }}">
{{--                        <img class="logo_light" width="150px"  src="assets/image/s/logo_light.png" alt="logo" />--}}
                        <img class="logo_dark" width="150px"  src="{{ url('uploads/logo/' . $settingInfo->logo) }}" alt="{{ $settingInfo['name_'.app()->getLocale()] }}" />
                    </a>
                    @endif

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-expanded="false">
                        <span class="ion-android-menu"></span>
                    </button>


                    @php
                        $desktopMenusTrees = App\Categories::CategoriesTree();
                        $brandMenus = App\Http\Controllers\webController::BrandsList();
                    @endphp
                    <div class="collapse navbar-collapse justify-content-end"  id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li><a class="nav-link" href="{{ url(app()->getLocale() . '/') }}">{{ __('webMessage.home') }}</a></li>
                            @if (!empty($desktopMenusTrees) && count($desktopMenusTrees) > 0)
                            <li class="dropdown">
                                <a class="dropdown-toggle nav-link" href="#" data-bs-toggle="dropdown">{{ __('webMessage.categories') }}</a>
                                <div class="dropdown-menu dropdown-reverse">
                                    <ul>
                                        @foreach ($desktopMenusTrees as $desktopMenusTree)
                                            @if (!empty($desktopMenusTree->childs) && count($desktopMenusTree->childs) > 0)
                                            <li>
                                                <a class="dropdown-item menu-link dropdown-toggler" href="{{ url(app()->getLocale() . '/products/' . $desktopMenusTree->id . '/' . $desktopMenusTree->friendly_url) }}">{{ $desktopMenusTree['name_'.app()->getLocale()] }}</a>
                                                <div class="dropdown-menu">
                                                    <ul>
                                                        @foreach ($desktopMenusTree->childs as $childCategory)
                                                        <li><a class="dropdown-item nav-link nav_item" href="{{ url(app()->getLocale() . '/products/' . $childCategory->id . '/' . $childCategory->friendly_url) }}">{{ $childCategory['name_'.app()->getLocale()] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                            @else
                                                <li><a class="dropdown-item nav-link nav_item" href="{{ url(app()->getLocale() . '/products/' . $desktopMenusTree->id . '/' . $desktopMenusTree->friendly_url) }}">{{ $desktopMenusTree['name_'.app()->getLocale()] }}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            @endif
                            @if (!empty($settingInfo->is_offer_menu))
                                <li>
                                    <a class="nav-link nav_item" href="{{ url(app()->getLocale() . '/offers') }}">{{ __('webMessage.offers') }}</a>
                                </li>
                            @endif
                            @if (!empty($settingInfo->is_brand_active) && !empty($brandMenus) && count($brandMenus) > 0)
                                <li class="dropdown">
                                    <a class="dropdown-toggle nav-link" href="#" data-bs-toggle="dropdown">{{ __('webMessage.brands') }}</a>
                                    <div class="dropdown-menu dropdown-reverse">
                                        <ul>
                                            @foreach ($brandMenus as $brandMenu)
                                                <li><a class="dropdown-item nav-link nav_item" href="{{ url(app()->getLocale() . '/brands/' . $brandMenu->slug) }}">{{ $brandMenu['title_'.app()->getLocale()] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @if ((new \App\bundleSetting())->is_active)
                                <li>
                                    <a class="nav-link nav_item" href="{{ route('webBundle', [app()->getLocale()]) }}">{{ __('webMessage.bundles.Bundle') }}</a>
                                </li>
                            @endif
                            @php
                                $about_details = App\Http\Controllers\webController::singlePageDetails(1)
                            @endphp
                            <li><a class="nav-link nav_item" href="{{url(app()->getLocale().'/page/'.$about_details->slug)}}">{{__('webMessage.aboutus')}}</a></li>
                            <li><a class="nav-link nav_item" href="{{url(app()->getLocale().'/blog')}}">{{__('webMessage.BLOG')}}</a></li>
                            <li><a class="nav-link nav_item" href="{{url(app()->getLocale().'/contactus')}}">{{__('webMessage.contactus')}}</a></li>
                            <li><a class="nav-link nav_item"  href="javascript:;" id="trackmyorder" class="trackorder icon-f-55"
                                    title="{{ trans('webMessage.trackorder') }}">&nbsp;<span>{{ __('webMessage.trackorder') }}</span></a></li>
                        </ul>
                    </div>


                    <ul class="navbar-nav attr-nav align-items-center">
                        @include('website.include.cart')
                    </ul>

                    <div class="pr_search_icon">
                        <a href="javascript:void(0);" class="nav-link pr_search_trigger"><i class="linearicons-magnifier"></i></a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- END HEADER -->
    @hasSection('breadcrumb')
    <!-- START SECTION BREADCRUMB -->
    <div class="breadcrumb_section bg_gray page-title-mini">
        <div class="container"><!-- STRART CONTAINER -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="page-title">
                        <h1>@yield('title') @yield('breadcrumb_title2')</h1>
                        @yield('breadcrumb_under_title')
                    </div>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb justify-content-md-end">
                        @yield('breadcrumb')
                    </ol>
                </div>
            </div>
        </div><!-- END CONTAINER-->
    </div>
    <!-- END SECTION BREADCRUMB -->
    @endif
@endif
