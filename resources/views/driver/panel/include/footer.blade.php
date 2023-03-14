@if(auth()->guard('driver')->check())
    <style>
        .icons8-whatsapp {
            display: inline-block;
            width: 48px;
            height: 48px;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iNDgiIGhlaWdodD0iNDgiCnZpZXdCb3g9IjAgMCA0OCA0OCIKc3R5bGU9IiBmaWxsOiMwMDAwMDA7Ij48cGF0aCBmaWxsPSIjZmZmIiBkPSJNNC44NjgsNDMuMzAzbDIuNjk0LTkuODM1QzUuOSwzMC41OSw1LjAyNiwyNy4zMjQsNS4wMjcsMjMuOTc5QzUuMDMyLDEzLjUxNCwxMy41NDgsNSwyNC4wMTQsNWM1LjA3OSwwLjAwMiw5Ljg0NSwxLjk3OSwxMy40Myw1LjU2NmMzLjU4NCwzLjU4OCw1LjU1OCw4LjM1Niw1LjU1NiwxMy40MjhjLTAuMDA0LDEwLjQ2NS04LjUyMiwxOC45OC0xOC45ODYsMTguOThjLTAuMDAxLDAsMCwwLDAsMGgtMC4wMDhjLTMuMTc3LTAuMDAxLTYuMy0wLjc5OC05LjA3My0yLjMxMUw0Ljg2OCw0My4zMDN6Ij48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZiIgZD0iTTQuODY4LDQzLjgwM2MtMC4xMzIsMC0wLjI2LTAuMDUyLTAuMzU1LTAuMTQ4Yy0wLjEyNS0wLjEyNy0wLjE3NC0wLjMxMi0wLjEyNy0wLjQ4M2wyLjYzOS05LjYzNmMtMS42MzYtMi45MDYtMi40OTktNi4yMDYtMi40OTctOS41NTZDNC41MzIsMTMuMjM4LDEzLjI3Myw0LjUsMjQuMDE0LDQuNWM1LjIxLDAuMDAyLDEwLjEwNSwyLjAzMSwxMy43ODQsNS43MTNjMy42NzksMy42ODMsNS43MDQsOC41NzcsNS43MDIsMTMuNzgxYy0wLjAwNCwxMC43NDEtOC43NDYsMTkuNDgtMTkuNDg2LDE5LjQ4Yy0zLjE4OS0wLjAwMS02LjM0NC0wLjc4OC05LjE0NC0yLjI3N2wtOS44NzUsMi41ODlDNC45NTMsNDMuNzk4LDQuOTExLDQzLjgwMyw0Ljg2OCw0My44MDN6Ij48L3BhdGg+PHBhdGggZmlsbD0iI2NmZDhkYyIgZD0iTTI0LjAxNCw1YzUuMDc5LDAuMDAyLDkuODQ1LDEuOTc5LDEzLjQzLDUuNTY2YzMuNTg0LDMuNTg4LDUuNTU4LDguMzU2LDUuNTU2LDEzLjQyOGMtMC4wMDQsMTAuNDY1LTguNTIyLDE4Ljk4LTE4Ljk4NiwxOC45OGgtMC4wMDhjLTMuMTc3LTAuMDAxLTYuMy0wLjc5OC05LjA3My0yLjMxMUw0Ljg2OCw0My4zMDNsMi42OTQtOS44MzVDNS45LDMwLjU5LDUuMDI2LDI3LjMyNCw1LjAyNywyMy45NzlDNS4wMzIsMTMuNTE0LDEzLjU0OCw1LDI0LjAxNCw1IE0yNC4wMTQsNDIuOTc0QzI0LjAxNCw0Mi45NzQsMjQuMDE0LDQyLjk3NCwyNC4wMTQsNDIuOTc0QzI0LjAxNCw0Mi45NzQsMjQuMDE0LDQyLjk3NCwyNC4wMTQsNDIuOTc0IE0yNC4wMTQsNDIuOTc0QzI0LjAxNCw0Mi45NzQsMjQuMDE0LDQyLjk3NCwyNC4wMTQsNDIuOTc0QzI0LjAxNCw0Mi45NzQsMjQuMDE0LDQyLjk3NCwyNC4wMTQsNDIuOTc0IE0yNC4wMTQsNEMyNC4wMTQsNCwyNC4wMTQsNCwyNC4wMTQsNEMxMi45OTgsNCw0LjAzMiwxMi45NjIsNC4wMjcsMjMuOTc5Yy0wLjAwMSwzLjM2NywwLjg0OSw2LjY4NSwyLjQ2MSw5LjYyMmwtMi41ODUsOS40MzljLTAuMDk0LDAuMzQ1LDAuMDAyLDAuNzEzLDAuMjU0LDAuOTY3YzAuMTksMC4xOTIsMC40NDcsMC4yOTcsMC43MTEsMC4yOTdjMC4wODUsMCwwLjE3LTAuMDExLDAuMjU0LTAuMDMzbDkuNjg3LTIuNTRjMi44MjgsMS40NjgsNS45OTgsMi4yNDMsOS4xOTcsMi4yNDRjMTEuMDI0LDAsMTkuOTktOC45NjMsMTkuOTk1LTE5Ljk4YzAuMDAyLTUuMzM5LTIuMDc1LTEwLjM1OS01Ljg0OC0xNC4xMzVDMzQuMzc4LDYuMDgzLDI5LjM1Nyw0LjAwMiwyNC4wMTQsNEwyNC4wMTQsNHoiPjwvcGF0aD48cGF0aCBmaWxsPSIjNDBjMzUxIiBkPSJNMzUuMTc2LDEyLjgzMmMtMi45OC0yLjk4Mi02Ljk0MS00LjYyNS0xMS4xNTctNC42MjZjLTguNzA0LDAtMTUuNzgzLDcuMDc2LTE1Ljc4NywxNS43NzRjLTAuMDAxLDIuOTgxLDAuODMzLDUuODgzLDIuNDEzLDguMzk2bDAuMzc2LDAuNTk3bC0xLjU5NSw1LjgyMWw1Ljk3My0xLjU2NmwwLjU3NywwLjM0MmMyLjQyMiwxLjQzOCw1LjIsMi4xOTgsOC4wMzIsMi4xOTloMC4wMDZjOC42OTgsMCwxNS43NzctNy4wNzcsMTUuNzgtMTUuNzc2QzM5Ljc5NSwxOS43NzgsMzguMTU2LDE1LjgxNCwzNS4xNzYsMTIuODMyeiI+PC9wYXRoPjxwYXRoIGZpbGw9IiNmZmYiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTE5LjI2OCwxNi4wNDVjLTAuMzU1LTAuNzktMC43MjktMC44MDYtMS4wNjgtMC44MmMtMC4yNzctMC4wMTItMC41OTMtMC4wMTEtMC45MDktMC4wMTFjLTAuMzE2LDAtMC44MywwLjExOS0xLjI2NSwwLjU5NGMtMC40MzUsMC40NzUtMS42NjEsMS42MjItMS42NjEsMy45NTZjMCwyLjMzNCwxLjcsNC41OSwxLjkzNyw0LjkwNmMwLjIzNywwLjMxNiwzLjI4Miw1LjI1OSw4LjEwNCw3LjE2MWM0LjAwNywxLjU4LDQuODIzLDEuMjY2LDUuNjkzLDEuMTg3YzAuODctMC4wNzksMi44MDctMS4xNDcsMy4yMDItMi4yNTVjMC4zOTUtMS4xMDgsMC4zOTUtMi4wNTcsMC4yNzctMi4yNTVjLTAuMTE5LTAuMTk4LTAuNDM1LTAuMzE2LTAuOTA5LTAuNTU0cy0yLjgwNy0xLjM4NS0zLjI0Mi0xLjU0M2MtMC40MzUtMC4xNTgtMC43NTEtMC4yMzctMS4wNjgsMC4yMzhjLTAuMzE2LDAuNDc0LTEuMjI1LDEuNTQzLTEuNTAyLDEuODU5Yy0wLjI3NywwLjMxNy0wLjU1NCwwLjM1Ny0xLjAyOCwwLjExOWMtMC40NzQtMC4yMzgtMi4wMDItMC43MzgtMy44MTUtMi4zNTRjLTEuNDEtMS4yNTctMi4zNjItMi44MS0yLjYzOS0zLjI4NWMtMC4yNzctMC40NzQtMC4wMy0wLjczMSwwLjIwOC0wLjk2OGMwLjIxMy0wLjIxMywwLjQ3NC0wLjU1NCwwLjcxMi0wLjgzMWMwLjIzNy0wLjI3NywwLjMxNi0wLjQ3NSwwLjQ3NC0wLjc5MWMwLjE1OC0wLjMxNywwLjA3OS0wLjU5NC0wLjA0LTAuODMxQzIwLjYxMiwxOS4zMjksMTkuNjksMTYuOTgzLDE5LjI2OCwxNi4wNDV6IiBjbGlwLXJ1bGU9ImV2ZW5vZGQiPjwvcGF0aD48L3N2Zz4=') 50% 50% no-repeat;
            background-size: 100%; }
    </style>
    <!--begin::Modal-->
    <div class="modal fade" id="order_short_view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="false">
        <div class="modal-dialog modal-lg" role="document"  style="height: 98%;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <h5 class="modal-title" id="order_short_view_label">Search Driver</h5>
                    {{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>--}}
                    <a href="" target="_blank" id="order_short_view_invoice" class="btn btn-outline-primary kt-pull-right"> <i class="fa fa-file-invoice-dollar"></i> Invoice </a>
                    <button class="btn btn-outline-primary kt-pull-right" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-hover">
                        <tbody>
                        <tr>
                            <th>
                                <strong>Order status</strong>
                            </th>
                            <td id="order_short_view_status">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Payment</strong>
                            </th>
                            <td>
                                <span id="order_short_view_price"></span>
                                <span class="kt-pull-right kt-badge kt-badge--inline kt-badge--success" id="order_short_view_paid">Paid</span>
                                <span class="kt-pull-right kt-badge kt-badge--inline kt-badge--danger" id="order_short_view_nopaid" onclick="openGateway();">Should Pay</span>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">
                                <strong>Customer</strong>
                            </th>
                            <td style="vertical-align: middle;" id="order_short_view_customer">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Deliver at</strong>
                            </th>
                            <td id="order_short_view_deliverat">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Area</strong>
                            </th>
                            <td id="order_short_view_area">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Block</strong>
                            </th>
                            <td id="order_short_view_blcok">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Street</strong>
                            </th>
                            <td id="order_short_view_street">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Avenue</strong>
                            </th>
                            <td id="order_short_view_avenue">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>House</strong>
                            </th>
                            <td id="order_short_view_house">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Floor</strong>
                            </th>
                            <td id="order_short_view_floor">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <strong>Description</strong>
                            </th>
                            <td id="order_short_view_description">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div id="button-background">
                        <span class="slide-text">Swipe To Change Status</span>
                        <div id="slider" class="bg-warning">
                            <i id="locker" class="fa fa-edit"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->
    <!--begin::Modal-->
    <div class="modal fade" id="camera_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"  style="height: 98%;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <h5 class="modal-title">Scan barcode</h5>
                    <button class="btn btn-outline-primary kt-pull-right" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->



    <script>
        var LastShortViewOrderId = null ;
        function openShortView(canupdate , orderId , orderStatus , price, phone , name , paid , deliverAt, deliverTime , area , block , street , avenue , hose , floor , invoice , description) {
            $("#order_short_view_label").html('#'+ orderId);
            // if ( phone !== "" ) {
            //     $("#order_short_view_customer").html('<a href="tel:'+phone+'"> <i class="fa fa-phone-volume" style="animation: shake 0.35s;animation-iteration-count: infinite;"></i> ' + name + '</a>');
            // } else {
            //     $("#order_short_view_customer").html(name);
            // }
            if ( phone !== "" ) {
                $('#order_short_view_customer').parent().show();
                $("#order_short_view_customer").html('<a style="margin-top: 16px;display: inline-block;" href="tel:'+phone+'"> <i class="fa fa-phone-volume" style="animation: shake 0.35s;animation-iteration-count: infinite;"></i> ' + phone + '</a><span class="kt-pull-right"><a href="https://api.whatsapp.com/send?phone='+ phone + '" target="_blank"><img src="data:image/png;base64,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"></a></span>');
            } else {
                $('#order_short_view_customer').parent().hide();
            }
            if ( paid === 0 ){
                $('#order_short_view_paid').hide();
                $('#order_short_view_nopaid').show();
            } else {
                $('#order_short_view_paid').show();
                $('#order_short_view_nopaid').hide();
            }
            if ( canupdate === 0 ){
                $('#button-background').hide();
            } else {
                $('#button-background').show();
            }

            if ( deliverTime !== "" ) {
                $("#order_short_view_deliverat").html(+deliverAt+' at ' + deliverTime);
            } else {
                $("#order_short_view_deliverat").html(deliverAt);
            }
            $("#order_short_view_area").html(area);
            $("#order_short_view_blcok").html(block);
            $("#order_short_view_street").html(street);
            $("#order_short_view_avenue").html(avenue);
            $("#order_short_view_house").html(hose);
            $("#order_short_view_floor").html(floor);
            $("#order_short_view_price").html(price);
            $("#order_short_view_status").html(orderStatus);
            $("#order_short_view_description").html(description);
            $("#order_short_view_invoice").attr('href' , invoice);
            $("#order_short_view").modal('show');
            LastShortViewOrderId = orderId;
        }
    </script>


    <script>
        var initialMouse = 0;
        var slideMovementTotal = 0;
        var mouseIsDown = false;
        var slider = $('#slider');

        slider.on('mousedown touchstart', function(event){
            mouseIsDown = true;
            slideMovementTotal = $('#button-background').width() - $(this).width() + 10;
            initialMouse = event.clientX || event.originalEvent.touches[0].pageX;
        });

        $(document.body, '#slider').on('mouseup touchend', function (event) {
            if (!mouseIsDown)
                return;
            mouseIsDown = false;
            var currentMouse = event.clientX || event.changedTouches[0].pageX;
            var relativeMouse = currentMouse - initialMouse;

            if (relativeMouse < slideMovementTotal) {
                $('.slide-text').fadeTo(300, 1);
                slider.animate({
                    left: "-10px"
                }, 300);
                return;
            }
            slider.addClass('unlocked');
            $('#locker').parent().click(function(){

                $("#OrderStatusMsg" + LastShortViewOrderId).removeClass('alert-solid-danger');
                $("#OrderStatusMsg" + LastShortViewOrderId).html('');
                $("#OrderStatusMsg" + LastShortViewOrderId).hide();
                $("#order_short_view").modal('hide');
                $("#kt_modal_edit_"+LastShortViewOrderId).modal('show');
            });
            setTimeout(function(){
                slider.on('click tap', function(event){
                    if (!slider.hasClass('unlocked'))
                        return;
                    slider.removeClass('unlocked');
                    $('#locker').parent().unbind("click");
                    slider.off('click tap');
                });
            }, 0);
        });
        $(document.body).on('mousemove touchmove', function(event){
            if (!mouseIsDown)
                return;

            var currentMouse = event.clientX || event.originalEvent.touches[0].pageX;
            var relativeMouse = currentMouse - initialMouse;
            var slidePercent = 1 - (relativeMouse / slideMovementTotal);

            $('.slide-text').fadeTo(0, slidePercent);

            if (relativeMouse <= 0) {
                slider.css({'left': '-10px'});
                return;
            }
            if (relativeMouse >= slideMovementTotal + 10) {
                slider.css({'left': slideMovementTotal + 'px'});
                return;
            }
            slider.css({'left': relativeMouse - 10});
        });

        function openGateway(){
            $("#kt_modal_pay_"+LastShortViewOrderId).modal('show');
        }

        function copyToClipboardLink(id) {
            var textBox = document.getElementById(id);
            textBox.select();
            document.execCommand("copy");
            //toastr.success("Patyment Link Has Been Coppied");
        }

    </script>

@endif