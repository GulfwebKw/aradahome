<?php

namespace App\Http\Controllers\posv1;

use App\Brand;
use App\Currency;
use App\Http\Controllers\AdminCustomersController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\v1\apiCartController;
use App\Http\Controllers\v1\apiController;
use App\Http\Controllers\webCartController;
use App\Inventory;
use App\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Country;
use App\Area;
use App\State;

use App\User;

use App\Settings;
use App\Product;
use App\Warranty;
use App\ProductGallery;
use App\ProductReview;
use App\ProductAttribute;

use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\ProductOptionsCustomChosen;

use App\Color;
use App\Size;
use App\Categories;

use App\OrdersTemp;
use App\Orders;
use App\OrdersDetails;
use App\OrdersTrack;
use App\OrdersTempOption;
use App\OrdersOption;

use App\Coupon;

use App\NotificationEmails;
use App\Transaction;
use App\Mail\SendGrid;
use App\Mail\SendGridOrder;
use App\ProductCategory;
use Curl;
use Mail;
use Common;


//rules
use App\Rules\Name;
use App\Rules\Mobile;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class PosProductController extends Controller
{
	public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;

    public function getDefaultCurrency(){
        return response()->json([
            'data' => [
                'currency' => Currency::default(false) ,
                'symbol' => Currency::default()
            ]
        ], $this->successStatus);
    }

	public static function get_csa_info($id)
	{
		$country = Country::where('id', $id)->first();
		return $country;
	}



	public static function get_delivery_charge($areaid , $order, $isExpress = false)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$fees = round($settingInfo->flat_rate, 3);
		if (!empty($areaid)) {
//			$areaInfo = Country::where('id', $areaid)->first();
//			if (!empty($areaInfo->id) && !empty($areaInfo->delivery_fee)) {
//				$fees = round($areaInfo->delivery_fee, 3);
//			}
            $fees = round(ShipmentController::getPrice($areaid , $order,true, $isExpress ) , 3);
		}
		return $fees;
	}

	public function getAreas()
	{
        $Country = [];
		$listCountries = Country::where('parent_id', 0)->where('id', '<' , 350)->orderBy('name_en', 'asc')->get();
		if (!empty($listCountries) && count($listCountries) > 0) {
            foreach ($listCountries  as $listCountry) {
                $State = [];
                $listStates = Country::where('parent_id', $listCountry->id)->orderBy('name_en', 'asc')->get();
                if (!empty($listStates) && count($listStates) > 0) {

                    foreach ($listStates as $listState) {
                        $State[] = [
                            "id" => $listState->id,
                            "name" => $listState->name_en,
                            "area" => self::getAreasChild($listState),
                        ];
                    }
                }
                $Country[] = [
                    "id" => $listCountry->id,
                    "name" => $listCountry->name_en,
                    "state" => $State,
                ];
            }

		}

		$response['data'] = $Country;
		return response($response, $this->successStatus);
	}

	public function getAreasChild($parent)
	{
		$State = [];
		$listStates = Country::where('parent_id', $parent->id)->orderBy('name_en', 'asc')->get();
		if (!empty($listStates) && count($listStates) > 0) {

			foreach ($listStates  as $listState) {
				$State[] = [
					"id" => $listState->id,
					"name" => $listState->name_en,
					"delivery_fee" => $listState->delivery_fee,
				];
			}
		} else {
            $State[] = [
                "id" => $parent->id,
                "name" => $parent->name_en,
                "delivery_fee" => $parent->delivery_fee,
            ];
        }
		return $State;
	}



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
					$optionDetailstxt .= '<br>' . $option_name . ':(' . self::getChildOptionsDtailsString($optionDetail->option_child_ids) . ')';
				}
			}
		}
		return $optionDetailstxt;
	}


	public static function getColorImage($productid, $colorid)
	{
		$Attributes     = ProductAttribute::where('product_id', $productid)->where('color_id', $colorid)->first();
		return $Attributes;
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

	//order confirmation
	public function checkoutConfirm(Request $request)
	{
		$tempid = 0;
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

        $validator = Validator::make($request->all(), [
            'transactions' => 'required|array',
            'transactions.*.type' => 'required',
            'transactions.*.amount' => 'required|numeric|between:0,99999999.99',
            'transactions.*.payment_id' => 'nullable',
            'transactions.*.status' => 'required|in:CANCELED,CAPTURED,INITIALIZED,NOT CAPTURED',
        ]);
        if ($validator->fails()) {
            $response['data'] = $validator->errors()->all();
            return response($response, $this->failedStatus);
        }

        $workTime = $request->user()->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, $this->failedStatus);
        }

		$settingInfo = Settings::where("keyname", "setting")->first();
		//get cusatomer ID
        if ( $request->has('customer_id')){
            $userDetails = User::where('id', $request->customer_id)->first();
            if (empty($userDetails->id)) {
                $response['data'] = trans('webMessage.chooseacustomer');
                return response($response, $this->failedStatus);
            }
        } else {
            $userDetails = null;
        }

		if (empty($request->temp_uniqueid)) {
			$response['data'] = trans('webMessage.tempidmissing');
			return response($response, $this->failedStatus);
		} else {
			$tempid = $request->temp_uniqueid;
		}
		$tempOrders = self::loadTempOrders($tempid);
		if (empty($tempOrders) || count($tempOrders) == 0) {
			$response['data'] = trans('webMessage.yourcartisempty');
			return response($response, $this->failedStatus);
		}

		//check quantity exiot or not
		$tempQuantityExist = self::isQuantityExistForOrder($tempid);
		if (empty($tempQuantityExist)) {
			$response['data'] = trans('webMessage.oneoftheitemqtyexceeded');
			return response($response, $this->failedStatus);
		}

		//check min order amount
		// $totalAmtchk = self::getTotalCartAmount($request->temp_uniqueid);
		// if (!empty($settingInfo->min_order_amount) && !empty($totalAmtchk) && $settingInfo->min_order_amount >  $totalAmtchk) {
		// 	$response['data'] = trans('webMessage.minimumordermessage') . ' ' . number_format($settingInfo->min_order_amount, 3) . ' ' . \App\Currency::default();
		// 	return response($response, $this->failedStatus);
		// }

        DB::beginTransaction();
        try {
            $expectedDate = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

            $orderid = strtolower($settingInfo->prefix) . $this->OrderserialNumber();
            $ordersid = $this->OrderSidNumber();
            $orderDetails = new OrdersDetails;
            $uid = 0;
            if (!$request->has('customer_id')) {
                $uid = $orderDetails->customer_id = $request->customer_id;
            }


            if (!empty($request->area)) {
                $areaInfo = Country::where('id', $request->area)->first();
                if (empty($request->state) and $areaInfo != null) {
                    $stateInfo = Country::where('id', $areaInfo->parent_id)->first();
                    if ($stateInfo != null and $stateInfo->parent_id == 0) {
                        $request->merge(['state' => $areaInfo->id]);
                    } elseif ($stateInfo != null and $stateInfo->parent_id != 0) {
                        $request->merge(['state' => $stateInfo->id]);
                    }
                }
                if (empty($request->country) and $areaInfo != null) {
                    $stateInfo = Country::where('id', $areaInfo->parent_id)->first();
                    if ($stateInfo != null and $stateInfo->parent_id == 0) {
                        $request->merge(['country' => $stateInfo->id]);
                    } elseif ($stateInfo != null and $stateInfo->parent_id != 0) {
                        $request->merge(['country' => $stateInfo->parent_id]);
                    }
                }
            }

            $orderDetails->order_id = $orderid;
            $orderDetails->pos_id = Auth::id();
            $orderDetails->sid = $ordersid;
            $orderDetails->order_id_md5 = md5($orderid);
            $orderDetails->latitude = !empty($request->latitude) ? $request->latitude : '';
            $orderDetails->longitude = !empty($request->longitude) ? $request->longitude : '';
            $orderDetails->name = $userDetails ? $userDetails->name : (!empty($request->name) ? $request->name : 'walk in customer');
            $orderDetails->email = $userDetails ? $userDetails->email : (!empty($request->email) ? $request->email : '');
            $orderDetails->mobile = $userDetails ? $userDetails->mobile : (!empty($request->mobile) ? $request->mobile : '');
            $orderDetails->country_id = !empty($request->country) ? $request->country : '2';
            $orderDetails->state_id = !empty($request->state) ? $request->state : '0';
            $orderDetails->area_id = !empty($request->area) ? $request->area : '0';
            $orderDetails->block = !empty($request->block) ? $request->block : '';
            $orderDetails->street = !empty($request->street) ? $request->street : '';
            $orderDetails->avenue = !empty($request->avenue) ? $request->avenue : '';
            $orderDetails->house = !empty($request->house) ? $request->house : '';
            $orderDetails->floor = !empty($request->floor) ? $request->floor : '';
            $orderDetails->landmark = !empty($request->landmark) ? $request->landmark : '';
            $orderDetails->order_status = 'completed';
            $orderDetails->is_paid = 1;
            $orderDetails->device_type = 'pos';
            $orderDetails->pay_mode = count($request->transactions) == 1  ? $request->transactions[0]['type'] : 'split';
            //coupon
            if (!empty($request->coupon_code)) {
                $orderDetails->is_coupon_used = 1;
                $orderDetails->coupon_code = !empty($request->coupon_code) ? $request->coupon_code : '';
                $orderDetails->coupon_amount = !empty($request->coupon_discount) ? $request->coupon_discount : '0';
                $orderDetails->coupon_free = !empty($request->coupon_free) ? $request->coupon_free : '0';
            }
            //user discount
            if (!empty($request->user_discount)) {
                $orderDetails->seller_discount = $request->user_discount;
            }

            //delivery charges
            if ($request->delivery_status) {
                $deliveryCharge = self::get_delivery_charge($request->area, $tempid , $request->is_express_delivery);
                $orderDetails->delivery_charges = $request->delivery_status ? $deliveryCharge : 0;
            }

            $orderDetails->strLang = $strLang;
            $orderDetails->save();
            //import temp order to order table
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
                $orders = new Orders;
                $orders->oid = $orderDetails->id;
                $orders->order_id = $orderid;
                $orders->product_id = $tempOrder->product_id;
                $orders->size_id = $tempOrder->size_id;
                $orders->color_id = $tempOrder->color_id;
                $orders->unit_price = $tempOrder->unit_price;
                $orders->quantity = $tempOrder->quantity;
                $orders->save();
                //add option
                $orderInventory2 = [];
                $tempOrderOptions = OrdersTempOption::where("oid", $tempOrder->id)->get();
                if (!empty($tempOrderOptions) && count($tempOrderOptions) > 0) {
                    foreach ($tempOrderOptions as $tempOrderOption) {
                        $orderInventory2 = webCartController::changeOptionQuantity($tempOrderOption->product_id, 'd', $tempOrderOption->option_child_ids, $tempOrder->quantity); //deduct qty
                        $OrderOption = new OrdersOption;
                        $OrderOption->product_id = $tempOrderOption->product_id;
                        $OrderOption->oid = $orders->id;
                        $OrderOption->option_id = $tempOrderOption->option_id;
                        $OrderOption->option_child_ids = $tempOrderOption->option_child_ids;
                        $OrderOption->save();
                        //remove option
                        $tempOrds = OrdersTempOption::find($tempOrderOption->id);
                        $tempOrds->delete();
                    }
                }
                $orderInventories = $tempOrderInventories = array_merge($orderInventory1, $orderInventory2);
                foreach ($tempOrderInventories as $i => $orderInventory)
                    if (!($orderInventory['q'] > 0))
                        unset($orderInventories[$i]);
                $orders->inventory = json_encode($orderInventories);
                $orders->save();
                //remove temp record
                $tempOrd = OrdersTemp::find($tempOrder->id);
                $tempOrd->delete();

                //plus sub total price
                $totalprice += $subtotalprice;
            }

            //show seller discount
            if (!empty($orderDetails->seller_discount)) {
                $discounted = $totalprice - $orderDetails->seller_discount;
                $totalprice = number_format($discounted, 3);
            }

            if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                $deliveryCharge = $orderDetails->delivery_charges;
                $totalprice = $totalprice + $deliveryCharge;
            }
            //update total amount
            $orderDetails->total_amount = $totalprice;
            $orderDetails->save();

            $posTotalPay = 0;
            foreach ($request->transactions as $transaction) {
                $transactionObject = new Transaction;
                $transactionObject->presult = $transaction['status'];
                $transactionObject->postdate = date("md");
                $transactionObject->udf1 = $orderid;
                $transactionObject->udf2 = $transaction['amount'];
                $transactionObject->udf3 = $strLang;
                $transactionObject->udf4 = $uid;
                $transactionObject->udf5 = $settingInfo->name_en;
                $transactionObject->trackid = $orderid;
                $transactionObject->pay_mode = $transaction['type'];
                $transactionObject->payment_id = $transaction['payment_id'] ?? null;
                $transactionObject->save();
                $posTotalPay = $posTotalPay + ($transaction['status'] == "CAPTURED" ? $transaction['amount'] : 0);
                if ( $transaction['status'] == "CAPTURED" ) {
                    if ($transaction['type'] == "PCOD") {
                        $lastCash = $workTime->lastCash();
                        $workTime->cashs()->create([
                            'pos_id' => $workTime->pos_id,
                            'amount' => $transaction['amount'],
                            'type' => "in",
                            'description' => "Receive cash for order #".$orderid. " - Transaction : ".$transactionObject->id,
                            'refrence_id' => $transactionObject->id,
                            'refrence_type' => Transaction::class,
                            'beforeCash' => $lastCash ? $lastCash->afterCash : 0,
                            'afterCash' => $lastCash ? $lastCash->afterCash + $transaction['amount'] : $transaction['amount'],
                        ]);
                        $workTime->cashPay = $workTime->cashPay + $transaction['amount'];
                    } else {
                        $workTime->cardPay = $workTime->cardPay + $transaction['amount'];
                    }
                }
            }
            $workTime->totalSell = $workTime->totalSell + round($totalprice , 2 ) ;
            $workTime->sell++;
            if ( $userDetails )
                $workTime->customers++;
            $workTime->save();
            if (round($posTotalPay , 2 ) < round($totalprice , 2 )) {
                DB::rollBack();
//                dd($posTotalPay < $totalprice , $posTotalPay ,$totalprice);
                $response['data'] = "The customer must pay " . number_format($totalprice - $posTotalPay , 2) . " " .Currency::default().".";
                return response($response, $this->failedStatus);
            }

            $isValidMobile = Common::checkMobile($orderDetails->mobile);
            if (!empty($settingInfo->sms_text_cod_active) && !empty($settingInfo->sms_text_cod_en) && !empty($settingInfo->sms_text_cod_ar) && !empty($isValidMobile)) {
                if ($strLang == "en") {
                    $smsMessage = $settingInfo->sms_text_cod_en;
                } else {
                    $smsMessage = $settingInfo->sms_text_cod_ar;
                }
                $to = $orderDetails->mobile;
                $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                //Common::SendSms($to, $sms_msg);
            }

            $response['data'] = ['trackid' => $orderid, 'expectedDate' => $expectedDate, 'message' => trans('webMessage.yourorderisplacedsucces')];
            DB::commit();
            return response($response, $this->successStatus);
        }catch (\Exception $exception){
            DB::rollBack();
            $response['data'] = $exception->getMessage();
            return response($response, $this->failedStatus);
        }
	}


	//order refund
	public function refundOrder(Request $request , OrdersDetails $order)
	{
        $workTime = $request->user()->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, $this->failedStatus);
        }
        if ( $order->order_status != 'completed' ){
            $response['data'] = 'Can not refund Order with status: "'.$order->order_status.'" !';
            return response($response, $this->failedStatus);
        }

        DB::beginTransaction();
        try {
            $posTotalPay = 0;
            foreach ($request->transactions as $transaction) {
                $posTotalPay = $posTotalPay + $transaction['amount'];
                if ($transaction['type'] == "PCOD") {
                    $lastCash = $workTime->lastCash();
                    $workTime->cashs()->create([
                        'pos_id' => $workTime->pos_id,
                        'amount' => $transaction['amount'],
                        'type' => "out",
                        'description' => "Refund cash for order #".$order->order_id ,
                        'refrence_id' => $order->id,
                        'refrence_type' => OrdersDetails::class,
                        'beforeCash' => $lastCash ? $lastCash->afterCash : 0,
                        'afterCash' => $lastCash ? $lastCash->afterCash - $transaction['amount'] : $transaction['amount'] * -1 ,
                    ]);
                    $workTime->cashRefund = $workTime->cashRefund + $transaction['amount'];
                } else {
                    $workTime->cardRefund = $workTime->cardRefund + $transaction['amount'];
                }
            }
            $workTime->totalRefund = $workTime->totalRefund + round($order->total_amount , 2 ) ;
            $workTime->refund++;
            $workTime->save();
            if ($posTotalPay > round($order->total_amount , 2 )) {
                DB::rollBack();
                $response['data'] = "You should refund " . number_format($posTotalPay - $order->total_amount  , 2) . " " .Currency::default()." less than now!";
                return response($response, $this->failedStatus);
            }

            $order->order_status = "returned";
            $orderLists   = Orders::where("oid", $order->id)->get();

            if (!empty($orderLists) && count($orderLists) > 0 and ! $order->is_qty_rollbacked) {
                foreach ($orderLists as $orderList) {
                    //option
                    $OrderOptions = OrdersOption::where("oid", $orderList->id)->get();
                    if (!empty($OrderOptions) && count($OrderOptions) > 0) {
                        foreach ($OrderOptions as $OrderOption) {
                            webCartController::changeOptionQuantity($OrderOption->product_id ,'a', $OrderOption->option_child_ids, $orderList->quantity, $orderList->inventory); //add qty
                        }
                    }
                    webCartController::rollbackedQuantity($orderList->product_id, $orderList->quantity, $orderList->size_id, $orderList->color_id, $orderList->inventory);
                }
            }
            $order->is_qty_rollbacked = 1;
            $order->save();

            DB::commit();
            $response['data'] = "Order refunded!";
            return response($response, $this->successStatus);
        }catch (\Exception $exception){
            DB::rollBack();
            $response['data'] = $exception->getMessage();
            return response($response, $this->failedStatus);
        }
	}

    public function refundItem(Request $request , OrdersDetails $order)
	{
        $workTime = $request->user()->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, $this->failedStatus);
        }

        if (empty($request->temp_uniqueid)) {
            $response['data'] = trans('webMessage.tempidmissing');
            return response($response, $this->failedStatus);
        } else {
            $tempid = $request->temp_uniqueid;
        }
        $tempOrders = self::loadTempOrders($tempid);
        if (empty($tempOrders) || count($tempOrders) == 0) {
            $response['data'] = trans('webMessage.yourcartisempty');
            return response($response, $this->failedStatus);
        }

        DB::beginTransaction();
        try {

            $orderLists   = Orders::where("oid", $order->id)->get();
            $discountNesbat =  $order->seller_discount / ( $order->total_amount -  ( $order->delivery_charges * 1 ) + $order->seller_discount  ) ;
            $product_ids = $orderLists->pluck('product_id')->toArray();
            $totalprice = 0;
            $totalDiscount = 0;
            foreach ($tempOrders as $tempOrder) {
                if ( ! in_array($tempOrder->product_id , $product_ids) ){
                    DB::rollBack();
                    return response(['data' => 'Item dosen\'t exist in order' , 'itemId' => $tempOrder->product_id], $this->failedStatus);
                }
                $lastOrderIndex = $orderLists->search(function($item, $key) use($tempOrder) { return $item->product_id == $tempOrder->product_id;});
                $lastOrder = $orderLists[$lastOrderIndex];
                if ( $lastOrder->quantity < $tempOrder->quantity ){
                    DB::rollBack();
                    return response(['data' => 'qunatity of refund more than order!' , 'itemId' => $tempOrder->product_id], $this->failedStatus);
                }
                $tempQuantity = $tempOrder->quantity;
                $tempPriceRefund =  $lastOrder->unit_price * $tempQuantity ;
                $totalDiscount = $totalDiscount +  $tempPriceRefund * $discountNesbat  ;
                $tempPriceRefund =  $tempPriceRefund  - $tempPriceRefund * $discountNesbat  ;
                $totalprice = $totalprice + $tempPriceRefund;
                $inventories = (array) json_decode($lastOrder->inventory , true);
                $inventories = array_reverse($inventories);
                foreach ($inventories as $i => $inventory){
                    if ( $tempQuantity <= 0 )
                        break;
                    if ( $tempQuantity > $inventory['q']){
                        $deductQuantity = $inventory['q'];
                        $tempQuantity -= $inventory['q'];
                        $inventory['q'] = 0 ;
                    } else {
                        $deductQuantity = $tempQuantity;
                        $inventory['q'] = $inventory['q'] - $tempQuantity;
                        $tempQuantity = 0;
                    }
                    if ( $inventory['q'] > 0 ){
                        $inventories[$i]['q'] = $inventory['q'] ;
                    } else {
                        unset($inventories[$i]);
                    }
                    AdminCustomersController::deductQuantity($lastOrder->product_id,$inventory['IID'], $deductQuantity, $lastOrder->size_id,  $lastOrder->color_id , 1 );
                }
                $orderInventory2 = [];
                $OrderOptions = OrdersOption::where("oid", $lastOrder->id)->get();
                if (!empty($OrderOptions) && count($OrderOptions) > 0) {
                    $tempQuantity = $tempOrder->quantity;
                    $productDetails = Product::find($lastOrder->product_id);
                    foreach ($OrderOptions as $OrderOption) {
                        //deduct qty from option
                        $Option = ProductOptions::find($OrderOption->option_child_ids);
                        $Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
                        $OptionsID = $Options->pluck('id')->toArray();
                        $inventories2 = (array) json_decode($lastOrder->inventory , true);
                        $inventories2 = array_reverse($inventories2);
                        foreach ($inventories2 as $inventory){
                            $productQuantity = $productDetails->getQuantity($inventory['IID'], null ,-1, true, false, true);
                            foreach ($productQuantity as $quantityOne){
                                if ( in_array($quantityOne->option_id,$OptionsID) ){
                                    if ( $tempQuantity > $inventory['q']){
                                        $quantityOne->quantity += $inventory['q'];
                                        $tempQuantity -= $inventory['q'] ;
                                        $quantityOne->save();
                                    } else {
                                        $quantityOne->quantity += $tempQuantity;
                                        $tempQuantity  = 0 ;
                                        $quantityOne->save();
                                        break ;
                                    }
                                }
                                if ( $tempQuantity == 0 ){
                                    break 3 ;
                                }
                            }
                        }
                    }
                }
                foreach ( $orderInventory2 as $inventoryUse){
                    $find = false ;
                    foreach ( $inventories as $i => $inventory){
                        if ( $inventory['IID'] == $inventoryUse['IID'] ){
                            $inventories[$i]['q'] -= $inventoryUse['q'];
                            $find = true;
                            break;
                        }
                    }
                    if ( ! $find ){
                        $inventories[] = $inventoryUse;
                    }
                }
                $tempOrderInventories = $inventories;
                foreach ( $tempOrderInventories as $i => $orderInventory )
                    if ( ! ( $orderInventory['q'] > 0 ) )
                        unset($inventories[$i]);
                usort($inventories, function($a, $b) {
                    $inventory1 = Inventory::find($a['IID']);
                    $inventory2 = Inventory::find($b['IID']);
                    return $inventory1->priority - $inventory2->priority;
                });
                $lastOrder->update(['inventory' => json_encode($inventories) , 'quantity' => $lastOrder->quantity - $tempOrder->quantity ]);
                $tempOrder->delete();
            }
            $orderCheckLists   = Orders::where("oid", $order->id)->get();
            $isAllReturn = true;
            foreach ($orderCheckLists as $orderCheckList){
                if ( $orderCheckList->quantity > 0){
                    $isAllReturn = false;
                    break;
                }
            }
            if ( $isAllReturn )
                $order->order_status = 'returned';

            $posTotalPay = 0;
            foreach ($request->transactions as $transaction) {
                $posTotalPay = $posTotalPay + $transaction['amount'];
                if ($transaction['type'] == "PCOD") {
                    $lastCash = $workTime->lastCash();
                    $workTime->cashs()->create([
                        'pos_id' => $workTime->pos_id,
                        'amount' => $transaction['amount'],
                        'type' => "out",
                        'description' => "Refund cash for order #".$order->order_id ,
                        'refrence_id' => $order->id,
                        'refrence_type' => OrdersDetails::class,
                        'beforeCash' => $lastCash ? $lastCash->afterCash : 0,
                        'afterCash' => $lastCash ? $lastCash->afterCash - $transaction['amount'] : $transaction['amount'] * -1 ,
                    ]);
                    $workTime->cashRefund = $workTime->cashRefund + $transaction['amount'];
                } else {
                    $workTime->cardRefund = $workTime->cardRefund + $transaction['amount'];
                }
            }
            $workTime->totalRefund = $workTime->totalRefund + round($totalprice , 2 ) ;
            $workTime->refund++;
            $workTime->save();
//            if ($posTotalPay > round($totalprice , 2 )) {
//                DB::rollBack();
//                $response['data'] = "You should refund " . number_format($posTotalPay - $totalprice  , 2) . " " .Currency::default()." less than now! (".number_format($totalprice  , 2) . " " .Currency::default() . ") " ;
//                return response($response, $this->failedStatus);
//            }
            $order->total_amount = $order->total_amount - $totalprice;
            $order->seller_discount = $order->seller_discount - $totalDiscount;
            $order->save();

            DB::commit();
            $response['data'] = "Order refunded!";
            return response($response, $this->successStatus);
        }catch (\Exception $exception){
            DB::rollBack();
            $response['data'] = $exception->getMessage();
            return response($response, $this->failedStatus);
        }
	}




	public static function isQuantityExistForOrder($tempid)
	{
		$flag = 0;
		$tempOrders = self::loadTempOrders($tempid);
		if (!empty($tempOrders) && count($tempOrders) > 0) {
			foreach ($tempOrders as $tempOrder) {
				$existQty = apiCartController::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id,$tempOrder);
				if ($existQty >= $tempOrder->quantity) {
					$flag = 1;
				}
			}
		}
		return $flag;
	}

	public function getPaymentMethod()
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$settingInfo = Settings::where("keyname", "setting")->first();
		$paymentLists = explode(",", $settingInfo->payments);

		$pay = [];

		foreach ($paymentLists as $paymentList) {
			$pay[] = [
				"name"      => trans('webMessage.payment_' . $paymentList),
				"key_name"  => strtoupper($paymentList),
				"image"     => url('uploads/paymenticons/' . strtolower($paymentList) . '.png')
			];
		}
		$response['data'] = $pay;
		return response($response, $this->successStatus);
	}

	//get category
	public function category(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$parent_id = !empty($request->parent_id) ? trim($request->parent_id) : 0;

		$category = DB::table('gwc_categories')->where('gwc_categories.is_active', 1)->where('gwc_categories.parent_id', $parent_id)
			->select('gwc_products_category.*', 'gwc_categories.*')
			->join('gwc_products_category', 'gwc_products_category.category_id', '=', 'gwc_categories.id')
			->groupBy('gwc_products_category.category_id')->get();
		if (!empty($category) && count($category) > 0) {
			$cats = [];
			foreach ($category as $cat) {
				$title = $strLang == "en" ? $cat->name_en : $cat->name_ar;
				$imageUrl = !empty($cat->image) ? url('uploads/category/thumb/' . $cat->image) : url('uploads/no-image.png');
				$caty[] = [
					"id"   => $cat->category_id,
					"name" => $title,
					"image" => $imageUrl,
					"child" => $this->childCategory($cat->category_id)
				];
			}
			$response['data'] = $caty;

			return response($response, $this->successStatus);
		} else {
			$response['data'] = trans('webMessage.recordnotfound');
			return response($response, $this->failedStatus);
		}
	}


	//get child category
	public function childCategory($parent_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$category = DB::table('gwc_categories')->where('gwc_categories.is_active', 1)->where('gwc_categories.parent_id', $parent_id)
			->select('gwc_products_category.*', 'gwc_categories.*')
			->join('gwc_products_category', 'gwc_products_category.category_id', '=', 'gwc_categories.id')
			->groupBy('gwc_products_category.category_id')->get();

		if (!empty($category) && count($category) > 0) {
			$cats = [];
			foreach ($category as $cat) {
				$title = $strLang == "en" ? $cat->name_en : $cat->name_ar;
				$imageUrl = !empty($cat->image) ? url('uploads/category/thumb/' . $cat->image) : url('uploads/no-image.png');
				$caty[] = [
					"id"   => $cat->category_id,
					"name" => $title,
					"image" => $imageUrl,
					"child" => $this->childCategory($cat->category_id)
				];
			}
			return $caty;
		}
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
			$response['data'] = trans('webMessage.idmissing');
			return response($response, $this->failedStatus);
		}

		$total = self::getTotalCartAmount($request->temp_uniqueid);
		if (empty($request->coupon_code)) {
			$response['data'] = trans('webMessage.coupon_required');
			return response($response, $this->failedStatus);
		}
		if (empty($total)) {
			$response['data'] = trans('webMessage.yourcartisempty');
			return response($response, $this->failedStatus);
		}

		$curDate = date("Y-m-d");
		$coupon = Coupon::where('is_active', 1)
			->where('coupon_code', $request->coupon_code)
			->where('is_for', 'app')
			->first();
		if (empty($coupon->id)) {
			$response['data'] = trans('webMessage.invalid_coupon_code');
			return response($response, $this->failedStatus);
		}
		if (!empty($coupon->id) && strtotime($curDate) < strtotime($coupon->start_date)) {
			$response['data'] = trans('webMessage.coupon_can_be_used_from') . $coupon->start_date;
			return response($response, $this->failedStatus);
		}
		if (!empty($coupon->id) && strtotime($curDate) > strtotime($coupon->end_date)) {
			$response['data'] = trans('webMessage.coupon_is_expired_on') . $coupon->end_date;
			return response($response, $this->failedStatus);
		}
		if (!empty($coupon->id) && ($total < $coupon->price_start || $total > $coupon->price_end)) {

			$response['data'] = trans('webMessage.coupon_can_be_apply_for_price_range') . \App\Currency::default() . ' ' . $coupon->price_start . ' - ' . \App\Currency::default() . ' ' . $coupon->price_end;
			return response($response, $this->failedStatus);
		}
		if (!empty($coupon->id) && empty($coupon->usage_limit)) {
			$response['data'] = trans('webMessage.usage_limit_exceeded');
			return response($response, $this->failedStatus);
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
        if (!empty($coupon->id) && !empty($coupon->is_free) and $totalKD > 0) {
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

		$response['data'] = [
			'coupon_free' => $gb_coupon_free,
			'coupon_code' => $request->coupon_code,
			'coupon_discount' => $discountAmt,
			'coupon_discount_text' => $discountAmttxt
		];
		return response($response, $this->successStatus);
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
			$response['data'] = trans('webMessage.tempidmissing') . '(ID)';
			return response($response, $this->failedStatus);
		}
		if (empty($request->temp_uniqueid)) {
			$response['data'] = trans('webMessage.tempidmissing') . '(TEMP UNIQUE ID)';
			return response($response, $this->failedStatus);
		}
		if (empty($request->quantity)) {
			$response['data'] = trans('webMessage.quantity_required');
			return response($response, $this->failedStatus);
		}

		$session_id = $request->temp_uniqueid;

		$tempOrder  = OrdersTemp::where('id', $request->tempid)->where('unique_sid', '=', $session_id)->first();
		if (!empty($tempOrder->id)) {
			$productDetails   = Product::where('id', $tempOrder->product_id)->first();


            if ( ! $request->has('refund') ) {
                if (!empty($productDetails->is_attribute) && (!empty($tempOrder->size_id) || !empty($tempOrder->color_id))) {
                    $aquantity = apiCartController::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id, $tempOrder);
                    if (!empty($request->quantity) && $request->quantity > $aquantity) {
                        $response['data'] = trans('webMessage.quantity_is_exceeded');
                        return response($response, $this->failedStatus);
                    }
                } else {
                    if (empty($productDetails->is_attribute) && !empty($request->quantity) && $request->quantity > $productDetails->quantity) {
                        $response['data'] = trans('webMessage.quantity_is_exceeded');
                        return response($response, $this->failedStatus);
                    }
                }
            }

			$tempOrder->quantity   = !empty($request->quantity) ? $request->quantity : '1';
			$tempOrder->save();
			$totalAmount = self::getTotalCartAmount($session_id);
			$countitems  = self::countTempOrders($session_id);
			$item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
			$response['data'] = ['total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.quantity_is_updated')];
			return response($response, $this->successStatus);
		} else {
			$response['data'] = trans('webMessage.norecordfound');
			return response($response, $this->failedStatus);
		}
	}
	////////////////////////////////Get Temp Orders/////////////////////
	public function getTempOrders(Request $request){
	if(!empty(app()->getLocale())){ $strLang = app()->getLocale();}else{$strLang="en";}
	if(empty($request->temp_uniqueid)){
	$response['data']=trans('webMessage.idmissing');
	return response($response,$this->failedStatus);
	}else{
	$tempid = $request->temp_uniqueid;
	$tempOrders = self::loadTempOrders($tempid);
	if(empty($tempOrders) || count($tempOrders)==0){
	$response['data']=trans('webMessage.yourcartisempty');
	return response($response,$this->failedStatus);
	}
	
	if(!empty($tempOrders) && count($tempOrders)>0){
	$totalAmount =0;$grandtotal =0;$subtotalprice=0;$attrtxt='';$t=1;
	$attribute_txt=[];$coupon_discount=0;$delivery_charges=0;
	$tempSub=[];
	foreach($tempOrders as $tempOrder){
	    $productDetails =self::getProductDetails($tempOrder->product_id);
		
		if(!empty($tempOrder->size_id)){
		$sizeName = self::sizeNameStatic($tempOrder->size_id,$strLang);
		$attribute_txt['size_id']  =$tempOrder->size_id;
		$attribute_txt['size_name']=$sizeName;
		}		
		if(!empty($tempOrder->color_id)){
		$colorName = self::colorNameStatic($tempOrder->color_id,$strLang);
		$attribute_txt['color_id']=$tempOrder->color_id;
		$attribute_txt['color_name']=$colorName;
		}
		
		$orderOptions = self::getOptionsDtailsOrder($tempOrder->id);
		if(!empty($orderOptions)){
		$attribute_txt['options']= $orderOptions;
		}
		
		$unitprice     = $tempOrder->unit_price;
		$subtotalprice = $unitprice*$tempOrder->quantity;
		$title         = $productDetails['title_'.$strLang];
		if(!empty($productDetails['image'])){
		$imageUrl = url('uploads/product/thumb/'.$productDetails['image']);
		}else{
		$imageUrl = url('uploads/no-image.png');
		}
		//available quantity
		$aquantity = apiCartController::getProductQuantity($tempOrder->product_id,$tempOrder->size_id,$tempOrder->color_id);
	   
	    $tempSub[]=[
	              "id"=>$tempOrder->id,
				  "product_id"=>$tempOrder->product_id,
				  "item_code"=>$productDetails->item_code,
                  "title"=>$title,
                  "translate"=> [
                      'en' => $productDetails['title_en'],
                      'ar' => $productDetails['title_ar'],
                  ],
				  "imageUrl"=>$imageUrl,
				  "attribute_txt"=>$attribute_txt,
				  "unitprice"=>$unitprice,
				  "color_id"=>(string)$tempOrder->color_id,
				  "size_id"=>(string)$tempOrder->size_id,
				  "quantity"=>$tempOrder->quantity,
				  "unique_sid"=>$tempOrder->unique_sid,
				  "available_quantity"=>$aquantity,
				  "subtotal"=>$subtotalprice,
				 ];	
				 
		//sum sub total to grand total
		$totalAmount+=$subtotalprice;	
		
		$attribute_txt=[];	 
	}

	}	
	}
	$userDiscount =$request->user_discount?$request->user_discount:0;
	if(!empty($tempSub) && count($tempSub)>0){
	$grandtotal = $totalAmount;

    if ($userDiscount > 0) {
	$discounted = $totalAmount - $userDiscount;
	$grandtotal = number_format($discounted, 3);
			}
			
	//check coupon discount
	if(!empty($request->coupon_discount)){
	$coupon_discount = (float)$request->coupon_discount;
	$grandtotal      = (float)($grandtotal-$request->coupon_discount);
	}
	//check delivery charges
	if(!empty($request->area_id)){
    $delivery_charges = self::get_delivery_charge($request->area_id , $tempid , $request->is_express_delivery);
	$grandtotal       = (float)($grandtotal+$delivery_charges);
	}
	
	$response['data']=[
	                  'temoOrders'=>$tempSub,
					  'total'=>$totalAmount,
					  'coupon_discount'=>$coupon_discount,
					  'user_discount'=>$userDiscount,
					  'delivery_charges'=>$delivery_charges,
					  'grandtotal'=>$grandtotal
					 ];
	return response($response,$this->successStatus);
	}else{
	$response['data']=trans('webMessage.yourcartisempty');
	return response($response,$this->failedStatus);
	}
	}

    //get product details by id
    public function getProductDetail(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }

        if (empty($request->product_id)) {
            $success['data'] = trans('webMessage.idmissing');
            return response()->json($success, $this->failedStatus);
        }

        $productDetails = Product::where('id', $request->product_id)->where('is_active', '!=', 0)->first();
        if (empty($productDetails->id)) {
            $success['data'] = trans('webMessage.norecordfound');
            return response()->json($success, $this->failedStatus);
        }
        ///collect item values
        $prodDetails['id']           = $productDetails->id;
        $prodDetails['item_code']    = $productDetails->item_code;
        $prodDetails['sku_no']       = (string)$productDetails->sku_no;
        $prodDetails['title']        = $productDetails['title_' . $strLang];
        $prodDetails['details']      = $productDetails['details_' . $strLang];
        $prodDetails['details_ios']  = strip_tags($productDetails['details_' . $strLang]);
        if ($productDetails->image) {
            $imageUrl_large              = url('uploads/product/' . $productDetails->image);
            $imageUrl_small              = url('uploads/product/thumb/' . $productDetails->image);
        } else {
            $imageUrl_large              = url('uploads/no-image.png');
            $imageUrl_small              = url('uploads/no-image.png');
        }
        $prodDetails['imageUrl_large'] = $imageUrl_large;
        $prodDetails['imageUrl_small'] = $imageUrl_small;

        //get gallery
        $galleries = apiController::getGalleries($productDetails->id);
        $gall = [];
        if (!empty($galleries)) {
            foreach ($galleries as $gallery) {
                $gall[] = ["large" => url('uploads/product/' . $gallery->image), "small" => url('uploads/product/thumb/' . $gallery->image)];
            }
        }
        $prodDetails['gallery']       =  $gall;
        $prodDetails['youtube_id']    =  (string)$productDetails->youtube_url_id;

        $prodDetails['caption_name']  =  (string)$productDetails['caption_' . $strLang];
        $prodDetails['caption_color'] =  (string)$productDetails['caption_color'];
        $prodDetails['quantity']      =  (string)$productDetails['quantity'];

        if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
            $prodDetails['countdown_datetime'] = (string)$productDetails['countdown_datetime'];
            $prodDetails['retail_price']     =  (float)$productDetails['countdown_price'];
            $prodDetails['old_price']        =  (float)$productDetails['retail_price'];
        } else {
            $prodDetails['countdown_datetime']   = "";
            $prodDetails['countDown_price']  = "";
            $prodDetails['retail_price']     =  (float)$productDetails['retail_price'];
            $prodDetails['old_price']        =  (float)$productDetails['old_price'];
        }
        $prodDetails['retail_price']   =  round($prodDetails['retail_price'],3);
        $prodDetails['old_price']   =  round($prodDetails['old_price'],3);

        $Options          = apiController::getProductOptions($productDetails['id']);
        $prodDetails['options'] = [
            "section_id" => 0,
            "sizes" => [],
            "colors" => [],
            "sizes_colors" => [],
            "others" => [],
        ];
        foreach ( $Options  as $option){
            if ( $option['option_id'] == 1 ){
                $prodDetails['options']['sizes'] = $option['child_options']['sizes'];
                $prodDetails['options']['section_id'] = 1;
            }elseif ( $option['option_id'] == 2 ){
                $prodDetails['options']['colors'] = $option['child_options']['colors'];
                $prodDetails['options']['section_id'] = 2;
            }elseif ( $option['option_id'] == 3 ){
                $prodDetails['options']['sizes_colors'] = $option['child_options']['sizes_colors']['sizes'];
                $prodDetails['options']['section_id'] = 3;
            }else{
                $prodDetails['options']['section_id'] = 4;
                $prodDetails['options']['others'] = [
                    'title' => $option['option_name'],
                    'type' => $option['option_type'] == "select for each order" ? "select" : $option['option_type'],
                    'is_required' => $option['is_required'],
                    'id' => $option['option_id'],
                    'values' => [],
                ];
                if ( is_array($option['child_options']['others']) )
                    foreach ( $option['child_options']['others'] as $child )
                        $prodDetails['options']['others']['values'][] = [
                            'id' => $child['id'] ,
                            //'id' => $child['option_value_id'] ,
                            'title' => $child['option_value_name_'.$strLang] ,
                            'quantity' => $child['quantity'] ,
                            'retail_price' => $child['is_price_add'] ?
                                ( $prodDetails['retail_price'] + $child['retail_price'] )
                                :
                                ( $child['is_deduct'] ?
                                    ( $prodDetails['retail_price'] - $child['retail_price'] )
                                    : 0
                                ) ,
                            'old_price' => 0
                        ];
                //$prodDetails['options']['log_just_for_test'] = $option;
            }
        }
        $prodDetails['default_currency'] = Currency::default();

        $success['data'] = ['productDetails' => $prodDetails];
        return response()->json($success, $this->successStatus);
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


	//get child
	public static function getChildOptionsDtailsString($ids)
	{

		$optxt = '';
		$explode = explode(",", $ids);
		if (count($explode) > 0) {
			for ($i = 0; $i < count($explode); $i++) {
				$optxt .= self::getJoinOptions($explode[$i]);
			}
		} else {
			$optxt .= self::getJoinOptions($ids);
		}
		return $optxt;
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

	//delete record from temp order
	public function removeTempOrder(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (empty($request->temp_uniqueid) || empty($request->id)) {
			$response['data'] = trans('webMessage.idmissing');
			return response($response, $this->failedStatus);
		}

		$tempOrder = OrdersTemp::where('unique_sid', $request->temp_uniqueid)->where('id', $request->id)->first();
		if (empty($tempOrder->id)) {
			$response['data'] = trans('webMessage.norecordfound');
			return response($response, $this->failedStatus);
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
		$response['data'] = trans('webMessage.itemsareremovedfromcart');
		return response($response, $this->successStatus);
	}
	
		//delete all record from temp order
	public function removeAllTempOrder(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (empty($request->temp_uniqueid)) {
			$response['data'] = trans('webMessage.idmissing');
			return response($response, $this->failedStatus);
		}

		$tempOrder = OrdersTemp::where('unique_sid', $request->temp_uniqueid)->first();
		if (empty($tempOrder->id)) {
			$response['data'] = trans('webMessage.norecordfound');
			return response($response, $this->failedStatus);
		}

		$tempOrders = self::loadTempOrders($request->temp_uniqueid);

		if (!empty($tempOrders) && count($tempOrders) > 0) {
			foreach ($tempOrders as $tempOrder) {
				self::removeOptions($tempOrder->id);
				$tempOrder->delete();
			}
		}
		$response['data'] = trans('webMessage.itemsareremovedfromcart');
		return response($response, $this->successStatus);
	}

	//Removing all options from OrdersTempOption based oid
	public static function removeOptions($oid)
	{
		$optionsboxs = OrdersTempOption::where("oid", $oid)->get();
		if (!empty($optionsboxs) && count($optionsboxs) > 0) {
			foreach ($optionsboxs as $optionsbox) {
				$tempOrdersOption = OrdersTempOption::find($optionsbox->id);
				$tempOrdersOption->delete();
			}
		}
	}
	
	
	///////////////////////////////////////////////Add to Cart//////////////////////////////
	public function addtocart(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}


		if (empty($request->product_id)) {
			$response['data'] = trans('webMessage.product_id_required');
			return response($response, $this->failedStatus);
		}

		if (empty($request->price)) {
			$response['data'] = trans('webMessage.price_required');
			return response($response, $this->failedStatus);
		}

		if (empty($request->quantity)) {
			$response['data'] = trans('webMessage.quantity_required');
			return response($response, $this->failedStatus);
		}

		if (empty($request->temp_uniqueid)) {
			$response['data'] = trans('webMessage.tempidmissing');
			return response($response, $this->failedStatus);
		}
        if (empty($request->option_scection_id)) {
            $request->merge(['option_scection_id' => 0 ]);
        }

		$productDetails   = Product::where('id', $request->product_id)->first();
		if (empty($productDetails->id)) {
			$response['data'] = trans('webMessage.item_not_found');
			return response($response, $this->failedStatus);
		}

		//check size/color attribute
		if ($request->option_scection_id == 3) {
			if (empty($request->size_attribute)) {
				$response['data'] = trans('webMessage.size_required');
				return response($response, $this->failedStatus);
			}
			if (empty($request->color_attribute)) {
				$response['data'] = trans('webMessage.color_required');
				return response($response, $this->failedStatus);
			}
            if ( ! $request->has('refund') ) {
                //check size color attr
                $aquantity = apiCartController::getProductQuantity($request->product_id, $request->size_attribute, $request->color_attribute);
                if (!empty($request->quantity) && $request->quantity > $aquantity) {
                    $response['data'] = trans('webMessage.quantity_is_exceeded');
                    return response($response, $this->failedStatus);
                }
            }
			//end size color attr
		} elseif ($request->option_scection_id == 1) {
			if (empty($request->size_attribute)) {
				$response['data'] = trans('webMessage.size_required');
				return response($response, $this->failedStatus);
			}
            if ( ! $request->has('refund') ) {
                //check size color attr
                $aquantity = apiCartController::getProductQuantity($request->product_id, $request->size_attribute, 0);
                if (!empty($request->quantity) && $request->quantity > $aquantity) {
                    $response['data'] = trans('webMessage.quantity_is_exceeded');
                    return response($response, $this->failedStatus);
                }
            }
			//end size color attr
		} elseif ($request->option_scection_id == 2) {
			if (empty($request->color_attribute)) {
				$response['data'] = trans('webMessage.color_required');
				return response($response, $this->failedStatus);
			}
            if ( ! $request->has('refund') ) {
                //check size color attr
                $aquantity = apiCartController::getProductQuantity($request->product_id, 0, $request->color_attribute);
                if (!empty($request->quantity) && $request->quantity > $aquantity) {
                    $response['data'] = trans('webMessage.quantity_is_exceeded');
                    return response($response, $this->failedStatus);
                }
            }
			//end size color attr
		} elseif ($request->option_scection_id == 4) {
            $flag = self::checkOptionsFields($request);
            if ($flag) {
                $response['data'] = trans('webMessage.options_required');
                return response($response, $this->failedStatus);
            }
		}
        if (empty($productDetails->is_attribute) && !empty($request->quantity) && $request->quantity > $productDetails->quantity) {
            $response['data'] = trans('webMessage.quantity_is_exceeded');
            return response($response, $this->failedStatus);
        }

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

        $tempOrdersObject = OrdersTemp::where($whereClause);
        foreach($productoptions as $productoption){
            if ( $productoption->option_type == "select for each order"){
                $tempOrdersObject->whereHas('options', function ($query) use($request,$productoption) {
                    return $query->where('product_id' ,  $request->product_id)
                        ->where('option_id' ,  $productoption->custom_option_id )
                        ->where('option_child_ids' ,  $request->input('option.'.$productoption->custom_option_id)??"" );
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

			$response['data'] = ['cart_item_id'=>$tempOrder->id  ,'total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.quantity_is_updated')];
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
            if ($request->option_scection_id == 4) {
                self::detailsTempOrders($request, $tempOrder->id);
            }
			//end
			$totalAmount = self::getTotalCartAmount($session_id);
			$countitems  = self::countTempOrders($session_id);
			$item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));

			$response['data'] = ['cart_item_id'=>$tempOrder->id ,'total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.item_is_added')];
			//end
		}

		return response($response, $this->successStatus);
	}


	//get temp orders 
	public static function loadTempOrders($tempid)
	{
		$session_id = $tempid;
		$tempOrders = OrdersTemp::where('unique_sid', $session_id)->orderBy('created_at', 'DESC')->get();
		return $tempOrders;
	}

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

	//count temp order
	public static function countTempOrders($tempid)
	{
		$session_id = $tempid;
		$tempOrders = OrdersTemp::where('unique_sid', $session_id)->get()->count();
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

	//add options
	public static function detailsTempOrders($request, $oid)
	{
		$productid = $request->product_id;
		$productoptions = ProductOptionsCustomChosen::where('product_id', $productid)
			->where('custom_option_id', '>=', 4)
            ->groupBy('custom_option_id')
			->get();
		if (!empty($productoptions) && count($productoptions) > 0) {
			foreach ($productoptions as $productoption) {
				if ($request->has('option.'. $productoption->custom_option_id )) {
					$tempOrderCheck = new OrdersTempOption;
					$tempOrderCheck->product_id       = $productid;
					$tempOrderCheck->oid              = $oid;
					$tempOrderCheck->option_id        = $productoption->custom_option_id;
                    $tempOrderCheck->option_child_ids = trim(implode(",", (array) $request->input('option.'. $productoption->custom_option_id )) , ',' );
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


	//check option validation
	public static function checkOptionsFields($request)
	{

        $productid = $request->product_id;
        $productoptions = ProductOptionsCustomChosen::where('product_id', $productid)
            ->where('custom_option_id', '>=', 4)
            ->orderBy('custom_option_id', 'ASC')->get();

        if (!empty($productoptions) && count($productoptions) > 0) {
            foreach ($productoptions as $productoption) {
                if ( ! $request->has('option.'. $productoption->custom_option_id ) and $productoption->is_required )
                    return true ;
            }
        }
        return false ;
	}


	//main search
	public function products(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$totalItems = [];

		$limit = 20;
		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		$settingInfo     = Settings::where("keyname", "setting")->first();


		$search = trim($request->keyword);
		$catid  = !empty($request->catid)?$request->catid:'0';

		

		if(!empty($catid)) {
		$productLists = Product::where('gwc_products.is_active', '!=', 0)->where('gwc_products_category.category_id', $catid);
		$productLists = $productLists->select('gwc_products.*','gwc_products_category.product_id','gwc_products_category.category_id');
		$productLists = $productLists->join('gwc_products_category','gwc_products_category.product_id','=','gwc_products.id');
		
		if (!empty($search)) {
			$explode_search = explode(' ', $search);
			$productLists = $productLists->where(function ($q) use ($search, $strLang) {
				$explode_search = explode(' ', $search);
				if (!empty(app()->getLocale())) {
					$strLang = app()->getLocale();
				} else {
					$strLang = "en";
				}
				$q->where('gwc_products.title_' . $strLang, 'like', '%' . $search . '%')
					->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $search . '%')
					->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
				if (count($explode_search) > 1 && !empty($productLists)) {
					foreach ($explode_search as $searchword) {
						$productLists = $productLists->orwhere('title_' . $strLang, 'like', '%' . $searchword . '%')
							->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $searchword . '%')
							->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
					}
				}
			});
		  }	
		}else{		
		$productLists = Product::where('gwc_products.is_active', '!=', 0);
		if (!empty($search)) {
			$explode_search = explode(' ', $search);
			$productLists = $productLists->where(function ($q) use ($search, $strLang) {
				$explode_search = explode(' ', $search);
				if (!empty(app()->getLocale())) {
					$strLang = app()->getLocale();
				} else {
					$strLang = "en";
				}
				$q->where('gwc_products.title_' . $strLang, 'like', '%' . $search . '%')
					->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $search . '%')
					->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
				if (count($explode_search) > 1 && !empty($productLists)) {
					foreach ($explode_search as $searchword) {
						$productLists = $productLists->orwhere('title_' . $strLang, 'like', '%' . $searchword . '%')
							->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $searchword . '%')
							->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
					}
				}
			});
		  }
        }



		//count total records
		$totalItems   = $productLists->get()->count();

		$productLists = $productLists->orderBy('gwc_products.id', 'DESC')->offset($offset)->limit($limit)->get();



		///customize product listising
		$prods = [];
		if (!empty($productLists) && count($productLists) > 0) {

			foreach ($productLists as $productList) {
				if (!empty($productList->image)) {
					$imageUrl = url('uploads/product/thumb/' . $productList->image);
				} else {
					$imageUrl = url('uploads/no-image.png');
				}

				if ($strLang == "en") {
					$title = $productList->title_en;
					$caption_title = (string)$productList->caption_en;
				} else {
					$title = $productList->title_ar;
					$caption_title = (string)$productList->caption_ar;
				}

				if (!empty($productList->countdown_datetime) && strtotime($productList->countdown_datetime) > strtotime(date('Y-m-d'))) {
					$retail_price    = (float)$productList->countdown_price;
					$old_price       = (float)$productList->retail_price;
				} else {
					$retail_price    = (float)$productList->retail_price;
					$old_price       = (float)$productList->old_price;
				}


				$prods[] = [
					'id'             => $productList->id,
					'title'          => $title,
					'is_attribute'   => $productList->is_attribute,
					'category_id' => "",
					'category' => "",
					'attributes'     => self::getAttributes($productList->id),
					'is_stock'       => self::IsAvailableQuantity($productList->id),
					'caption_title'  => $caption_title,
					'caption_color'  => (string)$productList->caption_color,
					'is_attribute'   => $productList->is_attribute,
					'sku_no'         => (string)$productList->sku_no,
					'quantity'       => (string)$productList->quantity,
					'item_code'      => $productList->item_code,
					'sku_no'         => (string)$productList->sku_no,
					'retail_price'   => $retail_price,
					'old_price'      => $old_price,
					'image'          => (string)$imageUrl
				];
			}
			$prods = $prods;
		}
		//end


		$response['data'] = ['productLists' => $prods, 'totalItems' => $totalItems];
		return response($response, $this->successStatus);
	}


    //category search
    public function productsByCategory(Request $request)
    {
        $totalItems = [];
        $settingInfo     = Settings::where("keyname", "setting")->first();


        $search = trim($request->keyword);
        $catid  = !empty($request->catid) ? $request->catid : '0';



        if (!empty($catid)) {
            $productLists = Product::where('gwc_products.is_active', '!=', 0)->where('gwc_products_category.category_id', $catid);
            $productLists = $productLists->select('gwc_products.*', 'gwc_products_category.product_id', 'gwc_products_category.category_id');
            $productLists = $productLists->join('gwc_products_category', 'gwc_products_category.product_id', '=', 'gwc_products.id');

            if (!empty($search)) {
                $explode_search = explode(' ', $search);
                $productLists = $productLists->where(function ($q) use ($search) {
                    $explode_search = explode(' ', $search);
                    $q->where('gwc_products.title_en', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.title_ar', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.details_ar' , 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.details_en' , 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
                    if (count($explode_search) > 1) {
                        foreach ($explode_search as $searchword) {
                            $q = $q->orwhere('title_ar', 'like', '%' . $searchword . '%')->orwhere('title_en', 'like', '%' . $searchword . '%')
                                ->orwhere('gwc_products.details_ar', 'like', '%' . $searchword . '%')->orwhere('gwc_products.details_en', 'like', '%' . $searchword . '%')
                                ->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
                        }
                    }
                });
            }
        } else {
            $productLists = Product::where('gwc_products.is_active', '!=', 0);
            if (!empty($search)) {
                $explode_search = explode(' ', $search);
                $productLists = $productLists->where(function ($q) use ($search) {
                    $explode_search = explode(' ', $search);
                    $q->where('gwc_products.title_ar', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.details_ar', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.title_en', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.details_en', 'like', '%' . $search . '%')
                        ->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
                    if (count($explode_search) > 1 ) {
                        foreach ($explode_search as $searchword) {
                            $q = $q->orwhere('title_ar', 'like', '%' . $searchword . '%')->orwhere('title_en', 'like', '%' . $searchword . '%')
                                ->orwhere('gwc_products.details_ar', 'like', '%' . $searchword . '%')->orwhere('gwc_products.details_en', 'like', '%' . $searchword . '%')
                                ->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
                        }
                    }
                });
            }
        }



        //count total records
        $totalItems   = $productLists->get()->count();

        $productLists = $productLists->orderBy('gwc_products.id', 'DESC')->get();

        $cart = ['added' => false , 'information' => []];
        if ( $request->has('temp_uniqueid') and $request->temp_uniqueid and $totalItems == 1){
            if ( $productLists[0]->is_attribute != "1" and $productLists[0]->quantity > 0 and $productLists[0]->item_code == $search){
                $whereClauseCart[] = ["product_id", "=", $productLists[0]->id];
                $whereClauseCart[] = ["unique_sid", "=", $request->temp_uniqueid];
                $tempOrdersObject = OrdersTemp::where($whereClauseCart);
                $tempOrder  = $tempOrdersObject->first();
                if (!empty($tempOrder->id)) {
                    if ( $productLists[0]->quantity > $tempOrder->quantity ) {
                        if (!empty($productLists[0]->countdown_datetime) && strtotime($productLists[0]->countdown_datetime) > strtotime(date('Y-m-d'))) {
                            $price = round($productLists[0]->countdown_price, 3);
                        } else {
                            $price = self::getProductPrice($productLists[0]->id);
                        }
                        $tempOrder->unit_price = $price;
                        $tempOrder->quantity = $tempOrder->quantity + 1;
                        $tempOrder->save();
                        $totalAmount = self::getTotalCartAmount($request->temp_uniqueid);
                        $countitems = self::countTempOrders($request->temp_uniqueid);
                        $item_text = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));

                        $cart['information'] = ['cart_item_id' => $tempOrder->id, 'total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.quantity_is_updated')];
                        //end
                        $cart['added'] = true ;
                    }
                } else {
                    if (!empty($productLists[0]->countdown_datetime) && strtotime($productLists[0]->countdown_datetime) > strtotime(date('Y-m-d'))) {
                        $price = round($productLists[0]->countdown_price, 3);
                    } else {
                        $price = self::getProductPrice($productLists[0]->id);
                    }
                    $tempOrder  = new OrdersTemp;
                    $tempOrder->product_id = $productLists[0]->id;
                    $tempOrder->quantity   = 1;
                    $tempOrder->unit_price = $price;
                    $tempOrder->unique_sid = $request->temp_uniqueid;
                    $tempOrder->save();
                    //end
                    $totalAmount = self::getTotalCartAmount($request->temp_uniqueid);
                    $countitems  = self::countTempOrders($request->temp_uniqueid);
                    $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));

                    $cart['information'] = ['cart_item_id'=>$tempOrder->id ,'total_amount' => round($totalAmount, 3), 'items_in_cart' => $countitems, 'cart_text' => $item_text, 'message' => trans('webMessage.item_is_added')];
                    //end
                    $cart['added'] = true ;
                }
            }
        }


        ///customize product listising
        $prods = [];
        if (!empty($productLists) && count($productLists) > 0) {

            foreach ($productLists as $productList) {
                if (!empty($productList->image)) {
                    $imageUrl = url('uploads/product/thumb/' . $productList->image);
                } else {
                    $imageUrl = url('uploads/no-image.png');
                }

                $title = $productList->title_en;
                $subTitle = $productList->extra_title_en;
                $caption_title = (string)$productList->caption_en;


                if (!empty($productList->countdown_datetime) && strtotime($productList->countdown_datetime) > strtotime(date('Y-m-d'))) {
                    $retail_price    = (float)$productList->countdown_pricecountdown_price;
                    $old_price       = (float)$productList->retail_price;
                } else {
                    $retail_price    = (float)$productList->retail_price;
                    $old_price       = (float)$productList->old_price;
                }
                if (!empty($productList->image)) {
                    $imageUrl = url('uploads/product/thumb/' . $productList->image);
                } else {
                    $imageUrl = url('uploads/no-image.png');
                }
                if (!empty($productList->rollover_image)) {
                    $imageRollOverUrl = url('uploads/product/thumb/' . $productList->rollover_image);
                } else {
                    $imageRollOverUrl = url('uploads/product/thumb/' . $productList->image);
                }

                $prods[] = [
                    'id'             => $productList->id,
                    'title'          => $title,
                    'translate'      => [
                        'en' => $productList->title_en,
                        'ar' => $productList->title_ar,
                    ],
                    'extra_title'    => $subTitle,
                    'image'          => $imageUrl,
                    'rolloverImage'  => $imageRollOverUrl,
                    'is_attribute'   => $productList->is_attribute,
                    'category_id' => "",
                    'category' => "",
                    //'attributes'     => self::getAttributes($productList->id),
                    'is_stock'       => self::IsAvailableQuantity($productList->id),
                    'caption_title'  => $caption_title,
                    'caption_color'  => (string)$productList->caption_color,
                    'is_attribute'   => $productList->is_attribute,
                    'sku_no'         => (string)$productList->sku_no,
                    'quantity'       => (string)$productList->quantity,
                    'item_code'      => $productList->item_code,
                    'sku_no'         => (string)$productList->sku_no,
                    'retail_price'   => $retail_price,
                    'old_price'      => $old_price,
                ];
            }
            $prods = $prods;
        }
        //end


		$response['data'] = ['productLists' => $prods, 'totalItems' => $totalItems , 'cart' => $cart];
		return response($response, $this->successStatus);
	}



	//get attributes

	public static function getAttributes($id)
	{
		$responsedata = [];
		$productDetails = Product::where('id', $id)->first();
		if (!empty($productDetails->is_attribute)) {

			$productoptions = ProductOptionsCustomChosen::where('product_id', $id)->orderBy('custom_option_id', 'ASC')->get();
			if (!empty($productoptions) && count($productoptions) > 0) {
				$option_name = '';
				$option_type = '';
				$attr_sizes = [];
				$attr_colors = [];
				$attr_sizescolors = [];
				$attr_other = [];
				foreach ($productoptions as $productoption) {
					$cutomOptions = DB::table('gwc_products_option_custom')->where('id', $productoption->custom_option_id)->first();
					$option_name = !empty($cutomOptions->option_name_en) ? $cutomOptions->option_name_en : '--';
					$option_type = !empty($cutomOptions->option_type) ? $cutomOptions->option_type : '--';
					//size
					if ($productoption->custom_option_id == 1) {
						$attr_sizes       =  self::getSizeByCustomIdProductId($productoption->custom_option_id, $id);
					} else {
						$attr_sizes = [];
					}
					//colors
					if ($productoption->custom_option_id == 2) {
						$attr_colors      = self::getColorByCustomIdProductId($productoption->custom_option_id, $id);
					} else {
						$attr_colors = [];
					}
					//size colors
					if ($productoption->custom_option_id == 3) {
						$attr_sizescolors = self::getColorSizeByCustomIdProductId($productoption->custom_option_id, $id);
					} else {
						$attr_sizescolors = [];
					}
					//other option
					if ($productoption->custom_option_id > 3) {
						$attr_other = self::getCustomOptions($productoption->custom_option_id, $id);
					} else {
						$attr_other = [];
					}

					$responsedata[] = [
						"name" => $option_name, "type" => $option_type, "Sizes" => $attr_sizes, "Colors" => $attr_colors, "SizesColors" => $attr_sizescolors, "Others" => $attr_other
					];
				}
			}
		}

		return $responsedata;
	}


	public static function getSizeByCustomIdProductId($custom_option_id, $product_id)
	{

		$Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $product_id)->where('gwc_products_attribute.custom_option_id', $custom_option_id);
		$Attributes = $Attributes->select(
			'gwc_sizes.*',
			'gwc_sizes.id as sizeid',
			'gwc_products_attribute.size_id',
			'gwc_products_attribute.product_id',
			'gwc_products_attribute.custom_option_id',
			'gwc_products_attribute.quantity',
			'gwc_products_attribute.retail_price',
			'gwc_products_attribute.old_price',
			'gwc_products_attribute.is_qty_deduct'
		);
		$Attributes = $Attributes->join("gwc_sizes", "gwc_sizes.id", "=", "gwc_products_attribute.size_id");
		$Attributes = $Attributes->where('gwc_products_attribute.size_id', '!=', 0)
			->where('gwc_products_attribute.quantity', '>', 0)
			->groupBy('gwc_products_attribute.size_id')
			->get();
		//return $Attributes;
		$attr = [];
		if (!empty($Attributes) && count($Attributes) > 0) {
			foreach ($Attributes as $Attribute) {
				$attr[] = [
					"id"               => $Attribute->id,
					"size_id"          => $Attribute->size_id,
					"size_name"        => $Attribute->title_en,
					"product_id"       => $Attribute->product_id,
					"custom_option_id" => $Attribute->custom_option_id,
					"quantity"         => $Attribute->quantity,
					"retail_price"     => $Attribute->retail_price,
					"old_price"        => $Attribute->old_price,
					"is_qty_deduct"    => $Attribute->is_qty_deduct
				];
			}
		}
		return $attr;
	}


	public static function getColorByCustomIdProductId($custom_option_id, $product_id)
	{

		$Attributes = ProductAttribute::where('product_id', $product_id)->where('custom_option_id', $custom_option_id);
		$Attributes = $Attributes->select(
			'gwc_colors.*',
			'gwc_colors.id as colorid',
			'gwc_products_attribute.color_id',
			'gwc_products_attribute.product_id',
			'gwc_products_attribute.custom_option_id',
			'gwc_products_attribute.*'
		);
		$Attributes = $Attributes->join("gwc_colors", "gwc_colors.id", "=", "gwc_products_attribute.color_id");
		$Attributes = $Attributes->where('gwc_products_attribute.color_id', '!=', 0)
			->where('gwc_products_attribute.quantity', '>', 0)
			->groupBy('gwc_products_attribute.color_id')
			->get();
		return $Attributes;
	}


	public static function getColorSizeByCustomIdProductId($custom_option_id, $product_id)
	{

		$Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $product_id)->where('gwc_products_attribute.custom_option_id', $custom_option_id);
		$Attributes = $Attributes->select(
			'gwc_sizes.*',
			'gwc_sizes.id as sizeid',
			'gwc_sizes.title_en as size_name',
			'gwc_products_attribute.size_id',
			'gwc_products_attribute.product_id',
			'gwc_products_attribute.custom_option_id',
			'gwc_products_attribute.*'
		);
		$Attributes = $Attributes->join("gwc_sizes", "gwc_sizes.id", "=", "gwc_products_attribute.size_id")
			->where('gwc_products_attribute.size_id', '!=', 0)
			->where('gwc_products_attribute.quantity', '>', 0)
			->groupBy('gwc_products_attribute.size_id')->get();

		$attr = [];
		if (!empty($Attributes) && count($Attributes) > 0) {
			foreach ($Attributes as $Attribute) {
				$attr[] = [
					"id"               => $Attribute->id,
					"size_id"          => $Attribute->size_id,
					"size_name"        => $Attribute->title_en,
					"product_id"       => $Attribute->product_id,
					"custom_option_id" => $Attribute->custom_option_id,
					"quantity"         => $Attribute->quantity,
					"retail_price"     => $Attribute->retail_price,
					"old_price"        => $Attribute->old_price,
					"is_qty_deduct"    => $Attribute->is_qty_deduct,
					"colors"           => self::getAttributesColors($Attribute->product_id, $Attribute->size_id, $Attribute->custom_option_id)
				];
			}
		}
		return $attr;
	}

	public static function getAttributesColors($product_id, $size_id, $custom_option_id)
	{

		$Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $product_id)
			->where('gwc_products_attribute.custom_option_id', $custom_option_id)
			->where('gwc_products_attribute.size_id', $size_id);
		$Attributes = $Attributes->select(
			'gwc_colors.*',
			'gwc_products_attribute.color_id',
			'gwc_products_attribute.product_id',
			'gwc_products_attribute.custom_option_id',
			'gwc_products_attribute.*'
		);
		$Attributes = $Attributes->join("gwc_colors", "gwc_colors.id", "=", "gwc_products_attribute.color_id")
			->where('gwc_products_attribute.color_id', '!=', 0)
			->where('gwc_products_attribute.quantity', '>', 0)
			->get();

		return $Attributes;
	}


	public static function getCustomOptions($custom_option_id, $product_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$customOptionDetails = ProductOptionsCustom::where('id', $custom_option_id)->first();

		$customOptionChilds  = ProductOptions::where('gwc_products_options.product_id', $product_id)
			->where('gwc_products_options.quantity', '>', 0)
			->where('gwc_products_options.custom_option_id', $custom_option_id);
		$customOptionChilds  = $customOptionChilds->select(
			'gwc_products_option_custom_chosen.custom_option_id',
			'gwc_products_option_custom_chosen.product_id',
			'gwc_products_option_custom_chosen.is_required',
			'gwc_products_option_custom_child.*',
			'gwc_products_option_custom_child.id as pocid',
			'gwc_products_options.*'
		);
		$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_child', 'gwc_products_option_custom_child.id', '=', 'gwc_products_options.option_value_id');

		$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_chosen', ['gwc_products_option_custom_chosen.product_id' => 'gwc_products_options.product_id', 'gwc_products_option_custom_chosen.custom_option_id' => 'gwc_products_options.custom_option_id']);



		$customOptionChilds  = $customOptionChilds->get();

		if ($strLang == "en" && !empty($customOptionDetails->option_name_en)) {
			$option_name  = $customOptionDetails->option_name_en;
		} else if ($strLang == "ar" && !empty($customOptionDetails->option_name_ar)) {
			$option_name  = $customOptionDetails->option_name_ar;
		} else {
			$option_name  = 'No Name';
		}

		if (!empty($customOptionDetails->option_type)) {
			$option_type = $customOptionDetails->option_type;
		} else {
			$option_type = 'NONE';
		}
		return ['CustomOptionName' => $option_name, 'CustomOptionType' => $option_type, 'childs' => $customOptionChilds];
	}

	public static function IsAvailableQuantity($product_id)
	{
		$qty = 0;
		$productDetails   = Product::where('id', $product_id)->first();
//		if (empty($productDetails['is_attribute'])) {
			$qty   = $productDetails['quantity'];
//		} else {
//			$qty   = ProductAttribute::where('product_id', $product_id)->get()->sum('quantity');
//		}
//
//		$qty = $qty + self::getOptionsQuantityTemp($product_id);

		return $qty;
	}

	///get option quantty
	public static function getOptionsQuantityTemp($productid)
	{
		$strOptions = ProductOptions::where("product_id", $productid)->sum("quantity");
		return $strOptions;
	}
}
