<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/animate.css')}}">
<!-- Latest Bootstrap min CSS -->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/bootstrap/css/bootstrap.min.css')}}">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
<!-- Icon Font CSS -->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/all.min.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/ionicons.min.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/themify-icons.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/linearicons.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/flaticon.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/simple-line-icons.css')}}">
<!--- owl carousel CSS-->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/owlcarousel/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/owlcarousel/css/owl.theme.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/owlcarousel/css/owl.theme.default.min.css')}}">
<!-- Magnific Popup CSS -->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/magnific-popup.css')}}">
<!-- Slick CSS -->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/slick.css')}}">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/slick-theme.css')}}">
<!-- Style CSS -->
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/style.css')}}?v2">
<link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/responsive.css?v1')}}?v1">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@if ( app()->getLocale() == "ar" )
    <!-- RTL CSS -->
    <link rel="stylesheet" href="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/css/rtl-style.css')}}">
@endif
{{--<style>--}}
    {{--.list .product_title {--}}
    {{--    text-align: {{ app()->getLocale() == "ar" ? "right" : "left" }} !important;--}}
    {{--}--}}
{{--</style>--}}
<style>
     #pro-search-form .select2.select2-container {
         width: 100% !important;
     }
    #pro-search-form .select2.select2-container .select2-selection{
        box-sizing: border-box  !important;
        margin: 0  !important;
        font-family: inherit  !important;
        display: block  !important;
        font-size: 1rem  !important;
        font-weight: 400  !important;
        line-height: 1.5  !important;
        background-color: #fff  !important;
        background-clip: padding-box  !important;
        border: 1px solid #ced4da  !important;
        appearance: none  !important;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out  !important;
        color: #000  !important;
        box-shadow: none  !important;
        height: 50px  !important;
        padding-right: 60px   !important;
        position: relative  !important;
        flex: 1 1 auto  !important;
        min-width: 0  !important;
        padding: 10px 20px  !important;
        border-radius: 30px  !important;
    }
    #pro-search-form .select2-selection__choice{
        display: none !important;
    }
</style>