<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{$settingInfo->name_en}} | @yield('title' , 'Driver panel')</title>
    <meta name="description" content="No aside layout examples">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--begin::Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">

    <!--end::Fonts -->


    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="{!! url('admin_assets/assets/plugins/global/plugins.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('admin_assets/assets/css/style.bundle.css') !!}" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->

    <!--begin::Layout Skins(used by all pages) -->
    <link href="{!! url('admin_assets/assets/css/skins/header/base/light.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('admin_assets/assets/css/skins/header/menu/light.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('admin_assets/assets/css/skins/brand/light.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('admin_assets/assets/css/skins/aside/dark.css') !!}" rel="stylesheet" type="text/css" />

    <!--end::Layout Skins -->
    <!--end::Layout Skins -->
    @if($settingInfo->favicon)
        <link rel="shortcut icon" href="{{url('uploads/logo/'.$settingInfo->favicon)}}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @media (min-width: 1025px) {
            .kt-header--fixed.kt-subheader--fixed.kt-subheader--enabled .kt-wrapper {
                padding-top: 62px !important;
            }
        }
        @font-face {
            font-family: 'kufi';
            src: url('{{url('assets/css/kufi/kufi.eot?#iefix')}}') format('embedded-opentype'),
            url('{{url('assets/css/kufi/kufi.woff')}}') format('woff'),
            url('{{url('assets/css/kufi/kufi.ttf')}}')  format('truetype'),
            url('{{url('assets/css/kufi/kufi.svg#DroidArabicKufi')}}') format('svg');
            font-weight: normal;
            font-style: normal;
        }
        .rtl {
            font-family: 'kufi', sans-serif;
            direction: rtl;
        }
    </style>
    @yield('header')

</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="{{route('driver.admin.home')}}">
            @if($settingInfo['logo'])
                <img alt="{{ $settingInfo->name_en }}" src="{!! url('uploads/logo/'.$settingInfo['logo']) !!}" height="40" />
            @endif
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
{{--        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>--}}
    </div>
</div>

<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                <!-- begin:: Header Menu -->

                <!-- Uncomment this to display the close button of the panel
<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
-->
                <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                    <div class="kt-header-logo">
                        <a href="{{route('driver.admin.home')}}">
                            @if($settingInfo['logo'])
                                <img alt="{{ $settingInfo->name_en }}" src="{!! url('uploads/logo/'.$settingInfo['logo']) !!}" height="40" />
                            @endif
                        </a>
                    </div>
                    <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
                        <div class="kt-header__topbar-user kt-hidden-desktop" style="padding: 15px;">
                            <img style="border-radius: 10px; max-width: 25%" class="mr-3" alt="Pic" src="{{Auth::guard('driver')->check() ? ( Auth::guard('driver')->user()->avatar ? url('uploads/users/'.Auth::guard('driver')->user()->avatar) : url('uploads/users/no-image.png')  ) : ( Auth::guard('admin')->user()->image ? url('uploads/users/'.Auth::guard('admin')->user()->image) : url('uploads/users/no-image.png')  ) }} ">
                            <span class="kt-header__topbar-username" style="top: 15px;position: absolute;">Hi, {{Auth::guard('driver')->check() ? Auth::guard('driver')->user()->first_name_en : Auth::guard('admin')->user()->name}}</span>
                            <buttn class="btn btn-icon btn-outline-danger btn-sm kt-pull-right"><i class="fa fa-sign-out-alt"></i></buttn>
                        </div>
                        <ul class="kt-menu__nav ">
                            @if(auth()->guard('admin')->check())
                            <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true">
                                <a href="{{route('driver.admin.home')}}" class="kt-menu__link">
                                    <span class="kt-menu__link-text">Assign orders to drivers</span>
                                </a>
                            </li>
                            <li class="kt-menu__item  kt-menu__item--submenu kt-menu__item--rel" data-ktmenu-submenu-toggle="hover" aria-haspopup="true">
                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                    <span class="kt-menu__link-text">Drivers</span>
                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                    <ul class="kt-menu__subnav">

                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.driver.index') }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-users"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Drivers</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.assigned_history')}}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-history"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Latest Tasks</span>
{{--                                                <span class="kt-menu__link-badge">--}}
{{--                                                    <span class="kt-badge kt-badge--success kt-badge--rounded">3</span>--}}
{{--                                                </span>--}}
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.driver.print' , ['en' ,null, 'allDriver' => true]) }}" target="_blank" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-print"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Print Drivers Barcode (En)</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.driver.print' , ['ar' ,null, 'allDriver' => true]) }}" target="_blank" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-print"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Print Drivers Barcode (Ar)</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="kt-menu__item  kt-menu__item--submenu kt-menu__item--rel" data-ktmenu-submenu-toggle="hover" aria-haspopup="true">
                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                    <span class="kt-menu__link-text">Orders</span>
                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                    <ul class="kt-menu__subnav">
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.search' , ['pending']) }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-dolly"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Pending Orders</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.search' , ['outfordelivery']) }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-shipping-fast"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Out for delivery</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.search' , ['completed']) }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-handshake"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Completed Orders</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.search' , ['returned']) }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-thumbs-down"></i>
                                                </span>
                                                <span class="kt-menu__link-text">Refund Orders</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true">
                                            <a href="{{ route('driver.admin.orders.search') }}" class="kt-menu__link ">
                                                <span class="kt-menu__link-icon">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </span>
                                                <span class="kt-menu__link-text">All Orders</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="kt-menu__item   kt-hidden-mobile " >
                                <a href="{{ route('driver.admin.logout') }}" class="bottomNavbar btn btn-bold btn-font-md btn-sm btn-warning">
                                    <span class="kt-menu__link-text">Sign Out</span>
                                </a>
                            </li>
                            @else
                                <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true">
                                    <a href="{{route('driver.panel.dashboard')}}" class="kt-menu__link">
                                        <i class="fa fa-shipping-fast"></i>
                                        <span class="kt-menu__link-text"> My task</span>
                                    </a>
                                </li>
                                <li class="kt-menu__item" aria-haspopup="true">
                                    <a href="{{route('driver.panel.dashboard')}}" class="kt-menu__link">
                                        <i class="fa fa-handshake"></i>
                                        <span class="kt-menu__link-text"> History</span>
                                    </a>
                                </li>
                                <li class="kt-menu__item  kt-hidden-mobile " >
                                    <a href="{{ route('driver.panel.logout') }}" class="bottomNavbar btn btn-bold btn-font-md btn-sm btn-warning">
                                        <span class="kt-menu__link-text">Sign Out</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- end:: Header Menu -->

                <!-- begin:: Header Topbar -->
                <div class="kt-header__topbar">
                    <!--begin: User Bar -->
                    <div class="kt-header__topbar-item kt-header__topbar-item--user">
                        <div class="kt-header__topbar-wrapper">
                            <div class="kt-header__topbar-user">
                                @if(auth()->guard('admin')->check())
                                    <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
                                    <span class="kt-header__topbar-username kt-hidden-mobile">{{Auth::guard('admin')->user()->name}}</span>
                                    @if(Auth::guard('admin')->user()->image)
                                        <img class="kt-avatar" alt="Pic" src="{!! url('uploads/users/'.Auth::guard('admin')->user()->image) !!}" />
                                    @else
                                        <img class="kt-avatar" alt="Pic" src="{!! url('uploads/users/no-image.png') !!}" />
                                    @endif
                                @else
                                    <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
                                    <span class="kt-header__topbar-username kt-hidden-mobile">{{Auth::guard('driver')->user()->full_name}}</span>
                                    @if(Auth::guard('driver')->user()->avatar)
                                        <img class="kt-avatar" alt="Pic" src="{!! url('uploads/users/'.Auth::guard('driver')->user()->avatar) !!}" />
                                    @else
                                        <img class="kt-avatar" alt="Pic" src="{!! url('uploads/users/no-image.png') !!}" />
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end: User Bar -->
                </div>
                <!-- end:: Header Topbar -->
            </div>

            <!-- end:: Header -->
            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                    <!--Begin::Dashboard 4-->

                    @if(Session::get('message-success'))
                        <div class="alert alert-light alert-success" role="alert">
                            <div class="alert-icon">
                                <i class="flaticon-alert kt-font-brand"></i>
                            </div>
                            <div class="alert-text">
                                {{ Session::get('message-success') }}
                            </div>
                        </div>
                    @endif

                    @if(Session::get('message-error'))
                        <div class="alert alert-light alert-danger" role="alert">
                            <div class="alert-icon">
                                <i class="flaticon-alert kt-font-brand"></i>
                            </div>
                            <div class="alert-text">
                                {{ Session::get('message-error') }}
                            </div>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-light alert-danger" role="alert">
                            <div class="alert-icon">
                                <i class="flaticon-alert kt-font-brand"></i>
                            </div>
                            <div class="alert-text">
                                {!! implode('', $errors->all('<div>:message</div>')) !!}
                            </div>
                        </div>
                    @endif

                    @yield('content')

                    <!--End::Dashboard 4-->
                </div>
                <!-- end:: Content -->
            </div>
            <!-- begin:: Footer -->
            <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer" >
                <div class="kt-container  kt-container--fluid lessImportantTD">
                    <div class="kt-footer__copyright ">
                        {!! __('adminMessage.websiteFooter') !!}
                    </div>
                    <div class="kt-footer__menu">

                    </div>
                </div>
                @if(auth()->guard('driver')->check())
                    <button onclick="openCameraAndStartScan();" class="btn btn-font-lg btn-label-warning-o2 btn-taller kt-hidden-desktop w-100"> <i class="fa fa-camera"></i> Scan barcode</button>
                @endif
            </div>
            <!-- end:: Footer -->
        </div>
    </div>
