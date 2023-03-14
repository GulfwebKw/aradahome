@php
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}|POS End Of Day Log</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
@include('gwc.css.user')

    <style>
        .table thead th {
            vertical-align: inherit !important;
        }
        .tab-slider--nav {
            width: 100%;
            float: left;
            margin-bottom: 20px;
        }
        .tab-slider--tabs {
            display: block;
            float: left;
            margin: 0;
            padding: 0;
            list-style: none;
            position: relative;
            border-radius: 35px;
            overflow: hidden;
            background: #fff;
            height: 35px;
            user-select: none;
        }
        .tab-slider--tabs:after {
            content: "";
            width: 140px;
            background: #5d78ff;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            transition: all 250ms ease-in-out;
            border-radius: 35px;
        }
        .tab-slider--tabs.slide:after {
            left: 140px;
        }
        .tab-slider--trigger {
            font-size: 12px;
            line-height: 1;
            width: 140px;
            font-weight: bold;
            color: #5d78ff;
            text-transform: uppercase;
            text-align: center;
            padding: 11px 20px;
            position: relative;
            z-index: 2;
            cursor: pointer;
            display: inline-block;
            transition: color 250ms ease-in-out;
            user-select: none;
        }
        .tab-slider--trigger.active {
            color: #fff;
        }
        .tab-slider--body {
            margin-bottom: 20px;
        }
        .table .bol {
            border-left-width: revert !important;
            border-left-style: solid !important;
            border-left-color: #000000 !important;
        }
        .table .bot {
            border-top-width: revert !important;
            border-top-style: solid !important;
            border-top-color: #000000 !important;
        }
        .table-detail-pricing tr:hover{
            background-color: transparent !important;
        }
    </style>
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
                                <a href="javascript:;" class="kt-subheader__breadcrumbs-link">End Of Day Log</a>

                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">
                            <!-- reset filtration button -->
                            @if(Request()->input('filter_dates') or Request()->input('pos_id') or Request()->input('contradictionCashOfSystem') )
                                <a href="{{Request()->url()}}" type="button"
                                   class="btn btn-danger btn-bold mx-2">{{__('adminMessage.reset')}}</a>
                        @endif

                        <!-- filter date -->
                            <form class="" id="kt_subheader_search_form" method="get">
                                @if(Request()->input('pos_id'))
                                    <input value="{{Request()->pos_id}}" type="hidden" name="pos_id">
                                @endif
                                @if(Request()->input('contradictionCashOfSystem'))
                                    <input value="{{Request()->contradictionCashOfSystem}}" type="hidden" name="contradictionCashOfSystem">
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


                            <!-- order status -->
                            <div class="btn-group mx-2">
                                <button type="button"
                                        class="btn btn-primary btn-bold dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    @if(Request()->input('contradictionCashOfSystem'))
                                        @if( Request()->input('contradictionCashOfSystem') == "=" )
                                            zero
                                        @elseif( Request()->input('contradictionCashOfSystem') == ">" )
                                            More than zero
                                        @elseif( Request()->input('contradictionCashOfSystem') == "<" )
                                            Less than zero
                                        @else
                                            All
                                        @endif
                                    @else{{strtoupper(__('adminMessage.all'))}}@endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['contradictionCashOfSystem' => 'all'])}}"
                                                    class="kt-nav__link" id="all">{{__('adminMessage.all')}}</a></li>
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['contradictionCashOfSystem' => '>'])}}"
                                                    class="kt-nav__link text-warning"><i class="fa fa-question-circle mr-1"></i> More than zero </a></li>
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['contradictionCashOfSystem' => '='])}}"
                                                    class="kt-nav__link text-success"><i class="fa fa-check-circle mr-1"></i> zero</a></li>
                                        <li class="kt-nav__item"><a
                                                    href="{{request()->fullUrlWithQuery(['contradictionCashOfSystem' => '<'])}}"
                                                    class="kt-nav__link text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Less than zero</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- filter date -->
                            <form class="" id="searchPosAdmin" method="get">
                                @if(Request()->input('filter_dates'))
                                    <input value="{{Request()->filter_dates}}" type="hidden" name="filter_dates">
                                @endif
                                @if(Request()->input('contradictionCashOfSystem'))
                                    <input value="{{Request()->contradictionCashOfSystem}}" type="hidden" name="contradictionCashOfSystem">
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
                                    End Of Day Log
                                </h3>
                            </div>
                        </div>

                        <div class="kt-portlet__body">
                        @if(auth()->guard('admin')->user()->can('pos-days-list'))
                            <!--begin: Datatable -->
                                <table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
                                    <thead>
                                    <tr>
                                        <th width="10" rowspan="2">#</th>
                                        <th rowspan="2">{{__('adminMessage.username')}}</th>
                                        <th rowspan="2">Start Shift</th>
                                        <th rowspan="2">End Shift</th>
{{--                                        <th colspan="3"  class="text-center">Contradiction</th>--}}
                                        <th colspan="2"  class="text-center">Contradiction</th>
                                        <th rowspan="2">Total Founds</th>
                                        <th rowspan="2">Total Earn</th>
                                        <th rowspan="2">Total Refund</th>
                                    </tr>
                                    <tr>
{{--                                        <th>System Cash</th>--}}
                                        <th>Count Cash</th>
                                        <th>Count Card</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($days))
                                        @foreach($days as $day)

                                            <tr class="search-body">
                                                <td>
                                                    {{$day->id}}

                                                    <span onclick="$('.moreDetails_{{ $day->id }}').toggle();$('.moreDetails_{{ $day->id }}_angel').toggle();" style="cursor: pointer;">
                                                        <i class="fa fa-2x text-primary fa-angle-down moreDetails_{{ $day->id }}_angel"></i>
                                                        <i class="fa fa-2x text-primary fa-angle-up moreDetails_{{ $day->id }}_angel" style="display: none;"></i>
                                                    </span>
                                                </td>
                                                <td>{{$day->pos->username}}</td>
                                                <td>{{$day->start}}</td>
                                                <td>{{$day->ended ?? "Working!"}}</td>
{{--                                                <td>--}}
{{--                                                    @if ( $day->contradictionCashOfSystem < 0 )--}}
{{--                                                        <span class=" font-weight-bold text-danger">--}}
{{--                                                            <i class="fa fa-exclamation-triangle mr-1"></i>--}}
{{--                                                    @elseif ( $day->contradictionCashOfSystem == 0 )--}}
{{--                                                        <span class=" font-weight-bold text-success">--}}
{{--                                                            <i class="fa fa-check-circle mr-1"></i>--}}
{{--                                                    @else--}}
{{--                                                        <span class=" font-weight-bold text-warning">--}}
{{--                                                            <i class="fa fa-question-circle mr-1"></i>--}}
{{--                                                    @endif--}}
{{--                                                    {{ number_format(round($day->contradictionCashOfSystem,2),2) }} {{ \App\Currency::default() }}--}}

