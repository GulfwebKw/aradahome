@extends('website.include.master')
@section('title' , trans('webMessage.BLOG'))
@section('breadcrumb' )
    <li class="breadcrumb-item"><a href="{{url(app()->getLocale().'/')}}">{{__('webMessage.home')}}</a></li>
    <li class="breadcrumb-item active">{{ __('webMessage.BLOG')  }}</li>
@endsection
@section('content')

    <!-- START SECTION BLOG -->
    <div class="section">
        <div class="container">
            <div class="row">
                @forelse($posts as $post)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="blog_post blog_style2 box_shadow1">
                        @if($post->image)
                        <div class="blog_img">
                            <a href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug]) }}">
                                <img src="{!! url('uploads/blog/'.$post->image) !!}" alt="{{ $post['title_'.app()->getLocale()] }}">
                            </a>
                        </div>
                        @endif
                        <div class="blog_content bg-white">
                            <div class="blog_text">
                                <h6 class="blog_title"><a href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug]) }}">{{ $post['title_'.app()->getLocale()] }}</a></h6>
                                <ul class="list_none blog_meta">
                                    <li><a href="#"><i class="ti-calendar"></i> {{ $post->created_at->format('F d, Y') }}</a></li>
                                    <li><a href="#"><i class="ti-comments"></i> {{ $post->publsihComments->count() }}</a></li>
                                </ul>
                                <p>{{ str_limit(preg_replace(['/<(?:br|p)[^>]*>/i','/<[^>]*>/','/\s+/u','/^\s+|\s+$/u' ],[' ', '', ' ', ''], html_entity_decode($post['details_'.app()->getLocale()], ENT_QUOTES, 'UTF-8')) , 200) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @empty
                    <div class="tt-page404">
                        <h3 class="tt-title">{{ __('webMessage.noresultfound') }}</h3>
                        <a href="javascript:history.go(-1);" class="btn">{{__('webMessage.goback')}}</a>
                    </div>
                @endforelse
            </div>

            <div class="row">
                <div class="col-12 mt-2 mt-md-4">
                    <div class="pagination pagination_style1 justify-content-center">
                        {!! $posts->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection