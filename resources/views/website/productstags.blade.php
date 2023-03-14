@php
    use Illuminate\Support\Facades\Session;
    $settingInfo = App\Http\Controllers\webController::settings();
    $scrumtree = '';
    $pixeltree = '';
    use Illuminate\Support\Facades\Cookie;
    if(app()->getLocale()=="en"){
    $strLang="en";
    }else{
    $strLang="ar";
    }
    if(!empty($categoryDetails['seo_description_'.$strLang])){
    $seo_description = $categoryDetails['seo_description_'.$strLang];
    }else{
    $seo_description = $settingInfo['seo_description_'.$strLang];
    }
    if(!empty($categoryDetails['seo_keywords_'.$strLang])){
    $seo_keywords = $categoryDetails['seo_keywords_'.$strLang];
    }else{
    $seo_keywords = $settingInfo['seo_keywords_'.$strLang];
    }
    $jsonprod='';
    if(!empty($productLists) && count($productLists)>0){
    foreach($productLists as $key=>$productListy){
    $jsonprod.='{"@type":"ListItem","position":"'.($key+1).'","url":"'.url(app()->getLocale().'/details/'.$productListy->id.'/'.$productListy->slug).'"},';
    }
    }
    $pixelids=[];
@endphp
@extends('website.include.master')
{{--@section('title' , __('webMessage.searchResults')  )--}}
@section('description' ,$seo_description )
@section('abstract' ,$seo_description )
@section('keywords' ,$seo_keywords )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    {!! str_replace( '<li ' , '<li class="breadcrumb-item"' , $scrumtree) !!}
@endsection
@section('head')
    @if($jsonprod)
        <script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"ItemList",
      "itemListElement":[{!!trim($jsonprod,',')!!}]
    }
</script>
    @endif
@endsection
@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row align-items-center mb-4 pb-1">
                        <div class="col-12">
                            <div class="product_header">
                                <div class="product_header_left">
                                    <div class="custom_select">
                                        <select name="product_sort_by" id="product_sort_by"   mykey="ptag" class="form-control form-control-sm">
                                            <option value="">{{ __('webMessage.latestitems') }}</option>
                                            <option value="popular" @if (session('brandbrand_sort_by') == 'popular') selected @endif>
                                                {{ __('webMessage.mostpopular') }}</option>
                                            <option value="max-price" @if (session('brandbrand_sort_by') == 'max-price') selected @endif>
                                                {{ __('webMessage.max_price') }}</option>
                                            <option value="min-price" @if (session('brandbrand_sort_by') == 'min-price') selected @endif>
                                                {{ __('webMessage.min_price') }}</option>
                                            <option value="a-z" @if (session('brandbrand_sort_by') == 'a-z') selected @endif>{{ __('webMessage.atoz') }}
                                            </option>
                                            <option value="z-a" @if (session('brandbrand_sort_by') == 'z-a') selected @endif>{{ __('webMessage.ztoa') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="product_header_right">
                                    <div class="products_view">
                                        <a href="javascript:Void(0);" class="shorting_icon grid active"><i class="ti-view-grid"></i></a>
                                        <a href="javascript:Void(0);" class="shorting_icon list"><i class="ti-layout-list-thumb"></i></a>
                                    </div>
                                    <div class="custom_select">
                                        <select  class="form-control form-control-sm" name="product_per_page" id="product_per_page" mykey="ptag">
                                            <option value="12" @if (session('brandbrand_per_page') == '12') selected @endif>{{ __('webMessage.show') }}
                                            </option>
                                            <option value="24" @if (session('brandbrand_per_page') == '24') selected @endif>24</option>
                                            <option value="48" @if (session('brandbrand_per_page') == '48') selected @endif>48</option>
                                            <option value="96" @if (session('brandbrand_per_page') == '96') selected @endif>96</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!empty($productLists) && count($productLists) > 0)
                        <div class="row shop_container grid">
                            @php $tagsDetails=''; @endphp
                            @foreach ($productLists as $productList)
                                @include('website.include.productV1' , ['productDetails' => $productList])
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-12 mt-2 mt-md-4">
                                <div class="pagination pagination_style1 justify-content-center">
                                    {!! $productLists->appends($_GET)->links() !!}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center tt_product_showmore">
                            {{ __('webMessage.norecordfound') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection