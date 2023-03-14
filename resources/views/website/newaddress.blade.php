@extends('website.include.master')
@php
    if(app()->getLocale()=="en"){$strLang="en";}else{$strLang="ar";}
@endphp
@section('title' , __('webMessage.newaddress') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account')}}">{{__('webMessage.myaccount')}}</a></li>
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/account?activeTab=Address')}}">{{__('webMessage.address')}}</a></li>
    <li class="breadcrumb-item active">{{__('webMessage.newaddress')}}</li>
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
                                <a class="nav-link active" href="{{url(app()->getLocale().'/account?activeTab=Address')}}" ><i class="ti-location-pin"></i>{{__('webMessage.address')}}</a>
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
                                <h3>{{__('webMessage.newaddress')}}</h3>
                            </div>
                            <div class="card-body">
                                @if(session('session_msg'))
                                    <div class="alert alert-success">{{session('session_msg')}}</div>
                                @endif
                                <form id="customer_reg_form" method="post" action="{{route('addressSave' , ['locale' => app()->getLocale()])}}" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="title">{{__('webMessage.title')}}<font color="#FF0000">*</font></label>
                                                <input type="text" name="title"  class="form-control" id="title" placeholder="{{__('webMessage.enter_title')}}" autcomplete="off" value="@if(old('title')) {{old('title')}} @endif">
                                                @if($errors->has('title'))
                                                    <label id="title-error" class="error" for="title">{{ $errors->first('title') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="latitude">{{__('webMessage.latitude')}}</label>
                                                <input type="text" name="latitude"  class="form-control" id="latitude" placeholder="{{__('webMessage.enter_latitude')}}" autcomplete="off" value="@if(old('latitude')) {{old('latitude')}} @endif">
                                                @if($errors->has('latitude'))
                                                    <label id="block-error" class="error" for="block">{{ $errors->first('latitude') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="longitude">{{__('webMessage.longitude')}}</label>
                                                <input type="text" name="longitude"  class="form-control" id="longitude" placeholder="{{__('webMessage.enter_longitude')}}" autcomplete="off" value="@if(old('longitude')) {{old('longitude')}} @endif">
                                                @if($errors->has('longitude'))
                                                    <label id="block-error" class="error" for="block">{{ $errors->first('longitude') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $countryid=0;
                                        $countryLists = App\Http\Controllers\webCartController::get_country($countryid);
                                    @endphp
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="country">{{__('webMessage.country')}}<font color="#FF0000">*</font></label>
                                                <select name="country"  class="form-control country_checkout" id="country" >
                                                    <option value="0">{{__('webMessage.choosecountry')}}</option>
                                                    @if(!empty($countryLists) && count($countryLists)>0)
                                                        @foreach($countryLists as $countryList)
                                                            <option value="{{$countryList->id}}" @if((!empty(old('country' , 2 )) && old('country' , 2 )==$countryList->id)) selected @endif>{{$countryList['name_'.$strLang]}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if($errors->has('country'))
                                                    <label id="country-error" class="error" for="country">{{ $errors->first('country') }}</label>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="state">{{__('webMessage.state')}}<font color="#FF0000">*</font></label>
                                                <select name="state"  class="form-control state_checkout" id="state_checkout" >
                                                    <option value="0">{{__('webMessage.choosestate')}}</option>
                                                    @if(!empty(old('country',2)))
                                                        @php
                                                            if(!empty(old('country',2))){$country_id=old('country',2);}else{$country_id='';}
                                                            $stateLists = App\Http\Controllers\webCartController::get_country($country_id);
                                                        @endphp
                                                        @foreach($stateLists as $stateList)
                                                            <option value="{{$stateList->id}}" @if((!empty(old('state')) && old('state')==$stateList->id)) selected @endif>{{$stateList['name_'.$strLang]}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if($errors->has('state'))
                                                    <label id="state-error" class="error" for="state">{{ $errors->first('state') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4" id="area_Div"  @if(! empty(old('state')) and App\Http\Controllers\webCartController::get_country($state_id)->count() == 0 ) style="display: none;" @endif>
                                            <div class="form-group mb-2">
                                                <label for="area">{{__('webMessage.area')}}<font color="#FF0000">*</font></label>
                                                <select name="area"  class="form-control" id="area_checkout" >
                                                    <option value="0">{{__('webMessage.choosearea')}}</option>
                                                    @if(!empty(old('state')))
                                                        @php
                                                            if(!empty(old('state'))){$state_id=old('state');}else{$state_id=0;}
                                                            $areaLists = App\Http\Controllers\webCartController::get_country($state_id);
                                                        @endphp
                                                        @foreach($areaLists as $areaList)
                                                            <option value="{{$areaList->id}}" @if((!empty(old('area')) && old('area')==$areaList->id)) selected @endif>{{$areaList['name_'.$strLang]}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if($errors->has('area'))
                                                    <label id="area-error" class="error" for="area">{{ $errors->first('area') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="block">{{__('webMessage.block')}}<font color="#FF0000">*</font></label>
                                                <input type="text" name="block"  class="form-control" id="block" placeholder="{{__('webMessage.enter_block')}}" autcomplete="off" value="@if(old('block')) {{old('block')}} @endif">
                                                @if($errors->has('block'))
                                                    <label id="block-error" class="error" for="block">{{ $errors->first('block') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="street">{{__('webMessage.street')}}<font color="#FF0000">*</font></label>
                                                <input type="text" name="street"  class="form-control" id="street" placeholder="{{__('webMessage.enter_street')}}" autcomplete="off" value="@if(old('street')) {{old('street')}} @endif">
                                                @if($errors->has('street'))
                                                    <label id="street-error" class="error" for="street">{{ $errors->first('street') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="avenue">{{__('webMessage.avenue')}}</label>
                                                <input type="text" name="avenue"  class="form-control" id="avenue" placeholder="{{__('webMessage.enter_avenue')}}" autcomplete="off" value="@if(old('avenue')) {{old('avenue')}} @endif">
                                                @if($errors->has('avenue'))
                                                    <label id="avenue-error" class="error" for="avenue">{{ $errors->first('avenue') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="house">{{__('webMessage.house')}}<font color="#FF0000">*</font></label>
                                                <input type="text" name="house"  class="form-control" id="house" placeholder="{{__('webMessage.enter_house')}}" autcomplete="off" value="@if(old('house')) {{old('house')}} @endif">
                                                @if($errors->has('house'))
                                                    <label id="house-error" class="error" for="house">{{ $errors->first('house') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="floor">{{__('webMessage.floor')}}</label>
                                                <input type="text" name="floor"  class="form-control" id="floor" placeholder="{{__('webMessage.enter_floor')}}" autcomplete="off" value="@if(old('floor')) {{old('floor')}} @endif">
                                                @if($errors->has('floor'))
                                                    <label id="floor-error" class="error" for="floor">{{ $errors->first('floor') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-lg-4">
                                            <div class="form-group mb-2">
                                                <label for="is_default" style="margin-top:10px;"><input type="checkbox" name="is_default"  id="is_default" autcomplete="off" value="1">&nbsp;{{__('webMessage.default_address')}}</label>

                                            </div>
                                        </div>

                                    </div>

                                    <div id="mapids" class="mb-2" style="width:100%;height:400px;"></div>


                                    <div class="row">
                                        <div class="col-auto">
                                            <div class="form-group mb-2">
                                                <button class="btn btn-border btn-fill-out" type="submit">{{__('webMessage.save')}}</button>
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

@section('js')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZDl2BsDI2qPQ0l-eJp5eVXetkFGkO75E&callback=initMap&libraries=&v=weekly" async ></script>
    <script>

        <!-- map -->
        function initMap() {
            const myLatlng = { lat: 29.3117, lng: 47.4818 };
            const map = new google.maps.Map(document.getElementById("mapids"), {
                zoom: 10,
                center: myLatlng
            });

            // Create the initial InfoWindow.
            let infoWindow = new google.maps.InfoWindow({
                content: "Click the map to get Lat/Lng!",
                position: myLatlng
            });
            infoWindow.open(map);
            // Configure the click listener.
            map.addListener("click", (mapsMouseEvent) => {
                // Close the current InfoWindow.
                infoWindow.close();
                // Create a new InfoWindow.
                infoWindow = new google.maps.InfoWindow({
                    position: mapsMouseEvent.latLng,
                });
                infoWindow.setContent(
                    JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
                );
                var obj = $.parseJSON(JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2));
                if(obj.lat!=""){
                    $("#latitude").val(obj.lat)
                }
                if(obj.lng!=""){
                    $("#longitude").val(obj.lng)
                }
                infoWindow.open(map);
            });
        }


    </script>
@endsection