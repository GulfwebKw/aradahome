<?php

namespace App\Http\Controllers\v1;

use App\Currency;
use App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\webCartController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Settings;
use App\Slideshow;
use App\Banner;
use App\Section;
use App\Product;
use App\ProductAttribute;
use App\ProductCategory;
//option
use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\ProductOptionsCustomChosen;

use App\Categories;
use App\ProductGallery;
use App\Warranty;
use App\SinglePages;
use App\Size;
use App\Color;
use App\OrdersTemp;
use App\Orders;
use App\OrdersDetails;
use App\OrdersTrack;
use App\OrdersTempOption;
use App\OrdersOption;
use App\Faq;
use App\Newsletter;
use App\Subjects;
use App\Brand;
use App\Contactus;
use App\CustomersWish;
use App\CustomersAddress;
use App\User;
use App\ProductReview;
use App\Coupon;
use App\Country;
use App\State;
use App\Area;
use App\NotificationEmails;
use App\Transaction;
use App\Mail\SendGrid;
use App\Mail\SendGridOrder;
use Curl;
use Mail;
use DB;

//rules
use App\Rules\Name;
use App\Rules\Mobile;

class apiCartController extends Controller
{
	public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;


	//order confirmation
	public function orderConfirm(Request $request)
	{
		$tempid = 0;
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$settingInfo = Settings::where("keyname", "setting")->first();
		//get cusatomer ID
		$customer_id = 0;
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$userDetails = User::where('api_token', $request->bearerToken())->first();
			if (!empty($userDetails->id)) {
				$customer_id = $userDetails->id;
			}
		}

		if (empty($request->temp_uniqueid)) {
			$success['data'] = trans('webMessage.tempidmissing');
			return response()->json($success, $this->failedStatus);
		} else {
			$tempid = $request->temp_uniqueid;
		}
		$tempOrders = self::loadTempOrders($tempid);
		if (empty($tempOrders) || count($tempOrders) == 0) {
			$success['data'] = trans('webMessage.yourcartisempty');
			return response()->json($success, $this->failedStatus);
		}

		//check quantity exiot or not
		$tempQuantityExist = self::isQuantityExistForOrder($tempid);
		if (empty($tempQuantityExist)) {
			$success['data'] = trans('webMessage.oneoftheitemqtyexceeded');
			return response()->json($success, $this->failedStatus);
		}

