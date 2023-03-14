@extends('website.include.master')
@section('title' , __('webMessage.myorders') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account')}}">{{__('webMessage.dashboard')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.myorders')}}</li>
@endsection
@php
    if(!empty(Auth::guard('webs')->user()->is_seller)){
        $userType=1;
    }else{
        $userType=0;
    }
@endphp
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
                                <a class="nav-link active" href="{{url(app()->getLocale().'/myorders')}}" ><i class="ti-shopping-cart-full"></i>{{__('webMessage.myorders')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/wishlist')}}" ><i class="ti-heart"></i>{{__('webMessage.wishlists')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url(app()->getLocale().'/account?activeTab=Address')}}" ><i class="ti-location-pin"></i>{{__('webMessage.address')}}</a>
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
                        <div class="card">
                            <div class="card-header">
                                <h3>{{__('webMessage.myorders')}}</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{__('webMessage.orderid')}}</th>
                                            <th>{{__('webMessage.date')}}</th>
                                            <th>{{__('webMessage.order_status')}}</th>
                                            <th>{{__('webMessage.grandtotal')}}</th>
                                            <th>{{__('webMessage.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($myorderLists as $myorderList)
                                            @php
                                                $getProperties = App\Http\Controllers\webCartController::getMyOrdersProperties($myorderList->id);
                                                $sellerDetails = App\Http\Controllers\AdminCustomersController::getCustomerDetails($myorderList->customer_id);
                                            @endphp
                                        <tr>
                                            <td>{{$myorderList->order_id}}</td>
                                            <td>{{$myorderList->created_at->format('F d, Y')}}</td>
                                            <td>{{__('webMessage.'.$myorderList->order_status)}}</td>
                                            <td>{{ \App\Currency::default()  }} {{number_format($getProperties['totalAmt'],3)}}</td>
                                            <td><a href="{{url(app()->getLocale().'/orderdetails/'.$myorderList->order_id)}}" class="btn btn-fill-out btn-sm">{{ __('webMessage.details') }}</a></td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{__('webMessage.norecordfound')}}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-12 mt-2 mt-md-4">
                                        <div class="pagination pagination_style1 justify-content-center">
                                            {!! $myorderLists->appends($_GET)->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
@endsection