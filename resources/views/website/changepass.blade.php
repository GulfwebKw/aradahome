@extends('website.include.master')
@section('title' , __('webMessage.changepassword') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account')}}">{{__('webMessage.dashboard')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.changepassword') }}</li>
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
                                <h3>{{__('webMessage.changepassword')}}</h3>
                            </div>
                            <form id="customer_reg_form" method="post" action="{{route('changepass',['locale'=>app()->getLocale()])}}" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group mb-3">
                                    <input type="password" name="oldpassword" class="form-control @if($errors->has('oldpassword')) error @endif" id="oldpassword" placeholder="{{__('webMessage.enter_oldpassword')}}" value="{{old('oldpassword')}}">
                                    @if($errors->has('oldpassword'))
                                        <label class="error" for="oldpassword">{{ $errors->first('oldpassword') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" name="newpassword" class="form-control @if($errors->has('newpassword')) error @endif" id="newpassword" placeholder="{{__('webMessage.enter_newpassword')}}" value="{{old('newpassword')}}">
                                    @if($errors->has('newpassword'))
                                        <label class="error" for="newpassword">{{ $errors->first('newpassword') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" name="confirmpassword" class="form-control @if($errors->has('confirmpassword')) error @endif" id="confirmpassword" placeholder="{{__('webMessage.enter_confirmpassword')}}" value="{{old('confirmpassword')}}">
                                    @if($errors->has('confirmpassword'))
                                        <label class="error" for="confirmpassword">{{ $errors->first('confirmpassword') }}</label>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-fill-out btn-block" name="login" >{{__('webMessage.save_changes')}}</button>
                                </div>
                                @if(session('session_msg'))
                                    <div class="alert-success">{{session('session_msg')}}</div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END LOGIN SECTION -->
@endsection