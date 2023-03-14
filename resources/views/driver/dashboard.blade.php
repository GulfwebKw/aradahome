@extends('driver.include.master')
@section('title' , 'Assign orders to drivers')

@section('header')

    <style>
        .row iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        .kt-content {
            padding: 7px;
        }
        .kt-container {
            padding: 0;
        }
        .kt-footer {
            display: none !important;
        }
        body {
            overflow-x: hidden;
        }
        @media (min-width: 768px) {
            .scrollme {
                position: inherit;
                margin-top: 15px;
            }
        }
        @media (max-width: 991px) {
            .hidden-sm {
                display: none !important;
            }
            .mb-sm-4 {
                margin-bottom: 1.5rem !important;
            }
        }

        .kt-widget31__content .kt-widget31__pic > img {
            width: 4rem;
            border-radius: 50%;
        }
        .kt-widget31__content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kt-widget31__content .kt-widget31__info {
            padding: 0 1.2rem;
            flex-grow: 1;
        }
        .kt-widget31__content .kt-widget31__info .kt-widget31__username {
            font-weight: 500;
            font-size: 1.1rem;
            color: #595d6e;
            transition: color 0.3s ease;
        }
        .kt-widget31__content .kt-widget31__info .kt-widget31__text {
            font-size: 1rem;
            margin: 0;
            font-weight: 400;
            color: #74788d;
        }
        .text-info {
            color: #5578eb !important;
        }
        .text-success {
            color: #0abb87 !important;
        }
        .text-danger {
            color: #fd397a !important;
        }
    </style>

@endsection


@section('content')
        <div class="row">
            <div class="col-md-7">
                <div class="scrollme">
                    <div class="row" id="getDetalsDiv">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fa fa-truck kt-font-brand mr-1"></i> Driver Id:
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="driverSearchInput" placeholder="Search for driver...">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-coffee text-info" id="driverSearchingStatus"></i>
                                        </span>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" onclick="openSearchModal()" type="button">Search!</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <form action="#" onsubmit="OrderIdInserted(); return false;">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">
                                                <i class="fa fa-dolly kt-font-brand mr-1"></i> Order Id:
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="orderSearchInput" placeholder="Search for driver...">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">
                                                <i class="fa fa-coffee text-info" id="orderSearchingStatus"></i>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="form-group form-group-last" id="alertBox">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kt-portlet__body kt-portlet__body--fit-y" style="">
                                <!--begin::Widget -->
                                <div class="border border-info kt-widget kt-widget--user-profile-1 pl-3 pr-3 mb-3 kt-iconbox--wave " style="border-radius: 37px;">
                                    <div class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--flag kt-ribbon--ver kt-ribbon--border-dash-hor kt-ribbon--info">
                                        <div class="kt-ribbon__target" style="top: 0; right: 20px; height: 45px;">
                                            <span class="kt-ribbon__inner"></span><i class="fa fa-shipping-fast"></i>
                                        </div>
                                        <div class="mt-3 w-100" style="display: inline-flex;">
                                            <div class="ml-1 mr-1">
                                                <img src="{!! url('uploads/logo/'.$settingInfo->favicon) !!}" style="max-height: 50px;" alt="image">
                                            </div>
                                            <div class="w-75">
                                                <h3 class="kt-portlet__head-title text-center" style="font-size: 1.4rem;font-weight: bold;color: #48465b;">
                                                    {{$settingInfo->name_en}}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kt-widget__head kt-ribbon mt-4">
                                        <div class="kt-widget__media">
                                            <img id="DriverAvatar" src="{!! url('uploads/users/'.( 'no-image.png')) !!}" alt="image" style="min-width: 38mm;max-height: 75mm;max-width: 45mm;border-radius: 8px;">
                                            <div id="DriverId" class="badge badge-info font-weight-bold mt-1 text-center w-100" style="font-size: inherit;" >---</div>
                                        </div>
                                        <div class="kt-widget__content w-100" style="padding-right: 1.6rem;">
                                            <div class="kt-widget__section">
                                                <div href="#" class="kt-widget__username">
                                                    <strong>Name: </strong><span id="DriverFullNameEn"></span>
                                                    <i class="flaticon2-correct kt-font-success" id="IsDriverActive"></i>
                                                </div>
                                                <div class="rtl text-right  mt-2" >
                                                    <strong>الاسم: </strong><span id="DriverFullNameAr"></span>
                                                </div>
                                                <div class="mt-2" >
                                                    <strong>Phone: </strong><span id="DriverPhone"></span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <img id="DriverBarcode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Widget -->
                            </div>
                        </div>
                    </div>
                    <div class="row"  id="tableOfHistory" style="overflow-x: hidden; overflow-y: auto">
                        <div class="col-md-12 hidden-sm">
                            <div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
                                <div class="kt-portlet__body" style="padding: 0">
                                    <table class="table table-hover table-light--primary">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="width: 45%">Order Details</th>
                                                <th colspan="2" style="width: 45%">Driver Details</th>
                                                <th style="width: 10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignHistory">
                                            <tr id="historyEmpty">
                                                <td colspan="5">
                                                    <div style="padding-top: 125px;width: 100%;height: 250px;text-align: center;">
                                                        <div>
                                                            <i class="fa fa-10x fa-shipping-fast text-secondary"></i>
                                                        </div>
                                                        <h2 class="mt-5 text-secondary">
                                                            You have not assigned any order to a driver yet!
                                                        </h2>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5" id="RightSidePanel" style="background-color: #ffffff">
                <iframe id="iframe" scrolling="no" style="display: none;" ></iframe>
                <div id="iframeDescription" style="padding-top: 125px;width: 100%;height: 90vh;text-align: center;">
                    <div>
                        <i class="fa fa-10x fa-file-invoice-dollar text-secondary" style="transform: rotate(20deg);"></i>
                    </div><h1 class="mt-5 text-secondary">
                        Insert Order Id...
                    </h1>
                </div>
            </div>
        </div>
