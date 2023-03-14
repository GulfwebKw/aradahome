@php
    $tempOrdersCount = App\Http\Controllers\webCartController::countTempOrders();
    $tempOrders = App\Http\Controllers\webCartController::loadTempOrders();
@endphp
<li class="dropdown cart_dropdown"><a class="nav-link cart_trigger" href="#" data-bs-toggle="dropdown"><i class="linearicons-bag2"></i><span class="cart_count" id="tt-badge-cart">{{ $tempOrdersCount }}</span></a>
    <div class="cart_box cart_right dropdown-menu dropdown-menu-right" id="TempOrderBoxDiv">
        @if (empty($tempOrders) || count($tempOrders) == 0)
            <!-- layout emty cart -->
            <a href="javascript:;" class="tt-cart-empty">
                <i class="icon-f-39"></i>
                <p>{{ __('webMessage.yourcartisempty') }}</p>
            </a>
        @else
            <div class="tt-cart-content">

                <div class="tt-cart-list">
                    @php
                        $subTotalAmount = 0;
                        $attrtxt = '';
                        $t = 1;
                    @endphp
                    @foreach ($tempOrders as $tempOrder)
                        @php
                            $prodDetails = App\Http\Controllers\webCartController::getProductDetails($tempOrder->product_id);
                            if ($prodDetails->image) {
                                $prodImage = url('uploads/product/thumb/' . $prodDetails->image);
                            } else {
                                $prodImage = url('uploads/no-image.png');
                            }

                            $subTotalAmount += $tempOrder->quantity * $tempOrder->unit_price;
                            if (!empty($tempOrder->size_id)) {
                                $sizeName = App\Http\Controllers\webCartController::sizeNameStatic($tempOrder->size_id, app()->getLocale());
                                $attrtxt .= '<li>' . __('webMessage.size') . ': ' . $sizeName . '</li>';
                            }
                            if (!empty($tempOrder->color_id)) {
                                $colorName = App\Http\Controllers\webCartController::colorNameStatic($tempOrder->color_id, app()->getLocale());
                                $attrtxt .= '<li>' . __('webMessage.color') . ': ' . $colorName . '</li>';
                                $colorImageDetails = App\Http\Controllers\webCartController::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                                if (!empty($colorImageDetails->color_image)) {
                                    $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                                }
                            }
                            $optionsDetailstxt = App\Http\Controllers\webCartController::getOptionsDtails($tempOrder->id);

                        @endphp
                        <div class="tt-item"
                             style="@if ($t > 3) display:none; @endif">
                            <a
                                    href="{{ url(app()->getLocale() . '/directdetails/' . $prodDetails->id . '/' . $prodDetails->slug) }}">
                                <div class="tt-item-img">
                                    <img src="{{ $prodImage }}"
                                         alt="@if (app()->getLocale() == 'en') {{ $prodDetails->title_en }} @else {{ $prodDetails->title_ar }} @endif">
                                </div>
                                <div class="tt-item-descriptions">
                                    <h2 class="tt-title">
                                        @if (app()->getLocale() == 'en')
                                            {{ $prodDetails->title_en }}
                                        @else
                                            {{ $prodDetails->title_ar }}
                                        @endif
                                    </h2>
                                    <ul class="tt-add-info">
                                        {!! $attrtxt !!}
                                        {!! $optionsDetailstxt !!}
                                    </ul>
                                    <div class="tt-quantity">
                                        {{ $tempOrder->quantity }} X</div>
                                    <div class="tt-price">
                                        {{ $tempOrder->unit_price }}
                                        {{ \App\Currency::default() }} </div>
                                </div>
                            </a>
                            <div class="tt-item-close">
                                <a href="javascript:;" id="{{ $tempOrder->id }}"
                                   class="tt-btn-close deleteFromTemp"></a>
                            </div>
                        </div>
                        @php
                            $attrtxt = '';
                            $t++;
                        @endphp
                    @endforeach

                    @if ($t > 3)
                        <div class="tt-item" align="center"><a
                                    href="{{ url(app()->getLocale() . '/cart') }}">{{ trans('webMessage.viewall') }}(+{{ $t - 4 }})</a>
                        </div>
                    @endif
                </div>

                <div class="tt-cart-total-row">
                    @php
                        $bundleDiscount = App\Http\Controllers\webCartController::loadTempOrdersBundleDiscount($tempOrders);
                        $subTotalAmount = $subTotalAmount - $bundleDiscount;
                    @endphp
                    @if ($bundleDiscount > 0)
                        <div class="tt-cart-total-title">
                            {{ __('webMessage.bundles.BundleDiscount') }}:
                        </div>
                        <div class="tt-cart-total-price" style="color: #FF0000;">
                            {{ round($bundleDiscount, 3) }}
                            {{ \App\Currency::default() }}</div>
                </div>
                <div class="tt-cart-total-row"
                     style="margin-top: 0px;padding-top: 10px;border-top: 0px;">
                    @endif
                    <div class="tt-cart-total-title">{{ __('webMessage.subtotal') }}:
                    </div>
                    <div class="tt-cart-total-price"> {{ round($subTotalAmount, 3) }}
                        {{ \App\Currency::default() }}</div>
                </div>
                <div class="tt-cart-btn">
                    <div class="tt-item">
                        <a href="{{ url(app()->getLocale() . '/checkout') }}"
                           class="btn">{{ __('webMessage.checkout') }}</a>
                    </div>
                    <div class="tt-item">
                        <a href="{{ url(app()->getLocale() . '/cart') }}"
                           class="btn-link-02 tt-hidden-mobile">{{ __('webMessage.viewcart') }}</a>
                        <a href="{{ url(app()->getLocale() . '/cart') }}"
                           class="btn btn-border tt-hidden-desctope">{{ __('webMessage.viewcart') }}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</li>