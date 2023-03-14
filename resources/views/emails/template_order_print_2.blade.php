@php
    $settingInfo = App\Http\Controllers\webController::settings();
    if(app()->getLocale()=="en"){
    $strLang="en";
    $dir='ltr';
    $align = 'left';
    $oalign = 'right';
    }else{
    $strLang="ar";
    $dir='rtl';
    $align = 'right';
    $oalign = 'left';
    }
@endphp
        <!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <style type="text/css">
        .pro_table {
            width: 100%;
            overflow-x: scroll;
        }

        .pro_table tr:first-child {
            font-weight: bold;
            text-align: center;
            background-color: #f3f3f3;
            height: 40px;
            font-family: Tahoma, Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .pro_table td:nth-child(1), .pro_table td:nth-child(2), .pro_table td:nth-child(3), .pro_table td:nth-child(5) {
            text-align: center;
            border: 1px #CCCCCC solid;
            padding: 5px;
        }

        .pro_table b {
            font-weight: bold;
            font-size: 12px;
        }

        .pro_table h5 {
            font-family: Tahoma, Arial, Helvetica, sans-serif;
        }

        .payment {
            width: 100%;
            float: <?php echo $align;?>;
        }

        .payment td:first-child {
            font-weight: bold;
            font-family: Tahoma, Arial, Helvetica, sans-serif;
            width: 120px;
            font-size: 12px;
            text-align: <?php echo $align;?>;
            padding: 5px;
            background-color: #f3f3f3;
            height: 24px;
        }

        .company {
            width: 100%;
            float: <?php echo $oalign;?>;
        }

        .company td:first-child {
            font-weight: bold;
            font-family: Tahoma, Arial, Helvetica, sans-serif;
            width: 70px;
            font-size: 12px;
            text-align: <?php echo $oalign;?>;
            padding: 5px;
            background-color: #f3f3f3;
            height: 30px;
        }

        .customers {
        }

        .customers b {
            font-weight: bold;
            font-size: 12px;
        }

        .orderDetails table {
            width: 100%;
            border-bottom: solid 1px #999;
            border- <?php echo $align;?>: solid 1px #999;
            direction: <?php echo $dir;?>;
        }

        .orderDetails table td {
            padding: 5px 10px;
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .orderDetails table th {
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .companytable table {
            width: 100%;
            border-bottom: solid 1px #999;
            border- <?php echo $align;?>: solid 1px #999;
            direction: <?php echo $dir;?>;
        }

        .companytable table td {
            padding: 5px 10px;
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .companytable table th {
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .invoicetable table {
            width: 100%;
            border-bottom: solid 1px #999;
            border- <?php echo $align;?>: solid 1px #999;
            direction: <?php echo $dir;?>;
        }

        .invoicetable table td {
            padding: 5px 10px;
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .invoicetable table th {
            border-top: solid 1px #999;
            border- <?php echo $oalign;?>: solid 1px #999;
        }

        .headertd {
            background-color: #333333;
            color: #FFFFFF;
            min-width: 70px !important;
        }
        .continer {
            width: 800px;
        }
    </style>
</head>
<body onLoad="@if( ! request()->has('driverSystem')) window.print() @endif">
<div class="driverSystem continer">
<table border="0" width="100%" cellspacing="0" cellpadding="0"
       style="font-family:Tahoma,Arial, Helvetica, sans-serif; font-size:12px;">
    <tr>
        <td>
            <!-- top header start -->
            <table border="0" width="100%" style="border-collapse: collapse" dir="{{$dir}}">
                <tr>
                    <td align="<?php echo $align;?>">
                        @if(!empty($settingInfo->emaillogo))
                            <img src="{{url('uploads/logo/'.$settingInfo->emaillogo)}}" style="max-height:97px;"
                                 alt="{{$settingInfo->name_en}}" border="0"/>
                        @endif
                    </td>
                    <td align="<?php echo $oalign;?>">
                        <img border="0" src="{{url('uploads/invoice_03.png')}}" width="209" height="97"></td>
                </tr>
            </table>
            <!-- top header end -->
        </td>
    </tr>
    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">
            <hr color="#DFE3EA">
        </td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">&nbsp;</td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>"><p><b
                        style="font-size:14px;">{{trans('webMessage.orderdetails')}}</b></p></td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">
            <!-- order status start -->
            <table border="0" width="100%" cellpadding="0"
                   style="border-collapse: collapse;font-family:Tahoma,Arial, Helvetica, sans-serif; font-size:12px;"
                   dir="{{$dir}}">
                <tr>
                    <td valign="top">
                        <div style="width:100%;float:<?php echo $align;?>;"
                             class="invoicetable">{!!$invoiceDetails!!}</div>

                    </td>

                </tr>
            </table>
            <!-- order status end -->
        </td>

    </tr>
    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">&nbsp;</td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>" style="border-style: none;border:1px #DFE3EA solid;"
            class="orderDetails">
            <!--Order listing start here -->
        {!!$orderDetails!!}
        <!-- Order listing end here -->
        </td>
    </tr>
    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">&nbsp;</td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">

            <table border="0" width="100%" cellpadding="0"
                   style="border-collapse: collapse;font-family:Tahoma,Arial, Helvetica, sans-serif; font-size:14px;"
                   dir="{{$dir}}">
                <tr>
                    <td valign="top">
                        <p><b style="font-size:14px;">{{__('webMessage.customerdetails')}}</b></p>
                        {!!$customerDetails!!}
                    </td>
                    <td valign="top">
                        @if(!empty($paymentDetails))
                            <p><b style="font-size:14px;">{{__('webMessage.transactiondetails')}}</b></p>
                            {!!$paymentDetails!!}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">&nbsp;</td>
    </tr>
    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">
            <hr color="#DFE3EA">
        </td>
    </tr>

    @if($strLang=="en" && !empty($settingInfo->order_note_en))
        <tr>
            <td dir="{{$dir}}" align="<?php echo $align;?>"><i>{!!$settingInfo->order_note_en!!}</i></td>
        </tr>
        <tr>
            <td dir="{{$dir}}" align="<?php echo $align;?>">
                <hr color="#DFE3EA">
            </td>
        </tr>
    @endif
    @if($strLang=="ar" && !empty($settingInfo->order_note_ar))
        <tr>
            <td dir="{{$dir}}" align="<?php echo $align;?>"><i>{!!$settingInfo->order_note_ar!!}</i></td>
        </tr>
        <tr>
            <td dir="{{$dir}}" align="<?php echo $align;?>">
                <hr color="#DFE3EA">
            </td>
        </tr>
    @endif

    <tr>
        <td dir="{{$dir}}" align="<?php echo $align;?>">
            <!-- footer start -->
            <table border="0" width="100%" cellspacing="0" cellpadding="0"
                   style="font-family:Tahoma,Arial, Helvetica, sans-serif; font-size:12px;">
                <tr>
                    @if(!empty($settingInfo->invoice_qrcode))
                        <td>
                            <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{ route('downloadApp').'?qr' }}&choe=UTF-8"  class="logo" style="max-height:100px; margin-left: 25px; margin-right: 25px;" />

                        </td>
                    @endif
                    <td dir="{{$dir}}" align="center">
                        <p>
                            @if($settingInfo->name_en && app()->getLocale()=="en"){!!$settingInfo->name_en!!}@endif
                            @if($settingInfo->name_ar && app()->getLocale()=="ar"){!!$settingInfo->name_ar!!}@endif
                            <br>
                            <small>
                                {{$settingInfo['address_'.$strLang]}}
                                @if(!empty($settingInfo->phone))<br>{{$settingInfo->phone}}@endif
                                @if(!empty($settingInfo->mobile)),{{$settingInfo->mobile}}@endif
                                @if(!empty($settingInfo->email))<br>{{$settingInfo->email}}@endif
                            </small>
                        </p>
                    </td>
                </tr>
            </table>
            <!-- footer end -->
        </td>
    </tr>
    <tr>
        <td align="center">
            <p class="text-center"><em>{{__('webMessage.thankyouforshopping')}}</em></p></td>
    </tr>

</table>
</div>
</body>
</html>