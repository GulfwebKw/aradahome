<style>
    .color-label{
        font-size: .7rem;
        text-align: center;
        margin: 6px;
    }
</style>
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
                <div class="product-image">
                    @if($productDetails->is_active=='2')
                        <span class="pr_flash bg-danger">{{__('webMessage.preorder')}}</span>
                    @elseif( ( ! empty($productDetails->caption_en) and app()->getLocale() == "en"  ) or ( ! empty($productDetails->caption_ar) and app()->getLocale() == "ar"  ) )
                        <span class="pr_flash" style="background-color:{{ $productDetails->caption_color }};color:#fff;border-radius:5px;font-size:12px;padding:3px;">{{ $productDetails['caption_'.app()->getLocale()] }}</span>
                    @endif
                    @if($productDetails->image)
                        <div class="product_img_box">
                            <img id="displaym-{{$productDetails->id}}" src='{{url('uploads/product/'.$productDetails->image)}}' data-zoom-image="{{url('uploads/product/'.$productDetails->image)}}" alt="{{ $productDetails['title_'.app()->getLocale()] }}" />
                            <a href="#" class="product_img_zoom" title="Zoom">
                                <span class="linearicons-zoom-in"></span>
                            </a>
                        </div>
                    @else
                        <div class="product_img_box">
                            <img id="displaym-{{$productDetails->id}}" src="{{url('uploads/no-image.png')}}" alt="">
                        </div>
                    @endif
                    @if(!empty($prodGalleries))
                        <div id="pr_item_gallery" class="product_gallery_item slick_slider" data-slides-to-show="4" data-slides-to-scroll="1" data-infinite="false">
                            @foreach($prodGalleries as $gallery)
                                <div class="item">
                                    <a href="#" class="product_gallery_item" data-image="{{url('uploads/product/'.$gallery->image)}}" data-zoom-image="{{url('uploads/product/'.$gallery->image)}}">
                                        <img src="{{url('uploads/product/thumb/'.$gallery->image)}}" alt="{{ $productDetails['title_'.app()->getLocale()] }}" />
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($productDetails->youtube_url_id and $productDetails->youtube_url)
                        <div>
                            <div class="embed-responsive embed-responsive-16by9 mt-2" style="border: 2px solid #ffbb00; padding: 10px">
                                <iframe class="embed-responsive-item w-100"
                                        src="https://www.youtube.com/embed/{{ $productDetails->youtube_url_id }}" allowfullscreen
                                        height="300"></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pr_detail">
                    <form name="addtocartDetailsForm" @if(request()->query('quick_view')) id="addtocartDetailsForm_{{$productDetails->id}}" @else  id="addtocartDetailsForm" @endif method="POST"
                          action="{{route('addtocartDetails',app()->getLocale())}}" enctype="multipart/form-data">

                        <div class="product_description">
                            <h4 class="product_title">@if(app()->getLocale()=="en") {{$productDetails->title_en}} @else {{$productDetails->title_ar}} @endif</h4>
                            @if(request()->query('quick_view'))
                            <div>@if(app()->getLocale()=="en") {{$productDetails->sdetails_en}} @else {{$productDetails->sdetails_ar}} @endif</div>
                            @endif
                            @if($productDetails->customizable)
                                <div class="clearfix"></div>
                                <div>
                                    <a class="btn btn-primary" href="{{url('/product/'.$productDetails->id.'/customize')}}"
                                       target="_blank">
                                        {{__('webMessage.customize_this_product')}}
                                    </a>
                                </div>
                            @endif
                            <div class="clearfix"></div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="product_id" id="product_id" value="{{$productDetails->id}}">

                                @if(!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime)>strtotime(date('Y-m-d')))
                                    <input type="hidden" name="price" id="unit_price"
                                           value="{{$productDetails->countdown_price}}">
                                @else
                                    <input type="hidden" name="price" id="unit_price"
                                           value="{{$productDetails->retail_price}}">
                                @endif
                                @if(!empty($productDetails->is_attribute) && $availableQty>0)
                                    @if(!empty($productoptions) && count($productoptions)>0)
                                        @foreach($productoptions as $productoption)
                                            <input type="hidden" name="option_id[]"
                                                   id="option_id_{{$productoption->id}}" value="{{$productoption->id}}">

                                            <!--check custom option for size/color - 1,2,3-->
                                            @if($productoption->custom_option_id==1)
                                                <input type="hidden" name="option_sc"
                                                       id="option_sc_{{$productoption->id}}"
                                                       value="{{$productoption->custom_option_id}}">
                                                @php
                                                    $SizeAttributes = App\Http\Controllers\webCartController::getSizeByCustomIdProductId($productoption->custom_option_id,$productDetails->id);
                                                @endphp
                                                        <!--size-->
                                                @if(!empty($SizeAttributes) && count($SizeAttributes)>0)
                                                    <div class="pr_switch_wrap">
                                                        <span class="switch_lable">{{__('webMessage.size')}}*:</span>
                                                        <div class="form-group">
                                                            <select class="form-control size_attr" name="size_attr"
                                                                    id="size_attr_{{$productDetails->id}}">
                                                                <option value="0">{{__('webMessage.choosesize')}}</option>
                                                                @foreach($SizeAttributes as $SizeAttribute)
                                                                    @php if($strLang=="en"){ $sizeName = $SizeAttribute->title_en;}else{$sizeName = $SizeAttribute->title_ar;}@endphp
                                                                    <option value="{{$SizeAttribute->size_id}}">{{$sizeName}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!--size end -->
                                            @elseif($productoption->custom_option_id==2)
                                                <input type="hidden" name="option_sc"
                                                       id="option_sc_{{$productoption->id}}"
                                                       value="{{$productoption->custom_option_id}}">
                                                @php
                                                    $ColorAttributes = App\Http\Controllers\webCartController::getColorByCustomIdProductId($productoption->custom_option_id,$productDetails->id);
                                                @endphp
                                                        <!--color-->
                                                @if(!empty($ColorAttributes) && count($ColorAttributes)>0)
                                                    <input type="hidden" name="is_color" id="is_color" value="1">
                                                    <input type="hidden" name="color_attr" id="color_attr" value="">
                                                    <div class="pr_switch_wrap">
                                                        <span class="switch_lable">{{__('webMessage.texture')}}:</span>
                                                        <ul class="tt-options-swatch options-large">
                                                            @foreach($ColorAttributes as $ColorAttribute)
                                                                @php
                                                                    if($ColorAttribute->color_code){$colorcode=$ColorAttribute->color_code;}else{$colorcode='none';}
                                                                @endphp

                                                                @if(!empty($ColorAttribute->image))
                                                                    <li id="li-{{$ColorAttribute->color_id}}">
                                                                        <a class="options-color mx-auto" href="javascript:;" id="{{$ColorAttribute->color_id}}">
                                                                                    <span class="swatch-img">
                                                                                        <img src="{{url('uploads/color/thumb/'.$ColorAttribute->image)}}" alt="">
                                                                                    </span>
                                                                            <span class="swatch-label color-black"></span>
                                                                        </a>
                                                                        <h6 class="color-label">{{ $ColorAttribute['title_'.app()->getLocale()] }}</h6>
                                                                    </li>
                                                                @else
                                                                    <li id="li-{{$ColorAttribute->color_id}}"><a href="javascript:;"
                                                                                                                 class="options-color mx-auto"
                                                                                                                 style="background-color:{{$colorcode}};"
                                                                                                                 id="{{$ColorAttribute->color_id}}"></a>
                                                                        <h6  class="color-label">{{ $ColorAttribute['title_'.app()->getLocale()] }}</h6></li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            @elseif($productoption->custom_option_id==3)
                                                <input type="hidden" name="option_sc" id="option_sc_{{$productoption->id}}" value="{{$productoption->custom_option_id}}">
                                                <!--size & color-->
                                                @php
                                                    $SizeAttributes = App\Http\Controllers\webCartController::getSizeByCustomIdProductId($productoption->custom_option_id,$productDetails->id);
                                                @endphp
                                                        <!--size-->
                                                @if(!empty($SizeAttributes) && count($SizeAttributes)>0)
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{__('webMessage.size')}}:</div>
                                                        <div class="form-group">
                                                            <select class="form-control size_attr" name="size_attr" id="size_attr_{{$productDetails->id}}">
                                                                <option value="0">{{__('webMessage.choosesize')}}</option>
                                                                @foreach($SizeAttributes as $SizeAttribute)
                                                                    @php if($strLang=="en"){ $sizeName = $SizeAttribute->title_en;}else{$sizeName = $SizeAttribute->title_ar;}@endphp
                                                                    <option value="{{$SizeAttribute->size_id}}">{{$sizeName}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif

                                                @php
                                                    $ColorAttributes = App\Http\Controllers\webCartController::getColorByCustomIdProductId($productoption->custom_option_id,$productDetails->id);
                                                @endphp
                                                        <!--color-->
                                                @if(!empty($ColorAttributes) && count($ColorAttributes)>0)
                                                    <input type="hidden" name="is_color" id="is_color" value="1">
                                                    <input type="hidden" name="color_attr" id="color_attr" value="">
                                                    <span id="color_box">
                                                        <div class="tt-wrapper">
                                                            <div class="tt-title-options">{{__('webMessage.texture')}}:</div>
                                                            <ul class="tt-options-swatch options-large">
                                                                @foreach($ColorAttributes as $ColorAttribute)
                                                                    @php
                                                                        if($ColorAttribute->color_code){$colorcode=$ColorAttribute->color_code;}else{$colorcode='none';}
                                                                    @endphp

                                                                    @if(!empty($ColorAttribute->image))
                                                                        <li>
                                                                            <a class="options-color mx-auto"  href="javascript:;" id="{{$ColorAttribute->color_id}}">
                                                                                <span class="swatch-img">
                                                                                    <img src="{{url('uploads/color/thumb/'.$ColorAttribute->image)}}" alt="">
                                                                                </span>
                                                                                <span class="swatch-label color-black"></span>
                                                                            </a>
                                                                            <h6 class="color-label">{{ $ColorAttribute['title_'.app()->getLocale()] }}</h6>
                                                                        </li>
                                                                    @else
                                                                        <li><a href="javascript:;" class="options-color mx-auto" style="background-color:{{$colorcode}};" id="{{$ColorAttribute->color_id}}" ></a>
                                                                        <h6  class="color-label">{{ $ColorAttribute['title_'.app()->getLocale()] }}</h6></li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                            <br clear="all">
                                                       </div>
                                                    </span>
                                                    <br clear="all">
                                                @endif

                                                <!--size & color end -->
                                            @else
                                                <!--optiona details-->
                                                @php
                                                    $customOptions = App\Http\Controllers\webCartController::getCustomOptions($productoption->custom_option_id,$productDetails->id);

                                                @endphp

                                                        <!--radio box -->
                                                @if(!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType']=="radio")
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}} @if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <ul class="optionradio">
                                                            @if(!empty($customOptions['childs']) && count($customOptions['childs'])>0)
                                                                @php $is_cadd_txt=''; @endphp
                                                                @foreach($customOptions['childs'] as $child)
                                                                    @php
                                                                        if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==1){
                                                                        $is_cadd="+";

                                                                        $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                        }else if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==2){
                                                                        $is_cadd="-";
                                                                        $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                        }else if(!empty($child->retail_price) && empty($child->is_price_add)){
                                                                        $is_cadd="";
                                                                        $is_cadd_txt=$child->retail_price.' '.\App\Currency::default();
                                                                        }else{
                                                                        $is_cadd="";
                                                                        $is_cadd_txt="";
                                                                        }

                                                                        $option_value_name = $strLang=="en"?$child->option_value_name_en:$child->option_value_name_ar;
                                                                    @endphp
                                                                    <li>
                                                                        <label for="option-{{$productDetails->id}}-{{$productoption->custom_option_id}}-{{$child->id}}"><input
                                                                                    class="checkOptionPrice"
                                                                                    type="radio"
                                                                                    name="option-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                                    id="option-{{$productDetails->id}}-{{$productoption->custom_option_id}}-{{$child->id}}"
                                                                                    value="{{$child->id}}">&nbsp;{{$option_value_name}}
                                                                            ({{$is_cadd_txt}})</label></li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @endif
                                                <!--end radio box -->
                                                <!--check box -->
                                                @if(!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType']=="checkbox")
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}}@if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <ul class="optionradio">
                                                            @if(!empty($customOptions['childs']) && count($customOptions['childs'])>0)
                                                                @php $is_cadd_txt=''; @endphp
                                                                @foreach($customOptions['childs'] as $child)

                                                                    @php
                                                                        if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==1){
                                                                        $is_cadd="+";
                                                                        $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                        }else if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==2){
                                                                        $is_cadd="-";
                                                                        $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                        }else if(!empty($child->retail_price) && empty($child->is_price_add)){
                                                                        $is_cadd="";
                                                                        $is_cadd_txt=$child->retail_price.' '.\App\Currency::default();
                                                                        }else{
                                                                        $is_cadd="";
                                                                        $is_cadd_txt="";
                                                                        }
                                                                        $option_value_name = $strLang=="en"?$child->option_value_name_en:$child->option_value_name_ar;
                                                                    @endphp
                                                                    <li>
                                                                        <label for="checkbox-{{$productDetails->id}}-{{$productoption->custom_option_id}}-{{$child->id}}"><input
                                                                                    class="checkOptionPricechk"
                                                                                    type="checkbox"
                                                                                    name="checkbox-{{$productDetails->id}}-{{$productoption->custom_option_id}}[]"
                                                                                    id="checkbox-{{$productDetails->id}}-{{$productoption->custom_option_id}}-{{$child->id}}"
                                                                                    value="{{$child->id}}">&nbsp;{{$option_value_name}}
                                                                            ({{$is_cadd_txt}})</label></li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @endif
                                                <!--end check box -->

                                                <!--select box -->
                                                @if(!empty($customOptions['CustomOptionName']) && ($customOptions['CustomOptionType']=="select" or $customOptions['CustomOptionType']=="select for each order"))
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}}@if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <div class="form-group">
                                                            <select class="form-control choose_select_options"
                                                                    name="select-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                    id="select-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                    @if(!empty($productoption->is_required)) required @endif>
                                                                @if(empty($productoption->is_required))
                                                                    <option value="0">---</option>
                                                                @endif
                                                                @if(!empty($customOptions['childs']) && count($customOptions['childs'])>0)
                                                                    @php $is_cadd_txt=''; @endphp
                                                                    @foreach($customOptions['childs'] as $child)

                                                                        @php
                                                                            if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==1){
                                                                            $is_cadd="+";
                                                                            $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                            }else if(!empty($child->retail_price) && !empty($child->is_price_add) && $child->is_price_add==2){
                                                                            $is_cadd="-";
                                                                            $is_cadd_txt=$is_cadd.' '.$child->retail_price.' '.\App\Currency::default();
                                                                            }else if(!empty($child->retail_price) && empty($child->is_price_add)){
                                                                            $is_cadd="";
                                                                            $is_cadd_txt=$child->retail_price.' '.\App\Currency::default();
                                                                            }else{
                                                                            $is_cadd="";
                                                                            $is_cadd_txt="";
                                                                            }
                                                                            $option_value_name = $strLang=="en"?$child->option_value_name_en:$child->option_value_name_ar;
                                                                        @endphp
                                                                        <option value="{{ $customOptions['CustomOptionType'] }}-{{$productDetails->id}}-{{$productoption->custom_option_id}}-{{$child->id}}">{{$option_value_name}}
                                                                            ({{$is_cadd_txt}})
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!--end select box -->
                                                <!--datetime -->
                                                @if(!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType']=="datetime")
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}}@if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control datetimepicker"
                                                                   name="datetime-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                   id="datetime-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                   @if(!empty($productoption->is_required)) required @endif>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!--datetime-->
                                                <!--file -->
                                                @if(!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType']=="file")
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}}@if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="file" accept="image/*" class="form-control"
                                                                   name="file-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                   id="file-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                   @if(!empty($productoption->is_required)) required @endif>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!--file-->
                                                <!--file -->
                                                @if(!empty($customOptions['CustomOptionName']) && $customOptions['CustomOptionType']=="textarea")
                                                    <div class="tt-wrapper">
                                                        <div class="tt-title-options">{{$customOptions['CustomOptionName']}}@if(!empty($productoption->is_required))
                                                                *@endif:
                                                        </div>
                                                        <div class="form-group">
                                                                    <textarea class="form-control"
                                                                              name="textarea-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                              id="textarea-{{$productDetails->id}}-{{$productoption->custom_option_id}}"
                                                                              @if(!empty($productoption->is_required)) required @endif></textarea>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!--file-->
                                                <!--optiona details end -->
                                            @endif
                                        @endforeach
                                    @endif
                                    <!--end options -->

                            @endif
                        <!--end attribute -->
                                <div class="pr_desc1">
                                    <div class="sizeDetails" id="sizeDetails"></div>
                                    <div class="colorDetails" id="colorDetails"></div>
                                </div>
{{--                                                    <div class="product_sort_info">--}}
{{--                                                        <ul>--}}
{{--                                                            <li><i class="linearicons-shield-check"></i> 1 Year AL Jazeera Brand Warranty</li>--}}
{{--                                                            <li><i class="linearicons-sync"></i> 30 Day Return Policy</li>--}}
{{--                                                            <li><i class="linearicons-bag-dollar"></i> Cash on Delivery available</li>--}}
{{--                                                        </ul>--}}
{{--                                                    </div>--}}

                        </div>
                        <div class="clearfix"></div>
                        <div class="product_price">
                            @if(!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime)>strtotime(date('Y-m-d')))
                                <span class="price">{{\App\Currency::default()}} <span id="display_price">{{number_format($productDetails->countdown_price,3)}}</span></span></span>
                                <del>{{\App\Currency::default()}} {{number_format($productDetails->old_price,3)}}</del>
                                <div class="countdown_time countdown_style4 mb-4" data-time="{{ $productDetails->countdown_datetime }}"></div>
                                <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                                    <link itemprop="url"
                                          href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}"/>
                                    <meta itemprop="availability" content="https://schema.org/InStock"/>
                                    <meta itemprop="priceCurrency" content="{{\App\Currency::default(false)->code}}"/>
                                    <meta itemprop="itemCondition" content="https://schema.org/NewCondition"/>
                                    <meta itemprop="price" content="{{round($productDetails->countdown_price,3)}}"/>
                                    <meta itemprop="priceValidUntil"
                                          content="{{date('Y-m-d',strtotime(date('Y-m-d').'+10 days'))}}"/>
                                </div>
                                <meta itemprop="sell_on_google_price"
                                      content="{{round($productDetails->retail_price,3)}} {{\App\Currency::default()}}"/>
                                <meta itemprop="sell_on_google_sale_price"
                                      content="{{round($productDetails->countdown_price,3)}} {{\App\Currency::default()}}"/>
                            @elseif ($checkBrandDiscount)
                                <span class="price">{{\App\Currency::default()}} <span id="display_price">{{number_format($brandDiscountedPrice->price , 3)}}</span></span>
                                <del>{{\App\Currency::default()}} {{number_format($brandDiscountedPrice->oldPrice , 3 )}}</del>
                                <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                                    <link itemprop="url"
                                          href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}"/>
                                    <meta itemprop="availability" content="https://schema.org/InStock"/>
                                    <meta itemprop="priceCurrency" content="{{\App\Currency::default(false)->code}}"/>
                                    <meta itemprop="itemCondition" content="https://schema.org/NewCondition"/>
                                    <meta itemprop="price" content="{{round($productDetails->countdown_price,3)}}"/>
                                    <meta itemprop="priceValidUntil"
                                          content="{{date('Y-m-d',strtotime(date('Y-m-d').'+10 days'))}}"/>
                                </div>
                                <meta itemprop="sell_on_google_price"
                                      content="{{round($productDetails->retail_price,3)}} {{\App\Currency::default()}}"/>
                                <meta itemprop="sell_on_google_sale_price"
                                      content="{{round($productDetails->countdown_price,3)}} {{\App\Currency::default()}}"/>
                            @else
                                <span class="price">{{\App\Currency::default()}} <span id="display_price">{{number_format($productDetails->retail_price,3)}}</span></span>
                                @if($productDetails->old_price)
                                    <del>{{\App\Currency::default()}} {{number_format($productDetails->old_price,3)}}</del>
                                    <meta itemprop="sell_on_google_price"
                                          content="{{round($productDetails->old_price,3)}} {{\App\Currency::default()}}"/>
                                    <meta itemprop="sell_on_google_sale_price"
                                          content="{{round($productDetails->retail_price,3)}} {{\App\Currency::default()}}"/>
                                @else
                                    <meta itemprop="sell_on_google_price"
                                          content="{{number_format($productDetails->retail_price,3)}} {{\App\Currency::default()}}"/>
                                @endif
                                <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                                    <link itemprop="url"
                                          href="{{url(app()->getLocale().'/directdetails/'.$productDetails->id.'/'.$productDetails->slug)}}"/>
                                    <meta itemprop="availability" content="https://schema.org/InStock"/>
                                    <meta itemprop="priceCurrency" content="{{\App\Currency::default(false)->code}}"/>
                                    <meta itemprop="itemCondition" content="https://schema.org/NewCondition"/>
                                    <meta itemprop="price" content="{{round($productDetails->retail_price,3)}}"/>
                                    <meta itemprop="priceValidUntil"
                                          content="{{date('Y-m-d',strtotime(date('Y-m-d').'+10 days'))}}"/>
                                </div>
                            @endif
                            @if ( $settingInfo->show_all_currencies and  ! request()->query('quick_view'))
                                <small>
                                    <a class="font-13 text-primary" onclick="$('#otherCurrency').modal('show');" data-toggle="modal" data-target="#otherCurrency"
                                       href="#">{{__('webMessage.otherCurrency')}}</a>
                                </small>
                                <!-- modal (size guid) -->
                                <div class="modal" id="otherCurrency" tabindex="-1" role="dialog"
                                     aria-label="otherCurrency" aria-hidden="true">
                                    <div class="modal-dialog modal-small">
                                        <div class="modal-content ">
                                            <div class="modal-body">
                                                <div class="tt-layout-product-info">
                                                    <h6 class="tt-title">{{__('webMessage.otherCurrency')}}</h6>
                                                    <div class="tt-table-responsive-md">
                                                        <table class="table" style="max-width: 100%;">
                                                            <tbody id="other_currency_display_price">
                                                            {!! \App\Currency::table( $checkBrandDiscount ? $brandDiscountedPrice->price : $productDetails->retail_price)  !!}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- modal (size guid) -->
                            @endif
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
                        
                        <div class="clearfix"></div>
                        <hr />
                        <div class="cart_extra">
                            @if($availableQty>0)
                                <div class="cart-product-quantity">
                                    <div class="quantity">
                                        <input type="button" value="-" class="minus minus-btn" id="{{$productDetails->id}}">
                                        <input type="text" name="quantity_attr" id="quantity_attr" value="1" title="{{ __('webMessage.quantity') }}" class="qty" size="{{$availableQty}}">
                                        <input type="button" value="+" class="plus plus-btn" id="{{$productDetails->id}}">
                                    </div>
                                </div>
                                <div class="cart_btn">
                                    <button class="btn btn-fill-out btn-addtocart" id="details_cartbtn" @if(request()->query('quick_view')) type="button" onclick="addToCartDetails({{$productDetails->id}})" @else  type="submit" @endif><i class="icon-basket-loaded"></i> {{__($productDetails->is_active==2 ? 'webMessage.preorder' : 'webMessage.addtocart_btn')}}</button>
                                    <a class="add_wishlist addtowishlist" href="javascript:;" id="{{$productDetails->id}}"><i class="icon-heart"></i></a>
                                </div>
                                <div id="quickresponse"></div>
                            @else
                                <div class="tt-wrapper">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inquiry_name"
                                                       class="control-label">{{__('webMessage.name')}} </label>
                                                <input type="text" class="form-control" id="inquiry_name"
                                                       name="inquiry_name"
                                                       placeholder="{{__('webMessage.enter_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inquiry_email"
                                                       class="control-label">{{__('webMessage.email')}}</label>
                                                <input type="email" class="form-control" id="inquiry_email"
                                                       name="inquiry_email" required
                                                       placeholder="{{__('webMessage.enter_email')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inquiry_mobile"
                                                       class="control-label">{{__('webMessage.mobile')}}</label>
                                                <input type="email" class="form-control" id="inquiry_mobile"
                                                       name="inquiry_mobile"
                                                       placeholder="{{__('webMessage.enter_mobile')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="inquiry_message"
                                                       class="control-label">{{__('webMessage.message')}} </label>
                                                <textarea class="form-control" id="inquiry_message"
                                                          name="inquiry_message"
                                                          placeholder="{{__('webMessage.write_some_text')}}"
                                                          rows="4"></textarea></div>
                                            <div class="form-group py-3">
                                                <button type="button" class="btn btn-fill-out btncartInquiry"
                                                        id="{{$productDetails->id}}"><i
                                                            class="icon-f-39"></i>{{  __($productDetails->is_active==2 ? 'webMessage.preorder' : 'webMessage.send')  }}</button>
                                                            &ensp;
                                                <img width="40" src="{{url('assets/images/ajax-loader.gif')}}"
                                                     id="loading-gif" style="display:none;">
                                                <a class="add_wishlist addtowishlist" href="javascript:;" id="{{$productDetails->id}}"><i class="icon-heart"></i></a>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>
                        
                        {{--@if($availableQty>0)
                            <div>{{ __('webMessage.quantity') }}: <strong>{{ $availableQty }}</strong></div>
                        @endif--}}
                        
                    </form>
                    <hr />
                    <ul class="product-meta">
                        @if(!empty($productDetails->item_code))
                            <li><span>{{__('webMessage.item_code')}}:</span> {{$productDetails->item_code}}</li>
                        @endif
                        @if(!empty($productDetails->sku_no))
                            <li><span>{{__('webMessage.sku_no')}}:</span> {{$productDetails->sku_no}}</li>
                        @endif
                        @if(!empty($brandDetails->image))
                            <li>
                                <a href="{{url(app()->getLocale().'/brands/'.$brandDetails->slug)}}">
                                    <img style="max-height:100px;max-width:100px;"
                                         src="{{url('uploads/brand/thumb/'.$brandDetails->image)}}">
                                </a>
                            </li>
                        @elseif($strLang=="en" && !empty($brandDetails->title_en))
                            <li>
                                <a href="{{url(app()->getLocale().'/brands/'.$brandDetails->slug)}}">
                                    {{$brandDetails->title_en}}
                                </a>
                            </li>
                        @elseif($strLang=="ar" && !empty($brandDetails->title_ar))
                            <li>
                                <a href="{{url('brands/'.$brandDetails->slug)}}">
                                    {{$brandDetails->title_ar}}
                                </a>
                            </li>
                        @endif
                        @if($availableQty && ($availableQty>0))
                            @if($availableQty && $availableQty>0)
                                <li><span>{{__('webMessage.availability')}}:</span> <span
                                            id="display_qty">{{$availableQty}}</span> <font
                                            color="#009900">{{__('webMessage.instock')}}</font></li>
                            @else
                                <li><span>{{__('webMessage.availability')}}:</span> <span
                                            id="display_qty">0</span> <font
                                            color="#ff0000">{{__('webMessage.outofstock')}}</font></li>
                            @endif
                        @endif
                        @if(!empty($tagsDetails))
                            <li>{{__('webMessage.tags')}}: {!!$tagsDetails!!} </li>
                        @endif
                        @if(!empty($productDetails['warranty']))
                            @php
                                $warrantyDetails = App\Http\Controllers\webController::getWarrantyDetails($productDetails['warranty']);
                            @endphp
                            <li>
                                {{strtoupper(__('webMessage.warranty'))}}: @if(app()->getLocale()=="en" && !empty($warrantyDetails->title_en)) {!!$warrantyDetails->title_en!!} @elseif(app()->getLocale()=="ar" && $warrantyDetails->title_ar) {!!$warrantyDetails->title_ar!!} @endif
                                <small>
                                    @if(app()->getLocale()=="en" && !empty($warrantyDetails->details_en)) {!!$warrantyDetails->details_en!!} @elseif(app()->getLocale()=="ar" && !empty($warrantyDetails->details_ar)) {!!$warrantyDetails->details_ar!!} @endif
                                </small>
                            </li>
                        @endif
                    </ul>

                    <div class="product_share">
                    <span>{{ __('webMessage.share') }}:</span>
                    <ul class="social_icons">
                        @php
                            if(app()->getLocale()=="en"){
                            $text = $productDetails->title_en;
                            }else{
                            $text = $productDetails->title_ar;
                            }
                            $url   = url(app()->getLocale().'/details/'.$productDetails->id.'/'.$productDetails->slug);
                            $image = url('/uploads/product/'.$productDetails->image);
                            $facebook_Share  = App\Http\Controllers\webController::createSocialLinks("facebook",$url,$text);
                            $twitter_Share   = App\Http\Controllers\webController::createSocialLinks("twitter",$url,$text);
                            $google_Share    = App\Http\Controllers\webController::createSocialLinks("googleplus",$url,$text);
                            $pinterest_Share = App\Http\Controllers\webController::createSocialLinks("pinterest",$url,$text,$image);
                            $whatsapp_Share = App\Http\Controllers\webController::createSocialLinks("whatsapp",$url,$text,$image);
                        @endphp
                        <li><a href="{{ $facebook_Share }}"><i class="ion-social-facebook"></i></a></li>
                        <li><a href="{{ $twitter_Share }}"><i class="ion-social-twitter"></i></a></li>
                        {{--<li><a href="{{$google_Share}}"><i class="ion-social-googleplus"></i></a></li>--}}
                        <li><a href="{{$whatsapp_Share}}"><i class="ion-social-whatsapp"></i></a></li>
                        {{--<li><a href="{{$pinterest_Share}}"><i class="ion-social-pinterest"></i></a></li>--}}
                    </ul>
                </div>
                </div>
            </div>
        </div>
