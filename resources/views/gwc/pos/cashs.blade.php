@php
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}|POS Cash Log</title>
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
                            <h3 class="kt-subheader__title">POS</h3>
                            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;" class="kt-subheader__breadcrumbs-link">Cash In/OUt Log</a>

                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">
                            <!-- reset filtration button -->
                            @if(Request()->input('filter_dates') or Request()->input('pos_id') or Request()->input('type') or Request()->input('q'))
                                <a href="{{Request()->url()}}" type="button"
                                   class="btn btn-danger btn-bold mx-2">{{__('adminMessage.reset')}}</a>
                        @endif

                        <!-- filter date -->
                            <form class="" id="kt_subheader_search_form" method="get">
                                @if(Request()->input('q'))
                                    <input value="{{Request()->q}}" type="hidden" name="q">
                                @endif
                                @if(Request()->input('type'))
                                    <input value="{{Request()->type}}" type="hidden" name="type">
                                @endif
                                @if(Request()->input('pos_id'))
                                    <input value="{{Request()->pos_id}}" type="hidden" name="pos_id">
                                @endif
                                <div class="kt-subheader__wrapper mx-2">
                                    <div class="kt-input-icon kt-input-icon--right kt-subheader__search"
                                         style="width: fit-content">
                                        <input autocomplete="off" type="text" class="form-control" name="filter_dates"
                                               id="kt_daterangepicker_range" placeholder="Select Date Range"
                                               value="@if(Request()->input('filter_dates')){{Request()->input('filter_dates')}}@endif">
                                        <button type="submit" style="border:0;"
                                                class="kt-input-icon__icon kt-input-icon__icon--right">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                          fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                          fill="#000000" fill-rule="nonzero"/>
                                </g>
                            </svg>
                        </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- filter date -->
                            <form class="" id="searchPosAdmin" method="get">
                                @if(Request()->input('q'))
                                    <input value="{{Request()->q}}" type="hidden" name="q">
                                @endif
                                @if(Request()->input('type'))
                                    <input value="{{Request()->type}}" type="hidden" name="type">
                                @endif
                                @if(Request()->input('filter_dates'))
                                    <input value="{{Request()->filter_dates}}" type="hidden" name="filter_dates">
                                @endif
                                <div class="kt-subheader__wrapper mx-2">
                                    <div class="kt-input-icon kt-input-icon--right kt-subheader__search"
                                         style="width: fit-content">
                                        <select name="pos_id" onchange="$('#searchPosAdmin').submit();" class="form-control">
                                            <option value="">All Pos User</option>
                                            @forelse($poss as $pos)
                                                <option value="{{$pos->id}}" @if ( Request()->input('pos_id') == $pos->id ) selected @endif >{{ $pos->username }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <!-- order status -->
                            <div class="btn-group mx-2">
                                <button type="button"
                                        class="btn btn-primary btn-bold dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    @if(Request()->input('type')){{strtoupper(Request()->input('type'))}}@else{{strtoupper(__('adminMessage.all'))}}@endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['type' => 'all'])}}"
                                                    class="kt-nav__link" id="all">{{__('adminMessage.all')}}</a></li>
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['type' => 'in'])}}"
                                                    class="kt-nav__link text-success" id="in"><i class="fa fa-download mr-1"></i> IN</a></li>
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['type' => 'out'])}}"
                                                    class="kt-nav__link text-danger" id="out"><i class="fa fa-upload mr-1"></i> OUT</a></li>
                                    </ul>
                                </div>
                            </div>
                            <form class="kt-margin-l-20" id="kt_subheader_search_form" method="get"
                                  action="{{request()->fullUrlWithQuery([])}}">
                                <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                                    <input value="@if(Request()->q){{Request()->q}}@endif" type="text"
                                           class="form-control" placeholder="{{__('adminMessage.searchhere')}}"
                                           id="searchCat" name="q">
                                    @if(Request()->input('type'))
                                        <input value="{{Request()->type}}" type="hidden" name="type">
                                    @endif
                                    @if(Request()->input('filter_dates'))
                                        <input value="{{Request()->filter_dates}}" type="hidden" name="filter_dates">
                                    @endif
                                    @if(Request()->input('pos_id'))
                                        <input value="{{Request()->pos_id}}" type="hidden" name="pos_id">
                                    @endif
                                    <button type="submit" style="border:0;"
                                            class="kt-input-icon__icon kt-input-icon__icon--right">
													<span>
														<svg xmlns="http://www.w3.org/2000/svg"
                                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                             height="24px" viewBox="0 0 24 24" version="1.1"
                                                             class="kt-svg-icon">
															<g stroke="none" stroke-width="1" fill="none"
                                                               fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24"/>
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                                                      fill="#000000" fill-rule="nonzero" opacity="0.3"/>
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                                                      fill="#000000" fill-rule="nonzero"/>
															</g>
														</svg>

                                                        <!--<i class="flaticon2-search-1"></i>-->
													</span>
                                    </button>
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
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
										<span class="kt-portlet__head-icon">
											<i class="kt-font-brand flaticon2-line-chart"></i>
										</span>
                                <h3 class="kt-portlet__head-title">
                                    Cash In/OUt Log
                                </h3>
                            </div>
                        </div>

                        <div class="kt-portlet__body">
                        @if(auth()->guard('admin')->user()->can('pos-cash-list'))
                            <!--begin: Datatable -->
                                <table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
                                    <thead>
                                    <tr>
                                        <th width="10">#</th>
                                        <th>{{__('adminMessage.username')}}</th>
                                        <th>{{__('adminMessage.amount')}}</th>
                                        <th>{{__('adminMessage.details')}}</th>
                                        <th>Before Cash</th>
                                        <th>After Cash</th>
                                        <th>Date/Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($cashs))
                                        @foreach($cashs as $cash)

                                            <tr class="search-body">
                                                <td>{{$cash->id}}</td>
                                                <td>{{$cash->pos->username}}</td>
                                                <td>
                                                    @if ( $cash->type == "in")
                                                        <span class=" font-weight-bold text-success">
                                                            <i class="fa fa-download mr-1"></i>
                                                    @else
                                                        <span class=" font-weight-bold text-danger">
                                                            <i class="fa fa-upload mr-1"></i>
                                                    @endif
                                                    {{ number_format(round($cash->amount,2),2) }} {{ \App\Currency::default() }}

                                                        </span>
                                                </td>
                                                <td>@if( \Illuminate\Support\Str::startsWith($cash->description , 'Start Cash for')) <strong>{{$cash->description}}</strong> @else {{$cash->description}} @endif</td>
                                                <td>
                                                    {{ number_format(round($cash->beforeCash,2),2) }} {{ \App\Currency::default() }}
                                                </td>
                                                <td>
                                                    {{ number_format(round($cash->afterCash,2),2) }} {{ \App\Currency::default() }}
                                                </td>
                                                <td>{{$cash->created_at}}</td>
                                            </tr>
                                        @endforeach
                                        <tr><td colspan="6" class="text-center">{{ $cashs->links() }}</td></tr>
                                    @else
                                        <tr><td colspan="6" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                    @endif
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-light alert-warning" role="alert">
                                    <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                    <div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
                                </div>
                        @endif
                        <!--end: Datatable -->
                        </div>
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

    $(function() {
        $('input[id="kt_daterangepicker_range"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
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
</script>
</body>
<!-- end::Body -->
</html>