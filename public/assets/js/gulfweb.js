///**********************LOAD BUNDLE PRODUCTS******************////
function openBundleProducts(id) {
    $.ajax({
        type: "GET",
        url: "/bundle/" + id + "/products",
        dataType: "json",
        cache: false,
        processData: false,
        success: function (msg) {
            if (msg.status == 200) {
                $("#bundleProducts").html(msg.html);
                $("#ModalquickView").modal("show");//show modal
                $('.tt-input-counter').find('.minus-btn, .plus-btn').on('click', function (e) {
                    var $input = $(this).parent().find('input');
                    var quantity = $(this).parent().find('div').html();
                    var count = parseInt($input.val(), 10) + parseInt(e.currentTarget.className === 'plus-btn' ? 1 : -1, 10);
                    if (count < 0)
                        count = 0;
                    if (count > quantity)
                        count = quantity;
                    $input.val(count).change();
                });
            }
        }
    });
    return false;
}

function addToCartDetails(id) {
    $("#details_cartbtn_" + id).prop("disabled", true);
    $("#loader-details-gif_" + id).show();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //check size input visible or not
    var product_id = $("#product_id_" + id).val();

    $.ajax({
        type: "POST",
        url: "/ajax_details_addtocart",
        data: $("#addtocartDetailsForm_" + id).serialize(),
        dataType: "json",
        cache: false,
        processData: false,
        success: function (msg) {
            autrefreshCart();//refresh temp cart - inner
            autrefreshCartCount();//refresh total
            if (msg.status == 200) {
                if (typeof isBundle === 'undefined') {
                    if ( msg.is_cart_popup==1 ) {
                        $("#spancartbox").html(msg.message);
                        $("#modalDefaultBox").modal("show");//show modal
                    } else {
                        toastr.success(msg.message);
                    }
                } else {
                    $("#item_is_added_bundle_" + product_id).show('fast');
                }
                $("#size_attr_" + product_id).val('0');
                //remove all check options
                $('input:checked').removeAttr('checked');
                //end remove all checked options
                PushDataLayers("add_to_cart",msg.payload);
            } else {
                $("#quickresponse").html(msg.message);
            }
        },
        complete: function () {
            $("#details_cartbtn").attr('disabled', false);
            $("#loader-details-gif").hide();

        },
        error: function (msg) {
            $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
        }
    });

}

function changeBundleTab(id) {
    $('.allSubCategory').hide();
    $('#category_' + id).show('fast');
    showSubCat = '#category_' + id;
}

//autoload temp box after adding the item to cart
function autrefreshCart() {
    if (typeof isBundle !== 'undefined') {
        $.ajax({
            type: "GET",
            url: "/" + bundleLang + "/bundle",
            data: "",
            cache: false,
            processData: false,
            success: function (msg) {
                var html = $('<div />').append(msg).find("#tt-pageContent").html();
                $('#tt-pageContent').html(html);
                if (showSubCat !== "0") {
                    $('.allSubCategory').hide();
                    $(showSubCat).show();
                }
            }
        });
    }
    $.ajax({
        type: "GET",
        url: "/ajax_reload_temp_order_box",
        data: "",
        dataType: "json",
        cache: false,
        processData: false,
        success: function (msg) {
            if (typeof isBundle !== 'undefined') {
                var BundleDiscount = $('<div />').append(msg.message).find("#BundleDiscountJSGet").html();
                $('.BundleDiscountJS').html(BundleDiscount);
                var subTotal = $('<div />').append(msg.message).find("#subtotalJSget").html();
                $('.subtotalJS').html(subTotal);
            }
            $("#TempOrderBoxDiv").html(msg.message);
            //delete item from cart
            $(".deleteFromTemp").click(function () {
                var id = $(this).attr("id");
                $.ajax({
                    type: "GET",
                    url: "/deleteTempOrdersAjax",
                    data: "id=" + id,
                    dataType: "json",
                    cache: false,
                    processData: false,
                    success: function (msg) {
                        autrefreshCart();//refresh temp cart - inner
                        autrefreshCartCount();//refresh total
                        PushDataLayers("remove_from_cart",msg.payload);
                    },
                    error: function (msg) {
                        //$("#tt-badge-cart").html('0');
                    }
                });
            });
            //end
        },
        error: function (msg) {
            $("#TempOrderBoxDiv").html('<div class="alert-error">Something was wrong</div>');
        }
    });
}

function autrefreshCartCount() {
    $.ajax({
        type: "GET",
        url: "/countTempOrdersAjax",
        data: "",
        dataType: "json",
        cache: false,
        processData: false,
        success: function (msg) {
            $("#tt-badge-cart").html(msg.message);
        },
        error: function (msg) {
            $("#tt-badge-cart").html('0');
        }
    });
}


