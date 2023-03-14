@extends('driver.include.master')
@section('title' , 'List Drivers')
@section('content')
    <!--Begin::Row-->
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <i class="fa fa-users"></i> List of drivers
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <a href="{{ route('driver.admin.driver.create') }}" class="btn btn-label-brand btn-bold btn-sm"><i class="fa fa-user-plus"></i> New Driver</a>
                        <form class="kt-quick-search__form" style="margin-left: 15px;" action="{{ route('driver.admin.driver.index') }}">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="flaticon2-search-1"></i></span></div>
                                <input name="q" type="text" autofocus value="{{ request()->q }}" class="form-control kt-quick-search__input" placeholder="Search...">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="kt-portlet__body">


                    <!--begin::Widget 11-->
                    <div class="kt-widget11">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td>#</td>
                                    <td>{{__('adminMessage.avatar')}}</td>
                                    <td>{{__('adminMessage.name')}}</td>
                                    <td>{{__('adminMessage.username')}}</td>
                                    <td>{{__('adminMessage.phone')}}</td>
                                    <td class="text-center">Barcode</td>
                                    <td class="kt-align-right">{{__('adminMessage.actions')}}</td>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($drivers as $driver)
                                <tr>
                                    <td><i class="fa fa-circle mr-2 {{ $driver->is_active ? 'text-success' : 'text-danger' }}"></i> {{$settingInfo->prefix.'D'.$driver->id}}</td>
                                    <td>
                                        <img src="{!! url('uploads/users/'.($driver->avatar ?? 'no-image.png')) !!}" class="UserAvatar" alt="image" style="width: 50px;">
                                    </td><td>
                                        {!! $driver->fullname !!} | {!! $driver->fullname_ar !!}</td>
                                    <td>{!! $driver->username !!}</td>
                                    <td>{!! $driver->phone !!}</td>
                                    <td class="text-center"><img src="data:image/png;base64,{{  base64_encode($BRGenerator->getBarcode($driver->id ? $settingInfo->prefix.'D'.$driver->id: $settingInfo->prefix.'D'. 'NEW', $BRGenerator::TYPE_CODE_128 , 2 , 50))}}"></td>
{{--                                    <td><span class="kt-badge kt-badge--inline kt-badge--brand">new</span></td>--}}
{{--                                    <td class="kt-align-right kt-font-brand kt-font-bold">$34,740</td>--}}
                                    <td class="kt-datatable__cell kt-align-right">
                                         <span style="overflow: visible; position: relative; width: 80px;">
                                             <div class="dropdown">
                                                 <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown"><i class="flaticon-more-1"></i></a>
                                                 <div class="dropdown-menu dropdown-menu-right">
                                                     <ul class="kt-nav">
                                                         <li class="kt-nav__item"><a href="{{ route('driver.admin.driver.edit' , [$driver->id])}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-contract"></i><span class="kt-nav__link-text">{{__('adminMessage.edit')}}</span></a></li>
                                                         <li class="kt-nav__item"><a href="{{ route('driver.admin.orders.assigned_history' , ['driver_id' => $driver->id])}}" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-shipping-fast"></i><span class="kt-nav__link-text">Task</span></a></li>
                                                         <li class="kt-nav__item"><a href="{{ route('driver.admin.orders.search' , ['' ,'driver_id' => $driver->id])}}" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-history"></i><span class="kt-nav__link-text">History</span></a></li>
                                                         <li class="kt-nav__item"><a href="{{ route('driver.admin.driver.print' , ['en',$driver->id])}}" target="_blank" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-print"></i><span class="kt-nav__link-text">Print Card (En)</span></a></li>
                                                         <li class="kt-nav__item"><a href="{{ route('driver.admin.driver.print' , ['ar',$driver->id])}}" target="_blank" class="kt-nav__link"><i class="kt-nav__link-icon fa fa-print"></i><span class="kt-nav__link-text">Print Card (Ar)</span></a></li>
                                                         <li class="kt-nav__item"><a href="javascript:;" data-toggle="modal" data-target="#kt_modal_{{$driver->id}}" class="kt-nav__link"><i class="kt-nav__link-icon flaticon2-trash"></i><span class="kt-nav__link-text">{{__('adminMessage.delete')}}</span></a></li>
                                                     </ul>
                                                 </div>
                                             </div>
                                         </span>

                                        <!--Delete modal -->
                                        <div class="modal fade" id="kt_modal_{{$driver->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{__('adminMessage.alert')}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6 class="modal-title ltr text-center">{!!__('adminMessage.alertDeleteMessage')!!}</h6>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('adminMessage.no')}}</button>
                                                        <form action="{{ route('driver.admin.driver.destroy' , [$driver->id]) }}" method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger">{{__('adminMessage.yes')}}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">{{__('adminMessage.recordnotfound')}}</td></tr>

                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="kt-widget11__action kt-align-right">
                            {{ $drivers->appends($_GET)->links() }}
                        </div>
                    </div>

                    <!--end::Widget 11-->

                </div>
            </div>
        </div>
    </div>

    <!--End::Row-->
@endsection

@section('js')
    <script>
        function barcodeRead(barcode){
            $('.kt-quick-search__input').val(barcode);
            $('.kt-quick-search__form').submit();
        }
    </script>
@endsection