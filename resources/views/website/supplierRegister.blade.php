@extends('website.include.master')
@section('title' , __('webMessage.supplier_registration') )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item active">{{__('webMessage.supplier_registration')}}</li>
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
								<h3>{{__('webMessage.supplier_registration')}}</h3>
							</div>
							@if(empty($isAccountCreated))
							<form id="customer_reg_form" method="post" action="{{route('supplierregister',['locale'=>app()->getLocale()])}}" enctype="multipart/form-data">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group mb-3">
									<input type="text" name="name" class="form-control @if($errors->has('name')) error @endif" id="name" placeholder="{{__('webMessage.enter_title')}}" value="{{old('name')}}">
									@if($errors->has('name'))
										<label class="error" for="name">{{ $errors->first('name') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="email" name="email" class="form-control @if($errors->has('email')) error @endif" id="email" placeholder="{{__('webMessage.enter_email')}}"  value="{{old('email')}}">
									@if($errors->has('email'))
										<label class="error" for="email">{{ $errors->first('email') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="text" name="mobile" class="form-control @if($errors->has('mobile')) error @endif" id="mobile" placeholder="{{__('webMessage.enter_mobile')}}"  value="{{old('mobile')}}">
									@if($errors->has('mobile'))
										<label class="error" for="mobile">{{ $errors->first('mobile') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="file" name="image" class="form-control @if($errors->has('image')) error @endif" id="image" placeholder="{{__('webMessage.choose_image')}}"  value="{{old('image')}}">
									@if($errors->has('image'))
										<label class="error" for="image">{{ $errors->first('image') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="text" name="username" class="form-control @if($errors->has('username')) error @endif" id="username" placeholder="{{__('webMessage.enter_username')}}"  value="{{old('username')}}">
									@if($errors->has('username'))
										<label class="error" for="username">{{ $errors->first('username') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<input type="password" name="password" class="form-control @if($errors->has('password')) error @endif" id="password" placeholder="{{__('webMessage.enter_password')}}"  value="{{old('password')}}">
									@if($errors->has('password'))
										<label class="error" for="password">{{ $errors->first('password') }}</label>
									@endif
								</div>
								<div class="form-group mb-3">
									<button type="submit" class="btn btn-fill-out btn-block"  >{{__('webMessage.create')}}</button>
								</div>
							</form>
							@else
								<div class="form-note text-center">{{trans('webMessage.suppluieraccountcreatedsuccess')}}</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END LOGIN SECTION -->
@endsection