		//check fields
		if (empty($request->name) and $settingInfo->validate_cust_name) {
			$success['data'] = trans('webMessage.name_required');
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($request->name) && strlen($request->name) > 150) {
			$success['data'] = trans('webMessage.max_name_chars_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->mobile) and $settingInfo->validate_cust_mob) {
			$success['data'] = trans('webMessage.mobile_required');
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($request->mobile)) {
			$isValidMobile = Common::checkMobile($request->mobile);
			if (empty($isValidMobile)) {
				$success['data'] = trans('webMessage.mobile_invalid');
				return response()->json($success, $this->failedStatus);
			}
		}
		if (empty($request->country)) {
			$success['data'] = trans('webMessage.country_required');
			return response()->json($success, $this->failedStatus);
		}
//		if (empty($request->state) ) {
//			$success['data'] = trans('webMessage.state_required');
//			return response()->json($success, $this->failedStatus);
//		}
		if (empty($request->area) and $settingInfo->validate_cust_area) {
			$success['data'] = trans('webMessage.area_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->block) and $settingInfo->validate_cust_block ) {
			$success['data'] = trans('webMessage.block_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->street) and $settingInfo->validate_cust_st ) {
			$success['data'] = trans('webMessage.street_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->house) and $settingInfo->validate_cust_hno) {
			$success['data'] = trans('webMessage.house_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->payment_method)) {
			$success['data'] = trans('webMessage.payment_method_required');
			return response()->json($success, $this->failedStatus);
		}

		//check min order amount
		$totalAmtchk = self::getTotalCartAmount($request->temp_uniqueid);
		if (!empty($settingInfo->min_order_amount) && !empty($totalAmtchk) && $settingInfo->min_order_amount >  $totalAmtchk) {
			$success['data'] = trans('webMessage.minimumordermessage') . ' ' . number_format($settingInfo->min_order_amount, 3) . ' ' . \App\Currency::default();
			return response()->json($success, $this->failedStatus);
		}

		$expectedDate = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));


		$orderid      = strtolower($settingInfo->prefix) . $this->OrderserialNumber();
		$ordersid     = $this->OrderSidNumber();
		$orderDetails = new OrdersDetails;
		$uid = 0;
		if (!empty($customer_id)) {
			$orderDetails->customer_id  = $customer_id;
			$uid = $customer_id;
		}

        if (!empty($request->area)){
            $areaInfo = Country::where('id' , $request->area )->first();
            if ( empty($request->state) and $areaInfo != null){
                $stateInfo = Country::where('id' ,  $areaInfo->parent_id )->first();
                if ( $stateInfo != null and $stateInfo->parent_id == 0 ){
                    $request->merge(['state' => $areaInfo->id]);
                } elseif ( $stateInfo != null and $stateInfo->parent_id != 0 ) {
                    $request->merge(['state' => $stateInfo->id]);
                }
            }
            if ( empty($request->country) and $areaInfo != null){
                $stateInfo = Country::where('id' ,  $areaInfo->parent_id )->first();
                if ( $stateInfo != null and $stateInfo->parent_id == 0 ){
                    $request->merge(['country' => $stateInfo->id]);
                } elseif ( $stateInfo != null and $stateInfo->parent_id != 0 ) {
                    $request->merge(['country' => $stateInfo->parent_id]);
                }
            }
        }
		$orderDetails->order_id     = $orderid;
		$orderDetails->sid          = $ordersid;
		$orderDetails->order_id_md5 = md5($orderid);
		$orderDetails->latitude     = !empty($request->latitude) ? $request->latitude : '';
		$orderDetails->longitude    = !empty($request->longitude) ? $request->longitude : '';
		$orderDetails->name         = !empty($request->name) ? $request->name : 'Guest';
		$orderDetails->email        = !empty($request->email) ? $request->email : '';
		$orderDetails->mobile       = !empty($request->mobile) ? $request->mobile : '';
		$orderDetails->country_id   = !empty($request->country) ? $request->country : '0';
		$orderDetails->state_id     = !empty($request->state) ? $request->state : '0';
		$orderDetails->area_id      = !empty($request->area) ? $request->area : '0';
		$orderDetails->block        = !empty($request->block) ? $request->block : '';
		$orderDetails->street       = !empty($request->street) ? $request->street : '';
		$orderDetails->avenue       = !empty($request->avenue) ? $request->avenue : '';
		$orderDetails->house        = !empty($request->house) ? $request->house : '';
		$orderDetails->floor        = !empty($request->floor) ? $request->floor : '';
		$orderDetails->landmark     = !empty($request->landmark) ? $request->landmark : '';
		$orderDetails->device_type  = $request->route('platform_prefix');
		$orderDetails->pay_mode	 = !empty($request->payment_method) ? $request->payment_method : '';
		//coupon 
		if (!empty($request->coupon_code)) {
			$orderDetails->is_coupon_used = 1;
			$orderDetails->coupon_code    = !empty($request->coupon_code) ? $request->coupon_code : '';
			$orderDetails->coupon_amount  = !empty($request->coupon_discount) ? $request->coupon_discount : '0';
			$orderDetails->coupon_free    = !empty($request->coupon_free) ? $request->coupon_free : '0';
		}
		//delivery charges
		$deliveryCharge = self::get_delivery_charge($request->area , $tempid , $request->is_express_delivery );
		$orderDetails->delivery_charges = !empty($request->coupon_free) ? 0 : $deliveryCharge;
		$orderDetails->strLang          = $strLang;
		$orderDetails->delivery_date    = $expectedDate;
		$orderDetails->save();
		//import temp order to order table
		$ordertxt_child = '';
		$subtotalprice = 0;
		$grandtotal = 0;
		$totalprice = 0;
		$orderOptions = '';
		foreach ($tempOrders as $tempOrder) {
			$productDetails = self::getProductDetails($tempOrder->product_id);
			if (!empty($tempOrder->size_id)) {
				$sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
				$sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
			} else {
				$sizeName = '';
			}
			if (!empty($tempOrder->color_id)) {
				$colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
				$colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
			} else {
				$colorName = '';
			}
			$orderOptions = self::getOptionsDtailsOrderBr($tempOrder->id);
			//deduct quantity
            $orderInventory1 = webCartController::deductQuantity($tempOrder->product_id, $tempOrder->quantity, $tempOrder->size_id, $tempOrder->color_id);
			$unitprice = $tempOrder->unit_price;
			$subtotalprice = $unitprice * $tempOrder->quantity;
			$title = $strLang == "en" ? $productDetails->title_en : $productDetails->title_ar;
			if (!empty($productDetails->sku_no)) {
				$skno = $productDetails->sku_no;
			} else {
				$skno = '';
			}

			$warrantyTxt = '';
			if (!empty($productDetails->warranty)) {
				$warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
				$warrantyTxt     = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
			}

			$ordertxt_child .= '<tr>
						<td><a href="' . url('details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . url('uploads/product/thumb/' . $productDetails['image']) . '" alt="' . $title . '" width="50"></a><br>' . $productDetails->item_code . '<br>' . $skno . '</td>
						<td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
						<td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
						<td align="center">' . $tempOrder->quantity . '</td>
						<td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
						</tr>';
			$orders = new Orders;
			$orders->oid       = $orderDetails->id;
			$orders->order_id  = $orderid;
			$orders->product_id = $tempOrder->product_id;
			$orders->size_id   = $tempOrder->size_id;
			$orders->color_id  = $tempOrder->color_id;
			$orders->unit_price = $tempOrder->unit_price;
			$orders->quantity  = $tempOrder->quantity;
			$orders->save();
			//add option
            $orderInventory2 = [];
			$tempOrderOptions = OrdersTempOption::where("oid", $tempOrder->id)->get();
			if (!empty($tempOrderOptions) && count($tempOrderOptions) > 0) {
				foreach ($tempOrderOptions as $tempOrderOption) {
                    $orderInventory2 = webCartController::changeOptionQuantity($tempOrderOption->product_id , 'd', $tempOrderOption->option_child_ids, $tempOrder->quantity); //deduct qty
					$OrderOption = new OrdersOption;
					$OrderOption->product_id       = $tempOrderOption->product_id;
					$OrderOption->oid              = $orders->id;
					$OrderOption->option_id        = $tempOrderOption->option_id;
					$OrderOption->option_child_ids = $tempOrderOption->option_child_ids;
					$OrderOption->save();
					//remove option
					$tempOrds = OrdersTempOption::find($tempOrderOption->id);
					$tempOrds->delete();
				}
			}
            $orderInventories = $tempOrderInventories = array_merge($orderInventory1,$orderInventory2);
            foreach ( $tempOrderInventories as $i => $orderInventory )
                if ( ! ( $orderInventory['q'] > 0 ) )
                    unset($orderInventories[$i]);
            $orders->inventory  =  json_encode($orderInventories);
            $orders->save();
			//remove temp record
			$tempOrd = OrdersTemp::find($tempOrder->id);
			$tempOrd->delete();

			//plus sub total price
			$totalprice += $subtotalprice;
		}

		$orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
						<tr>
						<td>' . trans('webMessage.image') . '</td>
						<td>' . trans('webMessage.details') . '</td>
						<td>' . trans('webMessage.unit_price') . '</td>
						<td>' . trans('webMessage.quantity') . '</td>
						<td>' . trans('webMessage.subtotal') . '</td>
						</tr>';
		$orderDetailsTxt .= $ordertxt_child;

		$orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
		//show discount if available but not free delivery
		if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
			$orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
			$totalprice = $totalprice - $orderDetails->coupon_amount;
		}
		if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
			$orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
		}

		if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
			$deliveryCharge  = $orderDetails->delivery_charges;
			$orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
			$totalprice = $totalprice + $deliveryCharge;
		}
		$orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
		$orderDetailsTxt .= '</table>';

		if ($settingInfo->invoice_template == 1) {
			$invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
			$invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderid . '</td></tr>';
			$invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
			if (!empty($orderDetails->is_paid)) {
				$txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
			} else {
				$txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
			}
			$invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
			$invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';
			$invoiceDetailsTxt .= '</table>';
			$invoiceDetailsTxt .= '</table>';
		} else {
			$invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
			$invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.orderid') . '</strong></td><td>' . $orderid . '</td><td><strong>' . trans('webMessage.paymentmethod') . '</strong></td><td>' . $orderDetails->pay_mode . '</td></tr>';
			if (!empty($orderDetails->is_paid)) {
				$txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
			} else {
				$txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
			}
			$invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.payment_status') . '</strong></td><td>' . $txtpaid . '</td><td><strong>' . trans('webMessage.date') . '</strong></td><td>' . $orderDetails->created_at . '</td></tr>';
			$invoiceDetailsTxt .= '</table>';
		}


		$customerDetailsTxt = '';
		if (!empty($orderDetails->name)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . $orderDetails->name . '</b><br>';
		}
		if (!empty($orderDetails->state_id)) {
			$stateInfo   = self::get_csa_info($orderDetails->state_id);
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . $stateInfo['name_' . $strLang] . ',';
		}
		if (!empty($orderDetails->area_id)) {
			$areaInfo    = self::get_csa_info($orderDetails->area_id);
			$customerDetailsTxt .= $areaInfo['name_' . $strLang] . '</b>,<br>';
		}
		if (!empty($orderDetails->block)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . trans('webMessage.block') . ' : ' . $orderDetails->block .'</b>'. ',';
		}
		if (!empty($orderDetails->street)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . trans('webMessage.street') . ' : ' . $orderDetails->street .'</b>'. ',';
		}
		if (!empty($orderDetails->avenue)) {
		}
		if (!empty($orderDetails->house)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . trans('webMessage.house') . ' : ' . $orderDetails->house .'</b>'. ',';
		}
		if (!empty($orderDetails->floor)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . trans('webMessage.floor') . ' : ' . $orderDetails->floor .'</b>' . ',';
		}
		if (!empty($orderDetails->landmark)) {
			$customerDetailsTxt .= '<b style="font-size:1.02rem">' . trans('webMessage.landmark') . ' : ' . $orderDetails->landmark .'</b>';
		}

		if (!empty($orderDetails->email)) {
			$customerDetailsTxt .= '<br><b style="font-size:1.02rem">' . trans('webMessage.email') . ' : ' . $orderDetails->email.'</b>';
		}
		if (!empty($orderDetails->mobile)) {
			$customerDetailsTxt .= '<br><b style="font-size:1.02rem">' . trans('webMessage.mobile') . ' : ' . $orderDetails->mobile.'</b>';
		}

		//update total amount 
		self::UpdateOrderAmounts($orderDetails->id, $totalprice);

		//track url	
		$trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url('/order-details') . '/' . md5($orderid);
		$paymentDetailsTxt = '';
		//send email notification if COD
		if ($request->payment_method == "COD") {
			//send email to admins
			$adminNotifications = NotificationEmails::where('is_active', 1)->get();
			if (!empty($adminNotifications) && count($adminNotifications) > 0) {
				foreach ($adminNotifications as $adminNotification) {
					$deartxt = !empty($adminNotification->name) ? trans('webMessage.dear') . ' ' . $adminNotification->name : trans('webMessage.dear') . ' ' . trans('webMessage.admin');
					$data = [
						'deartxt'         => $deartxt,
						'bodytxt'         => trans('webMessage.admin_order_msg_cod'),
						'customerDetails' => $customerDetailsTxt,
						'invoiceDetails'  => $invoiceDetailsTxt,
						'orderDetails'    => $orderDetailsTxt,
						'paymentDetails'  => $paymentDetailsTxt,
						'trackYourOrder'  => $trackYourOrderTxt,
						'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderid,
						'email_from'      => $settingInfo->from_email,
						'email_from_name' => $settingInfo->from_name
					];
					Mail::to($adminNotification->email)->send(new SendGridOrder($data));
				}
			}
			//send email to user
			if (!empty($orderDetails->email)) {
				$deartxt = !empty($orderDetails->name) ? trans('webMessage.dear') . ' ' . $orderDetails->name : trans('webMessage.dear') . ' ' . trans('webMessage.buyer');
				$data = [
					'deartxt'         => $deartxt,
					'bodytxt'         => trans('webMessage.user_order_msg_cod'),
					'customerDetails' => $customerDetailsTxt,
					'invoiceDetails'  => $invoiceDetailsTxt,
					'orderDetails'    => $orderDetailsTxt,
					'paymentDetails'  => $paymentDetailsTxt,
					'trackYourOrder'  => $trackYourOrderTxt,
					'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderid,
					'email_from'      => $settingInfo->from_email,
					'email_from_name' => $settingInfo->from_name
				];
				Mail::to($orderDetails->email)->send(new SendGridOrder($data));
			}

			//send sms notification for cod
			$isValidMobile = Common::checkMobile($orderDetails->mobile);
			if (!empty($settingInfo->sms_text_cod_active) && !empty($settingInfo->sms_text_cod_en) && !empty($settingInfo->sms_text_cod_ar) && !empty($isValidMobile)) {
				if ($strLang == "en") {
					$smsMessage = $settingInfo->sms_text_cod_en;
				} else {
					$smsMessage = $settingInfo->sms_text_cod_ar;
				}
				$to         = $orderDetails->mobile;
				$sms_msg    = $smsMessage . " #" . $orderDetails->order_id;
				Common::SendSms($to, $sms_msg);
			}

			//end sending sms for cod
			$success['data'] = ['trackid' => $orderid, 'expectedDate' => $expectedDate, 'message' => trans('webMessage.yourorderisplacedsucces')];
			return response()->json($success, $this->successStatus);
		}elseif ($request->payment_method == "MasterCard") {
            $success['data'] = ['payurl' => route('order.details.start.pay' , [\App\Country::getIsoById($orderDetails->country_id),$strLang,$orderDetails->order_id_md5,"MasterCard"])];
            return response()->json($success, $this->successStatus);
		}elseif ($request->payment_method == "Q8LINK") {

			$transaction = new Transaction;
			$transaction->presult  = 'INITIALIZED';
			$transaction->postdate = date("md");
			$transaction->udf1     = $orderid;
			$transaction->udf2     = $totalprice;
			$transaction->udf3     = $strLang;
			$transaction->udf4     = $uid;
			$transaction->udf5     = $settingInfo->name_en;
			$transaction->trackid  = $orderid;
			$transaction->save();
			///prepare payment
			if ($settingInfo->is_knet_live == '1') {
				$paymentgurl = 'https://www.q8link.net/clientpay/payment_processing_api.php';
			} else {
				$paymentgurl = 'https://www.q8link.net/clientpay/payment_processing_api.php';
			}

			$keyword      = "TABEEH";
			$returnurl    = url('knet_response_q8link_return');

			$response = Curl::to($paymentgurl)
				->withData([
					'pay_vendor'    => $keyword,
                    'pay_orderid'   => $transaction->id,
					'pay_returnurl' => $returnurl,
					'pay_amount'    => $totalprice
				])->post();
			$jsdecode = json_decode($response, true);
			if ($jsdecode['status'] == 200) {
				$success['data'] = ['payurl' => $jsdecode['data']];
				return response()->json($success, $this->successStatus);
			} else {
				$emsg = $jsdecode['info'];
				$success['data'] = trans('webMessage.paymentprocessingerrorfound') . '(' . $emsg . ')';
				return response()->json($success, $this->failedStatus);
			}
			//end prepare payment
		} elseif ($request->payment_method == "MF" and env('ACTIVE_MYFATOORAH_API' , false)) {
            $email   = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $mobile  = !empty($orderDetails->mobile) ? $orderDetails->mobile : $settingInfo->mobile;
            $name    = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $block   = !empty($orderDetails->block) ? $orderDetails->block : '';
            $street  = !empty($orderDetails->street) ? $orderDetails->street : '';

            $accessToken       =  Common::getToken();
            $payprocessDetails = Common::initPayment($name, $block, $street, '', '', '', $mobile, $email, $accessToken[0], $totalprice, $orderid, $uid, $strLang);

            if ($payprocessDetails['status'] == 1) {
                $success['data'] = ['payurl' => $payprocessDetails['payurl']];
                return response()->json($success, $this->successStatus);
            } else {
                $emsg = $payprocessDetails['message'];
                $success['data'] = trans('webMessage.paymentprocessingerrorfound') . '(' . $emsg . ')';
                return response()->json($success, $this->failedStatus);
            }
        }  else { // else for COD

			if ($request->payment_method == "KNET") {
				$payType = 1;
			} else {
				$payType = 2;
			}
			$transaction = new Transaction;
			$transaction->presult  = 'HOST TIMEOUT';
			$transaction->postdate = date("md");
			$transaction->udf1     = $orderid;
			$transaction->udf2     = $totalprice;
			$transaction->udf3     = $strLang;
			$transaction->udf4     = $uid;
			$transaction->udf5     = $settingInfo->name_en;
			$transaction->trackid  = $orderid;
			$transaction->save();

			///prepare payment
			if ($settingInfo->is_knet_live == '1') {
				$paymentgurl = 'https://www.dezsms.com/cbk_pay/api_payment_processing.php';
			} else {
				$paymentgurl = 'https://www.dezsms.com/cbk_pay_demo/api_payment_processing.php';
			}
			$returnurl   = url('knet_response');
			$item_details = "Purchasing from " . $settingInfo->name_en;
			$response = Curl::to($paymentgurl)
				->withData([
					'keyword'      => $settingInfo->gulfpay_key,
					'apikey'       => $settingInfo->gulfpay_token,
                    'refid'        => $transaction->id,
					'returnurl'    => $returnurl,
					'amount'       => $totalprice,
					'paytype'      => $payType,
					'item_details' => $item_details
				])->post();
			$jsdecode = json_decode($response, true);
			if ($jsdecode['status'] == 'success') {
				$success['data'] = ['payurl' => $jsdecode['payurl']];
				return response()->json($success, $this->successStatus);
			} else {
				$emsg = $jsdecode['message'];
				$success['data'] = trans('webMessage.paymentprocessingerrorfound') . '(' . $emsg . ')';
				return response()->json($success, $this->failedStatus);
			}
			//end prepare payment
		}
	}



	//get temp orders
	public function getTempOrders(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		if (empty($request->temp_uniqueid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		} else {
			$tempid = $request->temp_uniqueid;
			$tempOrders = self::loadTempOrders($tempid);
			if (empty($tempOrders) || count($tempOrders) == 0) {
				$success['data'] = trans('webMessage.yourcartisempty');
				return response()->json($success, $this->failedStatus);
			}

			if (!empty($tempOrders) && count($tempOrders) > 0) {
				$totalAmount = 0;
				$grandtotal = 0;
				$subtotalprice = 0;
				$attrtxt = '';
				$t = 1;
				$attribute_txt = [];
				$coupon_discount = 0;
				$delivery_charges = 0;
				$tempSub = [];
				foreach ($tempOrders as $tempOrder) {
					$productDetails = self::getProductDetails($tempOrder->product_id);

					if (!empty($tempOrder->size_id)) {
						$sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
						$attribute_txt['size_id']  = $tempOrder->size_id;
						$attribute_txt['size_name'] = $sizeName;
					}
					if (!empty($tempOrder->color_id)) {
						$colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
						$attribute_txt['color_id'] = $tempOrder->color_id;
						$attribute_txt['color_name'] = $colorName;
					}

					$orderOptions = self::getOptionsDtailsOrder($tempOrder->id);
					if (!empty($orderOptions)) {
						$attribute_txt['options'] = $orderOptions;
					}

					$unitprice     = $tempOrder->unit_price;
					$subtotalprice = $unitprice * $tempOrder->quantity;
					$title         = $productDetails['title_' . $strLang];
					if (!empty($productDetails['image'])) {
						$imageUrl = url('uploads/product/thumb/' . $productDetails['image']);
					} else {
						$imageUrl = url('uploads/no-image.png');
					}
					//available quantity
					$aquantity = self::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id);

					$tempSub[] = [
						"id" => $tempOrder->id,
						"product_id" => $tempOrder->product_id,
						"title" => $title,
						"imageUrl" => $imageUrl,
						"attribute_txt" => $attribute_txt,
						"unitprice" => $unitprice,
						"color_id" => (string)$tempOrder->color_id,
						"size_id" => (string)$tempOrder->size_id,
						"quantity" => $tempOrder->quantity,
						"unique_sid" => $tempOrder->unique_sid,
						"available_quantity" => $aquantity,
						"subtotal" => $subtotalprice,
					];

					//sum sub total to grand total
					$totalAmount += $subtotalprice;

					$attribute_txt = [];
				}
			}
		}
		if (!empty($tempSub) && count($tempSub) > 0) {
			$grandtotal = $totalAmount;

			//check coupon discount
			if (!empty($request->coupon_discount)) {
				$coupon_discount = (float)$request->coupon_discount;
				$grandtotal      = (float)($grandtotal - $request->coupon_discount);
			}
			//check delivery charges
			if (!empty($request->area_id)) {
                $delivery_charges = self::get_delivery_charge($request->area_id , $request->temp_uniqueid , $request->is_express_delivery);
				$grandtotal       = (float)($grandtotal + $delivery_charges);
			}

			$success['data'] = [
				'temoOrders' => $tempSub,
				'total' => $totalAmount,
				'coupon_discount' => $coupon_discount,
				'delivery_charges' => $delivery_charges,
				'grandtotal' => $grandtotal
			];

            $settingInfo = Settings::where("keyname", "setting")->first();
            $success['data']['requiredField'] = [
                'name' => (int) ( (boolean) $settingInfo->validate_cust_name ) ,
                'mobile' =>  (int) ( (boolean) $settingInfo->validate_cust_mob ) ,
                'area' =>  (int) ( (boolean) $settingInfo->validate_cust_area ) ,
                'block' =>  (int) ( (boolean) $settingInfo->validate_cust_block ) ,
                'street' =>  (int) ( (boolean) $settingInfo->validate_cust_st ) ,
                'hose_number' =>  (int) ( (boolean) $settingInfo->validate_cust_hno ) ,
            ];
			return response()->json($success, $this->successStatus);
		} else {
			$success['data'] = trans('webMessage.yourcartisempty');
			return response()->json($success, $this->failedStatus);
		}
	}

	//delete record from temp order
	public function removeTempOrder(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (empty($request->temp_uniqueid) || empty($request->id)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}

		$tempOrder = OrdersTemp::where('unique_sid', $request->temp_uniqueid)->where('id', $request->id)->first();
		if (empty($tempOrder->id)) {
			$success['data'] = trans('webMessage.norecordfound');
			return response()->json($success, $this->failedStatus);
		}
		//remove option if

		$optionsboxs = OrdersTempOption::where("oid", $request->id)->get();
		if (!empty($optionsboxs) && count($optionsboxs) > 0) {
			foreach ($optionsboxs as $optionsbox) {
				$tempOrdersOption = OrdersTempOption::find($optionsbox->id);
				$tempOrdersOption->delete();
			}
		}

		$tempOrder->delete();
		$success['data'] = trans('webMessage.itemsareremovedfromcart');
		return response()->json($success, $this->successStatus);
	}

	//apply coupon
	public function apply_coupon_to_cart(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$settingInfo = Settings::where("keyname", "setting")->first();

		if (empty($request->temp_uniqueid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}

		$total = self::getTotalCartAmount($request->temp_uniqueid);
		if (empty($request->coupon_code)) {
			$success['data'] = trans('webMessage.coupon_required');
			return response()->json($success, $this->failedStatus);
		}
		if (empty($total)) {
			$success['data'] = trans('webMessage.yourcartisempty');
			return response()->json($success, $this->failedStatus);
		}

		$curDate = date("Y-m-d");
		$coupon = Coupon::where('is_active', 1)
			->where('coupon_code', $request->coupon_code)
			->where('is_for', 'app')
			->first();
		if (empty($coupon->id)) {
			$success['data'] = trans('webMessage.invalid_coupon_code');
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($coupon->id) && strtotime($curDate) < strtotime($coupon->start_date)) {
			$success['data'] = trans('webMessage.coupon_can_be_used_from') . $coupon->start_date;
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($coupon->id) && strtotime($curDate) > strtotime($coupon->end_date)) {
			$success['data'] = trans('webMessage.coupon_is_expired_on') . $coupon->end_date;
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($coupon->id) && ($total < $coupon->price_start || $total > $coupon->price_end)) {

			$success['data'] = trans('webMessage.coupon_can_be_apply_for_price_range') . \App\Currency::default() . ' ' . $coupon->price_start . ' - ' . \App\Currency::default() . ' ' . $coupon->price_end;
			return response()->json($success, $this->failedStatus);
		}
		if (!empty($coupon->id) && empty($coupon->usage_limit)) {
			$success['data'] = trans('webMessage.usage_limit_exceeded');
			return response()->json($success, $this->failedStatus);
		}

        $totalWithOurOldPrice = 0;
        $tempOrders = self::loadTempOrders($request->temp_uniqueid);
        if (!empty($tempOrders) && count($tempOrders) > 0) {
            foreach ($tempOrders as $tempOrder) {
                $pro = Product::find($tempOrder->product_id);
                $brand = Brand::where('id', $pro->brand_id)->first();
                if ( $pro != null and ! ($pro->old_price >  $pro->retail_price or ( $brand != null and $brand->is_discount == 1 && $brand->discount > 0 )) ){
                    $totalWithOurOldPrice += ($tempOrder->quantity * $tempOrder->unit_price);
                }
            }
        }
        $totalKD = Currency::getOriginalPrice(  round($totalWithOurOldPrice, 3));
        $gb_coupon_free = 0 ;
        if (!empty($coupon->id) && !empty($coupon->is_free) and $totalKD > 0 ) {
            $domainCountry = Country::$countryInDomainModel;
            if ( ! ( empty($coupon->is_zone_free) and in_array($domainCountry->shipment_method  ,["zoneprice" , "dhl"]) ) ){
                $gb_coupon_free = 1 ;
            }
        }
		if (!empty($coupon->id) && $coupon->coupon_type == "amt") {
			$discountAmt    = $coupon->coupon_value;
		} else {
			$discountAmt    = round(($totalKD * $coupon->coupon_value) / 100, 3);
		}
        if ( $discountAmt > $totalKD )
            $discountAmt = $totalKD;
        $tempPrice = Currency::convertTCountry( $discountAmt);
        $discountAmttxt = 	\App\Currency::default() . ' ' .  ($tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $discountAmt);

		$success['data'] = [
			'coupon_free' => $gb_coupon_free,
			'coupon_code' => $request->coupon_code,
			'coupon_discount' => $discountAmt,
			'coupon_discount_text' => $discountAmttxt
		];
		return response()->json($success, $this->successStatus);
	}

	////////////////////////////////////////////////////////////////add/remove qty///////////////
	public function addremovequantity(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (empty($request->tempid)) {
			$success['data'] = trans('webMessage.tempidmissing') . '(ID)';
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->temp_uniqueid)) {
			$success['data'] = trans('webMessage.tempidmissing') . '(TEMP UNIQUE ID)';
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->quantity)) {
			$success['data'] = trans('webMessage.quantity_required');
			return response()->json($success, $this->failedStatus);
		}

		$session_id = $request->temp_uniqueid;

		$tempOrder  = OrdersTemp::where('id', $request->tempid)->where('unique_sid', '=', $session_id)->first();
		if (!empty($tempOrder->id)) {
			$productDetails   = Product::where('id', $tempOrder->product_id)->first();

			if (!empty($productDetails->is_attribute) ) {
                $aquantity = self::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id , $tempOrder);
				if (!empty($request->quantity) && $request->quantity > $aquantity) {
					$success['data'] = trans('webMessage.quantity_is_exceeded');
					return response()->json($success, $this->failedStatus);
				}
			} else {
				if (empty($productDetails->is_attribute) && !empty($request->quantity) && $request->quantity > $productDetails->quantity) {
					$success['data'] = trans('webMessage.quantity_is_exceeded');
					return response()->json($success, $this->failedStatus);
				}
			}

			$tempOrder->quantity   = !empty($request->quantity) ? $request->quantity : '1';
			$tempOrder->save();
			$totalAmount = self::getTotalCartAmount($session_id);
			$countitems  = self::countTempOrders($session_id);
			$item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
			$success['data'] = ['total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.quantity_is_updated')];
			return response()->json($success, $this->successStatus);
		} else {
			$success['data'] = trans('webMessage.norecordfound');
			return response()->json($success, $this->failedStatus);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////add to cart
	public function addtocart(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}


		if (empty($request->product_id)) {
			$success['data'] = trans('webMessage.product_id_required');
			return response()->json($success, $this->failedStatus);
		}

		if (empty($request->price)) {
			$success['data'] = trans('webMessage.price_required');
			return response()->json($success, $this->failedStatus);
		}

		if (empty($request->quantity)) {
			$success['data'] = trans('webMessage.quantity_required');
			return response()->json($success, $this->failedStatus);
		}

		if (empty($request->temp_uniqueid)) {
			$success['data'] = trans('webMessage.tempidmissing');
			return response()->json($success, $this->failedStatus);
		}

		$productDetails   = Product::where('id', $request->product_id)->first();
		if (empty($productDetails->id)) {
			$success['data'] = trans('webMessage.item_not_found');
			return response()->json($success, $this->failedStatus);
		}

		$requiredQty = $request->quantity;
		$productQuantityCheck = $productDetails->quantity;

		// Checking if Product Quantity is in -ive in db.
		if ($productQuantityCheck < 0) {
			$success['data'] = trans('Product is out of stock');
			return response()->json($success, $this->failedStatus);
		}
		// Checking if the given quantity is less than the Qty in db.
		if ($requiredQty > $productQuantityCheck) {
			$success['data'] = trans('Given quantity exceeds Available quantity');
			return response()->json($success, $this->failedStatus);
		}


		//check size/color attribute
		if (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 3) {
			if (empty($request->size_attribute)) {
				$success['data'] = trans('webMessage.size_required');
				return response()->json($success, $this->failedStatus);
			}
			if (empty($request->color_attribute)) {
				$success['data'] = trans('webMessage.color_required');
				return response()->json($success, $this->failedStatus);
			}
			//check size color attr
			$aquantity = self::getProductQuantity($request->product_id, $request->size_attribute, $request->color_attribute);
			if (!empty($request->quantity) && $request->quantity > $aquantity) {
				$success['data'] = trans('webMessage.quantity_is_exceeded');
				return response()->json($success, $this->failedStatus);
			}
			//end size color attr
		} elseif (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 1) {
			if (empty($request->size_attribute)) {
				$success['data'] = trans('webMessage.size_required');
				return response()->json($success, $this->failedStatus);
			}
			//check size color attr
			$aquantity = self::getProductQuantity($request->product_id, $request->size_attribute, 0);
			if (!empty($request->quantity) && $request->quantity > $aquantity) {
				$success['data'] = trans('webMessage.quantity_is_exceeded');
				return response()->json($success, $this->failedStatus);
			}
			//end size color attr
		} elseif (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 2) {
			if (empty($request->color_attribute)) {
				$success['data'] = trans('webMessage.color_required');
				return response()->json($success, $this->failedStatus);
			}
			//check size color attr
			$aquantity = self::getProductQuantity($request->product_id, 0, $request->color_attribute);
			if (!empty($request->quantity) && $request->quantity > $aquantity) {
				$success['data'] = trans('webMessage.quantity_is_exceeded');
				return response()->json($success, $this->failedStatus);
			}
			//end size color attr
		}

		//check other field validation
		$flag = self::checkOptionsFields($request);
		if (!empty($flag) && $flag > 0) {
			$success['data'] = trans('webMessage.options_required');
			return response()->json($success, $this->failedStatus);
		}
		//end check other field validation

		$session_id    = $request->temp_uniqueid;
		$whereClause[] = ["product_id", "=", $request->product_id];
		$whereClause[] = ["unique_sid", "=", $session_id];
		//size
		if (!empty($request->size_attribute)) {
			$whereClause[] = ["size_id", "=", $request->size_attribute];
		}
		//size
		if (!empty($request->color_attribute)) {
			$whereClause[] = ["color_id", "=", $request->color_attribute];
		}

		//check countdown price
		if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
			$price = round($productDetails->countdown_price, 3);
		} else {
			$price = self::getProductPrice($request->product_id, $request->size_attribute, $request->color_attribute);
			if (empty($price)) {
				$price = $request->price;
			}
			//check option price
			$price = self::getOptionsPrice($request, $price);
		}
        $productoptions = ProductOptionsCustomChosen::where('gwc_products_option_custom_chosen.product_id', $request->product_id)
            ->where('gwc_products_option_custom_chosen.custom_option_id', '>=', 4);

        $productoptions = $productoptions->select('gwc_products_option_custom.id', 'gwc_products_option_custom.option_type', 'gwc_products_option_custom_chosen.*');

        $productoptions = $productoptions->join('gwc_products_option_custom', 'gwc_products_option_custom.id', '=', 'gwc_products_option_custom_chosen.custom_option_id');
        $productoptions->groupBy('gwc_products_option_custom_chosen.custom_option_id');
        $productoptions = $productoptions->get();
        $selectOptions = [];
        foreach($productoptions as $productoption){
            if ( $productoption->option_type == "select for each order"){
                $selectOptions[] = "select-".$productoption->product_id."-".$productoption->custom_option_id;
            }
        }
        $tempOrdersObject = OrdersTemp::where($whereClause);
        if ( count($selectOptions) > 0 ) {
            foreach ($selectOptions as $selectOption) {
                $tempData = explode('-' , $request[$selectOption]);
                $tempOrdersObject->whereHas('options', function ($query) use($request, $tempData) {
                    return $query->where('product_id' ,  $tempData[1]??"")
                        ->where('option_id' ,  $tempData[2]??"" )
                        ->where('option_child_ids' ,  $tempData[3]??"" );
                });
            }
        }

        $tempOrder  = $tempOrdersObject->first();
		if (!empty($tempOrder->id)) {
			$tempOrder->unit_price = $price;
			$tempOrder->quantity   = $request->quantity;
			$tempOrder->save();
			$totalAmount = self::getTotalCartAmount($session_id);
			$countitems  = self::countTempOrders($session_id);
			$item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));

			$success['data'] = ['total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.quantity_is_updated')];
			//end
		} else {

			$tempOrder  = new OrdersTemp;
			$tempOrder->product_id = $request->product_id;
			$tempOrder->size_id    = $request->size_attribute;
			$tempOrder->color_id   = $request->color_attribute;
			$tempOrder->quantity   = $request->quantity;
			$tempOrder->unit_price = $price;
			$tempOrder->unique_sid = $session_id;
			$tempOrder->save();
			//add options
			self::detailsTempOrders($request, $tempOrder->id);
			//end
			$totalAmount = self::getTotalCartAmount($session_id);
			$countitems  = self::countTempOrders($session_id);
			$item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));

			$success['data'] = ['total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.item_is_added')];
			//end
		}
