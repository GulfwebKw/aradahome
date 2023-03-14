@php
    $settings = App\Http\Controllers\AdminSettingsController::getSetting();
    $theme    = $settings->theme;
@endphp
        <!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>

    <meta charset="utf-8" />
    <title>{{__('adminMessage.websiteName')}}|Most Sold</title>
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
                            <h3 class="kt-subheader__title">Most Sold</h3>
                            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="{{url('home')}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="javascript:;" class="kt-subheader__breadcrumbs-link">Most Sold</a>

                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">











                            <!-- reset filtration button -->
                            @if(Session::get('mostsold_filter_dates'))
                                <button type="button" class="btn btn-danger btn-bold resetMostsoldDateRange mx-2">{{__('adminMessage.reset')}}</button>
                            @endif

                            <!-- filter date -->
                                <div class="kt-subheader__wrapper mx-2">
                                    <div class="kt-input-icon kt-input-icon--right kt-subheader__search" style="width: fit-content">
                                        <input type="text" class="form-control"  name="kt_daterangepicker_range" id="kt_daterangepicker_range" placeholder="Select Date Range"
                                               value="@if(Session::get('mostsold_filter_dates')){{Session::get('mostsold_filter_dates')}}@endif">
                                        <button id="filterMostsoldsByDate" style="border:0;" class="kt-input-icon__icon kt-input-icon__icon--right">
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

                            <div class="btn-group ml-3">
                                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">@if(Session::has('payment_filter_status_Reports_MSO')) @if(Session::get('payment_filter_status_Reports_MSO')) {{__('adminMessage.paid')}} @else {{__('adminMessage.notpaid')}}  @endif @else{{strtoupper(__('adminMessage.all'))}}@endif</button>
                                <div class="dropdown-menu dropdown-menu-right">

                                    <ul class="kt-nav">
                                        <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link MSOPaystatus" id="">{{__('adminMessage.all')}}</a></li>
                                        <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link MSOPaystatus" id="1">{{__('adminMessage.paid')}}</a></li>
                                        <li class="kt-nav__item"><a href="javascript:;" class="kt-nav__link MSOPaystatus" id="0">{{__('adminMessage.notpaid')}}</a></li>

                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- end:: Subheader -->

                <!-- begin:: Content -->
                <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                    @include('gwc.includes.alert')
                    <div class="kt-portlet kt-portlet--mobile">
                      
                        @if(auth()->guard('admin')->user()->can('mostsold-list'))
                            <!--begin: Datatable -->
                                <table class="table table-striped- table-bordered table-hover table-checkable " id="kt_table_1">
                                    <thead>
                                    <tr>
                                        <th width="10">#</th>
                                        <th>{{__('adminMessage.item_code')}}</th>
                                        <th>{{__('adminMessage.title')}}</th>
                                        <th>{{__('adminMessage.image')}}</th>
                                        <th>Total Order</th>
                                        <th>{{__('adminMessage.quantity')}}</th>
                                        <th>{{__('adminMessage.retail_price')}}</th>
                                        @if(auth()->guard('admin')->user()->can('product-edit'))
                                            <th>{{__('adminMessage.status')}}</th>
                                            <th>{{__('adminMessage.export')}}</th>
                                        @endif
                                        <th width="10">{{__('adminMessage.actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($products))
                                        @php $p=1; @endphp
                                        @foreach($products as $key=>$product)
                                            <tr class="search-body">
                                                <td>{{ $p }}</td>
                                                <td>{!! $product->item_code !!}</td>
                                                <td>
                                                    {!! $product->title_en !!}
                                                    <br>
                                                    {!! $product->title_ar !!}
                                                </td>
                                                <td><img src="{!! asset('/uploads/product/thumb/' . $product->image) !!}" style="width:70px;height:70px" /></td>
                                                @php
                                                    if(isset($product->countdown_price) && $product->countdown_price > 0) {
                                                        $price = $product->countdown_price;
                                                    }
                                                    else {
                                                        $price = $product->retail_price;
                                                    }
                                                @endphp
                                                <td>{!! $product->soldQuantity . ' Orders <br>' . $product->soldQuantity * $price . ' '.\App\Currency::default() !!}</td>
                                                <td>{!! $product->quantity !!}</td>
                                                <td>{!! $product->retail_price . ' '.\App\Currency::default() !!}</td>
                                                @if(auth()->guard('admin')->user()->can('product-edit'))
                                                    <td>
                                                        <select style="width:100%;" class="form-control prodstatus" name="prodstatus" id="{{$product->id}}">
                                                            <option value="0" @if($product->is_active==0) selected @endif>{{__('adminMessage.notpublished')}}</option>
                                                            <option value="1" @if($product->is_active==1) selected @endif>{{__('adminMessage.published')}}</option>
                                                            <option value="2" @if($product->is_active==2) selected @endif>{{__('adminMessage.publishedpreorder')}}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <span class="kt-switch"><label><input value="{{$product->id}}" {{!empty($product->is_export_active)?'checked':''}} type="checkbox"  id="productexport" class="change_status"><span></span></label></span>
                                                    </td>
                                                @endif
                                                <td class="kt-datatable__cell">
                                                    <div style="overflow: visible; position: relative; width: 80px;">
                                                        <div class="dropdown">
                                                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">
                                                                <i class="flaticon-more-1"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <ul class="kt-nav">
                                                                    <li class="kt-nav__item">
                                                                        <a href="{{url('en/details/' . $product->id . '/' . $product->slug)}}" class="kt-nav__link" target="_blank">
                                                                            <i class="kt-nav__link-icon flaticon2-contract"></i>
                                                                            <span class="kt-nav__link-text">{{__('adminMessage.view')}}</span>
                                                                        </a>
                                                                    </li>
                                                                    @if(auth()->guard('admin')->user()->can('product-edit'))
                                                                        <li class="kt-nav__item">
                                                                            <a href="{{url('/gwc/product/' . $product->id . '/edit')}}" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon flaticon2-contract"></i>
                                                                                <span class="kt-nav__link-text">{{__('adminMessage.edit')}}</span>
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $p++; @endphp
                                        @endforeach
                                        <tr><td colspan="5"><strong>Total Sales : {{ number_format($totalPrice,3)  }} KD<br>Total Profit : {{ number_format($totalPrice - $totalCost,3)  }} KD</strong></td><td><h3>{{ $totalOrders }}</h3></td><td colspan="2"><h3>{{ number_format($totalPrice,3) }} KD</h3></td><td colspan="2"></td></tr>
                                        <tr><td colspan="10" class="text-center">{{ $products->links() }}</td></tr>
                                    @else
                                        <tr><td colspan="10" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div>
                                  
                                </div>
                            @else
                                <div class="alert alert-light alert-warning" role="alert">
                                    <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                    <div class="alert-text">{{__('adminMessage.youdonthavepermission')}}</div>
                                </div>
                        @endif
                        <!--end: Datatable -->
                       
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
        filterMostsoldsByDate();
    });

    function filterMostsoldsByDate()
    {
        var val = $("#kt_daterangepicker_range").val();
        $.ajax({
            type: "POST",
            url: "/gwc/mostsold/ajax",
            data: "mostsold_dates=" + val,
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

    //reset most sold date range
    $(".resetMostsoldDateRange").click(function () {
        $.ajax({
            type: "POST",
            url: "/gwc/mostsold/reset-date-range",
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