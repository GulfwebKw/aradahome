@extends('website.include.master')
@section('title' , __('webMessage.wishlist') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account')}}">{{__('webMessage.myaccount')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.wishlist')   }}</li>
@endsection

@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive wishlist_table">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="product-thumbnail">&nbsp;</th>
                                <th class="product-name">{{ __('webMessage.productname') }}</th>
                                <th class="product-price">{{ __('webMessage.unit_price') }}</th>
                                <th class="product-price"></th>
                                <th class="product-remove">{{ __('webMessage.remove') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($wishLists as $wishList)
                                @php
                                    $prodDetails = App\Http\Controllers\webCartController::getProductDetails($wishList->product_id);
                                    $isStock = App\Http\Controllers\webCartController::IsAvailableQuantity($prodDetails->id);
                                @endphp
                                @if(!empty($prodDetails->id))
                                    <tr>
                                        <td class="product-thumbnail">
                                            <a href="{{url(app()->getLocale().'/directdetails/'.$prodDetails->id.'/'.$prodDetails->slug)}}">
                                                @if(!empty($prodDetails->image))
                                                    <img src="{{url('uploads/product/thumb/'.$prodDetails->image)}}" alt="">
                                                @else
                                                    <img src="{{url('uploads/no-image.png')}}" alt="">
                                                @endif
                                            </a>
                                        </td>
                                        <td class="product-name" data-title="{{ __('webMessage.productname') }}">
                                            <a href="{{url(app()->getLocale().'/directdetails/'.$prodDetails->id.'/'.$prodDetails->slug)}}">
                                                @if(app()->getLocale()=="en") {{$prodDetails->title_en}} @else {{$prodDetails->title_ar}} @endif
                                            </a>
                                        </td>
                                        <td class="product-price" data-title="{{ __('webMessage.price') }}">{{\App\Currency::default()}} {{$prodDetails->retail_price}} @if($prodDetails->old_price) <del>{{\App\Currency::default()}} {{$prodDetails->old_price}}</del> @endif</td>
                                        <td class="product-price">
                                            @if (empty($isStock))
                                                <span class="bg-danger">{{ __('webMessage.outofstock') }}</span>
                                            @else
                                                @if ($prodDetails->is_active == '2')
                                                    <a href="javascript:;"
                                                       class="addtocartsingle addToCartPixelButton"
                                                       id="{{ $prodDetails->id }}"><i class="icon-basket-loaded"></i> {{ __('webMessage.preorder') }}</a>
                                                @else
                                                    <a href="javascript:;"
                                                       class="addtocartsingle addToCartPixelButton"
                                                       id="{{ $prodDetails->id }}"> <i class="icon-basket-loaded"></i> {{ __('webMessage.addtocart_btn') }}</a>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="product-remove" data-title="{{ __('webMessage.remove') }}"><a id="{{$wishList->id}}" href="javascript:;" class="removeitem"><i class="ti-close"></i></a></td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="4" class="text-center">{{__('webMessage.noiteminwishlist')}}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2 mt-md-4">
                            <div class="pagination pagination_style1 justify-content-center">
                                {!! $wishLists->appends($_GET)->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->

@endsection
