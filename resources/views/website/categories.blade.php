@extends('website.include.master')
@section('title' , __('webMessage.categories') )
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.categories') }}</li>
@endsection
@section('content')

    <div id="tt-pageContent">
        <div class="container-indent">
            <div id="app">
                <categories-component :locale="{{json_encode(app()->getLocale())}}"></categories-component>
            </div>
        </div>
    </div>
@endsection