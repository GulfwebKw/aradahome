@extends('driver.include.master')
@section('title' , $driver->exists ? 'Edit '.$driver->fullname : 'Create new driver')
@section('content')
    <div class="row">
        <div class="col-lg-8">

            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <i class="fa {{ $driver->exists ? 'fa-user-edit' : 'fa-user-plus' }}"></i> {{ $driver->exists ? 'Edit '.$driver->fullname : 'Create new driver' }}
                        </h3>
                    </div>
                </div>

                <!--begin::Form-->
                <form class="kt-form" method="POST" enctype="multipart/form-data" @if($driver->exists) action="{{ route('driver.admin.driver.update' , $driver->id ) }}" @else action="{{ route('driver.admin.driver.store') }}" @endif>
                    @if($driver->exists)
                        @method('PUT')
                    @endif
                    @csrf
                    <div class="kt-portlet__body">
                        <div class="kt-section kt-section--first">
                            <h3 class="kt-section__title">1. Driver Info:</h3>
                            <div class="kt-section__body">
                                <div  class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">First Name (En):</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control @if($errors->has('first_name_en')) is-invalid @endif" placeholder="Enter first name" name="first_name_en" value="{{ old('first_name_en' , $driver->first_name_en) }}">
                                                @if($errors->has('first_name_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('first_name_en') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Last Name (En):</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control @if($errors->has('last_name_en')) is-invalid @endif" placeholder="Enter last name" name="last_name_en" value="{{ old('last_name_en' , $driver->last_name_en) }}">
                                                @if($errors->has('last_name_en'))
                                                    <div class="invalid-feedback">{{ $errors->first('last_name_en') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Phone number:</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control @if($errors->has('phone')) is-invalid @endif" placeholder="Enter phone number" name="phone" value="{{ old('phone' , $driver->phone) }}">
                                                @if($errors->has('phone'))
                                                    <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">First Name (Ar):</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control rtl @if($errors->has('first_name_ar')) is-invalid @endif" placeholder="Enter first name" name="first_name_ar" value="{{ old('first_name_ar' , $driver->first_name_ar) }}">
                                                @if($errors->has('first_name_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('first_name_ar') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Last Name (Ar):</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control rtl @if($errors->has('last_name_ar')) is-invalid @endif" placeholder="Enter last name" name="last_name_ar" value="{{ old('last_name_ar' , $driver->last_name_ar) }}">
                                                @if($errors->has('last_name_ar'))
                                                    <div class="invalid-feedback">{{ $errors->first('last_name_ar') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="kt-section__title">2. Driver Account:</h3>
                            <div class="kt-section__body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Username:</label>
                                            <div class="col-lg-6">
                                                <input  name="username" value="{{ old('username' , $driver->username) }}" type="text" class="form-control  @if($errors->has('username')) is-invalid @endif" placeholder="Enter Username">
                                                @if($errors->has('username'))
                                                    <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                                                @endif
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Password:</label>
                                            <div class="col-lg-6">
                                                <input type="password" name="password" class="form-control @if($errors->has('password')) is-invalid @endif" placeholder="Enter password">
                                                @if($errors->has('password'))
                                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                                @endif
                                                <span class="form-text text-muted">{{ $driver->exists ? 'Leave it empty if you don\'t want to change password' : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Confirm Password:</label>
                                            <div class="col-lg-6">
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="Enter password again">
                                                <span class="form-text text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Is active:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch">
                                                    <label>
                                                        <input value="1" {{!empty($driver->is_active)?'checked':''}} type="checkbox" name="is_active" >
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">Avatar:</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="kt-avatar kt-avatar--outline kt-avatar--danger" id="kt_user_avatar_4">
                                                    <div class="kt-avatar__holder" style="background-image: url({!! url('uploads/users/'.($driver->avatar ?? 'no-image.png') ) !!})"></div>
                                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                        <i class="fa fa-pen"></i>
                                                        <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg">
                                                    </label>
                                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-times"></i>
													</span>
                                                </div>
                                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!--end::Form-->
            </div>

            <!--end::Portlet-->
        </div>

        <div class="col-md-4">
            <!--begin:: Widgets/Applications/User/Profile1-->
            <div class="kt-portlet">
                <div class="kt-portlet__head  kt-portlet__head--noborder">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Personal Card (En)
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <a href="{{ route('driver.admin.driver.print' , ['en' , $driver->id]) }}" target="_blank" class="btn btn-link">
                            <i class="kt-nav__link-icon fa fa-print"></i>
                            <span class="kt-nav__link-text">Print</span>
                        </a>
                    </div>
                </div>
                <div class="kt-portlet__body kt-portlet__body--fit-y">

                    <!--begin::Widget -->
                    <div class="border border-info kt-widget kt-widget--user-profile-1 pl-3 pr-3 mb-3 kt-iconbox--wave " style="border-radius: 37px;">
                        <div class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--flag kt-ribbon--ver kt-ribbon--border-dash-hor kt-ribbon--info">
                            <div class="kt-ribbon__target" style="top: 0; right: 20px; height: 45px;">
                                <span class="kt-ribbon__inner"></span><i class="fa fa-shipping-fast"></i>
                            </div>
                            <div class="mt-3 w-100" style="display: inline-flex;">
                                <div class="ml-1 mr-1">
                                    <img src="{!! url('uploads/logo/'.$settingInfo->favicon) !!}" style="max-height: 50px;" alt="image">
                                </div>
                                <div class="w-75">
                                    <h3 class="kt-portlet__head-title text-center" style="font-size: 1.4rem;font-weight: bold;color: #48465b;">
                                        {{$settingInfo->name_en}}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="kt-widget__head kt-ribbon mt-4">
                            <div class="kt-widget__media">
                                <img src="{!! url('uploads/users/'.($driver->avatar ?? 'no-image.png')) !!}" class="UserAvatar" alt="image" style="width: 150px;">
                                <div class="badge badge-info font-weight-bold mt-1 text-center w-100" style="font-size: inherit;">{{$driver->id ? $settingInfo->prefix.'D'.$driver->id: $settingInfo->prefix.'D'. 'NEW'}}</div>
                            </div>
                            <div class="kt-widget__content w-100" style="padding-right: 1.6rem;">
                                <div class="kt-widget__section">
                                    <div href="#" class="kt-widget__username">
                                        <strong>Name: </strong><span class="FullNameEn">{{ old( 'first_name_en' , $driver->first_name_en ) }} {{ old( 'last_name_en' , $driver->last_name_en ) }}</span>
                                        <i class="flaticon2-correct kt-font-success"></i>
                                    </div>
                                    <div class="rtl text-right  mt-2" >
                                        <strong>الاسم: </strong><span class=" FullNameAr">{{ old( 'first_name_ar' , $driver->first_name_ar ) }} {{ old( 'last_name_ar' , $driver->last_name_ar ) }}</span>
                                    </div>
                                </div>
{{--                                <div class="kt-widget__action UserName"></div>--}}
                                <div class="mt-3">
                                    <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode($driver->id ? $settingInfo->prefix.'D'.$driver->id: $settingInfo->prefix.'D'. 'NEW', $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Widget -->
                </div>
            </div>
            <!--end:: Widgets/Applications/User/Profile1-->


            <!--begin:: Widgets/Applications/User/Profile1-->
            <div class="kt-portlet">
                <div class="kt-portlet__head  kt-portlet__head--noborder">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Personal Card (Ar)
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <a href="{{ route('driver.admin.driver.print' , ['ar' , $driver->id]) }}" target="_blank" class="btn btn-link">
                            <i class="kt-nav__link-icon fa fa-print"></i>
                            <span class="kt-nav__link-text">Print</span>
                        </a>
                    </div>
                </div>
                <div class="kt-portlet__body kt-portlet__body--fit-y rtl" id="PersonalCardAr">

                    <!--begin::Widget -->
                    <div class="border border-info kt-widget kt-widget--user-profile-1 pl-3 pr-3 mb-3 kt-iconbox--wave " style="border-radius: 37px;">
                        <div class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--flag kt-ribbon--ver kt-ribbon--border-dash-hor kt-ribbon--info">
                            <div class="kt-ribbon__target" style="top: 0; left: 20px; height: 45px;">
                                <span class="kt-ribbon__inner"></span><i class="fa fa-shipping-fast"></i>
                            </div>
                            <div class="mt-3 w-100" style="display: inline-flex;">
                                <div class="ml-1 mr-1">
                                    <img src="{!! url('uploads/logo/'.$settingInfo->favicon) !!}" style="max-height: 50px;" alt="image">
                                </div>
                                <div class="w-75">
                                    <h3 class="kt-portlet__head-title text-center" style="font-size: 1.4rem;font-weight: bold;color: #48465b;">
                                        {{$settingInfo->name_ar}}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="kt-widget__head kt-ribbon mt-4">
                            <div class="kt-widget__media">
                                <img src="{!! url('uploads/users/'.($driver->avatar ?? 'no-image.png')) !!}" class="UserAvatar" alt="image" style="width: 150px;">
                                <div class="badge badge-info font-weight-bold mt-1 text-center w-100" style="font-size: inherit;">{{$driver->id ? $settingInfo->prefix.'D'.$driver->id: $settingInfo->prefix.'D'. 'NEW'}}</div>
                            </div>
                            <div class="kt-widget__content w-100" style="padding-right: 1.6rem;">
                                <div class="kt-widget__section">
                                    <div href="#" class="kt-widget__username rtl text-right">
                                        <strong>الاسم: </strong><span class="FullNameAr">{{ old( 'first_name_ar' , $driver->first_name_ar ) }} {{ old( 'last_name_ar' , $driver->last_name_ar ) }}</span>
                                        <i class="flaticon2-correct kt-font-success"></i>
                                    </div>
                                    <div class="mt-2" >
                                        <strong>Name: </strong><span class=" FullNameEn ">{{ old( 'first_name_en' , $driver->first_name_en ) }} {{ old( 'last_name_en' , $driver->last_name_en ) }}</span>
                                    </div>
                                </div>
{{--                                <div class="kt-widget__action UserName"></div>--}}
                                <div class="mt-3 text-right">
                                    <img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode($driver->id ? $settingInfo->prefix.'D'.$driver->id: $settingInfo->prefix.'D'. 'NEW', $BRGenerator::TYPE_CODE_128 , 2 , 50))}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Widget -->
                </div>
            </div>
            <!--end:: Widgets/Applications/User/Profile1-->
        </div>
    </div>
@endsection

@section('js')


    <!--begin::Page Scripts(used by this page) -->
    <script src="{{ url('admin_assets/assets/js/pages/crud/file-upload/ktavatar.js') }}" type="text/javascript"></script>

    <!--end::Page Scripts -->
    <script>
        $("input[name=first_name_en]").keyup(function (){
            $(".FullNameEn").html($("input[name=first_name_en]").val() + " " + $("input[name=last_name_en]").val() );
        })
        $("input[name=last_name_en]").keyup(function (){
            $(".FullNameEn").html($("input[name=first_name_en]").val() + " " + $("input[name=last_name_en]").val() );
        })
        $("input[name=first_name_ar]").keyup(function (){
            $(".FullNameAr").html($("input[name=first_name_ar]").val() + " " + $("input[name=last_name_ar]").val() );
        })
        $("input[name=last_name_ar]").keyup(function (){
            $(".FullNameAr").html($("input[name=first_name_ar]").val() + " " + $("input[name=last_name_ar]").val() );
        })
        $("input[name=username]").keyup(function (){
            $(".UserName").html($("input[name=username]").val() );
        })
        var lastPic = "";
        var intervalId = setInterval(function() {
            var pic =  $('.kt-avatar__holder').css('background-image');
            if ( lastPic !== pic ) {
                $('.UserAvatar').attr('src', pic.substring(5, pic.length - 2));
                lastPic = pic ;
            }
        }, 50);
    </script>
@endsection