</div>

<!-- end:: Page -->



@if(auth()->guard('admin')->check())
<!--begin::Modal-->
<div class="modal fade" id="search_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="search_modal_label">Search Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="driverSearchModal" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="recipient-name" class="form-control-label">ID:</label>
                                <input type="text" class="form-control driverSearchModalPress" name="id" value="{{ strtoupper($settingInfo->prefix.'D') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="recipient-name" class="form-control-label">Name:</label>
                                <input type="text" class="form-control driverSearchModalPress" name="name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recipient-name" class="form-control-label">Username:</label>
                                <input type="text" class="form-control driverSearchModalPress" name="username">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recipient-name" class="form-control-label">Phone:</label>
                                <input type="text" class="form-control driverSearchModalPress" name="phone">
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="modalSearchResult">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->
@endif

<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
<!-- end::Scrolltop -->

<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": [
                    "#c5cbe3",
                    "#a1a8c3",
                    "#3d4465",
                    "#3e4466"
                ],
                "shape": [
                    "#f0f3ff",
                    "#d9dffa",
                    "#afb4d4",
                    "#646c9a"
                ]
            }
        }
    };
</script>

<!-- end::Global Config -->

<!--begin::Global Theme Bundle(used by all pages) -->
<script src="{!! url('admin_assets/assets/plugins/global/plugins.bundle.js') !!}" type="text/javascript"></script>
<script src="{!! url('admin_assets/assets/js/scripts.bundle.js') !!}" type="text/javascript"></script>

