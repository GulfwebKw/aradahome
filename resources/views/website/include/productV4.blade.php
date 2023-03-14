@php
    $isStock = App\Http\Controllers\webCartController::IsAvailableQuantity($productDetails->id);
    $tagsDetails = App\Http\Controllers\webCartController::getTagsName($productDetails->tags_en, $productDetails->tags_ar);
    $checkBrandDiscount = App\Http\Controllers\webController::checkBrandDiscount($productDetails->brand_id);
    $brandDiscountedPrice = App\Http\Controllers\webController::calByBrandDiscount($productDetails->brand_id, $productDetails->retail_price);
@endphp
<div class="product_wrap">
    @if ($productDetails->is_active == '2')
        <span class="pr_flash bg-danger">{{ __('webMessage.preorder') }}</span>
    @elseif(empty($isStock))
        <span class="pr_flash bg-danger">{{ __('webMessage.outofstock') }}</span>
    @elseif( ( ! empty($productDetails->caption_en) and app()->getLocale() == "en"  ) or ( ! empty($productDetails->caption_ar) and app()->getLocale() == "ar"  ) )
        <span class="pr_flash" style="background-color:{{ $productDetails->caption_color }};color:#fff;border-radius:5px;font-size:12px;padding:3px;">{{ $productDetails['caption_'.app()->getLocale()] }}</span>
    @endif
    <div class="product_img">
        <a href="{{ url(app()->getLocale().'/'.'directdetails/' . $productDetails->id . '/' . $productDetails->slug) }}">
            <img src="@if ($productDetails->image) {{ url('uploads/product/thumb/' . $productDetails->image) }} @else {{ url('uploads/no-image.png') }} @endif" alt="{{ $productDetails['title_'.app()->getLocale()] }}">
            @if($productDetails->rollover_image)
                <img class="product_hover_img" src="@if($productDetails->rollover_image) {{url('uploads/product/thumb/'.$productDetails->rollover_image)}} @else {{url('uploads/no-image.png')}} @endif" alt="@if(app()->getLocale()=='en') {{$productDetails->title_en}} @else {{$productDetails->title_ar}} @endif">
            @endif
        </a>
    </div>
    <div class="product_info">
        <span id="responseMsg-{{ $productDetails->id }}"></span>
        <h6 class="product_title"><a href="{{ url(app()->getLocale().'/'.'directdetails/' . $productDetails->id . '/' . $productDetails->slug) }}">{{ $productDetails['title_'.app()->getLocale()] }}</a></h6>
        <div class="product_price">
            @if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d')))
                <span class="price">{{ \App\Currency::default() }} {{ number_format($productDetails->countdown_price, 3) }}</span>
                <input type="hidden" id="pixel_price_{{ $productDetails->id }}" value="{{ $productDetails->countdown_price }}">
                <del>{{ \App\Currency::default() }} {{ number_format($productDetails->old_price,3) }}</del>
            @elseif ($checkBrandDiscount)
                <span class="price">{{ \App\Currency::default() }} {{number_format( $brandDiscountedPrice->price , 3) }}</span>
                <input type="hidden" id="pixel_price_{{ $productDetails->id }}" value="{{ $brandDiscountedPrice->price }}">
                <del>{{ \App\Currency::default() }} {{ number_format($brandDiscountedPrice->oldPrice  , 3)}}</del>
            @else
                <span class="price">{{ \App\Currency::default() }} {{ number_format($productDetails->retail_price , 3) }}</span>
                <input type="hidden" id="pixel_price_{{ $productDetails->id }}" value="{{ $productDetails->retail_price }}">
                @if (!empty($productDetails->old_price))
                    <del>{{ \App\Currency::default() }} {{ number_format($productDetails->old_price,3) }}</del>
                @endif
            @endif
        </div>
        <div class="pr_desc">
            <p>{{ $productDetails['sdetails_'.app()->getLocale()] }}</p>
        </div>

        <div class="rating_wrap">
            @php
                $tempRating = \App\ProductReview::where('product_id', $productDetails->id)->avg('ratings');
                $tempRatingCount = \App\ProductReview::where('product_id', $productDetails->id)->count();
            @endphp
            <div class="rating" style="width: 70px;">
                <div class="product_rate" style="width:{{ round($tempRating * 100 / 5  , 1 )  }}%"></div>
            </div>
            <span class="rating_num">({{ $tempRatingCount }})</span>
        </div>
    </div>
</div>