$(document).ready(function () {
    setInterval(function () {
        $(".alert-danger").fadeOut();
        $(".alert-success").fadeOut();
    }, 6000);

    var BASE_URL = "";

    //load details options via ajax
    ///**********************DETAIL ADD To CART *******************////

    $("#checkoutform").on('submit', (function (e) {
        $("#loader-details-gif").show();
        $(".confirmcheckbutton").prop("disabled", true);
    }));

    //select option

    $(".choose_select_options").change(function (e) {
        $(this).prop("disabled", true);

        $("#loader-gif").show();
        var ids = $(this).attr("id");
        var val = $(this).val();
        var unit_price = $("#unit_price").val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_option_select_price",
            data: "ids=" + ids + "&unit_price=" + unit_price + "&val=" + val,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#loader-gif").hide();
                if (msg.status == 200) {
                    $("#display_price").html(msg.message);
                    $("#other_currency_display_price").html(msg.otherPrice);
					$("#quantity_attr").attr("size",msg.quantity);
                } else {
                    $("#quickresponse").html(msg.message);
                }
            },
            complete: function () {
                $('#' + ids).attr('disabled', false);
            },
            error: function (msg) {
                $("#loader-gif").hide();
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });


    $(".checkOptionPricechk").click(function (e) {
        $(this).prop("disabled", true);

        $("#loader-gif").show();
        var ids = $(this).attr("id");
        var unit_price = $("#unit_price").val();

        if ($(this).prop('checked') == true) {
            var isChecked = 1;
        } else {
            var isChecked = 0;
        }
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_option_check_price",
            data: "ids=" + ids + "&unit_price=" + unit_price + "&isChecked=" + isChecked,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#loader-gif").hide();
                if (msg.status == 200) {
                    $("#display_price").html(msg.message);
                    $("#other_currency_display_price").html(msg.otherPrice);
                } else {
                    $("#quickresponse").html(msg.message);
                }
            },
            complete: function () {
                $('#' + ids).attr('disabled', false);
            },
            error: function (msg) {
                $("#loader-gif").hide();
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });

    $(".checkOptionPrice").click(function (e) {

        $(this).prop("disabled", true);

        $("#loader-gif").show();
        var ids = $(this).attr("id");
        //alert(ids);
        var unit_price = $("#unit_price").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_option_price",
            data: "ids=" + ids + "&unit_price=" + unit_price,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#loader-gif").hide();
                if (msg.status == 200) {
                    $("#display_price").html(msg.message);
                    $("#other_currency_display_price").html(msg.otherPrice);
                } else {
                    $("#quickresponse").html(msg.message);
                }
            },
            complete: function () {
                $('#' + ids).attr('disabled', false);
            },
            error: function (msg) {
                $("#loader-gif").hide();
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });


    $("#addtocartDetailsForm").on('submit', (function (e) {
        $("#details_cartbtn").prop("disabled", true);
        $("#loader-details-gif").show();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //check size input visible or not
        var product_id = $("#product_id").val();

        $.ajax({
            type: "POST",
            url: BASE_URL + "/ajax_details_addtocart",
            data: $('#addtocartDetailsForm').serialize(),
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                autrefreshCart();//refresh temp cart - inner
                autrefreshCartCount();//refresh total
                if (msg.status == 200) {
                    if ( msg.is_cart_popup==1 ) {
                        $("#spancartbox").html(msg.message);
                        $("#modalDefaultBox").modal("show");//show modal
                    } else {
                        toastr.success(msg.message);
                    }
                    $("#size_attr_" + product_id).val('0');
                    //remove all check options
                    $('input:checked').removeAttr('checked');
                    //end remove all checked options

                    PushDataLayers("add_to_cart",msg.payload);
                } else {
                    $("#quickresponse").html(msg.message);
                }
            },
            complete: function () {
                $("#details_cartbtn").attr('disabled', false);
                $("#loader-details-gif").hide();

            },
            error: function (msg) {
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
        return false;
    }));
    ///**********************END DETAILS ADD TO CART **************////

    //put color id to color_attr
    $(".options-color").click(function () {

        //remove all check options
        $('input:checked').removeAttr('checked');
        //end remove all checked options


        var product_id = $("#product_id").val();
        var colorid = $(this).attr("id");
        $("#color_attr").val(colorid);

        if ($("#size_attr_" + product_id).is(":visible") == true) {
            var size_id = $("#size_attr_" + product_id).val();
        } else {
            var size_id = "";
        }


        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_color_image",
            data: "color_id=" + colorid + "&product_id=" + product_id + "&size_id=" + size_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $('.zoomWindowContainer').remove();
                    $('.zoomContainer').remove();
                    $("#displayd-" + product_id).attr("src", msg.message);
                    $("#displayd-" + product_id).attr("data-zoom-image", msg.message);
                    $("#displaya-" + product_id).attr("data-image", msg.message);
                    $("#displaym-" + product_id).attr("src", msg.message);
                    $('.zoomWindowContainer div').stop().css("background-image", msg.message);
                    //$('.slick-active img').attr("src",msg.message);

                    //change quantity size attr
                    // if (msg.quantity != "0") {
                        $("#quantity_attr").attr("size", msg.quantity);
                        $("#display_qty").html(msg.quantity);
                    // }


                    $("#display_price").html(msg.price);
                    $(".colorDetails").html(msg.details);
                    $("#colorDetails").html(msg.details);
                    $("#other_currency_display_price").html(msg.otherPrice);
                    $("#unit_price").val(msg.price);
                    if (msg.old_price != "0") {
                        $("#display_oldprice").html(msg.old_price);
                    } else {
                        $("#oldprices").hide();
                    }

                } else {
                    $("#quickresponse").html("<div class='alert-danger'>" + msg.message + "</div>");
                }
            },
            error: function (msg) {
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });


    $(".openmyLink").click(function () {
        var links = $(this).attr("id");
        if (links != "") {
            window.open(links);
        }
    });

    //track order
    $("#trackmyorder").click(function () {
        $("#modalPrderTrackBox").modal("show");//show modal
    });

    $(".TrackMyOrders").click(function () {
        var orderid = $("#trackorderid").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_track_orderid",
            data: "orderid=" + orderid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    window.location =  "/" +  msg.url;
                } else {
                    $("#responseTrackOrder").html(msg.message);
                }
            },
            error: function (msg) {
                $("#responseTrackOrder").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });


    $("#username_box").hide();
    $("#password_box").hide();
    $("#register_me").click(function () {
        if ($('#register_me').is(':checked') == true) {
            $("#username_box").show();
            $("#password_box").show();
        } else {
            $("#username_box").hide();
            $("#password_box").hide();
        }
    });
    //remove my order
    $(".removemyorder").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_remove_my_order",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#orderdiv" + id).hide();
                $("#responseMsgOrder").html(msg.message);
            },
            error: function (msg) {
                $("#responseMsgOrder").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });


    //get delivery price
    $(".area_checkout").change(function () {
        var areaid = $(this).val();
        var stateid = $("#state").val();
        var countryid = $("#country").val();
        var totalprice = $("#checkout_totalprice").val();
        var bundleDiscount = $("#checkout_bundleDiscount").val();


        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_area_delivery",
            data: "t=check&areaid=" + areaid + "&totalprice=" + totalprice + "&stateid=" + stateid + "&countryid=" + countryid + "&bundleDiscount=" + bundleDiscount,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#checktotalbox").html(msg.message);
            },
            error: function (msg) {
                $("#checktotalbox").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });

    $(".area_checkoutcart").change(function () {
        cartAreaRefresh();
    });
    //get address
    $("#myaddress").change(function () {
        var val = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_customer_address",
            data: "id=" + val,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error');
            }
        });
    });
    //checkout
    $(".country_checkout").change(function () {
        var parentid = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_country_state_area_request",
            data: "type=state&parentid=" + parentid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#state_checkout").html(msg.message);
                $("#area_checkout").html(msg.area);
            },
            error: function (msg) {
                $("#state_checkout").html('<select name="state"  class="form-control state_checkout" id="state_checkout" ><option value="0">--</option></select>');
            }
        });
    });
    $(".country_checkout_get_city_area").change(function () {
        var parentid = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_country_state_area_request",
            data: "type=all&parentid=" + parentid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#country").html(msg.message);
                $("#area").html(msg.message);
                $("#PaymentGayteways").html(msg.gateways);
            },
            error: function (msg) {
            }
        });
    });
    //area
    $(".state_checkout").change(function () {
        var parentid = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_country_state_area_request",
            data: "type=area&parentid=" + parentid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if ( msg.message === "close-area"){
                    $("#area_Div").hide();
                    $("#area_checkout").html(msg.area);
                } else {
                    $("#area_checkout").html(msg.message);
                    $("#area_Div").show();
                }
            },
            error: function (msg) {
                $("#area_checkout").html('<select name="area"  class="form-control" id="area_checkout" ><option value="0">--</option></select>');
            }
        });
    });
    //form
    $(".country_checkout_form").change(function () {
        var parentid = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_country_state_area_request",
            data: "type=state&parentid=" + parentid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#state").html(msg.message);
            },
            error: function (msg) {
                $("#state").html('<select name="state"  class="form-control state_checkout_form" id="state_checkout" ><option value="0">--</option></select>');
            }
        });
    });
    //area
    $(".state_checkout_form").change(function () {
        var parentid = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_get_country_state_area_request",
            data: "type=area&parentid=" + parentid,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#area").html(msg.message);
            },
            error: function (msg) {
                $("#area").html('<select name="area"  class="form-control area_checkout" id="area_checkout" ><option value="0">--</option></select>');
            }
        });
    });
    //apply coupon
    $(".applycouponbtn").click(function () {
        var coupon_code = $("#coupon_code").val();
        var totalprice = $("#checkout_totalprice").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_apply_coupon_to_cart",
            data: "coupon_code=" + coupon_code + "&totalprice=" + totalprice,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $("#result_coupon").html(msg.message);
                    $("#checktotalbox").html(msg.cartbox);
                } else {
                    $("#result_coupon").html(msg.message);
                }
            },
            error: function (msg) {
                $("#result_coupon").html('<div class="alert-danger">Something was wrong</div>');
            }
        });
    });


    $(".applyselletdiscountbtn").click(function () {
        var seller_discount = $("#seller_discount").val();
        var delivery_date = $("#delivery_date").val();
        var totalprice = $("#checkout_totalprice").val();

        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_apply_seller_discount_to_cart",
            data: "seller_discount=" + seller_discount + "&delivery_date=" + delivery_date + "&totalprice=" + totalprice,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $("#result_coupon").html(msg.message);
                    $("#checktotalbox").html(msg.cartbox);
                } else {
                    $("#result_coupon").html(msg.message);
                }
            },
            error: function (msg) {
                $("#result_coupon").html('<div class="alert-danger">Something was wrong</div>');
            }
        });
    });


    //shopping cart
    $(".deleteFromCart").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_remove_my_cart_item",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#cart-" + id).hide();
                $(".total_result").html(msg.total);
                $("#result_reponse_cart").html(msg.message);
                autrefreshCart();//refresh temp cart - inner
                autrefreshCartCount();//refresh total
            },
            error: function (msg) {
                $("#result_reponse_cart").html('<div class="alert-danger">Something was wrong</div>');
            }
        });
    });
    //remove my carts
    $(".removemycart").click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_remove_my_cart",
            data: "action=1",
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#result_reponse_cart").html(msg.message);
                window.location.reload();
            },
            error: function (msg) {
                $("#result_reponse_cart").html('<div class="alert-danger">Something was wrong</div>');
            }
        });
    });


    //product
    $("#viewallsearchresult").click(function () {
        var search_keyname = $("#search_keyname").val();
        if (search_keyname != "") {
            window.location = BASE_URL + "/search?sq=" + search_keyname;
        } else {
            return false;
        }
    });
    $("#search_btns").click(function () {
        var search_keyname = $("#search_keyname").val();
        if (search_keyname != "") {
            window.location = BASE_URL + "/search?sq=" + search_keyname;
        } else {
            return false;
        }
    });
    //top search
    $("#search_keyname").keyup(function () {
        var keyname = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_quick_search",
            data: "keyname=" + keyname,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#search_child_results").html(msg.message);
            },
            error: function (msg) {
                $("#search_child_results").html('Something was wrong.');
            }
        });
    });
    //clear all filteration
    $("#clearallfilter").click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_filter",
            data: "clear=1",
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //clear search history
    $("#clearallsearch").click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_search",
            data: "clear=1",
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //filter by color
    $(".filter_by_color").click(function () {
        var filter_by_color = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_filter_by_color",
            data: "filter_by_color=" + filter_by_color,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    //filter by size
    $(".filter_by_size").click(function () {
        var filter_by_size = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_filter_by_size",
            data: "filter_by_size=" + filter_by_size,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });
    //filter by tags
    $(".filter_by_tags").click(function () {
        var filter_by_tags = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_filter_by_tags",
            data: "filter_by_tags=" + filter_by_tags,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    $("#product_sort_by").change(function () {

        var product_sort_by = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_sort_by",
            data: "product_sort_by=" + product_sort_by,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //load product per/page
    $("#product_per_page").change(function () {
        var product_per_page = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_per_page",
            data: "product_per_page=" + product_per_page,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    //brand
    $("#brand_sort_by").change(function () {

        var brand_sort_by = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_brand_sort_by",
            data: "brand_sort_by=" + brand_sort_by,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //load brand per/page
    $("#brand_per_page").change(function () {
        var brand_per_page = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_brand_per_page",
            data: "brand_per_page=" + brand_per_page,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });
    ///
    //brand
    $("#offer_sort_by").change(function () {
        var offer_sort_by = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_offer_sort_by",
            data: "offer_sort_by=" + offer_sort_by,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //load offer per/page
    $("#offer_per_page").change(function () {
        var offer_per_page = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_offer_per_page",
            data: "offer_per_page=" + offer_per_page,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });
    ///search
    $(".search_by_size").click(function () {
        var search_by_size = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_search_by_size",
            data: "search_by_size=" + search_by_size,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    $(".search_by_color").click(function () {
        var search_by_color = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_search_by_color",
            data: "search_by_color=" + search_by_color,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });
    //filter by tags
    $(".search_by_tags").click(function () {
        var search_by_tags = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_search_by_tags",
            data: "search_by_tags=" + search_by_tags,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    $("#search_sort_by").change(function () {

        var search_sort_by = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_search_sort_by",
            data: "search_sort_by=" + search_sort_by,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });

    });
    //load product per/page
    $("#search_per_page").change(function () {
        var search_per_page = $(this).val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_search_per_page",
            data: "search_per_page=" + search_per_page,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });
    //get price range
    $(".rangeprice").click(function () {
        var rangeprice = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_product_price_range",
            data: "rangeprice=" + rangeprice,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    $(".search_rangeprice").click(function () {
        var search_rangeprice = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_search_price_range",
            data: "search_rangeprice=" + search_rangeprice,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                window.location.reload();
            },
            error: function (msg) {
                alert('Error Found');
            }
        });
    });

    //end product
    $('.form-control').focus(function () {
        var id = $(this).attr("id");
        $("#" + id + "-error").hide();
    });
    //subscribe newsletter
    $("#subscribeBtn").click(function (e) {
        var newsletter_email = $("#newsletter_email").val();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_newsletter_subscribe",
            data: "newsletter_email=" + newsletter_email,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#newslettermsg").html(msg.message);
            },
            error: function (msg) {
                $("#newslettermsg").html('<label for="email" class="error">Something was wrong</label>');
            }
        });
    });
    //add to cart - single
    $(".addtocartsingle").click(function () {

        var id = $(this).attr("id");
        $("#responseMsg-" + id).show();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_addtocart_single",
            data: "product_id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                autrefreshCart();//refresh temp cart - inner
                autrefreshCartCount();//refresh total
                if ( msg.is_cart_popup==1 ) {
                    $("#spancartbox").html(msg.message);
                    $("#modalDefaultBox").modal("show");//show modal
                } else {
                    toastr.success(msg.message);
                }
                PushDataLayers("add_to_cart",msg.payload);
            },
            error: function (msg) {
                $("#responseMsg-" + id).html('<div class="alert-error">Something was wrong</div>');
                //$("#responseMsg-"+id).fadeIn(1000);
            }
        });
    });

    //delete remove item from temp cart
    $(".deleteFromTemp").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/deleteTempOrdersAjax",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                autrefreshCart();//refresh temp cart - inner
                autrefreshCartCount();//refresh total
                PushDataLayers("remove_from_cart",msg.payload);
            },
            error: function (msg) {
                //$("#tt-badge-cart").html('0');
            }
        });
    });


    //load quick view modal for product
    $(".loadquickviewmodal").click(function (e) {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_quickview",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                if (msg.status == 200) {
                    $("#loadmoalcontent").html(msg.message);
                    $("#ModalquickViewProd").modal("show");
                    new LazyLoad();
                    $(".tt-mobile-product-slider").slick({
                        dots: false,
                        arrows: true,
                        infinite: false,
                        speed: 300,
                        slidesToShow: 1,
                        adaptiveHeight: true,
                        lazyLoad: 'progressive',
                    });
                    $('.slider-nav').slick({
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        asNavFor: '.tt-mobile-product-slider',
                        dots: false,
                        centerMode: true,
                        focusOnSelect: true,
                        prevArrow: false,
                        nextArrow: false
                    });

                    //quantity
                    $('.tt-input-counter').find('.minus-btn, .plus-btn').on('click', function (e) {
                        var $input = $(this).parent().find('input');
                        var count = parseInt($input.val(), 10) + parseInt(e.currentTarget.className === 'plus-btn' ? 1 : -1, 10);
                        $input.val(count).change();
                    });


                    //quick add to cart
                    $(".addtocartQuick").click(function () {
                        var product_id = $(this).attr("id");
                        var quantity = $("#attribute_qty_" + product_id).val();
                        var price = $("#unit_price_" + product_id).val();

                        if ($("#size_attribute_" + product_id).is(":visible") == true && $("#size_attribute_" + product_id).val() == "0") {
                            $("#quickresponse-" + product_id).html("<div class='alert-danger'>Please choose your size</div>");
                            return false;
                        }
                        if ($("input[name='color_attribute_" + product_id + "']").is(":visible") == true && $("input[name='color_attribute_" + product_id + "']").is(":checked") == false) {
                            $("#quickresponse-" + product_id).html("<div class='alert-danger'>Please choose your color</div>");
                            return false;
                        }
                        //size
                        if ($("#size_attribute_" + product_id).is(":visible") == true) {
                            var size_attribute = $("#size_attribute_" + product_id).val();
                        } else {
                            var size_attribute = "";
                        }
                        //color
                        if ($("input[name='color_attribute_" + product_id + "']").is(":visible") == true) {
                            var color_attribute = $("input[name='color_attribute_" + product_id + "']:checked").val();
                        } else {
                            var color_attribute = "";
                        }

                        var option_id = $("#option_id").val();
                        //
                        var child_option = [];
                        $.each($('.child_option:checked'), function () {
                            child_option.push($(this).val());
                        });

                        $.ajax({
                            type: "GET",
                            url: BASE_URL + "/ajax_quickview_addtocart",
                            data: "product_id=" + product_id + "&quantity=" + quantity + "&size_attribute=" + size_attribute + "&color_attribute=" + color_attribute + "&price=" + price + "&option_id=" + option_id + "&child_option=" + child_option,
                            dataType: "json",
                            cache: false,
                            processData: false,
                            success: function (msg) {
                                autrefreshCart();//refresh temp cart - inner
                                autrefreshCartCount();//refresh total
                                if (msg.status == 400) {
                                    $("#quickresponse-" + product_id).html(msg.message);
                                } else {
                                    if ( msg.is_cart_popup==1 ) {
                                        $("#spancartbox").html(msg.message);
                                        $("#modalDefaultBox").modal("show");//show modal
                                    } else {
                                        toastr.success(msg.message);
                                    }
                                    $("#ModalquickViewProd").modal("hide");
                                }
                            },
                            error: function (msg) {
                                $("#quickresponse-" + product_id).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                            }
                        });
                    });

                    //send inquiry
                    $(".btncartInquiryQuick").click(function () {
                        var product_id = $(this).attr("id");
                        $("#loading-gif-" + product_id).show();
                        var inquiry_name = $("#inquiry_name" + product_id).val();
                        var inquiry_email = $("#inquiry_email" + product_id).val();
                        var inquiry_mobile = $("#inquiry_mobile" + product_id).val();
                        var inquiry_message = $("#inquiry_message" + product_id).val();
                        $.ajax({
                            type: "GET",
                            url: BASE_URL + "/ajax_post_inquiry",
                            data: "product_id=" + product_id + "&inquiry_name=" + inquiry_name + "&inquiry_email=" + inquiry_email + "&inquiry_mobile=" + inquiry_mobile + "&inquiry_message=" + inquiry_message,
                            dataType: "json",
                            cache: false,
                            processData: false,
                            success: function (msg) {
                                $("#quickresponse-" + product_id).html(msg.message);
                                $("#loading-gif-" + product_id).hide();
                            },
                            error: function (msg) {
                                $("#quickresponse-" + product_id).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                                $("#loading-gif-" + product_id).hide();
                            }
                        });
                    });
                    //get price by size id
                    $(".size_attribute").change(function () {

                        var size_id = $(this).val();
                        var prodids = $(this).attr("id");
                        var prodids_split = prodids.split("_"); //product_id = prodids_split[2]
                        $('#loader-gif-' + prodids_split[2]).show();
                        $.ajax({
                            type: "GET",
                            url: BASE_URL + "/ajax_quickview_getPrice_BySize",
                            data: "product_id=" + prodids_split[2] + "&size_id=" + size_id,
                            dataType: "json",
                            cache: false,
                            processData: false,
                            success: function (msg) {
                                $("#display_price_" + prodids_split[2]).html(msg.message);
                                $("#unit_price_" + prodids_split[2]).val(msg.message);
                                if (msg.old_price != "0") {
                                    $("#display_oldprice_" + prodids_split[2]).html(msg.old_price);
                                } else {
                                    $("#oldprices" + prodids_split[2]).hide();
                                }
                                getColorBySize(prodids_split[2], size_id);
                            },
                            complete: function () {
                                $('#loader-gif-' + prodids_split[2]).hide();
                            },
                            error: function (msg) {
                                $("#quickresponse-" + prodids_split[2]).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                            }
                        });
                    });
                    //get price by color
                    $(".color_attribute").click(function () {
                        var color_id = $(this).val();
                        var prodids = $(this).attr("id");
                        var prodids_split = prodids.split("_"); //product_id = prodids_split[2]
                        $.ajax({
                            type: "GET",
                            url: BASE_URL + "/ajax_quickview_getPrice_ByColor",
                            data: "product_id=" + prodids_split[2] + "&color_id=" + color_id,
                            dataType: "json",
                            cache: false,
                            processData: false,
                            success: function (msg) {
                                $("#display_price_" + prodids_split[2]).html(msg.message);
                                if (msg.old_price != "0") {
                                    $("#display_oldprice_" + prodids_split[2]).html(msg.old_price);
                                } else {
                                    $("#oldprices" + prodids_split[2]).hide();
                                }
                                $("#unit_price_" + prodids_split[2]).val(msg.message);
                            },
                            error: function (msg) {
                                $("#quickresponse-" + prodids_split[2]).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                            }
                        });
                    });

                } else {
                    $("#loadmoalcontent").html("Error-1");
                    $("#ModalquickViewProd").modal("show");
                }
            },
            error: function (msg) {
                $("#loadmoalcontent").html("Error-2");
                $("#ModalquickViewProd").modal("show");
            }
        });
    });

    //end quick modal for product
    function getColorBySize(product_id, size_id) {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_quickview_getColor_BySize",
            data: "product_id=" + product_id + "&size_id=" + size_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#color_box_" + product_id).html(msg.message);
                //get price by color
                $(".color_attribute").click(function () {
                    var color_id = $(this).val();
                    var prodids = $(this).attr("id");
                    var prodids_split = prodids.split("_"); //product_id = prodids_split[2]
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + "/ajax_quickview_getPrice_ByColor",
                        data: "product_id=" + prodids_split[2] + "&color_id=" + color_id,
                        dataType: "json",
                        cache: false,
                        processData: false,
                        success: function (msg) {
                            $("#display_price_" + prodids_split[2]).html(msg.message);
                            if (msg.old_price != "0") {
                                $("#display_oldprice_" + prodids_split[2]).html(msg.old_price);
                            } else {
                                $("#oldprices" + prodids_split[2]).hide();
                            }
                            $("#unit_price_" + prodids_split[2]).val(msg.message);
                        },
                        error: function (msg) {
                            $("#quickresponse-" + prodids_split[2]).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                        }
                    });
                });
                //end
            },
            error: function (msg) {
                $("#quickresponse-" + product_id).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    }

    ///////////////add to cart for details page ///////////////////////////////////////////////////

    //get price by size id
    $(".size_attr").change(function () {
        //remove all check options
        $('input:checked').removeAttr('checked');
        //end remove all checked options

        $("#color_attr").val('');
        $('#loader-gif').show();
        var size_id = $(this).val();
        var prodids = $(this).attr("id");
        var prodids_split = prodids.split("_"); //product_id = prodids_split[2]
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_details_getPrice_BySize",
            data: "product_id=" + prodids_split[2] + "&size_id=" + size_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#display_price").html(msg.message);
                let currency = $("#display_price").parent().next('del').text().split(' ')[0];
                $("#display_price").parent().next('del').html(currency+ ' ' + msg.old_price.toFixed(3));
                $("#other_currency_display_price").html(msg.otherPrice);
                $("#unit_price").val(msg.message);
                $(".sizeDetails").html(msg.details);
                $("#sizeDetails").html(msg.details);
                if (msg.old_price != "0") {
                    $("#display_oldprice").html(msg.old_price);
                } else {
                    $("#oldprices").hide();
                }
                //change quantity size attr
                //if (msg.quantity != "0") {
                    $("#quantity_attr").attr("size", msg.quantity);
                    $("#display_qty").html(msg.quantity);
                //}
                var custom_option_id = 3;
                getColorBySizeDetails(custom_option_id, prodids_split[2], size_id);

            },
            complete: function () {
                $('#loader-gif').hide();
            },
            error: function (msg) {
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });

    function getColorBySizeDetails(custom_option_id, product_id, size_id) {
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_details_getColor_BySize",
            data: "custom_option_id=" + custom_option_id + "&product_id=" + product_id + "&size_id=" + size_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#color_box").html(msg.message);
                //get price by color
                $(".color_attr").click(function () {
                    var color_id = $(this).val();
                    var prodids = $(this).attr("id");
                    var prodids_split = prodids.split("_"); //product_id = prodids_split[2]
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + "/ajax_quickview_getPrice_ByColor",
                        data: "product_id=" + prodids_split[2] + "&color_id=" + color_id,
                        dataType: "json",
                        cache: false,
                        processData: false,
                        success: function (msg) {
                            $("#display_price").html(msg.message);
                            $("#other_currency_display_price").html(msg.otherPrice);
                            if (msg.old_price != "0") {
                                $("#display_oldprice").html(msg.old_price);
                            } else {
                                $("#oldprices").hide();
                            }
                            $("#unit_price").val(msg.message);
                        },
                        error: function (msg) {
                            $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                        }
                    });
                });
                //end
            },
            error: function (msg) {
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    }

    ////add item to wish list
    $(".addtowishlist").click(function () {
        var product_id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_add_to_wish_list",
            data: "product_id=" + product_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#quickresponse").html(msg.message);
                PushDataLayers("add_to_wishlist",msg.payload);
            },
            error: function (msg) {
                $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });

    $(".addtowishlistquick").click(function () {
        var product_id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_add_to_wish_list",
            data: "product_id=" + product_id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#responseMsg-" + product_id).html(msg.message);
                PushDataLayers("add_to_wishlist",msg.payload);
            },
            error: function (msg) {
                $("#responseMsg-" + product_id).html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });
    //remove from wish
    $(".removeitem").click(function () {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_remove_wish_list",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                $("#wishdiv" + id).hide();
                $("#responseMsgwish").html(msg.message);
            },
            error: function (msg) {
                $("#responseMsgwish").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
            }
        });
    });
    //post an inquiry
    $(".btncartInquiry").click(function () {
        $("#loading-gif").show();
        $(".is-invalid").removeClass('is-invalid');
        $(".invalid-feedback").remove();
        var product_id = $(this).attr("id");
        var inquiry_name = $("#inquiry_name").val();
        var inquiry_email = $("#inquiry_email").val();
        var inquiry_mobile = $("#inquiry_mobile").val();
        var inquiry_message = $("#inquiry_message").val();
        if(!inquiry_email){
            $("#inquiry_email").addClass('is-invalid').after(`
            <div class="invalid-feedback">Please enter a valid email</strong>
            `);

        $("#loading-gif").hide();
            return;
        }
        $.ajax({
            type: "GET",
            url: BASE_URL + "/ajax_post_inquiry",
            data: "product_id=" + product_id + "&inquiry_name=" + inquiry_name + "&inquiry_email=" + inquiry_email + "&inquiry_mobile=" + inquiry_mobile + "&inquiry_message=" + inquiry_message,
            dataType: "json",
            cache: false,
            processData: false,
            success: function (msg) {
                console.log(msg.message)
                $(".btncartInquiry").before(msg.message);
                setTimeout(function(){
                    $(".btncartInquiry").closest('.alert').remove()
                }, 5000)
                $("#loading-gif").hide();
            },
            error: function (msg) {
                $(".btncartInquiry").before("<div class='alert-danger'>Oops! There was something wrong.</div>");
                 setTimeout(function(){
                    $(".btncartInquiry").closest('.alert').remove()
                }, 5000)
                $("#loading-gif").hide();
            }
        });
    });

	// $(".cbx").change(function () {
	// 	var checkedValue = $('#cbx_tac').is(":checked") ? true : false;
	// 	if (checkedValue) {
	// 		$("#tac_modal").modal('toggle');
	// 	}
	// })
	var defaultCheck = $('.cbx').is(":checked") ? true : false;
	if (defaultCheck) {
		$('.confirmcheckbutton').prop('disabled', false);
	}
	$(".tac-message-error").css("display", "none");
	$(".cbx").on("change", function () {
		var checkedValue = $('#cbx_tac').is(":checked") ? true : false;
		if (checkedValue) {
			$("#tac_modal").modal('toggle');
			$(".tac-message-error").css("display", "none");
		} else {
			$('.confirmcheckbutton').prop('disabled', true);
			$('.regsiterUserAc').prop('disabled', true);
			$(".tac-message-error").css("display", "block");
		}

	});
	$("#close_tac_box").on('click', function () {
		$(".cbx").prop('checked', false);
		$(".tac-message-error").css("display", "block");
	});

	$("#tac_accept_btn").click(function () {
		var checkedValue = $('#cbx_tac').is(":checked") ? true : false;
		if (checkedValue) {
			$("#tac_modal").modal('toggle');
			$('.confirmcheckbutton').prop('disabled', false);
			$('.regsiterUserAc').prop('disabled', false);
			$(".tac-message-error").css("display", "none");
		}

	});

	//parseFloat($('.tt-price').text()).toFixed(3);

});