<!--end::Global Theme Bundle -->
<script>
    let barcode = "";
    let reading = false;

    document.addEventListener("keydown", e => {
        if (e.key == 'Enter') {
            if (typeof barcodeRead !== 'undefined' && typeof barcodeRead === 'function' && barcode !== "") {
                barcodeRead(barcode);
            }
            barcode = "";
        } else {
            if (e.key != 'Shift') {
                barcode += e.key;
            }
        }
        if (!reading) {
            reading = true;
            setTimeout( () => {
                barcode = "";
                reading = false;
            }, 200);
        }
    }, true);


    @if(auth()->guard('admin')->check())
        var functionSearch ;
        $('.driverSearchModalPress').keyup(function(e) {
            clearTimeout($.data(this, 'timer'));
            if (e.keyCode == 13)
                searchDriverModal();
            else
                $(this).data('timer', setTimeout(searchDriverModal, 500));
        });
        function searchDriverModal() {
            var existingForm = $("#driverSearchModal").serialize();
            $.post("{{ route('driver.admin.ajax.searchDriver') }}",existingForm,function(data, status){
                if ( status === "success") {
                    $("#modalSearchResult").html("");
                    data.forEach(functionSearch);
                }
            });
        }
    @endif

    function curl(link , callFun){
        $.ajax({
            url: link,
            dataType: 'json',
            success: function (result) {
                callFun(true , result , 200);
            },
            error:function (xhr){
                callFun(false , xhr.responseText , xhr.status);
            }
        });
        return false;
    }
</script>

@include('driver.panel.include.footer')
@yield('js')
</body>

<!-- end::Body -->
</html>