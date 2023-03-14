@extends('website.include.master')
@section('title' , __('webMessage.faq')  )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.faq')  }}</li>
@endsection

@section('content')
    <!-- STAT SECTION FAQ -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div id="accordion" class="accordion accordion_style1">
                        @if(!empty($faqs) && count($faqs)>0)
                            @foreach($faqs as $faq)
                                <div class="card">
                                    <div class="card-header" id="heading{{ $loop->index }}">
                                        <h6 class="mb-0"> <a class="collapsed" data-bs-toggle="collapse" href="#collapse{{ $loop->index }}" aria-expanded="{{$loop->first ? 'true' : 'false'  }}" aria-controls="collapse{{ $loop->index }}">@if(app()->getLocale()=="en") {{$faq->title_en}} @else {{$faq->title_ar}} @endif</a> </h6>
                                    </div>
                                    <div id="collapse{{ $loop->index }}" class="collapse @if( $loop->first ) show @endif" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#accordion">
                                        <div class="card-body">
                                            <p>@if(app()->getLocale()=="en") {!!$faq->details_en!!} @else {!!$faq->details_ar!!} @endif</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION FAQ -->

@endsection