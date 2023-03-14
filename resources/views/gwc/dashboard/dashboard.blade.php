@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>
    <meta charset="utf-8" />
    <title>{{ __('adminMessage.websiteName') }}|{{ __('adminMessage.dashboard') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
    @include('gwc.css.dashboard')
    <!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed @if (!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif
    kt-page--loading">

    <!-- begin:: Page -->

    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <div class="kt-header-mobile__logo">
            @php
                $settingDetailsMenu = App\Http\Controllers\AdminDashboardController::getSettingsDetails();
            @endphp
            <a href="{{ url('/gwc/home') }}">
                @if ($settingDetailsMenu['logo'])
                    <img alt="{{ __('adminMessage.websiteName') }}" src="{!! url('uploads/logo/' . $settingDetailsMenu['logo']) !!}" height="40" />
                @endif
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left"
                id="kt_aside_mobile_toggler"><span></span></button>

            <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more"></i></button>
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

                @php
                    $gaAccesstoken = App\Http\Controllers\AdminDashboardController::gareport();
                @endphp
                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                    @if (auth()->guard('admin')->user()->can('dashboard-view'))
                        <!--Begin::Dashboard 6-->
                        <div class="kt-container ">
                            <div class="row">
                                @if (!empty($categoryStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/category') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-slow">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.categories') }}
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $categoryStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $categoryStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $categoryStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $categoryStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if (!empty($productsStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/product') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-slow">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.products') }}
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $productsStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $productsStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $productsStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $productsStats['month'] }}</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($cutomersStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/customers') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.customers') }}
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $cutomersStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $cutomersStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $cutomersStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $cutomersStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($contactStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/contactus/inbox') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.contactinbox') }}
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $contactStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $contactStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $contactStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $contactStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($OrdersStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/orders') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.orders') }}({{ trans('adminMessage.all') }})
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $OrdersStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $OrdersStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $OrdersStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $OrdersStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($SoldOutStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/mostsold') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.orders') }}({{ trans('adminMessage.completed') }})
                                                        </h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $SoldOutStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $SoldOutStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $SoldOutStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $SoldOutStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($paymentStats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/orders?pmode=KNET') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.payments') }}(Online)</h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $paymentStats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $paymentStats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $paymentStats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $paymentStats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @php
                                    $paymentslist = explode(',', $settings->payments);
                                @endphp
                                @if (in_array('POSTKNET', $paymentslist))
                                    @if (!empty($poststats))
                                        <div class="col-lg-4">
                                            <a href="{{ url('gwc/orders?pmode=POSTKNET') }}"
                                                class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                                <div class="kt-portlet__body">
                                                    <div class="kt-iconbox__body">

                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                {{ __('adminMessage.payments') }}(By Link)</h3>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    {{ __('adminMessage.total') }}<span
                                                                        class="badge badge-success float-right"
                                                                        style="width:50px;">{{ $poststats['total'] }}</span>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    {{ __('adminMessage.today') }}<span
                                                                        class="badge badge-info float-right"
                                                                        style="width:50px;">{{ $poststats['today'] }}</span>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    {{ __('adminMessage.lastweek') }}<span
                                                                        class="badge badge-warning float-right"
                                                                        style="width:50px;">{{ $poststats['week'] }}</span>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    {{ __('adminMessage.lastthritydays') }}<span
                                                                        class="badge badge-danger float-right"
                                                                        style="width:50px;">{{ $poststats['month'] }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endif
                                @if (!empty($codstats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/orders?pmode=COD') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.payments') }}(Offline)</h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $codstats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $codstats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $codstats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $codstats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if (!empty($shipmenttats))
                                    <div class="col-lg-4">
                                        <a href="{{ url('gwc/orders?pmode=COD') }}"
                                            class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">

                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            {{ __('adminMessage.delivery_fee_dashboard') }}</h3>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.total') }}<span
                                                                    class="badge badge-success float-right"
                                                                    style="width:50px;">{{ $shipmenttats['total'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.today') }}<span
                                                                    class="badge badge-info float-right"
                                                                    style="width:50px;">{{ $shipmenttats['today'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastweek') }}<span
                                                                    class="badge badge-warning float-right"
                                                                    style="width:50px;">{{ $shipmenttats['week'] }}</span>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                {{ __('adminMessage.lastthritydays') }}<span
                                                                    class="badge badge-danger float-right"
                                                                    style="width:50px;">{{ $shipmenttats['month'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                <div class="col-lg-4">
                                    <a href="{{ url('gwc/orders?pmode=COD_KNET') }}"
                                        class="kt-portlet kt-iconbox kt-iconbox--animate-fast">
                                        <div class="kt-portlet__body">
                                            <div class="kt-iconbox__body">
                                                <div class="kt-iconbox__desc">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <h3 class="kt-iconbox__title">
                                                                {{ __('adminMessage.completed_order') }}</h3><span
                                                                class="badge badge-success"
                                                                style="width:100%;">{{ $SoldOutStats['total'] }}</span>
                                                        </div>
                                                        <div class="col-lg-12 mt-3">
                                                            <h3 class="kt-iconbox__title">
                                                                {{ __('adminMessage.amount_collected') }}</h3><span
                                                                class="badge badge-info"
                                                                style="width:100%;">{{ number_format($paymentStats['total'] + $codstats['total'] + $poststats['total'], 3) }}
                                                                {{\App\Currency::default()}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @if (!empty($profitstats))
                                    <div class="col-lg-8 ">
                                        <div class="kt-portlet kt-iconbox">
                                            <table
                                                class="table table-striped- table-bordered table-hover table-checkable "
                                                id="kt_table_1">
                                                <thead>
                                                    <tr>
                                                        <th><span>{{ trans('adminMessage.details') }}</span></th>
                                                        <th width="100">{{ trans('adminMessage.cost_price') }}</th>
                                                        <th width="100">{{ trans('adminMessage.sale_price') }}</th>
                                                        <th width="100">{{ trans('adminMessage.profit') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('adminMessage.total') }}</td>
                                                        <td>{{ number_format($profitstats['total']['costPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['total']['retailPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['total']['profitPrice'], 3) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('adminMessage.today') }}</td>
                                                        <td>{{ number_format($profitstats['today']['costPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['today']['retailPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['today']['profitPrice'], 3) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('adminMessage.lastweek') }}</td>
                                                        <td>{{ number_format($profitstats['week']['costPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['week']['retailPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['week']['profitPrice'], 3) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('adminMessage.lastthritydays') }}</td>
                                                        <td>{{ number_format($profitstats['month']['costPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['month']['retailPrice'], 3) }}
                                                        </td>
                                                        <td>{{ number_format($profitstats['month']['profitPrice'], 3) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <!--sales state-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <!--begin:: Complwted orders -->
                                    <div class="kt-portlet kt-portlet--head--noborder kt-portlet--height-fluid">
                                        <div class="kt-portlet__head kt-portlet__head--noborder">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    {{ __('adminMessage.yearlysalesreports') }}
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <!--begin::Widget 6-->
                                            <div class="kt-widget15">
                                                <p>
                                                <h5 class="kt-widget15__text">{{ __('adminMessage.salesreports') }}
                                                </h5>
                                                <div class="kt-widget15__chart">
                                                    <canvas id="kt_chart_sales_stats_wb" style="height:160px;"></canvas>
                                                </div>
                                                </p>
                                                <p>
                                                <h5 class="kt-widget15__text">{{ __('adminMessage.orderreports') }}
                                                </h5>
                                                <div class="kt-widget15__chart">
                                                    <canvas id="kt_chart_sales_stats_wb_order"
                                                        style="height:160px;"></canvas>
                                                </div>
                                                </p>
                                                @php
                                                    $salesgrow = App\Http\Controllers\AdminDashboardController::thisMonthGrow();
                                                    $ordersgrow = App\Http\Controllers\AdminDashboardController::thisMonthOrderGrow();
                                                @endphp
                                                <div class="kt-widget15__items kt-margin-t-40">
                                                    <h5>
                                                        {{ __('adminMessage.monthlygrow') }}
                                                    </h5>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="kt-widget15__item">
                                                                <span class="kt-widget15__stats">
                                                                    {{ round($salesgrow, 1) }}%
                                                                </span>
                                                                <span class="kt-widget15__text">
                                                                    {{ __('adminMessage.salesgrow') }}
                                                                </span>
                                                                <div class="kt-space-10"></div>
                                                                <div class="progress kt-widget15__chart-progress--sm">
                                                                    <div class="progress-bar bg-success"
                                                                        role="progressbar"
                                                                        style="width:{{ round($salesgrow, 1) }}%;"
                                                                        aria-valuenow="{{ round($salesgrow, 1) }}"
                                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="kt-widget15__item">
                                                                <span class="kt-widget15__stats">
                                                                    {{ round($ordersgrow, 1) }}%
                                                                </span>
                                                                <span class="kt-widget15__text">
                                                                    {{ __('adminMessage.ordersgrow') }}
                                                                </span>
                                                                <div class="kt-space-10"></div>
                                                                <div class="progress kt-progress--sm">
                                                                    <div class="progress-bar bg-warning"
                                                                        role="progressbar"
                                                                        style="width: {{ round($ordersgrow, 1) }}%;"
                                                                        aria-valuenow="{{ round($ordersgrow, 1) }}"
                                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end::Widget 6-->
                                        </div>
                                    </div>

                                    <!--end:: completed orders -->
                                </div>
                                <div class="col-lg-12">
                                    <!--begin::Portlet-->
                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <span class="kt-portlet__head-icon kt-hidden">
                                                    <i class="la la-gear"></i>
                                                </span>
                                                <h3 class="kt-portlet__head-title">
                                                    {{ __('adminMessage.trafficcharts') }}
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <div id="kt_amcharts_device" style="height: 500px;"></div>
                                        </div>
                                    </div>

                                    <!--end::Portlet-->
                                </div>
                            </div>
                            <!-- google analytics -->
                            @if (!empty($gaAccesstoken))
                                <div class="row">
                                    <div class="col-lg-12">
                                        <!--begin:: Widgets/Support Tickets -->
                                        <div class="kt-portlet kt-portlet--height-fluid">
                                            <div class="kt-portlet__head">
                                                <div class="kt-portlet__head-label">
                                                    <h3 class="kt-portlet__head-title">
                                                        {{ __('adminMessage.google_analytics_reports') }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="kt-portlet__body">
                                                <div id="loading" style="text-align:center;padding:5px;">
                                                    <div class="kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                                                </div>
                                                <div id="embed-api-auth-container"></div>
                                                <div id="chart-container"></div>
                                                <div id="view-selector-container" style="display:none;"></div>

                                                <div id="chart-1-container"></div>
                                                <div id="view-selector-1-container" style="display:none;"></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- end google google analytics -->

                        </div>
                        <!-- end:: iconbox -->
                        <!--End::Dashboard 6-->

                    @else
                        {{-- <div class="alert alert-light alert-warning" role="alert">
                            <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                            <div class="alert-text">{{ __('adminMessage.youdonthavepermission') }}</div>
                        </div> --}}
                        <div class="alert alert-light" role="alert">
                            {{-- <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div> --}}
                            <div class="" style="font-size: 2.35rem">
                                {{ __('Welcome! ' . auth()->user()->name) }}</div>
                        </div>
                    @endif
                </div>
                <!-- end:: Content -->
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
    @include('gwc.js.dashboard')

    <!--begin::Page Vendors(used by this page) -->
    <script src="{!! url('admin_assets/assets/plugins/amcharts/amcharts.js') !!}" type="text/javascript"></script>

    <script src="{!! url('admin_assets/assets/plugins/amcharts/serial.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/plugins/amcharts/radar.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/plugins/amcharts/pie.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/plugins/amcharts/polarScatter.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/plugins/amcharts/animate.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/js/pages/crud/metronic-datatable/base/html-table.js') !!}" type="text/javascript"></script>
    <script src="{!! url('admin_assets/assets/plugins/amcharts/light.js') !!}" type="text/javascript"></script>


    <!--sales chart -->
    @php
        $amountcharts = App\Http\Controllers\AdminDashboardController::getChartvalues();
        $ordercharts = App\Http\Controllers\AdminDashboardController::getChartvalues_Orders();
    @endphp

    <script>
        //device chart
        var chart = AmCharts.makeChart("kt_amcharts_device", {
            "rtl": KTUtil.isRTL(),
            "type": "serial",
            "theme": "light",
            "dataProvider": [{
                    "country": "Users(web)",
                    "visits": "{{ $trafficcharts['users_web'] }}"
                },
                {
                    "country": "Users(android)",
                    "visits": "{{ $trafficcharts['users_android'] }}"
                },
                {
                    "country": "Users(ios)",
                    "visits": "{{ $trafficcharts['users_ios'] }}"
                },
                {
                    "country": "Orders(web)",
                    "visits": "{{ $trafficcharts['orders_web'] }}"
                },
                {
                    "country": "Orders(android)",
                    "visits": "{{ $trafficcharts['orders_android'] }}"
                },
                {
                    "country": "Orders(ios)",
                    "visits": "{{ $trafficcharts['orders_ios'] }}"
                }
            ],
            "valueAxes": [{
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0
            }],
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [{
                "balloonText": "[[category]]: <b>[[value]]</b>",
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "visits"
            }],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "country",
            "categoryAxis": {
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
            },
            "export": {
                "enabled": true
            }
        });
        //end


        var config = {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                    "October", "November", "December"
                ],
                datasets: [{
                    label: {{\App\Currency::default()}},
                    borderColor: "#154360",
                    borderWidth: 2,
                    //pointBackgroundColor: KTApp.getStateColor('brand'),
                    backgroundColor: "#3498DB",
                    pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                    pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                    pointHoverBackgroundColor: "#ff0000",
                    pointHoverBorderColor: Chart.helpers.color("#ff0000").alpha(0.2).rgbString(),
                    data: [{{ $amountcharts }}]
                }]
            },
            options: {
                title: {
                    display: true,
                },
                tooltips: {
                    intersect: false,
                    mode: 'nearest',
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10
                },
                legend: {
                    display: false,
                    labels: {
                        usePointStyle: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                hover: {
                    mode: 'index'
                },
                scales: {
                    xAxes: [{
                        display: false,
                        gridLines: false,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: false,
                        gridLines: false,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                },
                elements: {
                    point: {
                        radius: 3,
                        borderWidth: 0,
                        hoverRadius: 8,
                        hoverBorderWidth: 2
                    }
                }
            }
        };
        var chart = new Chart(document.getElementById('kt_chart_sales_stats_wb'), config);
        //order chart
        var config_order = {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                    "October", "November", "December"
                ],
                datasets: [{
                    label: "ORDERS",
                    borderColor: "#154360",
                    borderWidth: 2,
                    //pointBackgroundColor: KTApp.getStateColor('brand'),
                    backgroundColor: "#009900",
                    pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                    pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                    pointHoverBackgroundColor: "#ff0000",
                    pointHoverBorderColor: Chart.helpers.color("#ff0000").alpha(0.2).rgbString(),
                    data: [{{ $ordercharts }}]
                }]
            },
            options: {
                title: {
                    display: true,
                },
                tooltips: {
                    intersect: false,
                    mode: 'nearest',
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10
                },
                legend: {
                    display: false,
                    labels: {
                        usePointStyle: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                hover: {
                    mode: 'index'
                },
                scales: {
                    xAxes: [{
                        display: false,
                        gridLines: false,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: false,
                        gridLines: false,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                },
                elements: {
                    point: {
                        radius: 3,
                        borderWidth: 0,
                        hoverRadius: 8,
                        hoverBorderWidth: 2
                    }
                }
            }
        };
        var chart2 = new Chart(document.getElementById('kt_chart_sales_stats_wb_order'), config_order);
    </script>
    <!--end chart -->
    @if (!empty($gaAccesstoken))
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script>
            (function(w, d, s, g, js, fjs) {
                g = w.gapi || (w.gapi = {});
                g.analytics = {
                    q: [],
                    ready: function(cb) {
                        this.q.push(cb)
                    }
                };
                js = d.createElement(s);
                fjs = d.getElementsByTagName(s)[0];
                js.src = 'https://apis.google.com/js/platform.js';
                fjs.parentNode.insertBefore(js, fjs);
                js.onload = function() {
                    g.load('analytics')
                };
            }(window, document, 'script'));
        </script>

        <script>
            gapi.analytics.ready(function() {
                var ids = 'ga:251559249';
                var ACCESS_TOKEN = '@php echo $gaAccesstoken; @endphp';

                gapi.analytics.auth.authorize({
                    serverAuth: {
                        access_token: ACCESS_TOKEN
                    }
                });

                /**
                 * Create a new ViewSelector instance to be rendered inside of an
                 * element with the id "view-selector-container".
                 */
                var viewSelector = new gapi.analytics.ViewSelector({
                    container: 'view-selector-container'
                });

                // Render the view selector to the page.
                viewSelector.execute();


                /**
                 * Create a new DataChart instance with the given query parameters
                 * and Google chart options. It will be rendered inside an element
                 * with the id "chart-container".
                 */
                var dataChart = new gapi.analytics.googleCharts.DataChart({
                    query: {
                        metrics: 'ga:sessions',
                        dimensions: 'ga:date',
                        'start-date': '30daysAgo',
                        'end-date': 'yesterday'
                    },
                    chart: {
                        container: 'chart-container',
                        type: 'LINE',
                        options: {
                            width: '100%'
                        }
                    }
                });


                /**
                 * Render the dataChart on the page whenever a new view is selected.
                 */
                viewSelector.on('change', function(ids) {
                    dataChart.set({
                        query: {
                            ids: ids
                        }
                    }).execute();
                });



                /**
                 * Create a ViewSelector for the first view to be rendered inside of an
                 * element with the id "view-selector-1-container".
                 */
                var viewSelector1 = new gapi.analytics.ViewSelector({
                    container: 'view-selector-1-container'
                });



                // Render both view selectors to the page.
                viewSelector1.execute();


                /**
                 * Create the first DataChart for top countries over the past 30 days.
                 * It will be rendered inside an element with the id "chart-1-container".
                 */
                var dataChart1 = new gapi.analytics.googleCharts.DataChart({
                    query: {
                        metrics: 'ga:sessions',
                        dimensions: 'ga:country',
                        'start-date': '30daysAgo',
                        'end-date': 'yesterday',
                        'max-results': 6,
                        sort: '-ga:sessions'
                    },
                    chart: {
                        container: 'chart-1-container',
                        type: 'PIE',
                        options: {
                            width: '100%',
                            pieHole: 4 / 9
                        }
                    }
                });
                /**
                 * Update the first dataChart when the first view selecter is changed.
                 */
                viewSelector1.on('change', function(ids) {
                    dataChart1.set({
                        query: {
                            ids: ids
                        }
                    }).execute();
                    $('#loading').hide();
                });
            });
        </script>
    @endif
</body>
<!-- end::Body -->

</html>
