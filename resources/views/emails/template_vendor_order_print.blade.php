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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	
<style type="text/css">
/* body */
body{margin:50px 0 100px 0; font:13px Arial, Helvetica, sans-serif; line-height:23px; color:#333;}

/* Animaiton */
:hover{transition:ease-in 0.2s; -moz-transition:ease-in 0.2s; -o-transition:ease-in 0.2s; -webkit-transition:ease-in 0.2s;}

/* float */
.left{float:<?php echo $align;?>;}
.right{float:<?php echo $oalign;?>;}}

/* alignment */
.text-right{text-align:<?php echo $oalign;?>;}
.text-left{text-align:<?php echo $align;?>;}
.text-center{text-align:center;}
.text-justify{text-align:justify;}
strong{font-weight:bold;}

/* clear */
.clear{clear:both;}
.clear5x{clear:both; height:5px;}
.clear10x{clear:both; height:10px;}
.clear15x{clear:both; height:15px;}
.clear20x{clear:both; height:20px;}
.clear30x{clear:both; height:30px;}
.clear40x{clear:both; height:40px;}
.clear50x{clear:both; height:50px;}
.clear60x{clear:both; height:60px;}

.header_date{width:45%;}
.customer{width:45%;}
/* table */
table{border-bottom:solid 1px #999; border-<?php echo $align;?>:solid 1px #999; direction:<?php echo $dir;?>;}
table td{padding:5px 10px; border-top:solid 1px #999; border-<?php echo $oalign;?>:solid 1px #999;}

/* Main */
.container{width:700px; margin:20px auto;}
.header{width:100%;}
.logo{float:<?php echo $align;?>;}
.header_date, .customer{float:<?php echo $oalign;?>; direction:<?php echo $dir;?>;}

.payment{width:280px; float:<?php echo $align;?>;}
.payment td:first-child{background-color:#f3f3f3; font-weight:bold; width:110px;}
.pro_table{width:100%; overflow-x:scroll;}
.pro_table tr:first-child{font-weight:bold; text-align:center; background-color:#f3f3f3;}
.pro_table td:nth-child(1), .pro_table td:nth-child(2), .pro_table td:nth-child(3), .pro_table td:nth-child(5){text-align:center;}

.headertd{min-width:70px !important;}

/*iPod, Tablet Devices*/	
@media all and (max-width: 999px) and (min-width: 480px) {}


/*Mobile Devices.*/		
@media all and (max-width: 480px) and (min-width: 320px){
	
.container{width:90%; margin:20px auto;}
.header{width:100%; text-align: center;}
.logo{float:none; margin: 0 0 20px 0;}
.header_date, .customer{float:none; direction:<?php echo $dir;?>;}

.payment{width:100%; margin: 20px 0 0 0;}
.pro_table{width:100%; display: block; overflow-x: auto;  white-space: nowrap;}

.clear50x{clear:both; height:30px;}
	
}
	</style>
</head>
<body onLoad="window.print()">   
<!-- onLoad="window.print()"-->
<div class="container">
			
		<div class="clear10x"></div>
			{!!$orderDetails!!}
	    <div class="clear10x"></div>
	        {!!$paymentDetails!!}
        <div class="clear"></div>
		<p class="text-center"><em>{{__('webMessage.thankyouforshopping')}}</em></p>	
      <div class="clear"></div>
	</div>
</body>
</html>