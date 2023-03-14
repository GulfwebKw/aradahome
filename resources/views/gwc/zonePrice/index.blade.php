@php
$settings = App\Http\Controllers\AdminSettingsController::getSetting();
$theme = $settings->theme;
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>

    <meta charset="utf-8" />
    <title>{{ __('adminMessage.websiteName') }}|{{ __('adminMessage.price') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--css files -->
    @include('gwc.css.user')

    <!-- token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed  @if (!empty($settings->is_admin_menu_minimize)) kt-aside--minimize @endif  kt-page--loading">

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
                <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                    <!-- begin:: Subheader -->
                    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                        <div class="kt-container  kt-container--fluid ">
                            <div class="kt-subheader__main">
                                <h3 class="kt-subheader__title">{{ __('adminMessage.price') }} {{ $zone->title_en }}</h3>
                                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                <div class="kt-subheader__breadcrumbs">
                                    <a href="{{ url('home') }}" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                    <span class="kt-subheader__breadcrumbs-separator"></span>
                                    <a href="{{ url('gwc/zones') }}" class="kt-subheader__breadcrumbs-home">
                                        {{ $zone->title_en }}
                                    </a>
                                    <span class="kt-subheader__breadcrumbs-separator"></span>
                                    <a href="javascript:;"
                                        class="kt-subheader__breadcrumbs-link">{{ __('adminMessage.price') }}</a>

                                </div>
                            </div>
                            <div class="kt-subheader__toolbar">
                                <div class="btn-group">
                                    <a href="{{ url('gwc/zones/'.$zone->id.'/price/create') }}"
                                        class="btn btn-brand btn-bold"><i
                                            class="la la-plus"></i>&nbsp;{{ __('adminMessage.createnew') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- end:: Subheader -->

                    <!-- begin:: Content -->
                    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                        @include('gwc.includes.alert')
                        <div class="kt-portlet kt-portlet--mobile">

                            @if (auth()->guard('admin')->user()->can('zones-list'))
                                <!--begin: Datatable -->
                                <table class="table table-striped- table-bordered table-hover table-checkable "
                                    id="kt_table_1">
                                    <thead>
                                        <tr>
                                            <th>{{ __('adminMessage.weight') }}</th>
                                            <th>{{ __('adminMessage.price') }}</th>
                                            <th width="10">{{ __('adminMessage.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($zone->prices))
                                            @foreach ($zone->prices as $key => $price)
                                                <tr class="search-body">
                                                    <td>{{ $price->from }} - {{ $price->to }}</td>
                                                    <td>{{ $price->price }}</td>
                                                    <td class="kt-datatable__cell">
                                                        <span
                                                            style="overflow: visible; position: relative; width: 80px;">
                                                            <div class="dropdown">
                                                                <a href="javascript:;"
                                                                    class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                                                    data-toggle="dropdown"><i
                                                                        class="flaticon-more-1"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="kt-nav">
                                                                        <li class="kt-nav__item"><a
                                                                                href="{{ url('gwc/zones/'.$zone->id.'/price/' . $price->id . '/edit') }}"
                                                                                class="kt-nav__link"><i
                                                                                    class="kt-nav__link-icon flaticon2-contract"></i><span
                                                                                    class="kt-nav__link-text">{{ __('adminMessage.edit') }}</span></a>
                                                                        </li>
                                                                        <li class="kt-nav__item"><a
                                                                                href="javascript:;"
                                                                                data-toggle="modal"
                                                                                data-target="#kt_modal_{{ $price->id }}"
                                                                                class="kt-nav__link"><i
                                                                                    class="kt-nav__link-icon flaticon2-trash"></i><span
                                                                                    class="kt-nav__link-text">{{ __('adminMessage.delete') }}</span></a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </span>

                                                        <!--Delete modal -->
                                                        <div class="modal fade" id="kt_modal_{{ $price->id }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">
                                                                            {{ __('adminMessage.alert') }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <h6 class="modal-title">
                                                                            {!! __('adminMessage.alertDeleteMessage') !!}</h6>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('adminMessage.no') }}</button>
                                                                        <form action="{{ url('gwc/zones/'.$zone->id.'/price/' . $price->id) }}" method="Post">
                                                                            @csrf
                                                                            @method("DELETE")
                                                                            <button type="submit" class="btn btn-danger"
                                                                                    >{{ __('adminMessage.yes') }}</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @endforeach
                                            {{-- <tr>
                                                <td colspan="8" class="text-center">{{ $currency->links() }}</td>
                                            </tr> --}}
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    {{ __('adminMessage.recordnotfound') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-light alert-warning" role="alert">
                                    <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                    <div class="alert-text">{{ __('adminMessage.youdonthavepermission') }}</div>
                                </div>
                            @endif
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
        $(document).ready(function() {
            $('#searchCat').keyup(function() {
                // Search text
                var text = $(this).val();
                // Hide all content class element
                $('.search-body').hide();
                // Search
                $('.search-body').each(function() {

                    if ($(this).text().indexOf("" + text + "") != -1) {
                        $(this).closest('.search-body').show();

                    }
                });

            });
        });
    </script>
</body>
<!-- end::Body -->

</html>
