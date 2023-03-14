@extends('website.include.master')
@section('title' , __('webMessage.contactus') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.contactus')}}</li>
@endsection
@section('header')
    @php
        $slideshows = App\Http\Controllers\webController::getSlideshow();
        $slidetxt='';
        if(!empty($slideshows) && count($slideshows)>0){
            foreach($slideshows as $slideshow){
                $slidetxt.='"'.url('/uploads/slideshow/'.$slideshow->image).'",';
            }
        }
    @endphp
    <style>.g-recaptcha {transform:scale(0.90);transform-origin:0 0;}</style>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Restaurant",
      "image": [
        {!!trim($slidetxt,',')!!}
        ],
       "@id": "{{url(app()->getLocale().'/contactus')}}",
      "name": "Kash5aStore",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{!!!empty($settingInfo->address_en)?$settingInfo->name_en:''!!}",
        "addressLocality": "Kuwait City",
        "addressRegion": "Kuwait",
        "postalCode": "00000",
        "addressCountry": "KW"
      },
      "review": {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "{{rand(1,5)}}",
          "bestRating": "{{rand(1,5)}}"
        },
        "author": {
          "@type": "Person",
          "name": "Gulfweb"
        }
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 48.0037848,
        "longitude": 29.3391489
      },
      "url": "{{url(app()->getLocale().'/')}}",
      "telephone": "@if($settingInfo->mobile){{$settingInfo->mobile}}@endif",
      "servesCuisine": "American",
      "priceRange": "1.5-50",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": [
            "Monday",
            "Thursday"
          ],
          "opens": "9:30",
          "closes": "20:00"
        }
      ],
      "menu": "{{url(app()->getLocale().'/')}}",
      "acceptsReservations": "True"
    }
    </script>
