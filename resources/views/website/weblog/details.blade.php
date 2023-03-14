@extends('website.include.master')
@section('title' , $post['title_'. app()->getLocale() ] )
@section('description' , str_limit(preg_replace(['/<(?:br|p)[^>]*>/i','/<[^>]*>/','/\s+/u','/^\s+|\s+$/u' ],[' ', '', ' ', ''], html_entity_decode($post['details_'.app()->getLocale()], ENT_QUOTES, 'UTF-8')) , 400) )
@section('abstract' ,str_limit(preg_replace(['/<(?:br|p)[^>]*>/i','/<[^>]*>/','/\s+/u','/^\s+|\s+$/u' ],[' ', '', ' ', ''], html_entity_decode($post['details_'.app()->getLocale()], ENT_QUOTES, 'UTF-8')) , 400) )
@section('keywords' , str_replace(' ' , ', ' , $post['title_'. app()->getLocale() ]) )
@section('content')

<style>
    iframe{
        max-width: 100vw;
    }
    .blog_text p{
        color: black
    }
</style>
    <!-- START SECTION BLOG -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-xl-9">
                    <div class="single_post">
                        <h2 class="blog_title">{{ $post['title_'.app()->getLocale()] }}</h2>
                        <ul class="list_none blog_meta">
                            <li><a href="#"><i class="ti-calendar"></i> {{ $post->created_at->format('F d, Y') }}</a></li>
                            <li><a href="#"><i class="ti-comments"></i> {{ $post->publsihComments->count() }} {{ __('webMessage.COMMENTS') }}</a></li>
                        </ul>
                        @if(Session::get('message-success'))
                            <div class="alert  alert-success" role="alert">
                                <div class="alert-icon">
                                    <i class="flaticon-alert kt-font-brand"></i>
                                </div>
                                <div class="alert-text">
                                    {{ Session::get('message-success') }}
                                </div>
                            </div>
                        @endif

                        @if(Session::get('message-error'))
                            <div class="alert  alert-danger" role="alert">
                                <div class="alert-icon">
                                    <i class="flaticon-alert kt-font-brand"></i>
                                </div>
                                <div class="alert-text">
                                    {{ Session::get('message-error') }}
                                </div>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert  alert-danger" role="alert">
                                <div class="alert-icon">
                                    <i class="flaticon-alert kt-font-brand"></i>
                                </div>
                                <div class="alert-text">
                                    {!! implode('', $errors->all('<div>:message</div>')) !!}
                                </div>
                            </div>
                        @endif
                        @if($post->image)
                            <div class="blog_img">
                                <img src="{!! url('uploads/blog/'.$post->image) !!}" alt="{{ $post['title_'.app()->getLocale()] }}" >
                            </div>
                        @endif
                        <div class="blog_content">
                            <div class="blog_text">
                                {!! $post['details_'. app()->getLocale() ] !!}
                            </div>
                            <div class="blog_post_footer">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-8 mb-3 mb-md-0">
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="social_icons  text-md-end">
                                            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug])) }}" class="sc_facebook"><i class="ion-social-facebook"></i></a></li>
                                            <li><a href="https://twitter.com/intent/tweet?url={{  urlencode(route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug])) }}" class="sc_twitter"><i class="ion-social-twitter"></i></a></li>
                                            <li><a href="https://plus.google.com/share?url={{  urlencode(route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug])) }}" class="sc_google"><i class="ion-social-googleplus"></i></a></li>
                                            <li><a href="https://api.whatsapp.com/send?text={{  urlencode(route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug])) }}" class="sc_android"><i class="ion-social-whatsapp"></i></a></li>
                                            <li><a href="{{  route('blog.home',[$domainCountry->code , app()->getLocale() , 'p' => $post->id ]) }}" class="sc_rss"><i class="ion-link"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="comment-area">
                        <div class="content_title">
                            <h5>({{ $post->publsihComments->count() }}) {{ __('webMessage.COMMENTS') }}</h5>
                        </div>
                        <ul class="list_none comment_list">
                            @component('website.weblog.comment_componnet' , ['comments' =>  $post->publsihFirstComments , 'firstLevel' => true , 'level' => 0 ]) @endcomponent
                        </ul>
                        <div class="content_title" id="leaveAReply">
                            <h5>{{ __('webMessage.LEAVE_A_REPLY') }}</h5>
                        </div>
                        <form action="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $post->id , $post->slug]) }}" method="post">
                            @csrf
                            <input type="hidden" id="reply_To" name="reply_id" value="">
                            @if(Session::get('message-success'))
                                <div class="alert  alert-success" role="alert">
                                    <div class="alert-icon">
                                        <i class="flaticon-alert kt-font-brand"></i>
                                    </div>
                                    <div class="alert-text">
                                        {{ Session::get('message-success') }}
                                    </div>
                                </div>
                            @endif

                            @if(Session::get('message-error'))
                                <div class="alert  alert-danger" role="alert">
                                    <div class="alert-icon">
                                        <i class="flaticon-alert kt-font-brand"></i>
                                    </div>
                                    <div class="alert-text">
                                        {{ Session::get('message-error') }}
                                    </div>
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="alert  alert-danger" role="alert">
                                    <div class="alert-icon">
                                        <i class="flaticon-alert kt-font-brand"></i>
                                    </div>
                                    <div class="alert-text">
                                        {!! implode('', $errors->all('<div>:message</div>')) !!}
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" required="" name="name" value="{{ old('name' , ( auth('webs')->check() ? auth('webs')->user()->name : '' ) ) }}" class="form-control @if($errors->has('name')) is-invalid @endif" id="inputName" placeholder="{{ __('webMessage.enter_your_name') }}">
                                    @if($errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input type="email" required=""  name="email" value="{{ old('email' , ( auth('webs')->check() ? auth('webs')->user()->email : '' ) ) }}" class="form-control @if($errors->has('email')) is-invalid @endif" id="inputEmail" placeholder="{{ __('webMessage.enter_email') }}">
                                    @if($errors->has('email'))
                                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <textarea class="form-control @if($errors->has('comment')) is-invalid @endif" required=""  name="comment" id="textarea" placeholder="{{ __('webMessage.Write_a_Comment') }}" rows="8">{{ old('comment') }}</textarea>
                                    @if($errors->has('comment'))
                                        <div class="invalid-feedback">{{ $errors->first('comment') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <button value="Submit" name="submit" class="btn btn-fill-out" type="submit">{{ __('webMessage.POST_COMMENT') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-3 mt-4 pt-2 mt-xl-0 pt-xl-0">
                    <div class="sidebar">
                        <div class="widget">
                            <div class="search_form">
                                <form class="form-default" action="{{ route('blog.home',[$domainCountry->code , app()->getLocale() ] ) }}">
                                    <input required=""  @if( app()->getLocale() == "ar" ) style="padding-right: 30px;" @endif  class="form-control"  name="q" placeholder="{{ __('webMessage.search') }}" value="{{ request()->query('q') }}" type="text">
                                    <button type="submit" title="Subscribe" class="btn icon_search">
                                        <i class="ion-ios-search-strong"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if( $lastPost->count() > 0 )
                        <div class="widget">
                            <h5 class="widget_title">{{ __('webMessage.RECENT_POST') }}</h5>
                            <ul class="widget_recent_post">
                                @foreach($lastPost as $lpost)
                                <li>
                                    <div class="post_footer">
                                        @if($lpost->image)
                                        <div class="post_img">
                                            <a href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $lpost->id , $lpost->slug]) }}"><img src="{!! url('uploads/blog/thumb/'.$lpost->image) !!}" alt="{{ $lpost['title_'.app()->getLocale()] }}"></a>
                                        </div>
                                        @endif
                                        <div class="post_content">
                                            <h6><a href="{{ route('blog.show',[$domainCountry->code , app()->getLocale() ,  $lpost->id , $lpost->slug]) }}">{{ $lpost['title_'.app()->getLocale()] }}</a></h6>
                                            <p class="small m-0">{{ $lpost->created_at->format('F d, Y') }}</p>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="widget">
                            <h5 class="widget_title">{{ __('webMessage.ARCHIVE') }}</h5>
                            <div class="widget_archive">
                                <select class="select form-control" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                                    <option value="{{ route('blog.home',[$domainCountry->code , app()->getLocale() ] ) }}"></option>
                                    @foreach($archive as $oneArchive)
                                        <option value="{{ route('blog.archive',[$domainCountry->code , app()->getLocale() , $oneArchive->year , $oneArchive->month ]) }}" @if(isset($archiveYear , $archiveMonth) and $archiveMonth == $oneArchive->month and $archiveYear == $oneArchive->year) selected @endif>{{ $oneArchive->month_name }} {{ $oneArchive->year }} ({{ $oneArchive->post_count }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION BLOG -->
@endsection