<!-- Latest jQuery -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/jquery-3.6.0.min.js')}}"></script>
<!-- popper min js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/popper.min.js')}}"></script>
<!-- Latest compiled and minified Bootstrap -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- owl-carousel min js  -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/owlcarousel/js/owl.carousel.min.js')}}"></script>
<!-- magnific-popup min js  -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/magnific-popup.min.js')}}"></script>
<!-- waypoints min js  -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/waypoints.min.js')}}"></script>
<!-- parallax js  -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/parallax.js')}}"></script>
<!-- countdown js  -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/jquery.countdown.min.js')}}"></script>
<!-- imagesloaded js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/imagesloaded.pkgd.min.js')}}"></script>
<!-- isotope min js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/isotope.min.js')}}"></script>
<!-- jquery.dd.min js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/jquery.dd.min.js')}}"></script>
<!-- slick js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/slick.min.js')}}"></script>
<!-- elevatezoom js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/jquery.elevatezoom.js')}}"></script>
<!-- scripts js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets_rtl' ) . '/js/scripts.js')}}"></script>
<!-- scripts js -->
<script src="{{asset(( app()->getLocale() == "en" ? 'assets' : 'assets' ) . '/js/gulfweb.js?v1')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>

<script>
    BASE_URL = '';
    $("#custome_locale_select").on("change", function () {
        window.location.href = $(this).find('option:selected').val();
    });

    $("#lng_changer").on("change", function () {
        window.location.href = $(this).find('option:selected').val();
    });
    $('.cart-product-quantity').find('.minus-btn, .plus-btn').on('click',function(e) {
            console.log(e);
            var $input = $(this).parent().find('.qty');
            console.log($input);
            var count = parseInt($input.val(), 10) + parseInt(e.currentTarget.className === 'plus plus-btn' ? 1 : -1, 10);
            console.log(e.currentTarget.className);
            console.log(count);
            $input.val(count).change();
        });
    $('.cart-product-quantity').find(".qty").change(function() {
            var _ = $(this);
            var min = 1;
            var val = parseInt(_.val(), 10);
            var max = parseInt(_.attr('size'), 10);
            val = Math.min(val, max);
            val = Math.max(val, min);
            _.val(val);
        })
        .on("keypress", function( e ) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });
    function initShort(){
        $(".options-color").click(function () {
            //remove all check options
            $('input:checked').removeAttr('checked');
            //end remove all checked options

            var product_id = $("#product_id").val();
            var colorid = $(this).attr("id");
            $("#color_attr").val(colorid);
            $(this).parent().parent().find('li').each(function () {
                $(this).removeClass('active');
            });
            $('#li-' + colorid).addClass('active');
            if ($("#size_attr_" + product_id).is(":visible") == true) {
                var size_id = $("#size_attr_" + product_id).val();
            } else {
                var size_id = "";
            }
        });
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
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + "/ajax_details_getColor_BySize",
                        data: "custom_option_id=" + custom_option_id + "&product_id=" + prodids_split[2] + "&size_id=" + size_id,
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

                },
                complete: function () {
                    $('#loader-gif').hide();
                },
                error: function (msg) {
                    $("#quickresponse").html("<div class='alert-danger'>Oops! There was something wrong.</div>");
                }
            });
        });
    }
    $(document).ready(function(){
        initShort();
    });
    $(document).ajaxComplete(function () {
        initShort();
    });

</script>

<!-- google analytics -->
@if ($settingInfo->google_analytics){!! $settingInfo->google_analytics !!}@endif
<!--facebook pixel -->
@if ($settingInfo->facebook_pixel){!! $settingInfo->facebook_pixel !!}@endif