@endsection
@section('content')
    <!-- START SECTION CONTACT -->
    <div class="section pb_70">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="contact_wrap contact_style3" style="min-height: 425px;">
                        <div class="contact_icon">
                            <i class="linearicons-map2"></i>
                        </div>
                        <div class="contact_text">
                            <span>{{__('webMessage.address')}}</span>
                            <span>{{__('webMessage.branch1')}}</span>
                            <p>
                                @if(app()->getLocale()=="en" && !empty($settingInfo->address_en)) {!!$settingInfo->address_en!!} @endif
                                @if(app()->getLocale()=="ar" && !empty($settingInfo->address_ar)) {!!$settingInfo->address_ar!!} @endif
                            </p>
                            <span>{{__('webMessage.branch2')}}</span>
                            <p >
                                @if(app()->getLocale()=="en")
                                    Al - Mubarakiya Street Al - Gharabali Street
                                @else
                                    معرض العراده المباركية شارع الغربللي
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @if($settingInfo->email)
                <div class="col-xl-4 col-md-6">
                    <div class="contact_wrap contact_style3" style="min-height: 425px;">
                        <div class="contact_icon">
                            <i class="linearicons-envelope-open"></i>
                        </div>
                        <div class="contact_text">
                            <span>{{__('webMessage.email')}}</span>
                            <p><a href="mailto:{{$settingInfo->email}}">{{$settingInfo->email}}</a></p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-xl-4 col-md-6">
                    <div class="contact_wrap contact_style3" style="min-height: 425px;">
                        <div class="contact_icon">
                            <i class="linearicons-tablet2"></i>
                        </div>
                        <div class="contact_text">
                            <span>{{__('webMessage.phone')}}</span>
                            <span>{{__('webMessage.branch1')}}</span>
                            <p dir="ltr" class="mb-0">@if($settingInfo->phone){{$settingInfo->phone}}@endif</p>
                            <p>
                                @if(app()->getLocale()=="en")
                                    From Saturday to Thursday 8am - 5pm Friday is closed
                                @else
                                    من السبت إلى الخميس من 8 صباحاً حتى 5 مساءً يوم الجمعة مغلق
                                @endif
                            </p>
                            <span>{{__('webMessage.branch2')}}</span>
                            <p dir="ltr" class="mb-0">@if($settingInfo->mobile){{$settingInfo->mobile}}@endif</p>
                            <p>
                                @if(app()->getLocale()=="en")
                                    9AM-1PM 4PM-9.30PM
                                @else
                                    من 9 صباحا حتى 1مساءً من 4 مساء حتى 9.30 مساء
                                @endif
                            </p>
{{--                            <p>--}}
{{--                                @if(app()->getLocale()=="en" && !empty($settingInfo->office_hours_en)) {!!$settingInfo->office_hours_en!!} @endif--}}
{{--                                @if(app()->getLocale()=="ar" && !empty($settingInfo->office_hours_ar)) {!!$settingInfo->office_hours_ar!!} @endif--}}
{{--                                    &nbsp;--}}
{{--                            </p>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION CONTACT -->

    <!-- START SECTION CONTACT -->
    <div class="section pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="heading_s1">
                        <h2>Get In touch</h2>
                    </div>
                    <p class="leads">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus blandit massa enim. Nullam id varius nunc id varius nunc.</p>
                    <div class="field_form">
                        <form id="contactformtxt" method="post" novalidate action="{{route('contactform',['locale'=>app()->getLocale()])}}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <input autocomplete="off" type="text" name="name" class="form-control" id="name" placeholder="{{__('webMessage.enter_your_name')}}*" value="{{old('name')}}">
                                    @if($errors->has('name'))
                                        <label id="name-error" class="error" for="name">{{ $errors->first('name') }}</label>
                                    @endif                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input autocomplete="off" type="email" name="email" class="form-control" id="email" placeholder="{{__('webMessage.enter_your_email')}}*" value="{{old('email')}}">
                                    @if($errors->has('email'))
                                        <label id="email-error" class="error" for="email">{{ $errors->first('email') }}</label>
                                    @endif                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input autocomplete="off" type="text" name="mobile" class="form-control" id="mobile" placeholder="{{__('webMessage.enter_your_mobile')}}*" value="{{old('mobile')}}">
                                    @if($errors->has('mobile'))
                                        <label id="mobile-error" class="error" for="mobile">{{ $errors->first('mobile') }}</label>
                                    @endif                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <select name="subject" id="subject" class="form-control">
                                        <option disabled="disabled" selected>{{__('webMessage.choose_your_subject')}}*</option>
                                        @if(count($subjectLists))
                                            @foreach($subjectLists as $subjectList)
                                                <option value="{{$subjectList->id}}" {{old('subject')==$subjectList->id?'selected':''}}>{{$subjectList->title_en}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('subject'))
                                        <label id="subject-error" class="error" for="subject">{{ $errors->first('subject') }}</label>
                                    @endif
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <textarea  class="form-control" rows="7" name="message"  id="message" placeholder="{{__('webMessage.write_some_text')}}*">{{old('message')}}</textarea>
                                    @if($errors->has('message'))
                                        <label id="message-error" class="error" for="message">{{ $errors->first('message') }}</label>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    <div class="g-recaptcha" data-sitekey="6LeMueQUAAAAAJ-ZUe9ZqGK3pma9VwbeoaYDgJte"></div>
                                    @if($errors->has('recaptchaError'))
                                        <label id="message-error" class="error" for="message">{{ $errors->first('recaptchaError') }}</label>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-3">
                                    <button type="submit" class="btn btn-fill-out" name="submit" value="Submit">{{__('webMessage.sendnow')}}</button>
                                </div>

                                @if(session('session_msg'))
                                <div class="col-md-12 mb-3">
                                    <div id="alert-msg" class="alert-msg text-center">{{session('session_msg')}}</div>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 pt-2 pt-lg-0 mt-4 mt-lg-0">
                    <iframe src="{!!$settingInfo->map_embed_url!!}" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION CONTACT -->
@endsection