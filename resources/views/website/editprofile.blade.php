@extends('website.include.master')
@php
    if(app()->getLocale()=="en"){$strLang="en";}else{$strLang="ar";}
@endphp
@section('title' , __('webMessage.editprofile') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account')}}">{{__('webMessage.myaccount')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.editprofile')}}</li>
@endsection

@section('content')
    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="dashboard_menu">
                        <ul class="nav nav-tabs flex-column" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/account')}}" ><i class="ti-layout-grid2"></i>{{__('webMessage.dashboard')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/myorders')}}" ><i class="ti-shopping-cart-full"></i>{{__('webMessage.myorders')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/wishlist')}}" ><i class="ti-heart"></i>{{__('webMessage.wishlists')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/account?activeTab=Address')}}" ><i class="ti-location-pin"></i>{{__('webMessage.address')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{url(app()->getLocale().'/editprofile')}}" ><i class="ti-id-badge"></i>{{__('webMessage.editprofile')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/changepass')}}" ><i class="ti-info-alt"></i>{{__('webMessage.changepassword')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);" onclick="event.preventDefault();document.getElementById('logout-forms').submit();" ><i class="ti-lock"></i>{{__('webMessage.logout')}}</a>
                            </li>
                            <form id="logout-forms" action="{{ url(app()->getLocale() . '/logout') }}"
                                  method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="dashboard_content">
                        <div class="card">
                            <div class="card-header">
                                <h3>{{__('webMessage.editprofile')}}</h3>
                            </div>
                            <div class="card-body">

                                @if(session('session_msg'))
                                    <div class="alert alert-success">{{session('session_msg')}}</div>
                                @endif
                                    <form id="customer_reg_form" method="post" action="{{route('editprofileSave',['locale'=>app()->getLocale()])}}" enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group mb-2">
                                            <label for="name">{{__('webMessage.name')}}<font color="#FF0000">*</font></label>
                                            <input type="text" name="name" class="form-control @if($errors->has('name')) error @endif" id="name" placeholder="{{__('webMessage.enter_name')}}" value="@if(Auth::guard('webs')->user()->name) {{Auth::guard('webs')->user()->name}} @else {{old('name')}} @endif">
                                            @if($errors->has('name'))
                                                <label class="error" for="name">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">{{__('webMessage.email')}}<font color="#FF0000">*</font></label>
                                            <input type="email" name="email" class="form-control @if($errors->has('email')) error @endif" id="email" placeholder="{{__('webMessage.enter_email')}}"  value="@if(Auth::guard('webs')->user()->email) {{Auth::guard('webs')->user()->email}} @else {{old('email')}} @endif">
                                            @if($errors->has('email'))
                                                <label class="error" for="email">{{ $errors->first('email') }}</label>
                                            @endif
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="mobile">{{__('webMessage.mobile')}}<font color="#FF0000">*</font></label>
                                            <input type="text" name="mobile" class="form-control @if($errors->has('mobile')) error @endif" id="mobile" placeholder="{{__('webMessage.enter_mobile')}}"  value="@if(Auth::guard('webs')->user()->mobile) {{Auth::guard('webs')->user()->mobile}} @else {{old('mobile')}} @endif">
                                            @if($errors->has('mobile'))
                                                <label class="error" for="mobile">{{ $errors->first('mobile') }}</label>
                                            @endif
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="image">{{__('webMessage.image')}}</label> @if(Auth::guard('webs')->user()->image) <img src="{{url('uploads/customers/thumb/'.Auth::guard('webs')->user()->image)}}" width="30" class="float-right"> @endif
                                            <input type="file" name="image" class="form-control @if($errors->has('image')) error @endif" id="image">
                                            @if($errors->has('image'))
                                                <label class="error" for="image">{{ $errors->first('image') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group mb-2">
                                            <label for="username">{{__('webMessage.username')}}<font color="#FF0000">*</font></label>
                                            <input type="text" name="username" class="form-control @if($errors->has('username')) error @endif" id="username" placeholder="{{__('webMessage.enter_username')}}"  value="@if(Auth::guard('webs')->user()->username) {{Auth::guard('webs')->user()->username}} @else {{old('username')}} @endif">
                                            @if($errors->has('username'))
                                                <label class="error" for="username">{{ $errors->first('username') }}</label>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="col-auto">
                                                <div class="form-group mb-2">
                                                    <button class="btn btn-border btn-fill-out" type="submit">{{__('webMessage.save_changes')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection