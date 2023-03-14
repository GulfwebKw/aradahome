@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}|Days Time Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
@include('gwc.css.user')

<!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
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
                            <h3 class="kt-subheader__title">Days Time Report</h3>
                            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;" class="kt-subheader__breadcrumbs-link">Days Time Report</a>

                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">

                            <!-- reset filtration button -->
                            @if(Session::get('daystime_filter_dates'))
                                <button type="button" class="btn btn-danger btn-bold resetDaysTimeDateRange mx-2">{{__('adminMessage.reset')}}</button>
                        @endif

                        <!-- filter date -->
                            <div class="kt-subheader__wrapper mx-2">
                                <div class="kt-input-icon kt-input-icon--right kt-subheader__search" style="width: fit-content">
                                    <input type="text" class="form-control"  name="kt_daterangepicker_range" id="kt_daterangepicker_range" placeholder="Select Date Range"
                                           value="@if(Session::get('daystime_filter_dates')){{Session::get('daystime_filter_dates')}}@endif">
                                    <button id="filterDaysTimeByDate" style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                    <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                                                </g>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- search box -->
                            <form class="kt-margin-l-20" id="kt_subheader_search_form">
                                <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                                    <input type="text" class="form-control" placeholder="{{__('adminMessage.searchhere')}}" id="searchCat" name="searchCat">
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
                            <!--<div class="btn-group">-->
                        <!--                               @if(auth()->guard('admin')->user()->can('options-create'))-->
                        <!--	<a href="{{url('gwc/options/create')}}" class="btn btn-brand btn-bold"><i class="la la-plus"></i>&nbsp;{{__('adminMessage.createnew')}}</a>-->
                            <!--                               @endif-->

                            <!--</div>-->
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
                                    Days Time Report
                                </h3>
                            </div>
                        </div>

                        <div class="kt-portlet__body">
                        @if(auth()->guard('admin')->user()->can('daystime-list'))

                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <span class="kt-portlet__head-icon kt-hidden">
                                            <i class="la la-gear"></i>
                                        </span>
                                        <h3 class="kt-portlet__head-title">
                                            Days Report
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <div id="kt_amcharts_days" style="height: 100%">

                                        <canvas id="daysChart"></canvas>
                                        <script>
                                            var ctx = document.getElementById('daysChart').getContext('2d');
                                            var myChart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                                                    datasets: [{
                                                        label: '',
                                                        data: [
                                                            {{$day[0]}} ,
                                                            {{$day[1]}} ,
                                                            {{$day[2]}} ,
                                                            {{$day[3]}} ,
                                                            {{$day[4]}} ,
                                                            {{$day[5]}} ,
                                                            {{$day[6]}} ,
                                                        ],
                                                        backgroundColor: [
                                                            'rgba(255, 99, 132, 0.2)',
                                                            'rgba(54, 162, 235, 0.2)',
                                                            'rgba(255, 206, 86, 0.2)',
                                                            'rgba(75, 192, 192, 0.2)',
                                                            'rgba(153, 102, 255, 0.2)',
                                                            'rgba(255, 159, 64, 0.2)',
                                                            'rgba(255, 99, 132, 0.2)',
                                                        ],
                                                        borderColor: [
                                                            'rgba(255, 99, 132, 1)',
                                                            'rgba(54, 162, 235, 1)',
                                                            'rgba(255, 206, 86, 1)',
                                                            'rgba(75, 192, 192, 1)',
                                                            'rgba(153, 102, 255, 1)',
                                                            'rgba(255, 159, 64, 1)',
                                                            'rgba(255, 99, 132, 1)',
                                                        ],
                                                        borderWidth: 1
                                                    }]
                                                },
                                                options: {
                                                    scales: {
                                                        y: {
                                                            beginAtZero: true
                                                        }
                                                    },
                                                    legend: {
                                                        display: false
                                                    },
                                                    tooltips: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                return tooltipItem.yLabel;
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>

                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                        <span class="kt-portlet__head-icon kt-hidden">
                                            <i class="la la-gear"></i>
                                        </span>
                                            <h3 class="kt-portlet__head-title">
                                                Times Report
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body">
                                        <div id="kt_amcharts_times" style="height: 100%">

                                            <canvas id="timesChart"></canvas>
                                            <script>
                                                var ctx = document.getElementById('timesChart').getContext('2d');
                                                var myChart = new Chart(ctx, {
                                                    type: 'bar',
                                                    data: {
                                                        labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                                                        datasets: [{
                                                            label: '',
                                                            data: [
                                                                {{$time[0]}} ,
                                                                {{$time[1]}} ,
                                                                {{$time[2]}} ,
                                                                {{$time[3]}} ,
                                                                {{$time[4]}} ,
                                                                {{$time[5]}} ,
                                                                {{$time[6]}} ,
                                                                {{$time[7]}} ,
                                                                {{$time[8]}} ,
                                                                {{$time[9]}} ,
                                                                {{$time[10]}} ,
                                                                {{$time[11]}} ,
                                                                {{$time[12]}} ,
                                                                {{$time[13]}} ,
                                                                {{$time[14]}} ,
                                                                {{$time[15]}} ,
                                                                {{$time[16]}} ,
                                                                {{$time[17]}} ,
                                                                {{$time[18]}} ,
                                                                {{$time[19]}} ,
                                                                {{$time[20]}} ,
                                                                {{$time[21]}} ,
                                                                {{$time[22]}} ,
                                                                {{$time[23]}} ,
                                                            ],
                                                            backgroundColor: [
                                                                'rgba(255, 99, 132, 0.2)',
                                                                'rgba(54, 162, 235, 0.2)',
                                                                'rgba(255, 206, 86, 0.2)',
                                                                'rgba(75, 192, 192, 0.2)',
                                                                'rgba(153, 102, 255, 0.2)',
                                                                'rgba(255, 159, 64, 0.2)',
                                                                'rgba(255, 99, 132, 0.2)',
                                                                'rgba(54, 162, 235, 0.2)',
                                                                'rgba(255, 206, 86, 0.2)',
                                                                'rgba(75, 192, 192, 0.2)',
                                                                'rgba(153, 102, 255, 0.2)',
                                                                'rgba(255, 159, 64, 0.2)',
                                                                'rgba(255, 99, 132, 0.2)',
                                                                'rgba(54, 162, 235, 0.2)',
                                                                'rgba(255, 206, 86, 0.2)',
                                                                'rgba(75, 192, 192, 0.2)',
                                                                'rgba(153, 102, 255, 0.2)',
                                                                'rgba(255, 159, 64, 0.2)',
                                                                'rgba(255, 99, 132, 0.2)',
                                                                'rgba(54, 162, 235, 0.2)',
                                                                'rgba(255, 206, 86, 0.2)',
                                                                'rgba(75, 192, 192, 0.2)',
                                                                'rgba(153, 102, 255, 0.2)',
                                                                'rgba(255, 159, 64, 0.2)',
                                                            ],
                                                            borderColor: [
                                                                'rgba(255, 99, 132, 1)',
                                                                'rgba(54, 162, 235, 1)',
                                                                'rgba(255, 206, 86, 1)',
                                                                'rgba(75, 192, 192, 1)',
                                                                'rgba(153, 102, 255, 1)',
                                                                'rgba(255, 159, 64, 1)',
                                                                'rgba(255, 99, 132, 1)',
                                                                'rgba(54, 162, 235, 1)',
                                                                'rgba(255, 206, 86, 1)',
                                                                'rgba(75, 192, 192, 1)',
                                                                'rgba(153, 102, 255, 1)',
                                                                'rgba(255, 159, 64, 1)',
                                                                'rgba(255, 99, 132, 1)',
                                                                'rgba(54, 162, 235, 1)',
                                                                'rgba(255, 206, 86, 1)',
                                                                'rgba(75, 192, 192, 1)',
                                                                'rgba(153, 102, 255, 1)',
                                                                'rgba(255, 159, 64, 1)',
                                                                'rgba(255, 99, 132, 1)',
                                                                'rgba(54, 162, 235, 1)',
                                                                'rgba(255, 206, 86, 1)',
                                                                'rgba(75, 192, 192, 1)',
                                                                'rgba(153, 102, 255, 1)',
                                                                'rgba(255, 159, 64, 1)',
                                                            ],
                                                            borderWidth: 1
                                                        }]
                                                    },
                                                    options: {
                                                        scales: {
                                                            y: {
                                                                beginAtZero: true
                                                            }
                                                        },
                                                        legend: {
                                                            display: false
                                                        },
                                                        tooltips: {
                                                            callbacks: {
                                                                label: function(tooltipItem) {
                                                                    return tooltipItem.yLabel;
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>

                        @else
                            <div class="alert alert-light alert-warning" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                <div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
                            </div>
                        @endif
                        </div>

                    </div>
                </div>

                <!-- end:: Content -->
            </div>

            <!-- begin:: Footer -->
        @include('gwc.includes.footer')

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
</script>

<script>
    $(function() {
        $('input[name="kt_daterangepicker_range"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });

    $('input[name="kt_daterangepicker_range"]').on('apply.daterangepicker', function(ev, picker) {
        filterDaysTimeByDate();
    });

    function filterDaysTimeByDate()
    {
        var val = $("#kt_daterangepicker_range").val();
        $.ajax({
            type: "POST",
            url: "/gwc/daystime/ajax",
            data: "daystime_dates=" + val,
            dataType: "json",
            cache: false,
            processData: false,
            success: function () {
                window.location.reload();
            },
            error: function () {
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
            }
        });
    }

    //reset days time date range
    $(".resetDaysTimeDateRange").click(function () {
        $.ajax({
            type: "POST",
            url: "/gwc/daystime/reset-date-range",
            dataType: "json",
            cache: false,
            processData: false,
            success: function () {
                window.location.reload();
            },
            error: function () {
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
            }
        });
    });
</script>

</body>
<!-- end::Body -->
</html>