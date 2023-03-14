<?php

namespace App\Http\Controllers\posv1;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Country;
use App\State;
use App\Area;
use App\Color;
use App\Customers;
use App\CustomersAddress;

use App\Settings;

use App\Mail\SendGrid;
use App\Orders;
use App\OrdersDetails;
use App\OrdersOption;
use App\Product;
use App\ProductOptions;
use App\ProductOptionsCustom;
use Mail;
use Common;

//rules
use App\Rules\Name;
use App\Size;
use Carbon\Carbon;

class PosCustomerController extends Controller
{
	public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;

	//add new customer with address
	//create new account
	public function AddNewCustomer(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		//field validation
		$validator = Validator::make($request->all(), [
			'name'   => ['nullable', 'string', 'min:4', 'max:190', new Name],
			'email'  => 'nullable|email|min:3|max:150|string|unique:gwc_customers,email',
			'mobile' => 'required|digits_between:'.config('MOBILE_VALIDATION_MIN' , 3).','.config('MOBILE_VALIDATION_MAX' , 10).'|unique:gwc_customers,mobile'
			// 			'area'   => 'required'
		]);

		if ($validator->fails()) {
			$errmsg = '';
			$allError = [];
			foreach ($validator->errors()->messages() as $error) {
				array_push($allError, $error[0]);
			}
			$success['data'] = $allError[0];
			return response()->json($success, $this->failedStatus);
		}

		$token = $this->getTokens();
		$customers = new Customers;
		$customers->name          = !empty($request->input('name')) ? $request->input('name') : 'No Name';
		$customers->email         = !empty($request->input('email')) ? $request->input('email') : '';
		$customers->mobile        = !empty($request->input('mobile')) ? $request->input('mobile') : '';
		$customers->username      = !empty($request->input('mobile')) ? $request->input('mobile') : '';
		$customers->password      = bcrypt($request->input('mobile'));
		$customers->is_active     = !empty($request->input('is_active')) ? $request->input('is_active') : '1';
		$customers->api_token     = $token;
		$customers->register_from = 'pos';
		$customers->save();

		if (!empty($request->area)) {
			$this->createAddress($request, $customers->id);
		}

		//send email notification
		if (!empty($request->email)) {
			$appendMessage = "<b>" . trans('webMessage.username') . " : </b>" . $request->input('mobile');
			$appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->input('mobile');
			$data = [
				'dear'            => trans('webMessage.dear') . ' ' . $request->input('name'),
				'footer'          => trans('webMessage.email_footer'),
				'message'         => trans('webMessage.your_account_created_success_txt') . "<br><br>" . $appendMessage,
				'subject'         => 'Account is created successfully',
				'email_from'      => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($request->email)->send(new SendGrid($data));
		}


		//end register device
		$customersDetails =  [
			"id"     => $customers->id,
			"name"   => !empty($customers->name) ? $customers->name : 'No Name',
			"mobile" => !empty($customers->mobile) ? $customers->mobile : '',
			"email"  => !empty($customers->email) ? $customers->email : '',
			"address" => $this->getCustomerAddress($customers->id)
		];

		return response()->json(['data' => $customersDetails], $this->successStatus);
	}

	public function createAddress($request, $customerid)
	{

		if (!empty($request->area) && !empty($customerid)) {

			$areaDetails          = Area::find($request->input('area'));

			$address              = new CustomersAddress;

			$address->customer_id = $customerid;
			$address->title       = "My Address";
			$address->country_id  = 2;
			$address->state_id    = !empty($areaDetails->parent_id) ? $areaDetails->parent_id : '';
			$address->area_id     = !empty($request->input('area')) ? $request->input('area') : '';
			$address->block       = !empty($request->input('block')) ? $request->input('block') : '';
			$address->street      = !empty($request->input('street')) ? $request->input('street') : '';
			$address->avenue      = !empty($request->input('avenue')) ? $request->input('avenue') : '';
			$address->house       = !empty($request->input('house')) ? $request->input('house') : '';
			$address->floor       = !empty($request->input('floor')) ? $request->input('floor') : '';
			$address->landmark    = !empty($request->input('landmark')) ? $request->input('landmark') : '';
			$address->latitude    = !empty($request->input('longitude')) ? $request->input('longitude') : '';
			$address->longitude   = !empty($request->input('longitude')) ? $request->input('longitude') : '';
			$address->is_default  = 1;
			$address->save();
			//save other 0
			$this->changeDefaultOther($customerid, $address->id);
		}
	}

	public  function changeDefaultOther($customerid, $defaultid)
	{
		$address = CustomersAddress::where('customer_id', $customerid)->where('id', '!=', $defaultid)->get();
		if (!empty($address) && count($address)) {
			foreach ($address as $addres) {
				$newAddres = CustomersAddress::find($addres->id);
				$newAddres->is_default = 0;
				$newAddres->save();
			}
		}
	}

	public function getCustomers(Request $request)
	{
		$searchParam = trim($request->search_key);
		$customerList = [];

		if (!empty($searchParam)) {
			$customerList = Customers::where('name', 'like', '%' . $searchParam . '%')
				->orWhere('email', 'like', '%' . $searchParam . '%')
				->orWhere('mobile', 'like', '%' . $searchParam . '%')
				->orderBy('name', 'asc')->get();
		} else
			$customerList = Customers::orderBy('name', 'asc')->get();

		if (count($customerList) > 0) {
			foreach ($customerList as $list) {
				$listing[] = [
					"id"     => $list->id,
					"name"   => !empty($list->name) ? $list->name : 'No Name',
					"mobile" => !empty($list->mobile) ? $list->mobile : '',
					"email"  => !empty($list->email) ? $list->email : '',
					//"address" => $this->getCustomerAddress($list->id)
				];
			}
			$response['data'] = $listing;
			return response($response, $this->successStatus);
		} else {
			$response['data'] = trans('webMessage.norecordfound');
			return response($response, $this->failedStatus);
		}
	}

	public function getCustomerAddress($userid)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$add = array();
		$subadd = [];
		$countryName = '';
		$stateName = '';
		$areaName = '';
		$addressDetail = CustomersAddress::where('customer_id', $userid)->first();
		if (!empty($addressDetail->id)) {

			$add['id']         = $addressDetail->id;
			$add['country_id'] = $addressDetail->country_id;
			$add['state_id']   = $addressDetail->state_id;
			$add['area_id']    = $addressDetail->area_id;
			//country
			$countryDetails = Country::where('is_active', 1)->where('id', $addressDetail->country_id)->first();
			if (!empty($countryDetails->id)) {
				$countryName    = Common::getLangString($countryDetails->name_en, $countryDetails->name_ar);
			}
			//state
			$stateDetails   = State::where('id', $addressDetail->state_id)->first();
			if (!empty($stateDetails->id)) {
				$stateName      = Common::getLangString($stateDetails->name_en, $stateDetails->name_ar);
			}
			//area
			$areaDetails = Area::where('id', $addressDetail->area_id)->first();
			if (!empty($areaDetails->id)) {
				$areaName    = Common::getLangString($areaDetails->name_en, $areaDetails->name_ar);
			}

			$add['country_name'] = $countryName;
			$add['state_name']   = $stateName;
			$add['area_name']    = $areaName;
			$add['block']        = !empty($addressDetail->block) ? $addressDetail->block : '';
			$add['street']       = !empty($addressDetail->street) ? $addressDetail->street : '';
			$add['avenue']       = !empty($addressDetail->avenue) ? $addressDetail->avenue : '';
			$add['house']        = !empty($addressDetail->house) ? $addressDetail->house : '';
			$add['floor']        = !empty($addressDetail->floor) ? $addressDetail->floor : '';
			$add['title']        = !empty($addressDetail->title) ? $addressDetail->title : 'My Address';
			$add['is_default']   = !empty($addressDetail->is_default) ? $addressDetail->is_default : '0';
			$add['landmark']     = !empty($addressDetail->landmark) ? $addressDetail->landmark : '';
			$add['latitude']     = !empty($addressDetail->latitude) ? $addressDetail->latitude : '';
			$add['longitude']    = !empty($addressDetail->longitude) ? $addressDetail->longitude : '';
			return $add;
		}

		return (object)$add;
	}