function PushDataLayers(eventName,pitems){
    dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
    dataLayer.push({
        event: eventName,
        ecommerce: {
            items: [pitems]
        }
    });

}

function cartAreaRefresh(){
    var areaid = $(".area_checkoutcart").val();
    var stateid = $("#state_checkout").val();
    var countryid = $("#country_checkout").val();
    var totalprice = $("#checkout_totalprice").val();
    var bundleDiscount = $("#checkout_bundleDiscount").val();


    $.ajax({
        type: "GET",
        url: BASE_URL + "/ajax_get_area_delivery",
        data: "t=cart&areaid=" + areaid + "&totalprice=" + totalprice + "&stateid=" + stateid + "&countryid=" + countryid + "&bundleDiscount=" + bundleDiscount,
        dataType: "json",
        cache: false,
        processData: false,
        success: function (msg) {
            $("#checktotalbox").html(msg.message);
        },
        error: function (msg) {
            $("#checktotalbox").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
        }
    });
}

    $(document).ready(function(){
        function formatState (state) {
          if (!state.id) {
            return state.text;
          }

          var baseUrl =  window.location.protocol + "//" + window.location.host;
          let img = baseUrl+'/uploads/no-image.png'
          if (state.image) {
           img = baseUrl+'/uploads/product/thumb/'+ state.image
          }
          var $state = $(
            '<span><img src="' + img + '" class="w-100px" /> &ensp;' + state.text + '</span>'
          );
          console.log(state)
          return $state;
        };
        const pathArray = window.location.pathname.split("/");
        var alocale = pathArray[1];
        console.log({pathArray})
         $("#searchPro").on('select2:select', function(e){
            var data = e.params.data;
            window.location = BASE_URL+"/details/"+data.id+"/"+data.slug;
         })
         $("#searchPro").select2({
          templateResult: formatState,
          placeholder: alocale == 'en' ? "Search" : "إبحث",
          ajax: {
           url: BASE_URL + "/search",
           type: "get",
           dataType: 'json',
           delay: 250,
           data: function (params) {
            return {
              sq: params.term // search term
            };
           },
           processResults: function (response) {

             return {
                results: response.map((value, index) => {
                  return {
                      'id': value.id,
                      'text': value['title_'+alocale],
                      'slug': value.slug,
                      'image': value.image
                  }
              })
             };
           },
           cache: true
          }
         });
});

