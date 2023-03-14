@extends('website.include.master')
@section('title' , __('webMessage.signup') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.signup')}}</li>
@endsection
@section('header')


@endsection
@section('content')
    <!-- START LOGIN SECTION -->
    <div class="login_register_wrap section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-md-10">
                    <div class="login_wrap">
                        <div class="padding_eight_all bg-white">
                            <div class="heading_s1">
                                <h3>{{__('webMessage.signup')}}</h3>
                            </div>
                            <form id="customer_reg_form" method="post" action="{{route('registerform',['locale'=>app()->getLocale()])}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group mb-3">
                                    <input type="text" name="name" class="form-control @if($errors->has('name')) error @endif" id="name" placeholder="{{__('webMessage.name')}}" value="{{old('name')}}">
                                    @if($errors->has('name'))
                                        <label class="error" for="name">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" name="email" class="form-control @if($errors->has('email')) error @endif" id="email" placeholder="{{__('webMessage.email')}}"  value="{{old('email')}}">
                                    @if($errors->has('email'))
                                        <label class="error" for="email">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" name="mobile" class="form-control @if($errors->has('mobile')) error @endif" id="mobile" placeholder="{{__('webMessage.mobile')}}"  value="{{old('mobile')}}">
                                    @if($errors->has('mobile'))
                                        <label class="error" for="mobile">{{ $errors->first('mobile') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" name="username" class="form-control @if($errors->has('username')) error @endif" id="username" placeholder="{{__('webMessage.username')}}"  value="{{old('username')}}">
                                    @if($errors->has('username'))
                                        <label class="error" for="username">{{ $errors->first('username') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" name="password" class="form-control @if($errors->has('password')) error @endif" id="password" placeholder="{{__('webMessage.password')}}"  value="{{old('password')}}">
                                    @if($errors->has('password'))
                                        <label class="error" for="password">{{ $errors->first('password') }}</label>
                                    @endif

                                </div>
                                <div class="login_footer form-group mb-3">
                                    <div class="chek-form">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" required type="checkbox" name="cbx_tac" id="exampleCheckbox2" value="1">
                                            <label class="form-check-label" for="exampleCheckbox2"><span>{{ __('webMessage.agree_to_terms_and_condition') }}</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="login_footer form-group mb-3">
                                    <div class="chek-form">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" type="checkbox" name="is_newsletter_active" id="exampleCheckbox" value="1">
                                            <label class="form-check-label" for="exampleCheckbox"><span>{{__('webMessage.subscribe_for_newletter')}}</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <div class="g-recaptcha" data-sitekey="6LeMueQUAAAAAJ-ZUe9ZqGK3pma9VwbeoaYDgJte"></div>
                                    @if($errors->has('recaptchaError'))
                                        <label id="message-error" class="error" for="message">{{ $errors->first('recaptchaError') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-fill-out btn-block" name="login" >{{__('webMessage.create')}}</button>
                                </div>
                            </form>
                            <div class="form-note text-center">{{__('webMessage.or')}} <a href="{{url(app()->getLocale().'/login')}}">{{__('webMessage.returntosignin')}}</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END LOGIN SECTION -->
@endsection