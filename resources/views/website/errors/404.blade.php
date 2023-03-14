@extends('website.include.master')
@section('title' , __('webMessage.404')  )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.404')  }}</li>
@endsection

@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="text-center order_complete">
                        <i class="fas fa-times-circle"></i>
                        <div class="heading_s1">
                            <h3>{{__('webMessage.404')}}</h3>
                        </div>
                        <p>{{__('webMessage.itslookeslikepageisremoved')}}</p>
                        <a href="javascript:history.go(-1);" class="btn btn-fill-out">{{__('webMessage.goback')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection
