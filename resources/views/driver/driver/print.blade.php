<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{$settingInfo->name_en}} | Print driver</title>
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
        body {
            background-color: #ffffff;
        }
        @media print {
            @page {
                size: @if ( ! $allDriver ) 95mm 55mm @else 297mm 210mm  @endif ;
                margin: @if (! $allDriver) 0mm 0mm 0mm 0mm @else 10mm 10mm 10mm 10mm  @endif ; ;
            }
            .badge {
                border: 0px solid #000;
            }
            .pageBlank {
                page-break-inside: always;
            }
        }
    </style>
</head>
<body>
    @forelse($drivers as $driver)
        @if($loop->iteration % 2 == 1)
            <div>
        @endif
        @if ( $lang == "en" )
            <div class="kt-portlet__body kt-portlet__body--fit-y" style="width: 480px;margin: 20px;float: left;">
                <!--begin::Widget -->
                <div class="border border-info kt-widget kt-widget--user-profile-1 pl-3 pr-3 mb-3 kt-iconbox--wave " style="border-radius: 37px;">
                    <div class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--flag kt-ribbon--ver kt-ribbon--border-dash-hor kt-ribbon--info">
                        <div class="kt-ribbon__target" style="top: 0; right: 20px; height: 45px;">
                            <span class="kt-ribbon__inner"></span><i class="fa fa-shipping-fast"></i>
                        </div>
                        <div class="mt-3 w-100" style="display: inline-flex;">
                            <div class="ml-1 mr-1">
                                <img src="{!! url('uploads/logo/'.$settingInfo->favicon) !!}" style="max-height: 50px;" alt="image">
                            </div>
                            <div class="w-75">
                                <h3 class="kt-portlet__head-title text-center" style="font-size: 1.4rem;font-weight: bold;color: #48465b;">
                                    {{$settingInfo->name_en}}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-widget__head kt-ribbon mt-4">
                        <div class="kt-widget__media">
                            <img src="{!! url('uploads/users/'.($driver->avatar ?? 'no-image.png')) !!}" alt="image" style="min-width: 38mm;max-height: 75mm;max-width: 45mm;border-radius: 8px;">
                            <div class="badge badge-info font-weight-bold mt-1 text-center w-100" style="font-size: inherit;">{{$settingInfo->prefix.'D'.$driver->id}}</div>
                        </div>
                        <div class="kt-widget__content w-100" style="padding-right: 1.6rem;">
                            <div class="kt-widget__section">
                                <div href="#" class="kt-widget__username">
                                    <strong>Name: </strong><span class="FullNameEn">{{  $driver->first_name_en  }} {{ $driver->last_name_en  }}</span>
                                    @if ( $driver->is_active)
                                        <i class="flaticon2-correct kt-font-success"></i>
                                    @endif
                                </div>
                                <div class="rtl text-right  mt-2" >
                                    <strong>الاسم: </strong><span class="">{{  $driver->first_name_ar  }} {{  $driver->last_name_ar }}</span>
                                </div>
                                @if($allDriver)
                                <div class="mt-2" >
                                    <strong>Phone: </strong><span>{{  $driver->phone  }}</span>
                                </div>
                                @endif
                            </div>
                            {{--                                <div class="kt-widget__action UserName"></div>--}}
                            <div class="mt-3">
                                <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode($settingInfo->prefix.'D'.$driver->id, $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Widget -->
            </div>
        @else
            <div class="kt-portlet__body kt-portlet__body--fit-y rtl" style="width: 480px;margin: 20px;float: left;">

                <!--begin::Widget -->
                <div class="border border-info kt-widget kt-widget--user-profile-1 pl-3 pr-3 mb-3 kt-iconbox--wave " style="border-radius: 37px;">
                    <div class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--flag kt-ribbon--ver kt-ribbon--border-dash-hor kt-ribbon--info">
                        <div class="kt-ribbon__target" style="top: 0; left: 20px; height: 45px;">
                            <span class="kt-ribbon__inner"></span><i class="fa fa-shipping-fast"></i>
                        </div>
                        <div class="mt-3 w-100" style="display: inline-flex;">
                            <div class="ml-1 mr-1">
                                <img src="{!! url('uploads/logo/'.$settingInfo->favicon) !!}" style="max-height: 50px;" alt="image">
                            </div>
                            <div class="w-75">
                                <h3 class="kt-portlet__head-title text-center" style="font-size: 1.4rem;font-weight: bold;color: #48465b;">
                                    {{$settingInfo->name_ar}}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-widget__head kt-ribbon mt-4">
                        <div class="kt-widget__media">
                            <img src="{!! url('uploads/users/'.($driver->avatar ?? 'no-image.png')) !!}" alt="image" style="min-width: 38mm;max-height: 75mm;max-width: 45mm;border-radius: 8px;">
                            <div class="badge badge-info font-weight-bold mt-1 text-center w-100" style="font-size: inherit;">{{$settingInfo->prefix.'D'.$driver->id}}</div>
                        </div>
                        <div class="kt-widget__content w-100" style="padding-right: 1.6rem;">
                            <div class="kt-widget__section">
                                <div href="#" class="kt-widget__username rtl text-right">
                                    <strong>الاسم: </strong><span class="">{{ $driver->first_name_ar }} {{ $driver->last_name_ar }}</span>
                                    @if ( $driver->is_active)
                                        <i class="flaticon2-correct kt-font-success"></i>
                                    @endif                                </div>
                                <div class="mt-2" >
                                    <strong>Name: </strong><span class="  ">{{$driver->first_name_en }} {{  $driver->last_name_en }}</span>
                                </div>
                                @if ( $allDriver)
                                <div class="mt-2" >
                                    <strong>Phone: </strong><span class="  ">{{$driver->phone}}</span>
                                </div>
                                    @endif
                            </div>
                            {{--                                <div class="kt-widget__action UserName"></div>--}}
                            <div class="mt-3 text-right">
                                <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode( $settingInfo->prefix.'D'.$driver->id, $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Widget -->
            </div>
        @endif
        @if($loop->last)
            <script>
                setTimeout(function () { window.print(); }, 0);
                window.onload = function () { setTimeout(function () {
                    @if( $drivers->currentPage() < $drivers->lastPage() )
                        window.location.href = "{!! $drivers->nextPageUrl() . ($allDriver ? '&allDriver=1' : '') !!}";
                    @else
                        window.close();
                    @endif
                }, 0); }
            </script>
        @endif
        @if($loop->iteration % 2 == 0)
            </div>
        @endif
    @empty
        <script>
            window.close();
        </script>
    @endforelse
</body>
</html>