@php
$settingInfo = App\Http\Controllers\webController::settings();
if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}

if(!empty($singleInfo['seo_description_'.$strLang])){
$seo_description = $singleInfo['seo_description_'.$strLang];
}else{
$seo_description = $settingInfo['seo_description_'.$strLang];
}
if(!empty($singleInfo['seo_keywords_'.$strLang])){
$seo_keywords = $singleInfo['seo_keywords_'.$strLang];
}else{
$seo_keywords = $settingInfo['seo_keywords_'.$strLang];
}
@endphp
<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">

<head>
    <meta charset="utf-8">
    <title>@if(app()->getLocale()=="en" && !empty($settingInfo->name_en)) {{$settingInfo->name_en}}
        @elseif(app()->getLocale()=="ar" && !empty($settingInfo->name_ar)) {{$settingInfo->name_ar}} @endif
        @hasSection('title') | @yield('title') @endif</title>
    <meta name="description" content="@yield('description' , $seo_description)" />
    <meta name="abstract" content="@yield('abstract' , $seo_description)">
    <meta name="keywords" content="@yield('keywords' , $seo_keywords)" />
    <meta name="Copyright" content="{{$settingInfo->name_en}}, Kuwait Copyright 2020 - {{date('Y')}}" />
    <META NAME="Geography" CONTENT="@if(app()->getLocale()==" en") {{$settingInfo->address_en}} @else
    {{$settingInfo->address_ar}} @endif">
    @if($settingInfo->extra_meta_tags) {!!$settingInfo->extra_meta_tags!!} @endif
    @if($settingInfo->favicon && !str_contains(request()->path(), 'products/'))
    <link rel="icon" href="{{url('uploads/logo/'.$settingInfo->favicon)}}">
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include("website.include.css")
    @yield('header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!--preloader -->
    @include("website.include.preloader")
    <!--end preloader -->
    @hasSection('homeSection')
    @yield('homeSection')
    @else
    <!--header -->
    @include("website.include.header")
    <!--end header -->

    <!-- START MAIN CONTENT -->
    <div class="main_content">
        @yield('content')
    </div>
    <!-- END MAIN CONTENT -->



    <!--footer-->
    @include("website.include.footer")
    @endif
    @yield('footer')

    <!-- modal (AddToCartProduct) -->
    @include("website.include.addtocart_modal")

    <a href="#" class="scrollup" style="display: none;"><i class="ion-ios-arrow-up"></i></a>


    <script>
        function fadeOut(el) {
            el.style.opacity = 1;
            (function fade() {
                if ((el.style.opacity -= .1) < 0) {
                    el.style.display = "none";
                } else {
                    requestAnimationFrame(fade);
                }
            })();
        };
        
        setTimeout(function(){
            fadeOut(document.querySelector('.preloader'))
        }, 500)
    </script>
    @include("website.include.js")

    @yield('js')

</body>

</html>
