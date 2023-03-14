@extends('website.include.master')
@php
    if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}

    if(!empty($singleInfo['seo_description_'.$strLang])){
    $seo_description = $singleInfo['seo_description_'.$strLang];
    }else{
    $seo_description = $settingInfo['seo_description_'.$strLang];
    }
    if(!empty($singleInfo['seo_keywords_'.$strLang])){
    $seo_keywords = $singleInfo['seo_keywords_'.$strLang];
    }else{
    $seo_keywords = $settingInfo['seo_keywords_'.$strLang];
    }
@endphp
@section('title' , app()->getLocale()=="en" && !empty($singleInfo->title_en) ? $singleInfo->title_en : (app()->getLocale()=="en" && !empty($singleInfo->title_ar) ? $singleInfo->title_ar : "-" )  )
@section('description' ,$seo_description )
@section('abstract' ,$seo_description )
@section('keywords' ,$seo_keywords )
@section('breadcrumb' )
	<li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
	<li class="breadcrumb-item active">{{ app()->getLocale()=="en" && !empty($singleInfo->title_en) ? $singleInfo->title_en : (app()->getLocale()=="en" && !empty($singleInfo->title_ar) ? $singleInfo->title_ar : "-" )   }}</li>
@endsection

@section('content')
	<!-- STAT SECTION FAQ -->
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="term_conditions">
						@if(app()->getLocale()=="en" && !empty($singleInfo->details_en)) {!!$singleInfo->details_en!!} @elseif(app()->getLocale()=="ar" && !empty($singleInfo->details_ar)) {!!$singleInfo->details_ar!!} @endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END SECTION FAQ -->

@endsection

@section('js')
	<script>
		gtag('event', 'screen_view', {
			'screen_name' : '{{ app()->getLocale()=="en" && !empty($singleInfo->title_en) ? $singleInfo->title_en : (app()->getLocale()=="en" && !empty($singleInfo->title_ar) ? $singleInfo->title_ar : "-" ) }}'
		});
	</script>
@endsection