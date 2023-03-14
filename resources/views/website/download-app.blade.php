@extends('website.include.master')
@section('title' , __('webMessage.Downloadnow')  )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item active">{{ __('webMessage.Downloadnow')  }}</li>
@endsection

@section('content')
	<!-- STAT SECTION FAQ -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="term_conditions">
						<div class="row">
							@if (request()->has('qr'))
								<div class="col-lg-4 col-sm-12">
									<a href="/">
										<img src="{{ asset('assets/images/download/app-website.png') }}" class="m-3" style="height: 85px;;width: 260px">
									</a>
								</div>
							@endif
							@if($settingInfo->ios_url != null )
								<div class="col-lg-4 col-sm-12">
									<a href="{{$settingInfo->ios_url}}">
										<img src="{{ asset('assets/images/download/app-store.png') }}" class="m-3" style="height: 85px;;width: 260px">
									</a>
								</div>
							@endif
							@if($settingInfo->android_url != null )
								<div class="col-lg-4 col-sm-12">
									<a href="{{$settingInfo->android_url}}">
										<img src="{{ asset('assets/images/download/google-play.png') }}" class="m-3" style="height: 85px;width: 260px">
									</a>
								</div>
							@endif
							@if($settingInfo->huawei_url != null )
								<div class="col-lg-4 col-sm-12">
									<a href="{{$settingInfo->huawei_url}}">
										<img src="{{ asset('assets/images/download/app-gallery.png') }}"  class="m-3" style="height: 85px;width: 260px">
									</a>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END SECTION FAQ -->

@endsection