	public function getTokens()
	{
		$token = Str::random(60);
		$token =  hash('sha256', $token);
		return $token;
	}

	public static function getOrders(Request $request)
	{
	    
        $user = $request->user();

		//$settingInfo = Settings::where("keyname", "setting")->first();

		$today = Carbon::today()->format('Y-m-d');
		//check search queries
		$searchName = $request->search_name;
		$searchEmail = $request->search_email;
		$searchMobile = $request->search_mobile;
		$searchOrderId = $request->search_order_id;
		$fromDate = $request->search_from_date;
		$toDate = $request->search_to_date;
		$customer_id = $request->search_customer_id;
        $checkUser = $request->checkUser == null || (bool)$request->checkUser;
        //search keywords

        $orderLists = [];


        $orderDetailsByFilter = OrdersDetails::where('device_type', '=', 'pos');

        $orderDetailsByFilter->when($checkUser, function ($query) use ($searchName) {
            return $query->where('pos_id' , Auth::id() );
        });
        $orderDetailsByFilter->when($searchName, function ($query) use ($searchName) {
            return $query->where('name', 'like', '%' . $searchName . '%');
        });
        $orderDetailsByFilter->when($customer_id, function ($query) use ($customer_id) {
            return $query->where('customer_id', $customer_id);
        });
        $orderDetailsByFilter->when($searchEmail, function ($query) use ($searchEmail) {
            return $query->where('email', 'like', '%' . $searchEmail . '%');
        });
        $orderDetailsByFilter->when($searchMobile, function ($query) use ($searchMobile) {
            return $query->where('mobile', 'like', '%' . $searchMobile . '%');
        });
        $orderDetailsByFilter->when($searchOrderId, function ($query) use ($searchOrderId) {
            return $query->where('order_id', 'like', '%' . $searchOrderId  . '%');
        });
        $orderDetailsByFilter->when((!empty($fromDate) && !empty($toDate)), function ($query) use ($fromDate, $toDate) {
            return $query->whereBetween('created_at', [$fromDate, $toDate]);
        } ,function ($query) use($user , $checkUser) {
            if ( $checkUser ){
                $workTime = $user->openWorkTime();
                if ( $workTime == null ){
                    $workTime = $user->lastOpenWorkTime();
                    return $query->whereBetween('created_at', [$workTime->start , $workTime->ended ]);
                }
                return $query->where('created_at', '>' , $workTime->start);
            }
        } );
        // $orderDetailsByFilter->when((!empty($fromDate) && !empty($toDate)), function ($query) use ($fromDate, $toDate) {
        //     return $query->whereBetween('created_at', [$fromDate, $toDate]);
        // });
        $orderLists = $orderDetailsByFilter->with('area')->orderBy('updated_at' , 'desc')->get();

		//filter by date range
		// if (!empty($fromDate) && !empty($toDate)) {
		// $orderLists = OrdersDetails::where('device_type', '=', 'pos')->whereBetween('created_at', [$fromDate, $toDate])->with('area')->get();
		// }

		// if (!empty($payStatus) && $payStatus  == 'paid') {
		// 	$orderLists = $orderLists->where('is_paid', '=', 1);
		// }
		// if (!empty($payStatus) &&  	$payStatus  ==  'notpaid') {
		// 	$orderLists = $orderLists->where('is_paid', '!=', 1);
		// }
		// if (!empty($customerId)) {
		// 	$orderLists = $orderLists->where('customer_id', '=', $customerId);
		// }

		// if (!empty($payMode)) {
		// 	$orderLists = $orderLists->where('pay_mode', '=', $payMode);
		// }

		// if (!empty($request->pmode) && $request->pmode == "COD") {
		// 	$orderLists = $orderLists->where('pay_mode', '=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		// } else if (!empty($request->pmode) && $request->pmode == "COD_KNET") {
		// 	$orderLists = $orderLists->where('is_paid', 1)->where('order_status', 'completed');
		// } else if (!empty($request->pmode) && $request->pmode == "KNET") {
		// 	$orderLists = $orderLists->where('pay_mode', '!=', 'COD')->where('is_paid', 1)->where('order_status', 'completed');
		// }

		// //collect customers listing for dropdown
		// $customersLists = DB::table('gwc_orders_details')
		// 	->select('gwc_orders_details.customer_id', 'gwc_customers.id', 'gwc_orders_details.name')
		// 	->join('gwc_customers', 'gwc_customers.id', '=', 'gwc_orders_details.customer_id')
		// 	->GroupBy('gwc_orders_details.customer_id')
		// 	->get();

		if (count($orderLists) > 0) {
			$data = ['orders' => $orderLists];
			$response = ['status' => 200, 'message' => 'success', 'data' => $data];
			return response($response, 200);
		} else {
			$response = ['status' => 200, 'message' => 'can not find any order!' , 'data' => [] ];
			return response($response, 200);
		}
	}