//        $settingInfo = Settings::where("keyname", "setting")->first();
//        $success['data']['requiredField'] = [
//            'name' => (boolean) $settingInfo->validate_cust_name ,
//            'mobile' => (boolean) $settingInfo->validate_cust_mob ,
//            'area' => (boolean) $settingInfo->validate_cust_area ,
//            'block' => (boolean) $settingInfo->validate_cust_block ,
//            'street' => (boolean) $settingInfo->validate_cust_st ,
//            'hose_number' => (boolean) $settingInfo->validate_cust_hno ,
//        ];
		return response()->json($success, $this->successStatus);
	}

	public static function get_delivery_charge($areaid , $order , $isExpress = false)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$fees = round($settingInfo->flat_rate, 3);
		if (!empty($areaid)) {
//			$areaInfo = Country::where('id', $areaid)->first();
//			if (!empty($areaInfo->id) && !empty($areaInfo->delivery_fee)) {
//				$fees = round($areaInfo->delivery_fee, 3);
//			}
            $fees = round(ShipmentController::getPrice($areaid , $order , true , $isExpress) , 3);
		}
		return $fees;
	}

	//////////////////////////////////////////support functions////////////////////////////////////////////////////////////////////
	//update total amount
	public static function UpdateOrderAmounts($id, $amount)
	{
		$orderDetails = OrdersDetails::Where('id', $id)->first();
		$orderDetails->total_amount = $amount;
		$orderDetails->save();
	}

	public static function ChangeUpdateQuantity($product_id)
	{
		$qty = 0;
		$productUpdate   = Product::where('id', $product_id)->first();
		if (!empty($productUpdate->is_attribute)) {
			$qty   = ProductAttribute::where('product_id', $productUpdate->id)->get()->sum('quantity');
			$productUpdate->quantity = $qty;
			$productUpdate->save();
		}
	}

	public static function isQuantityExistForOrder($tempid)
	{
		$flag = 0;
		$tempOrders = self::loadTempOrders($tempid);
		if (!empty($tempOrders) && count($tempOrders) > 0) {
			foreach ($tempOrders as $tempOrder) {
				$existQty = self::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id , $tempOrder);
				if ($existQty >= $tempOrder->quantity) {
					$flag = 1;
                    break;
				}
			}
		}
		return $flag;
	}
	//generate serial number with prefix
	//generate serial number with prefix
	public function OrderserialNumber()
	{
		$orderInfo = OrdersDetails::orderBy("id", "desc")->first();
		if (!empty($orderInfo->id)) {
			$lastProdId = ($orderInfo->id + 1);
		} else {
			$lastProdId = 1;
		}
		$seriamNum = $lastProdId;
		return $seriamNum;
	}

	//sid number
	public function OrderSidNumber()
	{
		$orderInfo = OrdersDetails::orderBy("id", "desc")->first();
		if (!empty($orderInfo->id)) {
			$lastProdId = ($orderInfo->id + 1);
		} else {
			$lastProdId = 1;
		}
		return $lastProdId;
	}

	public static function getOptionsDtailsOrder($oid)
	{
		$options = [];
		$option_name = '';
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$optionDetails = OrdersTempOption::where("oid", $oid)->get();
		if (!empty($optionDetails) && count($optionDetails) > 0) {
			foreach ($optionDetails as $optionDetail) {
				$optionParentDetails = ProductOptionsCustom::where("id", $optionDetail->option_id)->first();
				$option_name = $strLang == "en" ? $optionParentDetails->option_name_en : $optionParentDetails->option_name_ar;
				$options[] = [
					"custom_option_id" => $optionParentDetails->id,
					"custom_option_name" => $option_name,
					"child_options" => self::getChildOptionsDtails($optionDetail->option_child_ids)
				];
			}
		}
		return $options;
	}


	public static function getOptionsDtailsOrderBr($oid)
	{
		$optionDetailstxt = '';
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$optionDetails = OrdersOption::where("oid", $oid)->get();
		if (!empty($optionDetails) && count($optionDetails) > 0) {
			foreach ($optionDetails as $optionDetail) {
				$optionParentDetails = ProductOptionsCustom::where("id", $optionDetail->option_id)->first();
				if (!empty($optionParentDetails->id)) {
					$option_name = $strLang == "en" ? $optionParentDetails->option_name_en : $optionParentDetails->option_name_ar;
					$optionDetailstxt .= '<br>' . $option_name . ':(' . self::getChildOptionsDtails($optionDetail->option_child_ids) . ')';
				}
			}
		}
		return $optionDetailstxt;
	}



	public static function getChildOptionsDtails($ids)
	{

		$optxt = [];
		$explode = explode(",", $ids);
		if (count($explode) > 0) {
			for ($i = 0; $i < count($explode); $i++) {
				$optxt[] = self::getJoinOptions($explode[$i]);
			}
		} else {
			$optxt[] = self::getJoinOptions($ids);
		}
		return $optxt;
	}

	//
	public static function getJoinOptions($id)
	{
		$optionsy = '';
		$optionName = '';
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$options = ProductOptions::where("gwc_products_options.id", $id);
		$options = $options->select('gwc_products_options.*', 'gwc_products_option_custom_child.id as oid', 'gwc_products_option_custom_child.option_value_name_en', 'gwc_products_option_custom_child.option_value_name_ar');
		$options = $options->join('gwc_products_option_custom_child', 'gwc_products_option_custom_child.id', '=', 'gwc_products_options.option_value_id');
		$options = $options->orderBy('gwc_products_options.option_value_id', 'ASC')->get();
		if (!empty($options) && count($options) > 0) {
			foreach ($options as $option) {
				$optionName = ($strLang == "en" ? $option->option_value_name_en : $option->option_value_name_ar);
				$optionsy .= $optionName . ',';
			}
		}
		return trim($optionsy, ",");
	}


	//get product details
	public static function getProductDetails($id)
	{
		$prodDetails = Product::where('id', $id)->first();
		return $prodDetails;
	}

	//get Size Name
	public function sizeName($id, $strLang)
	{
		$txt = '--';
		$Details   = Size::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}
	//get color name
	public function colorName($id, $strLang)
	{
		$txt = '--';
		$Details   = Color::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}

	//get Size Name
	public static function sizeNameStatic($id, $strLang)
	{
		$txt = '--';
		$Details   = Size::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}
	//get color name
	public static function colorNameStatic($id, $strLang)
	{
		$txt = '--';
		$Details   = Color::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}
	//get Color Name
	public function colorDetails($id)
	{
		$Details   = Color::where('id', $id)->first();
		return $Details;
	}
	public static function colorDetailsStatic($id)
	{
		$Details   = Color::where('id', $id)->first();
		return $Details;
	}

	//get option price only
	/*
	public static function getOptionsPrice($ids){
	$optionPrice=0;
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	if(!empty($ids)){
	$explodechildids = explode(",",$ids);
    for($i=0;$i<count($explodechildids);$i++){
	$productChildOption = ProductOptionsChild::where("id",$explodechildids[$i])->first();	
	if($productChildOption['o_plus_minus']=='p'){
	$optionPrice+=$productChildOption['price'];
	}else{
	$optionPrice-=$productChildOption['price'];	
	}
	}
	}
	return ["optionPrice"=>$optionPrice];
	}
	*/
	//count temp order
	public static function countTempOrders($tempid)
	{
		$session_id = $tempid;
		$tempOrders = OrdersTemp::where('unique_sid', $session_id)->get()->count();
		return $tempOrders;
	}
	//get temp orders 
	public static function loadTempOrders($tempid)
	{
		$session_id = $tempid;
		$tempOrders = OrdersTemp::where('unique_sid', $session_id)->orderBy('created_at', 'DESC')->get();
		return $tempOrders;
	}

	public static function getTotalCartAmount($tempid)
	{
		$total = 0;
		$tempOrders = self::loadTempOrders($tempid);
		if (!empty($tempOrders) && count($tempOrders) > 0) {
			foreach ($tempOrders as $tempOrder) {
				$total += ($tempOrder->quantity * $tempOrder->unit_price);
			}
		}
		return $total;
	}
	//get product prices
	public static function getProductPrice($product_id, $size_id = 0, $color_id = 0)
	{
		$price = 0;
		$productDetails   = Product::where('id', $product_id)->first();
		if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
			$price = $productDetails['countdown_price'];
		} else {
			if (empty($productDetails['is_attribute'])) {
				$price = $productDetails['retail_price'];
			} else {
				if (!empty($size_id) && !empty($color_id)) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->first();
					if (!empty($attributes->id)) {
						$price = $attributes->retail_price;
					}
				} else if (!empty($size_id) && empty($color_id)) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->first();
					if (!empty($attributes->id)) {
						$price = $attributes->retail_price;
					}
				} else if (empty($size_id) && !empty($color_id)) {
					$attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->first();
					if (!empty($attributes->id)) {
						$price = $attributes->retail_price;
					}
				}
			}
		}
		return $price;
	}
	//get quantity

    //is quantity exist
    public static function getProductQuantity($product_id, $size_id = 0, $color_id = 0, $tempOrder = null)
    {
        $quantity = 0;
        $productDetails   = Product::where('id', $product_id)->first();
        if (empty($productDetails['is_attribute'])) {
            $quantity = $productDetails['quantity'];
        } else {
            if (!empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
                foreach ($attributes as $attribute )
                    foreach (($productDetails->getQuantity(-1,$attribute->id,null , true)) as $InventoryHave )
                        $quantity = $quantity + $InventoryHave->quantity;
            } else if (!empty($size_id) && empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
                foreach ($attributes as $attribute ){
                    foreach (($productDetails->getQuantity(-1,$attribute->id,null , true)) as $InventoryHave )
                        $quantity = $quantity + $InventoryHave->quantity;
                }
            } else if (empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
                foreach ($attributes as $attribute )
                    foreach (($productDetails->getQuantity(-1,$attribute->id,null , true)) as $InventoryHave )
                        $quantity = $quantity + $InventoryHave->quantity;
            }
            if ( $tempOrder != null ) {
                if ( get_class($tempOrder) == OrdersTemp::class )
                    $tempOrderOptions = OrdersTempOption::where("oid", $tempOrder->id)->get();
                else
                    $tempOrderOptions = OrdersOption::where("oid", $tempOrder->id)->get();
                if (!empty($tempOrderOptions) && count($tempOrderOptions) > 0) {
                    foreach ($tempOrderOptions as $tempOrderOption) {
                        $Option = ProductOptions::find($tempOrderOption->option_child_ids);
                        $Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
                        foreach ( $Options as $option)
                            foreach ($productDetails->getQuantity(-1, null, $option->id, true) as $quantities) {
                                $quantity = $quantity + $quantities->quantity;
                            }
                    }
                }
            }
        }
        return $quantity;
    }

	///get option quantty
	public static function getOptionsQuantityTemp($productid)
	{
		$strOptions = ProductOptions::where("product_id", $productid)->sum("quantity");
		return $strOptions;
	}

	//settings
	public static function settings()
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		return $settingInfo;
	}

	//check token valid or not 
	public function isTokenValid($token)
	{
		$flag = 1;
		if (empty($token)) {
			$flag = 0;
		}
		$user = User::where('api_token', $token)->first();
		if (empty($user)) {
			$flag = 0;
		}
		return $flag;
	}

	//
	public static function get_csa_info($id)
	{
		$country = Country::where('id', $id)->first();
		return $country;
	}

	//newsletter start
	public function NewsLettersSubscription($email)
	{
		if (!empty($email)) {
			$newsletter = Newsletter::where("newsletter_email", $email)->first();
			if (empty($newsletter->id)) {
				$newsletter = new Newsletter;
				$newsletter->newsletter_email = $email;
				$newsletter->save();
			}
		}
	}
	//end news letter
	///check option 
	//check option validation
	public static function checkOptionsFields($request)
	{

		$flag = 0;

		$productoptions = ProductOptionsCustomChosen::where('gwc_products_option_custom_chosen.product_id', $request->product_id)
			->where('gwc_products_option_custom_chosen.custom_option_id', '>=', 4);
		$productoptions = $productoptions->select('gwc_products_option_custom.id', 'gwc_products_option_custom.option_type', 'gwc_products_option_custom_chosen.*');
		$productoptions = $productoptions->join('gwc_products_option_custom', 'gwc_products_option_custom.id', '=', 'gwc_products_option_custom_chosen.custom_option_id');
		$productoptions = $productoptions->get();

		if (!empty($productoptions) && count($productoptions) > 0) {
			foreach ($productoptions as $productoption) {
				//option
				if (!empty($productoption->is_required) && $productoption->option_type == 'radio' && empty($request->input('option-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
					return 1;
				}
				if (!empty($productoption->is_required) && $productoption->option_type == 'checkbox' && empty($request->input('checkbox-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
					return 1;
				}
				if (!empty($productoption->is_required) && $productoption->option_type == 'select' && empty($request->input('select-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
					return 1;
				}
			}
		}
		return $flag;
	}

	//get option price
	public static function getOptionsPrice($request, $price)
	{

		$retailPrice = 0;
		$retailPriceCheck = 0;
		$retailPriceOption = 0;
		$retailPriceSelect = 0;

		$productoptions = ProductOptionsCustomChosen::where('product_id', $request->product_id)
			->where('custom_option_id', '>=', 4)
			->orderBy('custom_option_id', 'ASC')->get();

		if (!empty($productoptions) && count($productoptions) > 0) {
			foreach ($productoptions as $productoption) {
				//option start
				$oidOps = $request->input('option-' . $request->product_id . '-' . $productoption->custom_option_id);
				if (!empty($oidOps)) {
					$prodOption  = ProductOptions::where('id', $oidOps)->first();
					if ($prodOption->is_price_add == 1) {
						$retailPriceOption += $prodOption->retail_price;
					} else if ($prodOption->is_price_add == 2) {
						$retailPriceOption -= $prodOption->retail_price;
					}
				}
				//end option
				//select start
				$oidSel = $request->input('select-' . $request->product_id . '-' . $productoption->custom_option_id);
				if (!empty($oidSel)) {
					$explodeSelect = $oidSel; //explode("-",$oidSel);
					$prodSelect  = ProductOptions::where('id', $oidSel)->first();
					if ($prodSelect->is_price_add == 1) {
						$retailPriceSelect += $prodSelect->retail_price;
					} else if ($prodSelect->is_price_add == 2) {
						$retailPriceSelect -= $prodSelect->retail_price;
					}
				}
				//end select
				//check start
				$oidChks = $request->input('checkbox-' . $request->product_id . '-' . $productoption->custom_option_id);
				if (!empty($oidChks)) {
					$retailPriceCheck += self::checkPrices($oidChks);
				}
				//end check
			}
		}

		$optionPrice = $price + $retailPriceOption + $retailPriceCheck + $retailPriceSelect;

		return $optionPrice;
	}

	public static function checkPrices($oidChks)
	{
		$retailPriceCheck = 0;
		foreach ($oidChks as $oidChk) {
			$prodOption  = ProductOptions::where('id', $oidChk)->first();
			if ($prodOption->is_price_add == 1) {
				$retailPriceCheck += $prodOption->retail_price;
			} else if ($prodOption->is_price_add == 2) {
				$retailPriceCheck -= $prodOption->retail_price;
			}
		}
		return $retailPriceCheck;
	}

	//add options
	public static function detailsTempOrders($request, $oid)
	{
		$productid = $request->product_id;
		$productoptions = ProductOptionsCustomChosen::where('product_id', $productid)
			->where('custom_option_id', '>=', 4)
            ->groupBy('custom_option_id')
			->orderBy('custom_option_id', 'ASC')->get();

		if (!empty($productoptions) && count($productoptions) > 0) {
			foreach ($productoptions as $productoption) {
				//option
				if (!empty($request->input('option-' . $productid . '-' . $productoption->custom_option_id))) {
					$child_option = $request->input('option-' . $productid . '-' . $productoption->custom_option_id);
					$tempOrderOption = new OrdersTempOption;
					$tempOrderOption->product_id       = $productid;
					$tempOrderOption->oid              = $oid;
					$tempOrderOption->option_id        = $productoption->custom_option_id;
					$tempOrderOption->option_child_ids = $child_option;
					$tempOrderOption->save();
				}
				//select
				if (!empty($request->input('select-' . $productid . '-' . $productoption->custom_option_id))) {
					$child_option = $request->input('select-' . $productid . '-' . $productoption->custom_option_id);
					$tempOrderOption = new OrdersTempOption;
					$tempOrderOption->product_id       = $productid;
					$tempOrderOption->oid              = $oid;
					$tempOrderOption->option_id        = $productoption->custom_option_id;
					$tempOrderOption->option_child_ids = $child_option;
					$tempOrderOption->save();
				}
				//checkbox
				if (!empty($request->input('checkbox-' . $productid . '-' . $productoption->custom_option_id))) {
					$child_option = $request->input('checkbox-' . $productid . '-' . $productoption->custom_option_id);
					$tempOrderCheck = new OrdersTempOption;
					$tempOrderCheck->product_id       = $productid;
					$tempOrderCheck->oid              = $oid;
					$tempOrderCheck->option_id        = $productoption->custom_option_id;
					$tempOrderCheck->option_child_ids = implode(",", $child_option);
					$tempOrderCheck->save();
				}
			}
		}
	}
	//warranty
	public static function getWarrantyDetails($id)
	{
		$w = Warranty::where('id', $id)->first();
		return $w;
	}
}
