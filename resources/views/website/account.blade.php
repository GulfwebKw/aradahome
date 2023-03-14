@extends('website.include.master')
@section('title' , __('webMessage.dashboard') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.dashboard')}}</li>
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
                                <a class="nav-link @if(! request()->query('activeTab' , false)) active @endif" href="{{url(app()->getLocale().'/account')}}" ><i class="ti-layout-grid2"></i>{{__('webMessage.dashboard')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/myorders')}}" ><i class="ti-shopping-cart-full"></i>{{__('webMessage.myorders')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/wishlist')}}" ><i class="ti-heart"></i>{{__('webMessage.wishlists')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->query('activeTab' , false)) active @endif" href="{{url(app()->getLocale().'/account?activeTab=Address')}}" ><i class="ti-location-pin"></i>{{__('webMessage.address')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/editprofile')}}" ><i class="ti-id-badge"></i>{{__('webMessage.editprofile')}}</a>
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
                        @if(session('session_msg')) <br clear="all"><br clear="all">
                        <div class="alert  alert-success">{{session('session_msg')}}</div>
                        @endif
                        @if(session('session_msg_f')) <br clear="all"><br clear="all">
                        <div class="alert alert-danger">{{session('session_msg_f')}}</div>
                        @endif
                        @if(request()->query('activeTab' , false))
                            <a href="{{url(app()->getLocale().'/newaddress')}}" class="btn mb-3 btn-border btn-fill-out">{{__('webMessage.addnewaddress')}}</a>
                            @if(!empty($customerAddress) && count($customerAddress)>0)
                                <div class="row">
                                    @foreach($customerAddress as $customerAddr)
                                        @php
                                            $address = App\Http\Controllers\accountController::getCustAddress($customerAddr->id);
                                        @endphp
                                        <div class="col-lg-6" @if(!empty($customerAddr->is_default)) style="border:2px #0000FF solid;" @endif>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="tt-title">{{$customerAddr->title}}</h3>
                                                </div>
                                                <div class="card-body">
                                                    {!!$address!!}
                                                </div>
                                                <div class="card-footer">
                                                    <a title="{{__('webMessage.edit')}}" href="{{url(app()->getLocale().'/editaddress/'.$customerAddr->id)}}" class="@if(app()->getLocale()=='en') float-right @else float-left @endif btn btn-link " title="{{__('webMessage.edit')}}"><i class="ion-edit"></i> {{__('webMessage.edit')}}</a>
                                                    <a  title="{{__('webMessage.delete')}}" href="{{url(app()->getLocale().'/addressdelete/'.$customerAddr->id)}}" id="{{$customerAddr->id}}" class="deletemyAddress @if(app()->getLocale()=='en') float-right @else float-left @endif btn btn-outline-danger" title="{{__('webMessage.delete')}}"><i class="ion-android-delete"></i> {{__('webMessage.delete')}}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="card">
                                <div class="card-header">
                                    <h3>{{__('webMessage.dashboard')}}</h3>
                                </div>
                                <div class="card-body">
                                    <!-- TODO: dashboard of account text -->
                                    <table class="table">
                                        <tbody>
                                        @if(Auth::guard('webs')->user()->name)
                                            <tr>
                                                <td>{{__('webMessage.name')}}:</td>
                                                <td>{{Auth::guard('webs')->user()->name}} @if(!empty(Auth::guard('webs')->user()->is_seller))(SELLER)@endif</td>
                                            </tr>
                                        @endif
                                        @if(Auth::guard('webs')->user()->email)
                                            <tr>
                                                <td>{{__('webMessage.email')}}:</td>
                                                <td>{{Auth::guard('webs')->user()->email}}</td>
                                            </tr>
                                        @endif
                                        @if(Auth::guard('webs')->user()->mobile)
                                            <tr>
                                                <td>{{__('webMessage.mobile')}}:</td>
                                                <td>{{Auth::guard('webs')->user()->mobile}}</td>
                                            </tr>
                                        @endif
                                        @if(Auth::guard('webs')->user()->username)
                                            <tr>
                                                <td>{{__('webMessage.username')}}:</td>
                                                <td>{{Auth::guard('webs')->user()->username}}</td>
                                            </tr>
                                        @endif
                                        @if(Auth::guard('webs')->user()->created_at)
                                            <tr>
                                                <td>{{__('webMessage.created')}}:</td>
                                                <td>{{ \Carbon\Carbon::parse(Auth::guard('webs')->user()->created_at)->diffForHumans() }}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection