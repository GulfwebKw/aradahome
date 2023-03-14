@if ($settingInfo->social_whatsapp)
    @if (!empty($settingInfo->is_float_whatsapp))
        <a href="https://api.whatsapp.com/send?phone={{ $settingInfo->social_whatsapp }}&text={{ __('webMessage.whatsappsharetext') }}" target="_blank" class="float">
            <img src="{{asset('assets/images/whatsapp.png')}}" alt=""></a>
    @endif
@endif

<!-- START FOOTER -->
<footer class="footer_Nodark bg_gray">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="widget">
                        <div class="footer_logo">
                            @if ($settingInfo->logo)
                                <a href="{{ url(app()->getLocale() . '/') }}">
                                    <img src="{{ url('uploads/logo/' . $settingInfo->logo) }}" width="120px" alt="{{ $settingInfo['name_'.app()->getLocale()] }}" />
                                </a>
                            @endif
                        </div>
                        <p>{{$seo_description}}</p>
                    </div>
                    <div class="widget">
                        <ul class="social_icons social_white1">
                            @if ($settingInfo->social_facebook)
                                <li><a title="{{ __('webMessage.facebook') }}" target="_blank"
                                       href="{{ $settingInfo->social_facebook }}"><i class="ion-social-facebook"></i></a></li>
                            @endif
                            @if ($settingInfo->social_twitter)
                                <li><a title="{{ __('webMessage.twitter') }}" target="_blank"
                                       href="{{ $settingInfo->social_twitter }}"><i class="ion-social-twitter"></i></a></li>
                            @endif
                            @if ($settingInfo->social_instagram)
                                <li><a title="{{ __('webMessage.instagram') }}" target="_blank"
                                       href="{{ $settingInfo->social_instagram }}"><i class="ion-social-instagram-outline"></i></a></li>
                            @endif
                            @if ($settingInfo->social_linkedin)
                                <li><a title="{{ __('webMessage.linkedin') }}" target="_blank"
                                       href="{{ $settingInfo->social_linkedin }}"><i class="ion-social-linkedin"></i></a></li>
                            @endif
                            @if ($settingInfo->social_youtube)
                                <li><a title="{{ __('webMessage.youtube') }}" target="_blank"
                                       href="{{ $settingInfo->social_youtube }}"><i class="ion-social-youtube-outline"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                @php
                    $footerMenusTrees = App\Categories::CategoriesTree();
                    $singlePageLinks = App\Http\Controllers\webController::allSinglePagesLinks();
                @endphp
                @if (!empty($footerMenusTrees) && count($footerMenusTrees) > 0)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">{{ __('webMessage.categories') }}</h6>
                        <ul class="widget_links">
                            @foreach ($footerMenusTrees as $footerMenusTree)
                            <li><a href="{{ url(app()->getLocale() . '/products/' . $footerMenusTree->id . '/' . $footerMenusTree->friendly_url) }}">{{ $footerMenusTree['name_'.app()->getLocale()] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">
                            {{ strtoupper(__('webMessage.importantlinks')) }}</h6>
                        <ul class="widget_links">
                            <!--<li>-->
                            <!--    <a href="{{ url(app()->getLocale() . '/faq') }}">{{ __('webMessage.faq') }}</a>-->
                            <!--</li>-->
                            @if(($settingInfo->ios_url != null or $settingInfo->android_url != null or $settingInfo->huawei_url != null  ) and ! empty($settingInfo->invoice_qrcode) )
                                <li>
                                    <a href="{{route('downloadApp')}}">{{ __('webMessage.DownloadApp') }}</a>
                                </li>
                            @endif
                            @if (!empty(Auth::guard('webs')->user()->id))
                                <li>
                                    <a href="{{ url(app()->getLocale() . '/dashboard') }}">{{ __('webMessage.myaccount') }}</a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ url(app()->getLocale() . '/register') }}">{{ __('webMessage.signup') }}</a>
                                </li>
                                <li>
                                    <a href="{{ url(app()->getLocale() . '/login') }}">{{ __('webMessage.signin') }}</a>
                                </li>
                            @endif
                            @if ($settingInfo->supplier_registration == 1)
                                <li>
                                    <a href="{{ url(app()->getLocale() . '/supplier-registration') }}">{{ __('webMessage.supplier_registration') }}</a>
                                </li>
                            @endif
                            @foreach ($singlePageLinks as $links)
                                <li>
                                    <a target="__blank" href="{{ url(app()->getLocale().'/page/' .$links->slug ) }}">{{app()->getLocale()=='en'?$links->title_en:$links->title_ar}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">{{ strtoupper(__('webMessage.contactus')) }}</h6>
                        <ul class="contact_info contact_info_light1">
                            @if ( (app()->getLocale() == 'ar' && $settingInfo->address_ar) OR (app()->getLocale() == 'en' && $settingInfo->address_en) )
                            <li>
                                <i class="ti-location-pin"></i>
                                <p>{{$settingInfo['address_'.app()->getLocale()]}}</p>
                            </li>
                            @endif
                            @if( $settingInfo->email)
                            <li>
                                <i class="ti-email"></i>
                                <a href="mailto:{{ $settingInfo->email }}">{{ $settingInfo->email }}</a>
                            </li>
                            @endif
                            @if( $settingInfo->phone)
                            <li>
                                <i class="ti-mobile"></i>
                                <a href="tel:{{ $settingInfo->phone }}" dir="ltr">{{ $settingInfo->phone }}</a>
                            </li>
                            <li>
                                @if (app()->getLocale() == 'ar' && $settingInfo->office_hours_ar)
                                    <span style="max-width: 100%;color: #687188;"> {{ $settingInfo->office_hours_ar }}</span>
                                @elseif(app()->getLocale() == 'en' && $settingInfo->office_hours_en)
                                    <span style="max-width: 100%;color: #687188;"> {{ $settingInfo->office_hours_en }}</span>
                                @endif
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom_footer border-top-tran">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-lg-0">
                        @if ($settingInfo->copyrights_en && app()->getLocale() == 'en'){!! $settingInfo->copyrights_en !!}@endif
                        @if ($settingInfo->copyrights_ar && app()->getLocale() == 'ar'){!! $settingInfo->copyrights_ar !!}@endif
                    </p>
                </div>
                <div class="col-md-6">
                    @if (!empty($settingInfo->payments))
                        @php
                            $payments = explode(',', $settingInfo->payments);
                        @endphp
                        <ul class="footer_payment text-center text-lg-end">
                            @foreach ($payments as $payment)
                                <li><a href="#"><img src="{{ url('uploads/paymenticons/' . strtolower($payment) . '.png') }}"
                                                height="30" alt=""></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->


<!--order tracking -->
<div class="modal  fade" id="modalPrderTrackBox" tabindex="-1" role="dialog" aria-label="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                
                        <div type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="ion-ios-close-empty"></i></span>
                        </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input type="text" name="trackorderid" class="form-control" id="trackorderid"
                                placeholder="{{ __('webMessage.enter_order_id') }}" autcomplete="off">

                        </div>
                    </div>
                    <div class="col-lg-2">
                        <input type="button" value="{{ __('webMessage.checknow') }}"
                            class="btn btn-border TrackMyOrders">
                    </div>
                </div>
                <span id="responseTrackOrder"></span>
            </div>
        </div>
    </div>
</div>