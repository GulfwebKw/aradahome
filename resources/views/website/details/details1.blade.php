@extends( ! request()->query('quick_view') ? 'website.include.master' : 'website.details.DONotDelete')
@php
    if(!empty(app()->getLocale())){
    $strLang = app()->getLocale();
    }else{
    $strLang="en";
    }

    $oalign = $strLang=='en'?'right':'left';

    if(!empty($productDetails['seo_description_'.$strLang])){
    $seo_description = $productDetails['seo_description_'.$strLang];
    }else{
    $seo_description = $settingInfo['seo_description_'.$strLang];
    }
    if(!empty($productDetails['seo_keywords_'.$strLang])){
    $seo_keywords = $productDetails['seo_keywords_'.$strLang];
    }else{
    $seo_keywords = $settingInfo['seo_keywords_'.$strLang];
    }
@endphp

@section('title' , $productDetails['title_'.app()->getLocale()] )
@section('description' ,$seo_description )
@section('abstract' ,$seo_description )
@section('keywords' ,$seo_keywords )
@section('header')
    @if(!empty($settingInfo->og_title))
        <meta property="og:title"
              content="@if(!empty($productDetails['title_'.$strLang])){{$productDetails['title_'.$strLang]}}@endif">
    @endif
    @if(!empty($settingInfo->og_description))
        <meta property="og:description"
              content="@if(!empty($productDetails['details_'.$strLang])){!!$productDetails['title_'.$strLang]!!}@endif">
    @endif
    @if(!empty($settingInfo->og_url))
        <meta property="og:url"
              content="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}">
    @endif
    @if(!empty($settingInfo->og_image))
        <meta property="og:image" content="{{url('uploads/product/'.$productDetails->image)}}">
        @if(!empty($prodGalleries))
            @foreach($prodGalleries as $gallery)
                <meta property="og:image" content="{{url('uploads/product/'.$gallery->image)}}">
            @endforeach
        @endif
    @endif

    @if(!empty($settingInfo->og_brand))
        @if(!empty($brandDetails['title_'.$strLang]))
            <meta property="product:brand" content="{{$brandDetails['title_'.$strLang]}}">
        @endif
    @endif
    @if(!empty($settingInfo->og_availability))
        @if($availableQty>0)
            <meta property="product:availability" content="in stock">
        @endif
    @endif
    @if(!empty($settingInfo->og_condition))
        <meta property="product:condition" content="new">
    @endif
    @if(!empty($settingInfo->og_amount))
        @if(!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime)>strtotime(date('Y-m-d')))
            <meta property="product:price:amount" content="{{round($productDetails->countdown_price,3)}}">
            <meta property="product:sale_price_dates:start" content="{{date('Y-m-d')}}">
            <meta property="product:sale_price_dates:end" content="{{$productDetails->countdown_datetime}}">
        @else
            <meta property="product:price:amount" content="{{round($productDetails->retail_price,3)}}">
        @endif
    @endif
    @if(!empty($settingInfo->og_currency))
        <meta property="product:price:currency" content="{{\App\Currency::default(false)->code}}">
    @endif
    @if(!empty($settingInfo->og_retailer_item_id))
        <meta property="product:retailer_item_id" content="{{$productDetails->item_code}}">
    @endif
    @if(!empty($settingInfo->og_title))
        <meta property="product:item_group_id" content="{{$productDetails->item_code}}">
    @endif
    @if(!empty($productDetails->sku_no))
        <meta itemprop="sku" content="{{$productDetails->sku_no}}"/>
    @endif
    <!--end FB tags -->
    <style>.g-recaptcha {
            transform: scale(0.90);
            transform-origin: 0 0;
        }</style>
    @php
        if(!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime)>strtotime(date('Y-m-d'))){
        $gprice = round($productDetails->countdown_price,3);
        }else{
        $gprice = round($productDetails->retail_price,3);
        }
    @endphp
@endsection
@php
    $catTreeName =App\Http\Controllers\webController::getCatTreeNameByPid($productDetails->id);
    $checkBrandDiscount = App\Http\Controllers\webController::checkBrandDiscount($productDetails->brand_id);
    $brandDiscountedPrice = App\Http\Controllers\webController::calByBrandDiscount($productDetails->brand_id, $productDetails->retail_price);
@endphp

@if(! request()->query('quick_view'))
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    {!! str_replace( '<li ' , '<li class="breadcrumb-item"' , $catTreeName) !!}
    <li class="breadcrumb-item active">{{ $productDetails['title_'.app()->getLocale()] }}</li>

@endsection
@section('content')
<style>
#Description p {
    color: black;
}
    iframe:not(.embed-responsive-item) {
        max-width: 100%;
        height: auto;
        aspect-ratio: 6/4;
    }