{{--                                                        </span>--}}
{{--                                                </td>--}}
                                                <td>
                                                    <span style="cursor: pointer"  title="Count: {{ number_format( round($day->countCash , 2 ), 2 ) }} {{ \App\Currency::default() }}&#010;System: {{ number_format( $day->lastCashRelation  == null ? 0 : round($day->lastCashRelation->afterCash , 2 ), 2 ) }} {{ \App\Currency::default() }}"
                                                        @if ( $day->contradictionCountCash < 0 )
                                                        class=" font-weight-bold text-danger">
                                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                                    @elseif ( $day->contradictionCountCash == 0 )
                                                        class=" font-weight-bold text-success">
                                                            <i class="fa fa-check-circle mr-1"></i>
                                                    @else
                                                        class=" font-weight-bold text-warning" >
                                                            <i class="fa fa-question-circle mr-1"></i>
                                                    @endif
                                                    {{ number_format(round($day->contradictionCountCash,2),2) }} {{ \App\Currency::default() }}

                                                        </span>
                                                </td>
                                                <td>
                                                    <span style="cursor: pointer"  title="Count: {{ number_format( round($day->countCard , 2 ), 2 ) }} {{ \App\Currency::default() }}&#010;System: {{ number_format( round($day->cardPay - $day->cardRefund , 2 ), 2 ) }} {{ \App\Currency::default() }}"
                                                    @if ( $day->contradictionCountCard < 0 )
                                                        class=" font-weight-bold text-danger">
                                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                                    @elseif ( $day->contradictionCountCard == 0 )
                                                        class=" font-weight-bold text-success">
                                                            <i class="fa fa-check-circle mr-1"></i>
                                                    @else
                                                        class=" font-weight-bold text-warning">
                                                            <i class="fa fa-question-circle mr-1"></i>
                                                    @endif
                                                    {{ number_format(round($day->contradictionCountCard,2),2) }} {{ \App\Currency::default() }}

                                                        </span>
                                                </td>
                                                <td>
                                                    @php
                                                        if ( $day->lastCashRelation  == null )
                                                             $TotalFound = 0 ;
                                                        else
                                                            $TotalFound = round($day->lastCashRelation->afterCash,2) + round($day->cardPay , 2 ) - round($day->cardRefund,2) ;

                                                    @endphp
                                                    @if ( $TotalFound < 0  )
                                                                <span class="font-weight-bold text-danger">
                                                    @else
                                                                        <span>
                                                    @endif
                                                    {{ number_format( $TotalFound  ,2) }} {{ \App\Currency::default() }}
                                                    </span>
                                                </td>
                                                 <td>
                                                     {{ number_format(round($day->totalSell,2) - round($day->totalRefund,2),2) }} {{ \App\Currency::default() }}
                                                </td>
                                                <td>
                                                    @if ( $day->totalRefund > 0 and $day->totalRefund >= $day->totalSell / 2 and $day->totalRefund < $day->totalSell )
                                                        <span class="font-weight-bold text-warning">
                                                    @elseif ( $day->totalRefund > 0 and $day->totalRefund >= $day->totalSell )
                                                        <span class="font-weight-bold text-danger">
                                                    @else
                                                        <span>
                                                    @endif
                                                    {{ number_format(round($day->totalRefund,2),2) }} {{ \App\Currency::default() }}
                                                        </span>
                                                </td>
                                            </tr>
                                            <tr class="moreDetails moreDetails_{{ $day->id }} bg-secondary shadow" style="display: none;">
                                                <td colspan="9">
                                                    <div class="row mt-1 mr-1 ml-1">
                                                        <div class="tab-slider--nav">
                                                            <ul class="tab-slider--tabs">
                                                                <li class="tab-slider--trigger active" rel="tab_Details_{{ $day->id }}" tabNo="1">Details</li>
                                                                <li class="tab-slider--trigger" rel="tab_table_{{ $day->id }}" tabNo="2">Pricing table</li>
                                                            </ul>
                                                        </div>
                                                        <div style="display: inline-block;" class="w-100">
                                                            <div id="tab_Details_{{ $day->id }}" class="tab-slider--body">
                                                                <div class="row mt-1 mr-1 ml-1">
                                                                    <div class="col-md-4">
                                                                        <div class="row">
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Sell Cash:</strong> {{ number_format(round($day->cashPay,2),2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Sell Card:</strong> {{ number_format(round($day->cardPay,2),2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Refund Cash:</strong> {{ number_format(round($day->cashRefund,2),2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Refund Card:</strong> {{ number_format(round($day->cardRefund,2),2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="row">
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Start Cash:</strong> {{ number_format(round($day->startCash,2),2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                            @php
                                                                                if ( $day->lastCashRelation  != null )
                                                                                    $cashIn = round($day->lastCashRelation->afterCash,2) - round($day->startCash , 2 ) - round($day->cashPay,2) + round($day->cashRefund,2) ;
                                                                                else
                                                                                    $cashIn = 0 ;
                                                                            @endphp
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>Cash In/Out:</strong>
                                                                                {{ number_format($cashIn,2) }} {{ \App\Currency::default() }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="row">
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>No. Register Customers:</strong> {{ number_format($day->customers) }}
                                                                            </div>
                                                                            <div class="col-md-12 mb-1">
                                                                                <strong>No. All Customers:</strong> {{ number_format($day->sell) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tab_table_{{ $day->id }}" class="tab-slider--body w-100" style="display: none;">
                                                                <div class="mt-1 mr-1 ml-1">
                                                                    <table class="table table-detail-pricing text-center table-striped- table-checkable w-100">
                                                                        <thead class="border-0">
                                                                        <tr class="border-0">
                                                                            <th class="border-0"></th>
                                                                            <th class="border-0">Sell</th>
                                                                            <th class="border-0">Refund</th>
                                                                            <th class="border-0">Start</th>
                                                                            <th class="border-0">In/Out</th>
                                                                            <th class="border-0 font-weight-bold bol">Total</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody class="border-0">
                                                                        <tr class="border-0">
                                                                            <td class="border-0" style="font-weight: 500;">Cash</td>
                                                                            <td class="border-0"><span title="Sell Cash" style="cursor: pointer">{{ number_format(round($day->cashPay,2),2) }}</span></td>
                                                                            <td class="border-0"><span title="Refund Cash" style="cursor: pointer">@if(round($day->cashRefund,2) > 0) -@endif{{ number_format(round($day->cashRefund,2),2) }}</span></td>
                                                                            <td class="border-0"><span title="Start Cash" style="cursor: pointer">{{ number_format(round($day->startCash,2),2) }}</span></td>
                                                                            <td class="border-0 @if($cashIn < 0 ) text-danger @endif "><span title="Cash In/Out" style="cursor: pointer">{{ number_format($cashIn,2) }}</span></td>
                                                                            <th class="border-0 font-weight-bold bol @if($day->lastCashRelation  != null and $day->lastCashRelation->afterCash < 0 ) text-danger @endif "><span title="Total Cash" style="cursor: pointer">{{ number_format( $day->lastCashRelation  != null ? $day->lastCashRelation->afterCash : 0 ,2) }} {{ \App\Currency::default() }}</span></th>
                                                                        </tr>
                                                                        <tr class="border-0">
                                                                            <td class="border-0" style="font-weight: 500;">Card</td>
                                                                            <td class="border-0"><span title="Sell Card" style="cursor: pointer">{{ number_format(round($day->cardPay,2),2) }}</span></td>
                                                                            <td class="border-0"><span title="Refund Card" style="cursor: pointer">@if(round($day->cardRefund,2) > 0) -@endif{{ number_format(round($day->cardRefund,2),2) }}</span></td>
                                                                            <td class="border-0"><span title="Start Card" style="cursor: pointer">0</span></td>
                                                                            <td class="border-0"><span title="Card In/Out" style="cursor: pointer">0</span></td>
                                                                            <th class="border-0 font-weight-bold bol @if($day->cardPay - $day->cardRefund < 0 ) text-danger @endif "><span title="Total Card" style="cursor: pointer">{{ number_format($day->cardPay - $day->cardRefund ,2) }} {{ \App\Currency::default() }}</span></th>
                                                                        </tr>
                                                                        </tbody>
                                                                        <tfoot class="border-0">
                                                                        <tr class="border-0">
                                                                            <th rowspan="2" class="border-0 font-weight-bold bot" style="vertical-align: inherit !important;">Total</th>
                                                                            <td class="border-0 font-weight-bold bot @if(round($day->cardPay + $day->cashPay,2) < 0 ) text-danger @endif "><span title="Total Sell" style="cursor: pointer">{{ number_format(round($day->cardPay + $day->cashPay,2),2) }}</span></td>
                                                                            <td class="border-0 font-weight-bold bot @if(round($day->cardRefund + $day->cashRefund ,2) < 0 ) text-danger @endif "><span title="Total Refund" style="cursor: pointer">@if(round($day->cardRefund + $day->cashRefund ,2) > 0) -@endif{{ number_format(round($day->cardRefund + $day->cashRefund,2),2) }}</span></td>
                                                                            <td rowspan="2" class="border-0 font-weight-bold bot @if(round($day->startCash,2) < 0 ) text-danger @endif " style="vertical-align: inherit !important;"><span title="Total Start Cash/Card" style="cursor: pointer">{{ number_format(round($day->startCash,2),2) }}</span></td>
                                                                            <td rowspan="2" class="border-0 font-weight-bold bot @if($cashIn < 0 ) text-danger @endif " style="vertical-align: inherit !important;"><span title="Total Cash/Card In/Out" style="cursor: pointer">{{ number_format($cashIn,2) }}</span></td>
                                                                            <th rowspan="2" class="border-0 font-weight-bold bol bot @if($TotalFound < 0 ) text-danger @endif " style="vertical-align: inherit !important;"><span title="Total Found" style="cursor: pointer">{{ number_format( $TotalFound  ,2) }} {{ \App\Currency::default() }}</span></th>
                                                                        </tr>
                                                                        <tr class="border-0">
                                                                            <td colspan="2" class="border-0 font-weight-bold bot @if(round($day->totalSell,2) - round($day->totalRefund,2) < 0 ) text-danger @endif "><span title="Total Earn" style="cursor: pointer">Earn: {{ number_format(round($day->totalSell,2) - round($day->totalRefund,2),2) }}</span></td>
                                                                        </tr>
                                                                        </tfoot>
                                                                    </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1 mr-1 ml-1">
                                                        <div class="col-md-4 mt-1 mb-1">
                                                            <a href="/gwc/pos/cash?shift_id={{ $day->id }}" class="btn btn-primary">List all cash log</a>
                                                        </div>
                                                        <div class="col-md-4 mt-1 mb-1">
                                                            <a href="/gwc/pos/transactions/{{ $day->start }}/{{ $day->ended  ?? '3022-01-01%2001:01:01'}}/{{ $day->pos_id }}" class="btn btn-primary">List all transactions</a>
                                                        </div>
                                                        <div class="col-md-4 mt-1 mb-1">
                                                            <a href="/gwc/pos/orders/{{ $day->start }}/{{ $day->ended ?? '3022-01-01%2001:01:01' }}/{{ $day->pos_id }}" class="btn btn-primary">List all orders</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr><td colspan="8" class="text-center">{{ $days->links() }}</td></tr>
                                    @else
                                        <tr><td colspan="8" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
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
    $(".tab-slider--nav li").click(function() {
        $(this).parent().parent().parent().find(".tab-slider--body").hide();
        var activeTab = $(this).attr("rel");
        $("#"+activeTab).fadeIn();
        if($(this).attr("tabNo") === "2"){
            $(this).parent().parent().find('.tab-slider--tabs').addClass('slide');
        }else{
            $(this).parent().parent().find('.tab-slider--tabs').removeClass('slide');
        }
        $(this).parent().find("li").removeClass("active");
        $(this).addClass("active");
    });

</script>
</body>
<!-- end::Body -->
</html>