<table style="display: none;">
    <tbody  id="sampleHistory">
    <tr id="orderHistory_{orderId}">
        <td>
            <div class="font-weight-bold">#{orderIdOrginal}</div>
            <div>{area}, B: {block}, S: {street}</div>
        </td>
        <td>
            <img src="{orderBarCode}">
        </td>
        <td>
            <div class="kt-widget31 kt-widget31__item kt-widget31__content">
                <div class="kt-widget31__pic kt-widget4__pic--pic">
                    <img src="{driverAvatar}" alt="">
                </div>
                <div class="kt-widget31__info">
                        <span href="#" class="kt-widget31__username">
                            {driverName}
                        </span>
                    <p class="kt-widget31__text">
                        <s>{lastDriverName}</s>
                    </p>
                </div>
            </div>
        </td>
        <td>
            <img src="{driverBarCode}">
        </td>
        <td>
            <div class="btn-group" role="group" aria-label="First group">
                <i class="fa fa-spinner fa-spin fa-2x text-warning orderHistory_{orderId}_loader"></i>
                <button type="button" class="btn btn-warning btn-sm orderHistory_{orderId}_actions" style="display: none" title="Undo" onclick="undo('{orderId}' , '{lastDriverId}' )"><i class="fa fa-undo"></i></button>
                <button type="button" class="btn btn-brand orderHistory_{orderId}_actions" style="display: none" title="Change driver" onclick="changeDriverOfOrder('{orderId}');"><i class="fa fa-user-edit"></i></button>
            </div>
        </td>
    </tr>
    </tbody>
</table>
@endsection