	public function getOrderItems(Request $request)
	{
		$orderId = $request->order_id;
        $totalSubPriceOfAllItem = 0 ;
		if (!empty($orderId)) {
			$orders = Orders::where('oid', $orderId)->get();
			if (count($orders) > 0) {
				$items = [];

				foreach ($orders  as $order) {
					$productDetails = Product::where('id', $order->product_id)->first();
					$productAttribute = [];
					if (!empty($order->size_id)) {
						$size = Size::where('id', $order->size_id)->first();
						$sizesAttribute = [
							'id' => $size->id,
							'type' => 'size',
							'name' => $size->title_en,
						];
						$productAttribute[] = (object) $sizesAttribute;
					}
					if (!empty($order->color_id)) {
						$color = Color::where('id', $order->color_id)->first();
						$colorAttribute = [
							'id' => 	$color->id,
							'type' => 'color',
							'name' => $color->title_en
						];
						$productAttribute[] = (object) $colorAttribute;
					}

					$orderOptions = self::getOptionsDtailsOrder($order->id);
					if (!empty($orderOptions)) {
						$otherAttribute = [
							'options' => $orderOptions
						];
						$productAttribute[] = (object) 	$otherAttribute;
					}

					$unitprice     = $order->unit_price;
					$subtotalprice = $unitprice * $order->quantity;
					$title         = $productDetails->title_en;
					if (!empty($productDetails->image)) {
						$imageUrl = url('uploads/product/thumb/' . $productDetails->image);
					} else {
						$imageUrl = url('uploads/no-image.png');
					}

					$items[] = [
						"id" => $order->id,
						"product_id" => $order->product_id,
						"item_code" => $productDetails->item_code,
						"title" => $title,
                        'translate'      => [
                            'en' => $productDetails->title_en,
                            'ar' => $productDetails->title_ar,
                        ],
						"imageUrl" => $imageUrl,
						"productAttributes" => $productAttribute,
						"unitprice" => $unitprice,
						"quantity" => $order->quantity,
						"unique_sid" => $order->unique_sid,
						"subtotal" => $subtotalprice,
					];
                    $totalSubPriceOfAllItem += $subtotalprice;
				}
                $orderDetail = OrdersDetails::where('id', $orderId)->first();
                $orderTransactions = Transaction::where('udf1', $orderDetail->order_id)->get();
                $paid = Transaction::where('udf1', $orderDetail->order_id)->where('presult', 'CAPTURED')->selectRaw('sum(udf2) as paid')->first();
				$data = ['subTotal' => $totalSubPriceOfAllItem,'details' => $orderDetail,'orderItems' => $items,'transactions' => $orderTransactions , 'pay' => $paid->paid, 'total' => $orderDetail->total_amount];

                $countryName = ['en' => '' , 'ar' =>''];
                $stateName = ['en' => '' , 'ar' =>''];
                $areaName = ['en' => '' , 'ar' =>''];
                //country
                $countryDetails = Country::where('is_active',1)->where('id',$orderDetail->country_id)->first();
                if($countryDetails != null){
                    $countryName =[
                        'en' => $countryDetails->name_en,
                        'ar' => $countryDetails->name_ar
                    ];
                }
                //state
                $stateDetails = State::where('is_active',1)->where('id',$orderDetail->state_id)->first();
                if($stateDetails != null){
                    $stateName =[
                        'en' => $stateDetails->name_en,
                        'ar' => $stateDetails->name_ar
                    ];
                }
                //area
                $areaDetails = Area::where('is_active',1)->where('id',$orderDetail->area_id)->first();
                if($areaDetails != null){
                    $areaName =[
                        'en' => $areaDetails->name_en,
                        'ar' => $areaDetails->name_ar
                    ];
                }
                $data['details']['countryName'] = $countryName;
                $data['details']['stateName'] = $stateName;
                $data['details']['areaName'] = $areaName;
                $response = ['status' => 200, 'message' => 'success', 'data' => $data];
				return response($response, $this->successStatus);
			} else {
				$response = ['status' => 404, 'message' => 'No Item(s) found for this Order ID'];
				return response($response, 404);
			}
		} else {
			$response = ['status' => 400, 'message' => 'Order ID is missing'];
			return response($response, 400);
		}
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
		$optionDetails = OrdersOption::where("oid", $oid)->get();
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
}
