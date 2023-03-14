@extends('website.include.master')
@section('title' , __('webMessage.paymentprocessing')  )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item active">{{ __('webMessage.paymentprocessing')  }}</li>
@endsection

@section('content')
	<!-- STAT SECTION FAQ -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="term_conditions">
						<div class="text-center w-100">{{trans('webMessage.waituntilredirect')}}</div>
						<div class="text-center w-100"><img src="{{url('assets/images/ajax-loader.gif')}}"></div>
						@if(!empty($fields))
							<form id="payment_confirmation" name="payment_confirmation" action="https://testsecureacceptance.cybersource.com/pay" method="post"/>
							@foreach($fields as $name => $value)
								<input type="hidden" id="{{$name}}" name="{{$name}}" value="{{$value}}"/>
							@endforeach

							<input type="hidden" id="signature" name="signature" value="{{Common::sign($fields)}}"/>
							</form>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END SECTION FAQ -->

@endsection