@section('js')
    <script>
        var driverCache = {};
        var orderCache = {};
        function barcodeRead(barcode){
            if ( parseInt(barcode.replace ( /[^\d.]/g, '' )) > 0 && barcode.toLowerCase().startsWith("{{ strtolower($settingInfo->prefix.'D') }}") ) {
                $("#driverSearchingStatus").addClass('fa-barcode').removeClass('fa-coffee').removeClass('fa-keyboard');
                $('#driverSearchInput').val(barcode);
                emptyDriver();
                loadDriver(barcode);
            } else if ( parseInt(barcode.replace ( /[^\d.]/g, '' )) > 0 && barcode.toLowerCase().startsWith("{{ strtolower($settingInfo->prefix) }}") ) {
                $("#orderSearchInput").val(barcode);
                $("#orderSearchingStatus").addClass('fa-barcode').removeClass('fa-coffee').removeClass('fa-keyboard');
                emptyOrder();
                loadOrder(barcode);
            }
        }

        function openSearchModal(){
            functionSearch = function(item, index, arr) {
                $('#modalSearchResult').append("<tr><td>"+item.DriverId+"</td><td><img src='"+item.avatar+"' style='max-height: 32px;border-radius: 50%;margin-right: 5px;'> "+item.fullname_en+"</td><td>"+item.username+"</td><td>"+item.phone+"</td><td><button class='btn btn-primary' onclick='barcodeRead(\""+item.DriverId+"\");$(\"#search_modal\").modal(\"hide\");$(\"#driverSearchModal\").resetForm();$(\"#modalSearchResult\").html(\"\");' >Set Driver ID</td></tr>")
            }
            $('#search_modal_label').html('Search Driver');
            $('#search_modal').modal('show');
        }

        function changeDriverOfOrder(orderId){
            functionSearch = function(item, index, arr) {
                $('#modalSearchResult').append("<tr><td>"+item.DriverId+"</td><td><img src='"+item.avatar+"' style='max-height: 32px;border-radius: 50%;margin-right: 5px;'> "+item.fullname_en+"</td><td>"+item.username+"</td><td>"+item.phone+"</td><td><button class='btn btn-primary' onclick='undo(\""+orderId+"\",\""+item.DriverId+"\");$(\"#search_modal\").modal(\"hide\");$(\"#driverSearchModal\").resetForm();$(\"#modalSearchResult\").html(\"\");' >Change Driver</td></tr>")
            }
            $('#search_modal_label').html('Change Driver #'+orderId);
            $('#search_modal').modal('show');
        }

        function copyToClipboardLink(id) {
            var textBox = document.getElementById(id);
            textBox.select();
            document.execCommand("copy");
            toastr.success("Patyment Link Has Been Coppied");
        }
        document.getElementsByTagName('iframe')[0].addEventListener("load", ev => {
            let width = ev.target.offsetWidth - 30;
            ev.target.style.height = ev.target.contentWindow.document.documentElement.scrollHeight + 'px';
            const new_style_element = document.createElement("style");
            const new_script_element = document.createElement("script");
            new_style_element.textContent = ".driverSystem {background-color: #ffffff;max-width: "+width+"px;} .headertd{min-width:0px!important;}"
            new_script_element.textContent = "body";
            ev.target.contentDocument.head.appendChild(new_style_element);
            if ( lastOrder_id !== "" ) {
                IsSearchingOrder = false;
                $("#orderSearchInput").prop('disabled', false);
                alert("success", "Order #" + lastOrder_id + " set.", 2, {
                    'element': '#orderSearchingStatus',
                    'alertIcon': 'fa-file-invoice-dollar text-success',
                    'disapperIcon': 'fa-coffee',
                    'deleteIcon': ['fa-keyboard', 'fa-spinner fa-spin', 'fa-barcode']
                });
                assignOrder(lastOrder_id);
                lastOrder_id = "";
            }
        });

        $(document).ready(function () {
            var el = $('.scrollme');
            // var originalelpos = el.offset().top; // take it where it originally is on the page
            var originalelpos = 0 ;
            var FirstTableHeight =  $('#RightSidePanel').parent().height() - $('#getDetalsDiv').height()  - 50 ;
            $("#tableOfHistory").height(FirstTableHeight);
            //run on scroll
            $(window).scroll(function () {
                var el = $('.scrollme'); // important! (local)
                var elpos = el.offset().top; // take current situation
                var windowpos = $(window).scrollTop();
                var finaldestination = windowpos + originalelpos;
                var tableHeight =  $('#iframe').parent().height() - $('#getDetalsDiv').height() - finaldestination - 10 ;
                if ( tableHeight < FirstTableHeight){
                    tableHeight = FirstTableHeight;
                }
                $("#tableOfHistory").height(tableHeight);
                el.stop().animate({ 'top': finaldestination }, 500);
            });
        });

        function assignOrder(orderId) {
            var order , driver;
            var existingDriverId = $("#driverSearchInput").val();
            if (typeof driverCache[existingDriverId.toLowerCase()] === undefined) {
                alert('danger' , 'Please select driver, then assign order!');
                return ;
            } else
                driver =  driverCache[existingDriverId.toLowerCase()];
            if (typeof orderCache[orderId.toLowerCase()] === undefined) {
                alert('danger' , 'Can not find order!');
                return ;
            } else
                order =  orderCache[orderId.toLowerCase()];

            $("#orderHistory_"+( order.order_id.toLowerCase() ) ).remove();
            var sampleHtml = $('#sampleHistory').html();
            sampleHtml = sampleHtml.replaceAll('{orderId}', order.order_id.toLowerCase() );
            sampleHtml = sampleHtml.replaceAll('{orderIdOrginal}', order.order_id );
            sampleHtml = sampleHtml.replaceAll('{area}', order.area.name_en  ? order.area.name_en  : "");
            sampleHtml = sampleHtml.replaceAll('{block}', order.block );
            sampleHtml = sampleHtml.replaceAll('{street}', order.street );
            sampleHtml = sampleHtml.replaceAll('{orderBarCode}', order.barcode );
            sampleHtml = sampleHtml.replaceAll('{driverAvatar}', driver.avatar );
            sampleHtml = sampleHtml.replaceAll('{driverName}', driver.fullname_en );
            sampleHtml = sampleHtml.replaceAll('{driverBarCode}', driver.barcode );
            sampleHtml = sampleHtml.replaceAll('{driverId}', driver.DriverId );
            if ( order.hasLastDriver) {
                sampleHtml = sampleHtml.replaceAll('{lastDriverId}', order.lastDriverId );
                sampleHtml = sampleHtml.replaceAll('{lastDriverName}', order.lastDriver.first_name_en + " " + order.lastDriver.last_name_en);
            } else {
                sampleHtml = sampleHtml.replaceAll('{lastDriverId}', "" );
                sampleHtml = sampleHtml.replaceAll('{lastDriverName}', "");
            }
            $('#historyEmpty').hide();
            $('#assignHistory').prepend(sampleHtml);
            $("#orderSearchInput").val('');
            assign(order.order_id , driver.DriverId);
        }
        function undo(orderID,driverId){
            $('.orderHistory_'+orderID.toLowerCase()+'_actions').hide();
            $('.orderHistory_'+orderID.toLowerCase()+'_loader').show();
            curl('{{ route('driver.admin.ajax.assign' , ['','']) }}/' + orderID + '/' +driverId  , function (result, data, httpCode) {
                if (result) {
                    console.log(result);
                    $("#orderHistory_"+( data.order_id.toLowerCase() ) ).remove();
                    var sampleHtml = $('#sampleHistory').html();
                    sampleHtml = sampleHtml.replaceAll('{orderId}', data.order_id.toLowerCase() );
                    sampleHtml = sampleHtml.replaceAll('{orderIdOrginal}', data.order_id );
                    sampleHtml = sampleHtml.replaceAll('{area}', data.area.name_en  ? data.area.name_en  : "");
                    sampleHtml = sampleHtml.replaceAll('{block}', data.block );
                    sampleHtml = sampleHtml.replaceAll('{street}', data.street );
                    sampleHtml = sampleHtml.replaceAll('{orderBarCode}', data.barcode );
                    sampleHtml = sampleHtml.replaceAll('{driverAvatar}', data.driver.avatar );
                    sampleHtml = sampleHtml.replaceAll('{driverName}',  data.driver.fullname_en );
                    sampleHtml = sampleHtml.replaceAll('{driverBarCode}',  data.driver.barcode );
                    sampleHtml = sampleHtml.replaceAll('{driverId}', data.driver.DriverId );
                    if ( result.hasLastDriver) {
                        sampleHtml = sampleHtml.replaceAll('{lastDriverId}', data.lastDriverId );
                        sampleHtml = sampleHtml.replaceAll('{lastDriverName}', data.lastDriver.first_name_en + " " + data.lastDriver.last_name_en);
                    } else {
                        sampleHtml = sampleHtml.replaceAll('{lastDriverId}', "" );
                        sampleHtml = sampleHtml.replaceAll('{lastDriverName}', "");
                    }
                    $('#historyEmpty').hide();
                    $('#assignHistory').prepend(sampleHtml);
                    $('.orderHistory_'+orderID.toLowerCase()+'_actions').show();
                    $('.orderHistory_'+orderID.toLowerCase()+'_loader').hide();
                    $('#orderHistory_'+orderID.toLowerCase()).addClass('alert-success');
                    setTimeout(function() {
                        $('#orderHistory_'+orderID.toLowerCase()).removeClass('alert-success');
                    }, 2000 );
                } else {
                    $('.orderHistory_'+orderID.toLowerCase()+'_loader').removeClass('fa-spinner').removeClass('fa-spin').removeClass('text-warning').addClass('fa-exclamation-triangle');
                    $('#orderHistory_'+orderID.toLowerCase()).addClass('alert-warning');
                    alert("danger", "Order #" + orderID + " <b> Can not </b> assign to Driver #"+driverId+".", 3 );
                }
            });
        }
        function assign(orderID,driverId){
            $('.orderHistory_'+orderID.toLowerCase()+'_actions').hide();
            $('.orderHistory_'+orderID.toLowerCase()+'_loader').show();
            curl('{{ route('driver.admin.ajax.assign' , ['','']) }}/' + orderID + '/' +driverId  , function (result, data, httpCode) {
                if (result) {
                    $('.orderHistory_'+orderID.toLowerCase()+'_actions').show();
                    $('.orderHistory_'+orderID.toLowerCase()+'_loader').hide();
                    $('#orderHistory_'+orderID.toLowerCase()).addClass('alert-success');
                    setTimeout(function() {
                        $('#orderHistory_'+orderID.toLowerCase()).removeClass('alert-success');
                    }, 2000 );
                    // alert("success", "Order #" + orderID + " assign to Driver #"+driverId+".", 2 );
                } else {
                    $('.orderHistory_'+orderID.toLowerCase()+'_loader').removeClass('fa-spinner').removeClass('fa-spin').removeClass('text-warning').addClass('fa-exclamation-triangle');
                    $('#orderHistory_'+orderID.toLowerCase()).addClass('alert-warning');
                    alert("danger", "Order #" + orderID + " <b> Can not </b> assign to Driver #"+driverId+".", 3 );
                }
            });
        }
        let guid = () => {
            let s4 = () => {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            }
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
        }
        function alert(type , message , sec = 3 , icon = false ){
            var id = "alert-" + guid() ;
            $("#alertBox").prepend("<div class=\"alert alert-solid-"+type+"\" id=\""+id+"\">"+message+"</div>");
            $(function() {
                setTimeout(function() {
                    $("#"+id).hide('slow');
                    if ( icon ){
                        $(icon.element).removeClass( icon.alertIcon).addClass( icon.disapperIcon);
                    }
                }, sec * 1000 );
                setTimeout(function() {
                    $("#"+id).remove();
                }, ( sec + 5 ) * 1000 );
            });
            if ( icon ){
                var el = $(icon.element);
                for (var i = 0; i < icon.deleteIcon.length; i++) {
                    el.removeClass(icon.deleteIcon[i]);
                }
                el.addClass(icon.alertIcon);
            }
        }

        var IsSearchingDriver = false;
        function loadDriver(driverId){
            if ( parseInt(driverId.replace ( /[^\d.]/g, '' )) > 0 && driverId.toLowerCase().startsWith("{{ strtolower($settingInfo->prefix.'D') }}") ) {
                $("#driverSearchingStatus").addClass('fa-spinner fa-spin').removeClass('fa-coffee').removeClass('fa-keyboard');
                if ( ! IsSearchingDriver ) {
                    IsSearchingDriver = true;
                    curl('{{ route('driver.admin.ajax.driver' , ['']) }}/' + driverId, function (result, data, httpCode) {
                        if (result) {
                            $("#DriverFullNameEn").html(data.fullname_en);
                            $("#DriverFullNameAr").html(data.fullname_ar);
                            $("#DriverPhone").html(data.phone);
                            $("#DriverId").html(data.DriverId);
                            $("#DriverAvatar").attr("src", data.avatar);
                            $("#DriverBarcode").attr("src", data.barcode);
                            if (data.is_active)
                                $("#IsDriverActive").show();
                            else
                                $("#IsDriverActive").hide();
                            driverCache[data.DriverId.toLowerCase()] = data;
                            alert("success", "Driver #" + driverId + " set.", 2 , {
                                'element' : '#driverSearchingStatus' ,
                                'alertIcon' : 'fa-user-check text-success' ,
                                'disapperIcon' : 'fa-coffee' ,
                                'deleteIcon' : ['fa-keyboard' , 'fa-spinner fa-spin' , 'fa-barcode']
                            });
                        } else {
                            emptyDriver(driverId, true, httpCode);
                        }
                        IsSearchingDriver = false;
                    });
                }
            } else {
                emptyDriver(driverId , true , 404 );
            }
        }
        var IsSearchingOrder = false;
        var lastOrder_id = "";
        function loadOrder(orderId){
            if ( parseInt(orderId.replace ( /[^\d.]/g, '' )) > 0 && orderId.toLowerCase().startsWith("{{ strtolower($settingInfo->prefix) }}") ) {
                $("#orderSearchingStatus").addClass('fa-spinner fa-spin').removeClass('fa-coffee').removeClass('fa-keyboard');
                if ( ! IsSearchingOrder ) {
                    IsSearchingOrder = true;
                    $("#orderSearchInput").prop('disabled', true);
                    curl('{{ route('driver.admin.ajax.order' , ['']) }}/' + orderId, function (result, data, httpCode) {
                        if (result) {
                            lastOrder_id = data.order_id;
                            $("#iframe").attr("src", data.invoiceUrl);
                            $("#iframeDescription").hide();
                            $("#iframe").show();
                            orderCache[data.order_id.toLowerCase()] = data;
                        } else {
                            emptyOrder(orderId, true, httpCode);
                            IsSearchingOrder = false;
                            $("#orderSearchInput").prop('disabled', false);
                        }
                    });
                }
            } else {
                emptyOrder(orderId , true , 404 );
            }
        }
        function emptyDriver(driverId = "", error = false , httpCode = 200){
            $("#DriverFullNameEn").html("");
            $("#DriverFullNameAr").html("");
            $("#DriverPhone").html("");
            $("#DriverId").html("---");
            $("#DriverAvatar").attr("src", "{!! url('uploads/users/'.( 'no-image.png')) !!}");
            $("#DriverBarcode").attr("src", "");
            $("#IsDriverActive").hide();
            if (error ) {
                if (httpCode === 404) {
                    alert("danger", "Can not find any driver with ID : " + driverId + "!", 3 ,  {
                        'element' : '#driverSearchingStatus' ,
                        'alertIcon' : 'fa-user-slash text-danger' ,
                        'disapperIcon' : 'fa-coffee' ,
                        'deleteIcon' : ['fa-keyboard' , 'fa-spinner fa-spin' , 'fa-barcode']
                    });
                } else {
                    alert("danger", "Unknown error!", 3 , {
                        'element' : '#driverSearchingStatus' ,
                        'alertIcon' : 'fa-server text-danger' ,
                        'disapperIcon' : 'fa-coffee' ,
                        'deleteIcon' : ['fa-keyboard' , 'fa-spinner fa-spin' , 'fa-barcode']
                    });
                }
            }
        }

        function emptyOrder(orderId = "", error = false , httpCode = 200){
            $("#iframeDescription").show();
            $("#iframe").hide();
            $("#iframe").attr("src", "//");
            if (error ) {
                if (httpCode === 404) {
                    alert("danger", "Can not find any pending Order with ID : " + orderId + "!", 3 ,  {
                        'element' : '#orderSearchingStatus' ,
                        'alertIcon' : 'fa-file-excel text-danger' ,
                        'disapperIcon' : 'fa-coffee' ,
                        'deleteIcon' : ['fa-keyboard' , 'fa-spinner fa-spin' , 'fa-barcode']
                    });
                } else {
                    alert("danger", "Unknown error!", 3 , {
                        'element' : '#orderSearchingStatus' ,
                        'alertIcon' : 'fa-server text-danger' ,
                        'disapperIcon' : 'fa-coffee' ,
                        'deleteIcon' : ['fa-keyboard' , 'fa-spinner fa-spin' , 'fa-barcode']
                    });
                }
            }
        }

        $('#driverSearchInput').keyup(function(e) {
            clearTimeout($.data(this, 'timer'));
            $("#driverSearchingStatus").removeClass('fa-coffee').addClass('fa-keyboard');
            if (e.keyCode == 13)
                searchDriver(true);
            else
                $(this).data('timer', setTimeout(searchDriver, 500));
        });
        function searchDriver(force) {
            var existingString = $("#driverSearchInput").val();
            $("#driverSearchingStatus").addClass('fa-coffee').removeClass('fa-keyboard');
            if (!force && existingString.length <= {{ strlen($settingInfo->prefix.'D') }} ) return;
            emptyDriver();
            loadDriver(existingString);
        }

        function OrderIdInserted(){
            var existingString = $("#orderSearchInput").val();
            $("#orderSearchingStatus").addClass('fa-coffee').removeClass('fa-keyboard');
            emptyOrder();
            loadOrder(existingString);
            return false;
        }
    </script>
@endsection