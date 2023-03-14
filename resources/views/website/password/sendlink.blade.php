@extends('website.include.master')
@section('title' , __(request()->token ? 'webMessage.resetforgotpassword' : 'webMessage.resetforgotpassword') )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/login')}}">{{__('webMessage.login')}}</a></li>
	<li class="breadcrumb-item active">{{__(request()->token ? 'webMessage.resetforgotpassword' : 'webMessage.resetforgotpassword')}}</li>
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
								<h3>{{__(request()->token ? 'webMessage.resetforgotpassword' : 'webMessage.resetforgotpassword')}}</h3>
							</div>
							@if(request()->token)
							<form method="post" class="fpass-validation-active" id="fpass-form-main-form" action="{{route('password.token',['locale'=>app()->getLocale(),'token'=>request()->token])}}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group mb-3">
									<input type="email" name="email" class="form-control @if($errors->has('email')) error @endif" id="email" placeholder="{{__('webMessage.enter_email')}}"  value="{{old('email')}}">
									@if($errors->has('email'))
										<label class="error" for="email">{{ $errors->first('email') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="password" name="new_password" class="form-control @if($errors->has('new_password')) error @endif" id="new_password" placeholder="{{__('webMessage.enter_new_password')}}"  value="{{old('new_password')}}">
									@if($errors->has('new_password'))
										<label class="error" for="new_password">{{ $errors->first('new_password') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="password" name="confirm_password" class="form-control @if($errors->has('confirm_password')) error @endif" id="confirm_password" placeholder="{{__('webMessage.enter_confirm_password')}}"  value="{{old('confirm_password')}}">
									@if($errors->has('confirm_password'))
										<label class="error" for="confirm_password">{{ $errors->first('confirm_password') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<button type="submit" class="btn btn-fill-out btn-block" >{{__('webMessage.save_changes')}}</button>
								</div>
							</form>
							@else
								<form method="post" class="fpass-validation-active" id="fpass-form-main-form" action="{{route('password.email',['locale'=>app()->getLocale()])}}">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">

									<div class="form-group mb-3">
										<input type="email" name="email" class="form-control @if($errors->has('email')) error @endif" id="email" placeholder="{{__('webMessage.enter_email')}}"  value="{{old('email')}}">
										@if($errors->has('email'))
											<label class="error" for="email">{{ $errors->first('email') }}</label>
										@endif
									</div>
									<div class="form-group mb-3">
										<button type="submit" class="btn btn-fill-out btn-block" >{{__('webMessage.send_link_btn')}}</button>
									</div>
								</form>
							@endif
							<div class="form-note text-center">{{__('webMessage.or')}} <a href="{{url(app()->getLocale().'/login')}}">{{__('webMessage.returntosignin')}}</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END LOGIN SECTION -->
@endsection