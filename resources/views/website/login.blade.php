@extends('website.include.master')
@section('title' , __('webMessage.signin') )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item active">{{__('webMessage.signin')}}</li>
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
								<h3>{{__('webMessage.signin')}}</h3>
							</div>
							<form id="customer_login_form" method="post" action="{{route('loginform',['locale'=>app()->getLocale()])}}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group mb-3">
									<input type="text" name="login_username"  class="form-control @if($errors->has('login_username')) error @endif" id="login_username" placeholder="{{__('webMessage.enter_username_or_email')}}" autcomplete="off" value="@if(Cookie::get('xlogin_username')) {{Cookie::get('xlogin_username')}} @elseif(old('login_username')) {{old('login_username')}} @endif">
									@if($errors->has('login_username'))
										<label id="login_username" class="error" for="login_username">{{ $errors->first('login_username') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="password" name="login_password"  class="form-control @if($errors->has('login_password')) error @endif" id="login_password" placeholder="{{__('webMessage.enter_password')}}" autcomplete="off"  value="@if(Cookie::get('xlogin_password')) {{Cookie::get('xlogin_password')}} @elseif(old('login_password')) {{old('login_password')}} @endif">
									@if($errors->has('login_password'))
										<label id="login_password" class="error" for="login_username">{{ $errors->first('login_password') }}</label>
									@endif							</div>
								<div class="login_footer form-group mb-3">
									<div class="chek-form">
										<div class="custome-checkbox">
											<input type="checkbox" id="remember_me" name="remember_me" @if(Illuminate\Support\Facades\Cookie::get('xremember_me')) checked @endif  value="1">
											<label for="remember_me"><span class="check"></span><span class="box"></span>&nbsp;{{__('webMessage.remember_me_txt')}}</label>
										</div>
									</div>
									<a href="{{url(app()->getLocale().'/password/reset')}}">{{__('webMessage.forgot_password_txt')}}</a>
								</div>
								<div class="form-group mb-3">
									<button type="submit" class="btn btn-fill-out btn-block" name="login" >{{__('webMessage.login')}}</button>
								</div>
							</form>
							<div class="form-note text-center">{{__('webMessage.newcustomer')}}? <a href="{{url(app()->getLocale().'/register')}}">{{__('webMessage.createanaccount')}}</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END LOGIN SECTION -->
@endsection