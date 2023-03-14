@extends('website.include.master')
@section('title' , __('webMessage.orderdetails')  )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/myorders')}}">{{ __('webMessage.myorders')  }}</a></li>
	<li class="breadcrumb-item active">{{ __('webMessage.orderdetails')  }}</li>
@endsection

@section('content')
	<!-- START SECTION SHOP -->
	<div class="section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="text-center order_complete">
						<i class="fas fa-times-circle"></i>
						<div class="heading_s1">
							<h3>{{__('webMessage.orderdetails')}}</h3>
						</div>
						@if(session('session_msg'))
							<p>{{session('session_msg')}}</p>
						@endif
						@if(session('session_msg_error'))
							<p>{{session('session_msg_error')}}</p>
						@endif

						@if(!empty(Request()->ErrorText))
							<p>{{Request()->ErrorText}}</p>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END SECTION SHOP -->
@endsection