</style>

    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            @include('website.details.shortDetails')
{{--            </div>--}}
            <div class="row">
                <div class="col-12">
                    <div class="large_divider clearfix"></div>
                </div>
            </div>
        @if(!empty($productDetails['details_'.$strLang]) && strlen($productDetails['details_'.$strLang])>30)
            <div class="row">
                <div class="col-12">
                    <div class="tab-style3">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ !session()->has('reviews') ? 'active' : '' }}" id="Description-tab" data-bs-toggle="tab" href="#Description" role="tab" aria-controls="Description" aria-selected="true">{{strtoupper(__('webMessage.description'))}}</a>
                            </li>
                            @if(!empty($settingInfo->is_review_active))
                            <li class="nav-item">
                                <a class="nav-link {{ session()->has('reviews') ? 'active' : '' }}" id="Additional-info-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">{{strtoupper(__('webMessage.reviews'))}} ({{count($ReviewsLists)}})</a>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content shop_info_tab">
                            <div class="tab-pane fade {{ !session()->has('reviews') ? 'show active' : '' }}" id="Description" role="tabpanel" aria-labelledby="Description-tab">
                                {!!$productDetails['details_'.app()->getLocale()]!!}
                            </div>
                            @if(!empty($settingInfo->is_review_active))
                            <div class="tab-pane fade show {{ session()->has('reviews') ? 'show active' : '' }}" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="comments">
                                    <ul class="list_none comment_list mt-4">
                                        @php
                                            $agRating = 0;
                                            $k=0;
                                        @endphp
                                        @forelse($ReviewsLists as $ReviewsList)
                                            @php
                                                if($ReviewsList->customer_id){
                                                $customerDetails = App\Http\Controllers\webController::getCustomerDetails($ReviewsList->customer_id);
                                                }
                                                $agRating+=$ReviewsList->ratings;
                                            @endphp
                                            <li itemprop="review" itemtype="http://schema.org/Review" itemscope>
                                                <div class="comment_img">
                                                    @if(!empty($customerDetails) && $customerDetails->image)
                                                        <img src="{{url('uploads/customers/thumb/'.$customerDetails->image)}}" style="width: 100%;"/>
                                                    @else
                                                        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTI4IDEyOCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHJvbGU9ImltZyIgYXJpYS1sYWJlbD0ieHhsYXJnZSI+CiAgICA8Zz4KICAgICAgICA8Y2lyY2xlIGN4PSI2NCIgY3k9IjY0IiByPSI2NCIgZmlsbD0iI2MxYzdkMCIgLz4KICAgICAgICA8Zz4KICAgICAgICAgICAgPHBhdGggZmlsbD0iI2ZmZiIKICAgICAgICAgICAgICAgIGQ9Ik0xMDMsMTAyLjEzODggQzkzLjA5NCwxMTEuOTIgNzkuMzUwNCwxMTggNjQuMTYzOCwxMTggQzQ4LjgwNTYsMTE4IDM0LjkyOTQsMTExLjc2OCAyNSwxMDEuNzg5MiBMMjUsOTUuMiBDMjUsODYuODA5NiAzMS45ODEsODAgNDAuNiw4MCBMODcuNCw4MCBDOTYuMDE5LDgwIDEwMyw4Ni44MDk2IDEwMyw5NS4yIEwxMDMsMTAyLjEzODggWiIgLz4KICAgICAgICAgICAgPHBhdGggZmlsbD0iI2ZmZiIKICAgICAgICAgICAgICAgIGQ9Ik02My45OTYxNjQ3LDI0IEM1MS4yOTM4MTM2LDI0IDQxLDM0LjI5MzgxMzYgNDEsNDYuOTk2MTY0NyBDNDEsNTkuNzA2MTg2NCA1MS4yOTM4MTM2LDcwIDYzLjk5NjE2NDcsNzAgQzc2LjY5ODUxNTksNzAgODcsNTkuNzA2MTg2NCA4Nyw0Ni45OTYxNjQ3IEM4NywzNC4yOTM4MTM2IDc2LjY5ODUxNTksMjQgNjMuOTk2MTY0NywyNCIgLz4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPgo=" style="width: 100%;"/>
                                                    @endif
                                                </div>
                                                <div class="comment_block">
                                                    <div class="rating_wrap">
                                                        <div class="rating">
                                                            <div class="product_rate" style="width:{{ round($ReviewsList->ratings * 100 / 5 )  }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div itemprop="reviewRating" itemtype="http://schema.org/Rating" itemscope>
                                                        <meta itemprop="ratingValue" content="{{$ReviewsList->ratings}}" />
                                                        <meta itemprop="bestRating" content="5" />
                                                    </div>
                                                    <p class="customer_meta">
                                                        <span class="review_author">{{$ReviewsList->name}}</span>
                                                        <span class="comment-date">{{ \Carbon\Carbon::parse($ReviewsList->created_at)->diffForHumans() }}</span>
                                                    </p>
                                                    <div class="description">
                                                        <p>{!!$ReviewsList->message!!}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <div class="tt-message-info">
                                                {{__('webMessage.bethefirstreview')}} <strong><span>“{{ $productDetails['title_'.app()->getLocale()] }}”</span></strong>
                                            </div>
                                        @endforelse
                                    </ul>
                                </div>
                                @php
                                    if(isset($k) && empty($k)){$k=1;}

                                    if(empty($agRating)){$agRating=1;}
                                    $avrgRat = !empty($k)?($agRating/$k):1;
                                @endphp
                                <div itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>
                                    <meta itemprop="reviewCount" content="{{$k}}" />
                                    <meta itemprop="ratingValue" content="{{$avrgRat}}" />
                                </div>
                                <div class="review_form field_form">
                                    @if(count($ReviewsLists)==0)
                                    <h5>{{__('webMessage.bethefirstreview')}} “{{ $productDetails['title_'.app()->getLocale()] }}”</h5>
                                    @endif
                                    <p>{{__('webMessage.reviewnote')}}</p>

                                    @if(session('session_msg'))
                                        <div class="alert alert-success">{{session('session_msg')}}</div>
                                    @endif

                                    <form class="row mt-3" name="reviewform" id="reviewform" method="post" action="{{url(app()->getLocale().'/details/'.request()->id.'/'.request()->slug)}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="product_id" value="{{$productDetails->id}}">
                                        <div class="form-group col-md-6 mt-2">
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="{{__('webMessage.enter_name')}}">
                                            @if($errors->has('name'))
                                                <label id="name-error" class="error" for="name">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6 mt-2">
                                            <input type="email" class="form-control" id="email" value="{{ old('email') }}" name="email" placeholder="{{__('webMessage.enter_email')}}">
                                            @if($errors->has('email'))
                                                <label id="email-error" class="error" for="email">{{ $errors->first('email') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group col-6 mt-2">
                                            <select name="ratings" id="ratings" class="form-control">
                                                <option value="5" @if(old('ratings')=="5") selected @endif>{{__('webMessage.superexcellent')}}</option>
                                                <option value="4.5" @if(old('ratings')=="4.5") selected @endif>{{__('webMessage.excellent')}}</option>
                                                <option value="4" @if(old('ratings')=="4") selected @endif>{{__('webMessage.verygood')}}</option>
                                                <option value="3.5" @if(old('ratings')=="3.5") selected @endif>{{__('webMessage.good')}}</option>
                                                <option value="3" @if(old('ratings')=="3") selected @endif>{{__('webMessage.poor')}}</option>
                                                <option value="2.5" @if(old('ratings')=="2.5") selected @endif>{{__('webMessage.verypoor')}}</option>
                                                <option value="2" @if(old('ratings')=="2") selected @endif>{{__('webMessage.notbad')}}</option>
                                                <option value="1.5" @if(old('ratings')=="1.5") selected @endif>{{__('webMessage.bad')}}</option>
                                                <option value="1" @if(old('ratings')=="1") selected @endif>{{__('webMessage.verybad')}}</option>
                                            </select>
                                            @if($errors->has('ratings'))
                                                <label id="ratings-error" class="error" for="ratings">{{ $errors->first('ratings') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group col-12 mt-2">
                                            <textarea class="form-control"  id="message" name="message" placeholder="{{__('webMessage.writeyourreview')}}" rows="8">{{ old('message') }}</textarea>
                                            @if($errors->has('message'))
                                                <label id="message-error" class="error" for="message">{{ $errors->first('message') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="6LeMueQUAAAAAJ-ZUe9ZqGK3pma9VwbeoaYDgJte"></div>
                                            @if($errors->has('recaptchaError'))
                                                <label id="message-error" class="error" for="message">{{ $errors->first('recaptchaError') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group col-12 mt-2">
                                            <button type="submit" class="btn btn-fill-out" name="submit" value="Submit">{{__('webMessage.sendnow')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="small_divider"></div>
                    <div class="divider"></div>
                    <div class="medium_divider"></div>
                </div>
            </div>
        @endif
        @if(!empty($relatedProducts) && count($relatedProducts)>0)
            <div class="row">
                <div class="col-12">
                    <div class="heading_s1">
                        <h3>{{trans('webMessage.related_product')}}</h3>
                    </div>
                    <div class="releted_product_slider carousel_slider owl-carousel owl-theme" data-margin="20" data-responsive='{"0":{"items": "2"}, "481":{"items": "2"}, "768":{"items": "3"}, "1199":{"items": "4"}}'>
                        @foreach($relatedProducts as $relatedProduct)
                            @include('website.include.productV2' , ['productDetails' => $relatedProduct])
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>
    <!-- END SECTION SHOP -->

@endsection
    @section('js')
        <script>
            gtag("event", "view_item", {
                currency: "{{\App\Currency::default()}}",
                value: {{$productDetails->retail_price}},
                items: [
                    {
                        item_id: "{{$productDetails->id}}",
                        item_name: "{{$productDetails->title_en}}",
                        affiliation: "",
                        coupon: "",
                        currency: "{{\App\Currency::default()}}",
                        discount: "",
                        index: 5,
                        item_brand: "{{$brandDetails->title_en??''}}",
                        item_category: "{{$catTreeName}}",
                        price: {{$productDetails->retail_price}},
                        quantity: 1
                    }
                ]
            });
        </script>
    @endsection
@endif

@if(request()->query('quick_view'))
    <div class="ajax_quick_view">
        @include('website.details.shortDetails')
    </div>

    <script src="{{ asset('assets/js/scripts.js') }}"></script>
@endif
