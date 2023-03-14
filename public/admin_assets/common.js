$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };


    var BASE_URL = '';

    $(document).on("click", ".putsessionmenu", function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/putmesession",
            data: "keyname=chosenmenu&id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
            },
            error: function (xhr, status, error) {
            }
        });
    });


    setTimeout(function () {
        $(".alert-danger").fadeToggle();
        $(".alert-success").fadeToggle();
    }, 5000);
    $(document).on("click", ".loadordernotifymodal", function () {
        var orderid = $(this).attr("id");
        var type = $(this).attr("alt");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/notification/ajax",
            data: "orderid=" + orderid + "&type=" + type,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $("#response_message").html(msg.title);
                    $("#response_message").html(msg.message);
                    $("#kt_modal_default_gulfweb").modal('show');
                } else {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                    //notification end
                }
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".changevendororderstatus").click(function () {
        var id = $(this).attr("id");
        var order_status = $("#order_status" + id).val();
        var extra_comment = $("#extra_comment" + id).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/vendor-orders/status/ajax",
            data: "order_id=" + id + "&order_status=" + order_status + "&extra_comment=" + extra_comment,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                $("#OrderStatusMsg" + id).html(msg.message);
                if (msg.status == 200) {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //notification end
                } else {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                    //notification end
                }
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    <!--load modal -->
    $(".loadordermodal").click(function () {
        var ids = $(this).attr("id");

        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/vorder/vendororder/ajax",
            data: "ids=" + ids,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    $("#loadresponsemessage").html(msg.message);
                    $("#kt_modal_default_modal").modal("show");
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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
    <!--end order modal-->

    $(".toggle_more").click(function () {
        var id = $(this).attr("id");
        if ($('#table-' + id + ':visible').length) {
            $('#table-' + id).toggle("slow");
        } else {
            $('#table-' + id).toggle("slow");
        }
    });

    $(".toggle_more_order").click(function () {
        var id = $(this).attr("id");
        if ($('.table-' + id + ':visible').length) {
            $('.table-' + id).toggle("slow");
        } else {
            $('.table-' + id).toggle("slow");
        }
    });
//seller discount from admin
    $("#applydiscountamount").click(function () {
        var delivery_date = $("#delivery_date").val();
        var seller_discount = $("#seller_discount").val();
        var delivery_charges = $("#delivery_charges").val();
        var oid = $("#oid").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/order/discountapply/ajax",
            data: "delivery_date=" + delivery_date + "&seller_discount=" + seller_discount + "&oid=" + oid + "&delivery_charges=" + delivery_charges,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //$("#optionChildId-"+optionChildId).hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
                //notification end
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    })
//update item status from listing
    $(".prodstatus").change(function () {
        var id = $(this).attr("id");
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/ajax/" + id,
            data: "id=" + id + "&val=" + val,
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

    $(".deleteOtherChosenOption").click(function () {
        var optionChildId = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/deleteotherchosenoption/ajax",
            data: "optionChildId=" + optionChildId,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //$("#optionChildId-"+optionChildId).hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
                //notification end
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    })

    $(".deleteParentOptions").click(function () {
        var optionChildId = $(this).attr("id");
        var product_id = $("#product_id").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/deleteattributeparent/ajax",
            data: "optionChildId=" + optionChildId + "&product_id=" + product_id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //$("#optionChildId-"+optionChildId).hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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
    $(".deleteParentInventory").click(function () {
        var optionChildId = $(this).attr("id");
        var product_id = $("#product_id").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/deleteinventoryparent/ajax",
            data: "id=" + optionChildId + "&product_id=" + product_id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //$("#optionChildId-"+optionChildId).hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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
//remove custom chosen option szie/color
    $(".removeAttCustomOption").click(function () {
        var optionChildId = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/deleteattribute/ajax",
            data: "optionChildId=" + optionChildId,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //$("#optionChildId-"+optionChildId).hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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

//add custom option for item
    $(".addcustomoption").click(function () {
        var cust_options = $("#cust_options").val();
        var product_id = $("#product_id").val();
        var inventory_id = $("#selectInventory").val();
        var has_option = $("#is_attribute").val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/options/addchosenoption/ajax",
            data: "cust_options=" + cust_options + "&product_id=" + product_id + "&inventory_id=" + inventory_id+ "&has_option=" + has_option,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1);
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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

//remove option 

    $(".deletechildoption").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/options/deletechildoption/ajax",
            data: "id=" + id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                if (msg.status == "200") {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                } else {
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                }
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

    $(".viewcustomerorder").click(function () {
        var val = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=order_customers&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.userType == "admin") {
                    window.location = BASE_URL + '/gwc/orders';
                }
                if (msg.userType == "vendor") {
                    window.location = BASE_URL + '/vendor/orders';
                }
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".viewcustomerwish").click(function () {
        var val = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=wish_customers&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location = BASE_URL + '/gwc/customers/wishitems'
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//reset product filteration
    $(".resetProductFilters").click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/reset/ajax",
            data: "s=1",
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//filter items via section


    $(".filterByOutofStock").click(function () {
        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=item_outofstock&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".filterByStatus").click(function () {
        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=item_status&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".filterBySections").click(function () {
        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=item_sections&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    $(".filterBySectionsDirect").click(function () {
        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=item_sections&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location = BASE_URL + '/gwc/product'
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//wish items
    $("#wish_customers").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=wish_customers&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//store customer id to cookie
    $("#order_customers").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=order_customers&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $("#vpayment_customers").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=vpayment_customers&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $("#vpay_mode").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=vpay_mode&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $("#order_pay_mode").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/storetocookie/ajax",
            data: "key=pay_mode&val=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//update single quantity
    $(".updatesingleqty").click(function () {

        var id = $(this).attr("id");
        $("#qty_gif_" + id).show();
        var quantity = $("#quantity_" + id).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/editsinglequantity/ajax",
            data: "id=" + id + "&quantity=" + quantity,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                $("#qty_gif_" + id).hide();
                if (msg.status == 200) {
                    $("#q-" + id).html(quantity);
                    $("#qtyedit-" + id).html(msg.message);
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //notification end
                } else {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                    //notification end
                }

            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
                $("#qty_gif_" + id).hide();
            }
        });
    })
//delete child option
    $(".deleteChildOption").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/product/editoptions/delete/ajax",
            data: "id=" + id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $("#cdiv-" + id).hide();
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //notification end
                } else {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                    //notification end
                }
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//reset order date range
    $(".resetorderdaterange").click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/resetSearch/ajax",
            data: "s=1",
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//change order status
    $(".changeorderstatus").click(function () {
        var id = $(this).attr("id");
        var order_status = $("#order_status" + id).val();
        var pay_status = $("#pay_status" + id).val();
        var extra_comment = $("#extra_comment" + id).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/status/ajax",
            data: "id=" + id + "&order_status=" + order_status + "&pay_status=" + pay_status + "&extra_comment=" + extra_comment,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                $("#OrderStatusMsg" + id).html(msg.message);
                if (msg.status == 200) {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'success');
                    //notification end
                } else {
                    //notification start
                    var notify = $.notify({message: msg.message});
                    notify.update('type', 'danger');
                    //notification end
                }
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    $("#filterBydatesId").click(function () {
        var val = $("#kt_daterangepicker_range").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "dates=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $("#vpaymentfilterBydatesId").click(function () {
        var val = $("#kt_daterangepicker_range").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "vdates=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $("#filterBydatesPayent").click(function () {
        var val = $("#kt_daterangepicker_range").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "payment_dates=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//order status
    $(".orderstatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "order_status=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });


    $(".paymentstatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/payments/ajax",
            data: "payment_status=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
//payment status

    $(".releasestatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/payments/ajax",
            data: "release_status=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".vpaystatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "vpay_status=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".paystatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "pay_status=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });

    $(".MSOPaystatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "payment_status_Reports_MSO=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    $(".RDPaystatus").click(function () {

        var val = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/orders/ajax",
            data: "payment_status_Reports_Delivery=" + val,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    /*Document Ready Start*/
    $(".change_status").change(function () {
        var keys = $(this).attr("id");
        var id = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/" + keys + "/ajax/" + id,
            data: "id=" + id,
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

//update gallery details
    $(".updateGalleryDetails").click(function () {
        var id = $(this).attr("id");
        var title_en = $("#atitle_en_" + id).val();
        var title_ar = $("#atitle_ar_" + id).val();
        var display_order = $("#display_order_" + id).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/productGallery/" + id + "/" + title_en + "/" + title_ar + "/" + display_order,
            data: "id=" + id,
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

    $(".updateAttributeDetails").click(function () {
        var id = $(this).attr("id");
        var color = $("#color_" + id).val();
        var size = $("#size_" + id).val();
        var quantity = $("#quantity_" + id).val();
        var retail_price = $("#retail_price_" + id).val();
        var old_price = $("#old_price_" + id).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/productAttribute/" + id + "/" + color + "/" + size + "/" + quantity + "/" + retail_price + "/" + old_price,
            data: "id=" + id,
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

    $(".updateCategoryDetails").click(function () {
        var id = $(this).attr("id");
        var category = $("#category-" + id).val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/productCategory/" + id + "/" + category,
            data: "id=" + id,
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
//choose default address
    $(".chooseDefault").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/customers/addressDefault/ajax/" + id,
            data: "id=" + id,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (msg) {
                //notification start
                var notify = $.notify({message: msg.message});
                notify.update('type', 'success');
                //notification end
                window.location.reload();
            },
            error: function (msg) {
                //notification start
                var notify = $.notify({message: 'Error occurred while processing'});
                notify.update('type', 'danger');
                //notification end
            }
        });
    });
    //load child
    $('#country').on('change', function (e) {
        //console.log(e);
        var id = e.target.value;
        //console.log(id);
        //ajax
        $.get(BASE_URL + '/gwc/country/ajax-state/' + id, function (data) {
            //success data
            //console.log(data);
            var state = $('#state');
            state.empty();
            $.each(data, function (key, val) {
                var option = $('<option/>', {id: val['id'], value: val['name_en']});
                state.append('<option value ="' + val['id'] + '">' + val['name_en'] + '</option>');
            });
        });
    });
    $('#state').on('change', function (e) {
        //console.log(e);
        var id = e.target.value;
        //console.log(id);
        //ajax
        $.get(BASE_URL + '/gwc/state/ajax-area/' + id, function (data) {
            //success data
            //console.log(data);
            var area = $('#area');
            area.empty();
            $.each(data, function (key, val) {
                var option = $('<option/>', {id: val['id'], value: val['name_en']});
                area.append('<option value ="' + val['id'] + '">' + val['name_en'] + '</option>');
            });
        });
    });
    //change asorting
    $(".update_asorting").change(function () {
        var keys = $(this).attr("alt");
        var id = $(this).attr("id");
        var val = $(this).val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/" + keys + "/image/ajaxAsorting/" + id,
            data: "val=" + val,
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

    //change asorting
    $(".change_asorting").change(function () {
        var keys = $(this).attr("alt");
        var id = $(this).attr("id");
        var val = $(this).val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/gwc/" + keys + "/ajaxAsorting/" + id,
            data: "val=" + val,
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

    /*Document Ready End*/
});
