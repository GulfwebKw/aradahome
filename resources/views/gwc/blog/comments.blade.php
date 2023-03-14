@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>

    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}| Blog Comments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
@include('gwc.css.user')

<!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if(!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        @php
            $settingDetailsMenu = App\Http\Controllers\AdminDashboardController::getSettingsDetails();
        @endphp
        <a href="{{url('/gwc/home')}}">
            @if($settingDetailsMenu['logo'])
                <img alt="{{__('adminMessage.websiteName')}}" src="{!! url('uploads/logo/'.$settingDetailsMenu['logo']) !!}" height="40" />
            @endif
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>

        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
</div>

<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

        <!-- begin:: Aside -->
    @include('gwc.includes.leftmenu')

    <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

            <!-- begin:: Header -->
        @include('gwc.includes.header')


        <!-- end:: Header -->
            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                <!-- begin:: Subheader -->
                <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                    <div class="kt-container  kt-container--fluid ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title">Blog Comments</h3>
                            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;" class="kt-subheader__breadcrumbs-link">List of Comments</a>

                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">
                            <form class="kt-margin-l-20" id="kt_subheader_search_form">
                                <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                                    <input type="text" class="form-control" placeholder="{{__('adminMessage.searchhere')}}" id="searchCat" value="{{request()->q}}" name="q">
                                    <span class="kt-input-icon__icon kt-input-icon__icon--right">
													<span>
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
															</g>
														</svg>

                                                        <!--<i class="flaticon2-search-1"></i>-->
													</span>
												</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- end:: Subheader -->

                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                    @include('gwc.includes.alert')
                    <div class="kt-portlet kt-portlet--mobile">

                        <!--begin: Datatable -->
                            <table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
                                <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th>Writer</th>
                                    <th>{{__('adminMessage.status')}}</th>
                                    <th>Comment</th>
                                    <th>Post</th>
                                    <th>At</th>
                                    <th>{{__('adminMessage.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($comments))
                                    @foreach($comments as $comment)
                                        <tr class="search-body @if($comment->status == "waiting") alert-warning @elseif($comment->status == "reject") alert-dark @endif">
                                            <td>{{$comment->id}}</td>

                                            <td>@if($comment->writer) <a href="/gwc/customers/{{ $comment->writer->id }}/view">{{ $comment->writer->name }}</a> @else {{ $comment->name }} @endif</td>
                                            <td>{{$comment->status}}</td>
                                            <td>{{$comment->comment}}</td>
                                            <td>
                                                <a href="{{ route('blog.show' , ['kw', 'en',  $comment->post->id , $comment->post->slug]) }}"> {{$comment->post->title_en }}</a>
                                            </td>
                                            <td>{{$comment->created_at}}</td>
                                            <td>
                                                <select name="status"  id="{{ $comment->id }}" class="change_status_blog form-control">
                                                    <option value="waiting"  @if($comment->status == "waiting") selected @endif >waiting</option>
                                                    <option value="published"  @if($comment->status == "published") selected @endif >published</option>
                                                    <option value="reject"  @if($comment->status == "reject") selected @endif >reject</option>
                                                </select>
                                            </td>
                                        </tr>

                                    @endforeach
                                    <tr><td colspan="8" class="text-center">{{ $comments->links() }}</td></tr>
                                @else
                                    <tr><td colspan="8" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                @endif
                                </tbody>
                            </table>
                    <!--end: Datatable -->

                    </div>
                </div>

                <!-- end:: Content -->
            </div>

            <!-- begin:: Footer -->
            @include('gwc.includes.footer');

            <!-- end:: Footer -->
        </div>
    </div>
</div>

<!-- end:: Page -->

<!-- begin::Quick Panel -->


<!-- end::Quick Panel -->

<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>

<!-- end::Scrolltop -->

<!-- js files -->
@include('gwc.js.user')
<!-- BEGIN PAGE LEVEL PLUGINS -->


<script type="text/javascript">
    $(document).ready(function(){
        $('#searchCat').keyup(function(){
            // Search text
            var text = $(this).val();
            // Hide all content class element
            $('.search-body').hide();
            // Search
            $('.search-body').each(function(){

                if($(this).text().indexOf(""+text+"") != -1 ){
                    $(this).closest('.search-body').show();

                }
            });

        });
    });
    
    $(".change_status_blog").change(function () {
        var keys = $(this).attr("id");
        var id = $(this).val();
        $.ajax({
            type: "GET",
            url:  "/gwc/blog/comment/" + keys,
            data: "status=" + id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                var notify = $.notify({message: msg.message});
                notify.update('type', 'success');
                //notification end
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    
</script>
</body>
<!-- end::Body -->
</html>