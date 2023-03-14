<?php

namespace App\Http\Controllers\v1;

use App\Area;
use App\Currency;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Country;
use App\Transaction;
use App\Settings;
use App\Slideshow;
use App\Banner;
use App\Section;
use App\Product;
use App\ProductGallery;
use App\ProductReview;
use App\ProductAttribute;

use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\ProductOptionsCustomChosen;

use App\CustomersWish;

use App\Color;
use App\Size;
use App\Categories;
use App\SinglePages;
use App\Newsletter;
use App\Faq;
use App\Subjects;
use App\Contactus;
use App\WebPush;
use App\WebPushMessage;
use App\User;
use App\Mail\SendGrid;
use Mail;
use Common;

//rules
use App\Rules\Name;
use App\Rules\Mobile;
use App\State;

class apiController extends Controller
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

    public function getGateways(Request $request,$countryCode = null){
        $listGateways = Country::getGateways($countryCode);
        if ( $request->route('platform_prefix') == "android")
            return response()->json([
                'data' =>  Country::getGateways($countryCode)
            ], $this->successStatus);

        $result = [];

        foreach ( $listGateways as $i => $payment) {
            if($payment=='COD'){
                $paytxt = trans('webMessage.payment_COD');
            }else if($payment=='KNET'){
                $paytxt = trans('webMessage.payment_KNET');
            }else if($payment=='TPAY'){
                $paytxt = trans('webMessage.payment_TPAY');
            }else if($payment=='GKNET'){
                $paytxt = trans('webMessage.payment_GKNET');
            }else if($payment=='GTPAY'){
                $paytxt = trans('webMessage.payment_GTPAY');
            }else if($payment=='TAH'){
                $paytxt = trans('webMessage.payment_TAH');
            }else if($payment=='MF'){
                $paytxt = trans('webMessage.payment_MF');
            }else if($payment=='PAYPAL'){
                $paytxt = trans('webMessage.payment_PAYPAL');
            }else if($payment=='POSTKNET'){
                $paytxt = trans('webMessage.payment_POSTKNET');
            }else if($payment=='CS'){
                $paytxt = trans('webMessage.payment_CS');
            }else if($payment=='Q8LINK'){
                $paytxt = trans('webMessage.payment_Q8LINK');
            }else if($payment=='MasterCard'){
                $paytxt = trans('webMessage.payment_MasterCard');
            }
            $result[] = [
                'code' => $payment,
                'title' => $paytxt,
                'image' => url('uploads/paymenticons/'.strtolower($payment).'.png')
            ];
        }
        return response()->json([
            'data' =>  $result
        ], $this->successStatus);
    }

	//get country / state / area
	public function getCSA(Request $request)
	{
		$areaLists = Country::where('is_active', 1)->where('parent_id', $request->parent_id)->get();
        if ( $areaLists->isEmpty() ) {
            $stateLists = Country::where('is_active', 1)->where('id', $request->parent_id)->first();
            if ( $stateLists != null){
                $countryLists = Country::where('is_active', 1)->where('id', $stateLists->parent_id)->first();
                if ( $countryLists != null){
                    $areaLists = Country::where('is_active', 1)->where('id', $request->parent_id)->get();
                }
            }
        }
		$success['data'] = [$areaLists];
		return response()->json($success, $this->successStatus);
	}

	public function getAreasOnly(Request $request)
	{

		$id = $request->countryId;

		if (empty($id)) {
			$response = ['error' => 'Country Id is Missing'];
			return response($response, 401);
		}
		$states = State::where('parent_id', $id)->get();
		foreach ($states as $state) {
			$areas = Area::where('parent_id', $state->id)->where('is_active',1)->get();
            if ( $areas->isEmpty() ){
                $stateInfo = $state;
                $allAreas[] = (object)$stateInfo;
            } else {
                foreach ($areas as $area) {
                    $areaInfo = $area;
                    $allAreas[] = (object)$areaInfo;
                }
            }

		}

		if (!empty($areas)) {
			$response = ['message' => 'success', 'data' => $allAreas];
			return response($response, 200);
		} else {
			$response = ['error' => 'No Areas Found!'];
			return response($response, 404);
		}
	}

	//get push notification
	public function getPushMessage(Request $request)
	{
		$limit = 20;

		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		$result = [];
		$pushLists = WebPushMessage::where('id', '!=', '')->orderBy('created_at', 'DESC')->offset($offset)->limit($limit)->get();
		if (!empty($pushLists) && count($pushLists) > 0) {

			/*foreach($pushLists as $pushList){
				$result[]=[
					'id'=>$pushList->id,
					'title'=>$pushList->title,
					'message'=>$pushList->message,
					'image'=>(string)$pushList->large_image_url,
					'created'=>$pushList->created_at
					];	
			}*/

			$success['data'] = $pushLists;
		} else {
			$message = trans('webMessage.noresultfound');
			$success['data'] = $message;
		}

		return response()->json($success, $this->successStatus);
	}

	/////////////////////////////////////////////////show search results////////////////////////////////
	//quick search
	public function QuickSearchResults(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		if (!empty($request->sq) && strlen($request->sq) >= 2) {
			$search = $request->sq;
			$products = Product::where('is_active', '!=', 0)
				->where('title_' . $strLang, 'like', '%' . $search . '%')
				->orwhere('details_' . $strLang, 'like', '%' . $search . '%')
				->orwhere('item_code', 'like', '%' . $search . '%');
			$products = $products->where('is_active', '!=', 0)->limit(6)
				->orderBy('most_visited_count', 'DESC')
				->get();
			if (!empty($products) && count($products) > 0) {
				$result = [];
				foreach ($products as $product) {
					if ($product->image) {
						$imgurl = url('uploads/product/thumb/' . $product->image);
					} else {
						$imgurl = url('uploads/no-image.png');
					}
					$result[] = [
						'id' => $product->id,
						'title' => $product['title_' . $strLang],
						'image' => $imgurl,
						'retail_price' => $product->retail_price,
						'old_price' => $product->old_price
					];
				}
				$success['data'] = $result;
				return response()->json($success, $this->successStatus);
			} else {
				$message = trans('webMessage.noresultfound');
				$success['data'] = $message;
				return response()->json($success, $this->failedStatus);
			}
		} else {
			$message = trans('webMessage.noresultfound');
			$success['data'] = $message;
			return response()->json($success, $this->failedStatus);
		}
	}
	//main search
	public function searchResults(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$limit = 20;
		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id

		$prodSizes = [];
		$settingInfo     = Settings::where("keyname", "setting")->first();
		//get sorting option
		if (!empty($request->search_sort_by) && $request->search_sort_by == "popular") {
			$sortKeyName = 'most_visited_count';
			$sortKey = 'DESC';
		} else if (!empty($request->search_sort_by) && $request->search_sort_by == "max-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'DESC';
		} else if (!empty($request->search_sort_by) && $request->search_sort_by == "min-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'ASC';
		} else if (!empty($request->search_sort_by) && $request->search_sort_by == "a-z") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'ASC';
		} else if (!empty($request->search_sort_by) && $request->search_sort_by == "z-a") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'DESC';
		} else {
			$sortKeyName = 'id';
			$sortKey = 'DESC';
		}
		//load items per page
		if (!empty($request->search_per_page)) {
			$recordPerPage = $request->search_per_page;
		} else {
			$recordPerPage = $settingInfo->item_per_page_front;
		}
		//check search keyname is empty or not
		if (empty($request->sq)) {
			$success['data'] = trans('webMessage.searchkeywordrequired');
			return response()->json($success, $this->failedStatus);
		}

		$search = $request->sq;
		$explode_search = explode(' ', $search);
		if (!empty($search)) {
			$productLists = Product::where('gwc_products.is_active', '!=', 0);
			//filter by size
			if (!empty($request->search_by_size) && empty($request->search_by_color)) {
				$size_id = $request->search_by_size;
				$productLists = $productLists->select(
					'gwc_products_attribute.product_id',
					'gwc_products_attribute.size_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
					->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
					->where('gwc_products_attribute.size_id', '=', $size_id);
			} else if (empty($request->search_by_size) && !empty($request->search_by_color)) {
				$color_id = $request->search_by_color;
				$productLists = $productLists->select(
					'gwc_products_attribute.product_id',
					'gwc_products_attribute.color_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
					->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
					->where('gwc_products_attribute.color_id', '=', $color_id);
			} else if (!empty($request->search_by_size) && !empty($request->search_by_color)) {
				$size_id = $request->search_by_size;
				$color_id = $request->search_by_color;
				$productLists = $productLists->select(
					'gwc_products_attribute.product_id',
					'gwc_products_attribute.size_id',
					'gwc_products_attribute.color_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
					->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
					->where('gwc_products_attribute.size_id', '=', $size_id)->where('gwc_products_attribute.color_id', '=', $color_id);
			}
			//filter by tags
			if (!empty($request->search_by_tags)) {
				$searchTags = $request->search_by_tags;
				if ($strLang == "en") {
					$productLists = $productLists->whereRaw("find_in_set('" . $searchTags . "',gwc_products.tags_en)");
				} else {
					$productLists = $productLists->whereRaw("find_in_set('" . $searchTags . "',gwc_products.tags_ar)");
				}
			}
			//filter by price range
			if (!empty($request->search_rangeprice)) {
				$explodePrice = explode('-', $request->search_rangeprice);
				$productLists = $productLists->where('gwc_products.retail_price', '>=', $explodePrice[0])
					->where('gwc_products.retail_price', '<=', $explodePrice[1]);
			}
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

			//count total records
			$totalItems = $productLists->get()->count();

			//get max price
			$retailPriceRanges = $productLists->max('gwc_products.retail_price');

			$productLists = $productLists->orderBy('gwc_products.most_visited_count', 'DESC')->offset($offset)->limit($limit)->get();


			///$productLists->appends(['sq' => $search]);
			//get categories lists
			$productCategoriesLists = $this->getSearchCategories($search, $strLang);
			$prodSizes              = $this->getSizeBySearch($search, $strLang);
			$prodColors             = $this->getColorBySearch($search, $strLang);
			$cattags                = $this->getTagsBySearch($search, $strLang);
		} else {
			$productLists = [];
			$productCategoriesLists = [];
			$prodSizes = [];
			$prodColors = [];
			$cattags  = [];
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
				if (!empty($productList->rollover_image)) {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->rollover_image);
				} else {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->image);
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
					'image'          => (string)$imageUrl,
					'rolloverImage'  => (string)$imageRollOverUrl,
					'is_wish_item'   => self::getWhishStatus($productList->id, $userid)

				];
			}
			$prods = $prods;
		}
		//end


		$success['data'] = [
			'productLists' => $prods,
			'productCategoriesLists' => $productCategoriesLists,
			'retailPriceRanges' => $retailPriceRanges,
			'prodSizes' => $prodSizes,
			'prodColors' => $prodColors,
			'totalItems' => $totalItems,
			'cattags' => $cattags
		];
		return response()->json($success, $this->successStatus);
	}


	//get offers
	public function getOffers(Request $request)
	{

		$limit = 20;
		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id


		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$settingInfo     = Settings::where("keyname", "setting")->first();

		$categoryDetails = [];

		//get sorting option
		if (!empty($request->product_sort_by) && $request->product_sort_by == "popular") {
			$sortKeyName = 'most_visited_count';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "max-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "min-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "a-z") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "z-a") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'DESC';
		} else {
			$sortKeyName = 'id';
			$sortKey = 'DESC';
		}
		//load items per page
		if (!empty($request->product_per_page)) {
			$recordPerPage = $request->product_per_page;
		} else {
			$recordPerPage = $settingInfo->item_per_page_front;
		}
		//product listings
		if (!empty($request->filter_by_size) && empty($request->filter_by_color)) {
			$size_id = $request->filter_by_size;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.is_offer' => 1, 'gwc_products_attribute.size_id' => $size_id])
				->where('gwc_products.is_active', '!=', 0);
		} else if (empty($request->filter_by_size) && !empty($request->filter_by_color)) {
			$color_id = $request->filter_by_color;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.is_offer' => 1, 'gwc_products_attribute.color_id' => $color_id])
				->where('gwc_products.is_active', '!=', 0);
		} else if (!empty($request->filter_by_size) && !empty($request->filter_by_color)) {
			$color_id = $request->filter_by_color;
			$size_id = $request->filter_by_size;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.is_offer' => 1, 'gwc_products_attribute.size_id' => $size_id, 'gwc_products_attribute.color_id' => $color_id])
				->where('gwc_products.is_active', '!=', 0);
		} else {
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->where(['gwc_products.is_offer' => 1])->where('gwc_products.is_active', '!=', 0);
		}
		//filter by tags
		if (!empty($request->filter_by_tags)) {
			$search = $request->filter_by_tags;
			$productLists = $productLists->whereRaw("find_in_set('" . $search . "',gwc_products.tags_en)")
				->whereRaw("find_in_set('" . $search . "',gwc_products.tags_ar)");
		}
		//filter by price range
		if (!empty($request->rangeprice)) {
			$explodePrice = explode('-', $request->rangeprice);
			$productLists = $productLists->where('gwc_products.retail_price', '>=', $explodePrice[0])
				->where('gwc_products.retail_price', '<=', $explodePrice[1]);
		}
		//count total records
		$totalItems = $productLists->groupBy('gwc_products.id')->get()->count();
		//price range
		$retailPriceRanges = $productLists->max('gwc_products.retail_price');

		if (!empty($request->catid)) {
			$productLists = $productLists->where('gwc_products_category.category_id', $request->catid);
		}

		$productLists = $productLists->groupBy('gwc_products.id')->orderBy($sortKeyName, $sortKey)->offset($offset)->limit($limit)->get();


		//check subcategoris exit or not
		$productCategoriesLists = DB::table('gwc_categories')
			->select(
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_products.id as pid',
				'gwc_products.*',
				'gwc_categories.*'
			)
			->join('gwc_products_category', 'gwc_products_category.category_id', '=', 'gwc_categories.id')
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->where(['gwc_products.is_offer' => 1, 'gwc_categories.is_active' => 1])
			->where('gwc_products.is_active', '!=', 0)
			->groupBy('gwc_products_category.category_id')->get();
		//get sizes
		$prodSizes = DB::table('gwc_products_category')
			->select(
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_products.id as pid',
				'gwc_products_attribute.size_id',
				'gwc_products_attribute.product_id',
				'gwc_products_category.product_id',
				'gwc_sizes.id',
				'gwc_sizes.is_active',
				'gwc_sizes.title_en',
				'gwc_sizes.title_en'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_sizes', 'gwc_sizes.id', '=', 'gwc_products_attribute.size_id')
			->where(['gwc_products.is_offer' => 1, 'gwc_sizes.is_active' => 1])
			->where('gwc_products.is_active', '!=', 0)
			->where('gwc_products_attribute.size_id', '!=', 0)
			->groupBy('gwc_products_attribute.size_id')
			->get();

		//get colors
		$prodColors = DB::table('gwc_products_category')
			->select(
				'gwc_products.id as product_id',
				'gwc_products.is_active',
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_products.id as pid',
				'gwc_products_attribute.product_id',
				'gwc_products_attribute.color_id',
				'gwc_colors.id',
				'gwc_colors.title_en',
				'gwc_colors.title_ar',
				'gwc_colors.color_code',
				'gwc_colors.is_active'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_colors', 'gwc_colors.id', '=', 'gwc_products_attribute.color_id')
			->where(['gwc_products.is_offer' => 1, 'gwc_colors.is_active' => 1])
			->where('gwc_products.is_active', '!=', 0)
			->where('gwc_products_attribute.color_id', '!=', 0);

		if (!empty($request->filter_by_size)) {
			$prodColors->where('gwc_products_attribute.size_id', '=', $request->filter_by_size);
		}
		$prodColors = $prodColors->groupBy('gwc_products_attribute.color_id')->get();
		//customize color
		$prodColorsY = [];
		if (!empty($prodColors) && count($prodColors) > 0) {
			foreach ($prodColors as $prodColor) {
				if (!empty($prodColor->image)) {
					$colorImage = url('uploads/colors/thumb/' . $prodColor->image);
				} else {
					$colorImage = "";
				}
				$prodColorsY[] = [
					'id' => $prodColor->id,
					'color_name_en' => $prodColor->title_en,
					'color_name_ar' => $prodColor->title_ar,
					'color_code'    => (string)$prodColor->color_code,
					'colorImage'    => $colorImage
				];
			}
		}

		$cattags = [];

		$prodtags = DB::table('gwc_products_category')
			->select('gwc_products_category.product_id', 'gwc_products_category.category_id', 'gwc_products.id', 'gwc_products.tags_en', 'gwc_products.tags_ar', 'gwc_products.is_active')
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->where('gwc_products.is_offer', '=', 1)->where('gwc_products.is_active', '!=', 0)
			->get();
		if (!empty($prodtags) && count($prodtags) > 0) {
			$tags = '';
			foreach ($prodtags as $prodtag) {
				if ($strLang == 'en' && !empty($prodtag->tags_en)) {
					$tags .= $prodtag->tags_en . ',';
				} else if ($strLang == 'ar' && !empty($prodtag->tags_ar)) {
					$tags .= $prodtag->tags_ar . ',';
				}
			}
			$ftags = trim($tags, ',');
			$arrTags = explode(",", $ftags);
			$cattags = array_unique($arrTags);
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
				if (!empty($productList->rollover_image)) {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->rollover_image);
				} else {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->image);
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
					'image'          => (string)$imageUrl,
					'rolloverImage'  => (string)$imageRollOverUrl,
					'is_wish_item'   => self::getWhishStatus($productList->id, $userid)

				];
			}
			$prods = $prods;
		}
		//end

		//customize category
		$prodCategoryY = [];
		if (!empty($productCategoriesLists) && count($productCategoriesLists) > 0) {
			foreach ($productCategoriesLists as $prodCategory) {
				if (!empty($prodCategory->image)) {
					$catImage = url('uploads/category/thumb/' . $prodCategory->image);
				} else {
					$catImage = "";
				}
				$prodCategoryY[] = [
					'id'            => $prodCategory->id,
					'name_en'       => $prodCategory->name_en,
					'name_ar'       => $prodCategory->name_ar,
					'catImage'      => $catImage
				];
			}
		}
		//end customize category

		$success['data'] = [
			'productLists'           => $prods,
			'categoryDetails'        => $categoryDetails,
			'productCategoriesLists' => $prodCategoryY,
			'retailPriceRanges'      => $retailPriceRanges,
			'prodSizes'              => $prodSizes,
			'prodColors'             => $prodColorsY,
			'totalItems'             => $totalItems,
			'cattags'                => $cattags
		];
		return response()->json($success, $this->successStatus);
	}

	//get search categories
	public function getSearchCategories($search, $strLang = "en")
	{

		$listQuery = Product::query()
			->select(
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_categories.name_en',
				'gwc_categories.name_ar',
				'gwc_categories.friendly_url',
				'gwc_categories.id as cid',
				'gwc_products.*'
			)
			->join('gwc_products_category', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_categories', 'gwc_categories.id', '=', 'gwc_products_category.category_id')
			->where('gwc_products.is_active', '!=', 0);
		//search part start
		$listQuery = $listQuery
			->where('gwc_products.title_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
		$explode_search = explode(' ', $search);
		if (count($explode_search) > 1) {
			foreach ($explode_search as $searchword) {
				$listQuery = $listQuery->orwhere('gwc_products.title_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
			}
		}
		//end search part			   
		$listQuery = $listQuery->where('gwc_categories.is_active', 1)->groupBy('gwc_categories.id')->get();

		//customize category
		$prodColorsY = [];
		if (!empty($listQuery) && count($listQuery) > 0) {
			foreach ($listQuery as $prodCategory) {
				if (!empty($prodCategory->image)) {
					$catImage = url('uploads/category/thumb/' . $prodCategory->image);
				} else {
					$catImage = "";
				}
				$prodColorsY[] = [
					'id'            => $prodCategory->cid,
					'name_en'       => $prodCategory->name_en,
					'name_ar'       => $prodCategory->name_ar,
					'catImage'      => $catImage
				];
			}
		}
		//end customize category

		return $prodColorsY;
	}
	/////////////////////////////////////////////get sizes from search////////////////////////////////////////
	public function getSizeBySearch($search, $strLang)
	{
		//get sizes
		$listQuery = Product::query()
			->select(
				'gwc_products.id as pid',
				'gwc_products_attribute.product_id',
				'gwc_products_attribute.size_id',
				'gwc_products_attribute.color_id',
				'gwc_products_attribute.quantity',
				'gwc_products_attribute.retail_price',
				'gwc_products_attribute.old_price',
				'gwc_sizes.id'
			)
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_sizes', 'gwc_sizes.id', '=', 'gwc_products_attribute.size_id')
			->where(['gwc_sizes.is_active' => 1])->where('gwc_products.is_active', '!=', 0);
		//search part start
		$listQuery = $listQuery
			->where('gwc_products.title_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
		$explode_search = explode(' ', $search);
		if (count($explode_search) > 1) {
			foreach ($explode_search as $searchword) {
				$listQuery = $listQuery->orwhere('gwc_products.title_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
			}
		}
		//end search part	
		$listQuery = $listQuery->where('gwc_products_attribute.size_id', '!=', 0)->groupBy('gwc_products_attribute.size_id')
			->get();
		return $listQuery;
	}

	/////////////////////////////////////////////get color from search////////////////////////////////////////
	public function getColorBySearch($search, $strLang)
	{
		//get sizes
		$listQuery = Product::query()
			->select(
				'gwc_products.id as pid',
				'gwc_products_attribute.product_id',
				'gwc_products_attribute.size_id',
				'gwc_products_attribute.color_id',
				'gwc_products_attribute.quantity',
				'gwc_products_attribute.retail_price',
				'gwc_products_attribute.old_price',
				'gwc_colors.*'
			)
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_colors', 'gwc_colors.id', '=', 'gwc_products_attribute.color_id')
			->where(['gwc_colors.is_active' => 1])->where('gwc_products.is_active', '!=', 0);
		//search part start
		$listQuery = $listQuery
			->where('gwc_products.title_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('gwc_products.item_code', 'like', '%' . $search . '%');
		$explode_search = explode(' ', $search);
		if (count($explode_search) > 1) {
			foreach ($explode_search as $searchword) {
				$listQuery = $listQuery->orwhere('gwc_products.title_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.details_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('gwc_products.item_code', 'like', '%' . $searchword . '%');
			}
		}
		//end search part	
		$listQuery = $listQuery->where('gwc_products_attribute.color_id', '!=', 0)->groupBy('gwc_products_attribute.color_id')
			->get();

		//customize color
		$prodColorsY = [];
		if (!empty($listQuery) && count($listQuery) > 0) {
			foreach ($listQuery as $prodColor) {
				if (!empty($prodColor->image)) {
					$colorImage = url('uploads/colors/thumb/' . $prodColor->image);
				} else {
					$colorImage = "";
				}
				$prodColorsY[] = [
					'id' => $prodColor->id,
					'color_name_en' => $prodColor->title_en,
					'color_name_ar' => $prodColor->title_ar,
					'color_code'    => (string)$prodColor->color_code,
					'colorImage'    => $colorImage
				];
			}
		}
		//end customize color


		return $prodColorsY;
	}

	/////////////////////////////get popular items by search /////////////////////////////
	public function getPopularItemsBySearch($search, $strLang)
	{
		$explode_search = explode(' ', $search);
		$listQuery = Product::where('is_active', '!=', 0)
			->where('title_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('details_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('item_code', '=', '%' . $search . '%');
		if (count($explode_search) > 1) {
			foreach ($explode_search as $searchword) {
				$listQuery = $listQuery->orwhere('title_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('details_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('item_code', '=', '%' . $searchword . '%');
			}
		}
		$listQuery = $listQuery->orderBy('most_visited_count', 'DESC')
			->limit(5)
			->get();
		return $listQuery;
	}

	////////////////////////////////////////get tags by search//////////////////////////////
	public function getTagsBySearch($search, $strLang)
	{
		$explode_search = explode(' ', $search);
		$prodtags = Product::where('is_active', '!=', 0)
			->where('title_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('details_' . $strLang, 'like', '%' . $search . '%')
			->orwhere('item_code', '=', '%' . $search . '%');
		if (count($explode_search) > 1) {
			foreach ($explode_search as $searchword) {
				$prodtags = $prodtags->orwhere('title_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('details_' . $strLang, 'like', '%' . $searchword . '%')
					->orwhere('item_code', '=', '%' . $searchword . '%');
			}
		}
		$prodtags = $prodtags->get();
		$cattags = [];
		if (!empty($prodtags) && count($prodtags) > 0) {
			$tags = '';
			foreach ($prodtags as $prodtag) {
				if ($strLang == 'en' && !empty($prodtag->tags_en)) {
					$tags .= $prodtag->tags_en . ',';
				} else if ($strLang == 'ar' && !empty($prodtag->tags_ar)) {
					$tags .= $prodtag->tags_ar . ',';
				}
			}
			$ftags = trim($tags, ',');
			$arrTags = explode(",", $ftags);
			$cattags = array_unique($arrTags);
		}
		return $cattags;
	}
	//////////////////////////////End Search result/////////////////////////////////////////

	//get product details by id
	public function getProductDetails(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id


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
		$prodDetails['is_wish_item'] = self::getWhishStatus($productDetails->id, $userid);
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
		$galleries = $this->getGalleries($productDetails->id);
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

		$prodDetails['ratings']          = self::getProductRatings($productDetails['id']);
		$prodDetails['options']          = self::getProductOptions($productDetails['id']);
		$prodDetails['reviews']          = self::getProductReviews($productDetails['id'], $strLang);
        $prodDetails['default_currency'] = Currency::default();
        $prodDetails['other_currency'] = [];
        $prodDetails['web_url'] = [
            'en' => asset('en/details/' . $productDetails->id . '/' . $productDetails->slug) ,
            'ar' => asset('ar/details/' . $productDetails->id . '/' . $productDetails->slug) ,
            'auto' => asset($strLang.'/details/' . $productDetails->id . '/' . $productDetails->slug) ,
        ];
        $categoreies = $this->getCategoryByPid($productDetails->id);
        $prodDetails['categories'] = $categoreies;
        $prodDetails['categories_breadcrumb'] = array_merge([[
                'id' => null,
                'web_url' =>  url(app()->getLocale()) ,
                'title' => trans('webMessage.home') ,
                "level"=> 0,
                'isHome' => true,
                'isProduct' => false,
            ]] , $categoreies , [[
                'id' => $productDetails->id,
                'web_url' =>  asset($strLang.'/details/' . $productDetails->id . '/' . $productDetails->slug) ,
                'title' => $productDetails['title_' . $strLang] ,
                "level"=> ( count($categoreies) +1 ),
                'isHome' => false,
                'isProduct' => true,
            ]]);
        $multiCurrencies = Currency::convert($prodDetails['retail_price']);
        $settingInfo = Settings::where("keyname","setting")->first();
        foreach ($multiCurrencies as $Currency){
            if ( $Currency->code == $settingInfo->base_currency )
                continue;
            $prodDetails['other_currency'][] = [
                'title' => $Currency['title_'. $strLang],
                'symbol' => $Currency->symbol,
                'price' => $Currency->price,
                'code' => $Currency->code,
            ];
        }

		//related items
		$relatedItems = self::getRelatedItems($productDetails->id);
		$related = [];

		if (!empty($relatedItems) && count($relatedItems) > 0) {

			foreach ($relatedItems as $relatedItem) {
				if ($relatedItem->image) {
					$imageUrl_large = url('uploads/product/' . $relatedItem->image);
					$imageUrl_small = url('uploads/product/thumb/' . $relatedItem->image);
				} else {
					$imageUrl_large = url('uploads/no-image.png');
					$imageUrl_small = url('uploads/no-image.png');
				}



				if (!empty($relatedItem->countdown_datetime) && strtotime($relatedItem->countdown_datetime) > strtotime(date('Y-m-d'))) {
					$retail_price     =  (float)$relatedItem['countDown_price'];
					$old_price        =  (float)$relatedItem['retail_price'];
				} else {
					$retail_price     =  (float)$relatedItem['retail_price'];
					$old_price        =  (float)$relatedItem['old_price'];
				}


				$related[] = [
					"id" => $relatedItem->id,
					'title'    => $relatedItem['title_' . $strLang],
					'imageUrl_large' => $imageUrl_large,
					'imageUrl_small' => $imageUrl_small,
					'caption_name'   => (string)$relatedItem['caption_' . $strLang],
					'caption_color'  => (string)$relatedItem['caption_color'],
					'retail_price'   => $retail_price,
					'old_price'      => $old_price
				];
			}
		}

		$success['data'] = ['productDetails' => $prodDetails, 'related' => $related];
		return response()->json($success, $this->successStatus);
	}
    //get categories
    private  function getCategoryByPid($productid)
    {
        $ProdCatInfo = ProductCategory::where("product_id", $productid)->orderBy('category_id', 'desc')->first();
        $categories = $this->getChildCatName($ProdCatInfo->category_id);
        foreach ($categories as $i => $category){
            $categories[$i]['level'] = ( $i + 1 );
            $categories[$i]['isHome'] = false;
            $categories[$i]['isProduct'] = false;
        }
        return $categories;
    }
    private function getChildCatName($id )
    {
        $categories = [];
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $ProdCat = Categories::where("id", $id)->first();
        if (!empty($ProdCat->parent_id)) {
            $categories = array_merge($this->getChildCatName($ProdCat->parent_id) , $categories) ;
        }
        if (!empty($ProdCat->id)) {
            $categories[] = [
                'id' => $ProdCat->id,
                'web_url' =>  url(app()->getLocale() . '/products/' . $ProdCat->id . '/' . $ProdCat->friendly_url) ,
                'title' =>$ProdCat['name_' . $strLang] ,
            ];
        }
        return $categories;
    }

	//get product reviews
	public static function getProductReviews($product_id, $strLang)
	{
		$reviews = [];
		$ReviewsLists = ProductReview::where('is_active', 1)->where('product_id', $product_id)->orderBy('created_at', 'DESC')->get();
		if (!empty($ReviewsLists) && count($ReviewsLists) > 0) {
			foreach ($ReviewsLists as $ReviewsList) {
				$reviews[] = [
					"id" => $ReviewsList->id,
					"name" => $ReviewsList->name,
					"email" => $ReviewsList->email,
					"message" => $ReviewsList->message,
					"ratings" => $ReviewsList->ratings,
					"created_at" => $ReviewsList->created_at
				];
			}
		}
		return $reviews;
	}
	//get items options
	public static function getProductOptions($product_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$parentOption = [];
		$productoptions = ProductOptionsCustomChosen::where('gwc_products_option_custom_chosen.product_id', $product_id);
		$productoptions = $productoptions->select(
			'gwc_products_option_custom.*',
			'gwc_products_option_custom_chosen.product_id',
			'gwc_products_option_custom_chosen.custom_option_id',
			'gwc_products_option_custom_chosen.is_required'
		);
		$productoptions = $productoptions->join('gwc_products_option_custom', 'gwc_products_option_custom.id', '=', 'gwc_products_option_custom_chosen.custom_option_id');
		$productoptions = $productoptions->where('gwc_products_option_custom.is_active', 1)->orderBy('gwc_products_option_custom.display_order', 'ASC')->groupBy('custom_option_id')->get();

		if (!empty($productoptions) && count($productoptions) > 0) {
			foreach ($productoptions as $productoption) {
				$parentOption[] = [
					"product_id"   => $productoption->product_id,
					"option_id"    => $productoption->custom_option_id,
					"option_name"  => $strLang == "en" ? $productoption->option_name_en : $productoption->option_name_ar,
					"option_type"  => $productoption->option_type,
					"is_required"  => $productoption->is_required,
					"child_options" => self::childOptions($productoption->custom_option_id, $productoption->product_id)
				];
			}
		}
		return $parentOption;
	}
	//get child options
	public static function childOptions($custom_option_id, $product_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$childOption = [];

        $productDetails = Product::findOrFail($product_id);
		if ($custom_option_id == 1) { //size
			$childOptSize = [];
			$sizeAttributes = self::getSizeByCustomIdProductId($custom_option_id, $product_id);
			if (!empty($sizeAttributes) && count($sizeAttributes) > 0) {
				foreach ($sizeAttributes->groupBy('size_id') as $sizeAttribute) {
                    $quantity = 0 ;
                    foreach ($productDetails->getQuantity(-1, $sizeAttribute->pluck('attribute_id')->toArray(), null, true) as $quantities) {
                        $quantity = $quantity + $quantities->quantity;
                    }
                    if ( $quantity <= 0 )
                        continue;
                    $sizeAttribute = $sizeAttribute->first();
					$childOptSize[] = [
						'size_id'      => $sizeAttribute->size_id,
						'size_name'    => $strLang == "en" ? $sizeAttribute->title_en : $sizeAttribute->title_ar,
                        'quantity'     => $quantity,
						'retail_price' => round($sizeAttribute->retail_price, 3),
						'old_price'    => round($sizeAttribute->old_price, 3)
					];
				}
				$childOption['sizes'] = $childOptSize;
				$childOption['sizes_colors'] = (object)[];
				$childOption['colors'] = [];
			}
		} else if ($custom_option_id == 2) { //color
			$childOptColor = [];
			$colorAttributes = self::getColorByCustomIdProductId($custom_option_id, $product_id);
			if (!empty($colorAttributes) && count($colorAttributes) > 0) {
				$product_color_image = '';
				$color_image = '';
				foreach ($colorAttributes->groupBy('color_id') as $colorAttribute) {
                    $quantity = 0 ;
                    foreach ($productDetails->getQuantity(-1, $colorAttribute->pluck('attribute_id')->toArray(), null, true) as $quantities) {
                        $quantity = $quantity + $quantities->quantity;
                    }
                    if ( $quantity <= 0 )
                        continue;
                    $colorAttribute = $colorAttribute->first();
					if (!empty($colorAttribute->color_image)) {
						$product_color_image = url('uploads/color/' . $colorAttribute->color_image);
					}
					if (!empty($colorAttribute->image)) {
						$color_image = url('uploads/color/' . $colorAttribute->image);
					}
					$childOptColor[] =       [
						'id'                  => $colorAttribute->id,
						'color_id'            => $colorAttribute->color_id,
						'color_name'          => $strLang == "en" ? $colorAttribute->title_en : $colorAttribute->title_ar,
						'color_image'         => $color_image,
						'color_code'          => !empty($colorAttribute->color_code) ? $colorAttribute->color_code : '',
						'product_color_image' => $product_color_image,
                        'quantity'            => $quantity,
						'retail_price'        => round($colorAttribute->retail_price, 3),
						'old_price'           => round($colorAttribute->old_price, 3)
					];
				}
				$childOption['colors'] = $childOptColor;
				$childOption['sizes_colors'] = (object)[];
				$childOption['sizes'] = [];
			}
		} else if ($custom_option_id == 3) {
			$childOption['sizes']        = [];
			$childOption['colors']       = [];
			$childOption['sizes_colors'] = self::getAttributes($custom_option_id, $product_id);
		} else {
			$childOption['sizes']        = [];
			$childOption['colors']       = [];
			$childOption['sizes_colors'] = (object)[];
			$childOption['others']       = self::getCustomOptions($custom_option_id, $product_id);
		}
		return $childOption;
	}

	//get options
	public static function getCustomOptions($custom_option_id, $product_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
        $productDetails = Product::findOrFail($product_id);

		$customOptionChilds  = ProductOptions::where('gwc_products_options.product_id', $product_id)
			->where('gwc_products_options.custom_option_id', $custom_option_id);
		$customOptionChilds  = $customOptionChilds->select(
			'gwc_products_option_custom_chosen.custom_option_id',
			'gwc_products_option_custom_chosen.product_id',
			'gwc_products_option_custom_chosen.is_required',
			'gwc_products_option_custom_child.*',
			'gwc_products_options.*'

		);
		$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_child', 'gwc_products_option_custom_child.id', '=', 'gwc_products_options.option_value_id');

		$customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_chosen', 'gwc_products_option_custom_chosen.id' ,'=', 'gwc_products_options.chosen_id');
//        $customOptionChilds = $customOptionChilds->groupBy('gwc_products_option_custom_chosen.id');
		$customOptionChilds  = $customOptionChilds->get();
		$chldopt = [];
		if (!empty($customOptionChilds) && count($customOptionChilds) > 0) {
			foreach ($customOptionChilds->groupBy('option_value_id') as $customOptionChild) {
                $quantity = 0 ;
                foreach ($productDetails->getQuantity(-1, null, $customOptionChild->pluck('id')->toArray(), true) as $quantities) {
                    $quantity = $quantity + $quantities->quantity;
                }
                if ( $quantity <= 0 )
                    continue;
                $customOptionChild = $customOptionChild->first();
				$chldopt[] = [
					"id"                   => $customOptionChild->id,
					"option_value_name_en" => $customOptionChild->option_value_name_en,
					"option_value_name_ar" => $customOptionChild->option_value_name_ar,
					"option_value_id"      => $customOptionChild->option_value_id,
					"weight"               => $customOptionChild->weight,
					"quantity"             => $quantity,
					"sku_no"               => $customOptionChild->sku_no,
					"retail_price"         => round($customOptionChild->retail_price, 3),
					"is_price_add"         => $customOptionChild->is_price_add,
					"is_deduct"            => $customOptionChild->is_deduct,
					"custom_option_id"     => $customOptionChild->custom_option_id
				];
			}
		}

		return $chldopt;
	}

	//get size by customuid
	public static function getSizeByCustomIdProductId($custom_option_id, $product_id)
	{

        $Attributes = ProductAttribute::where('product_id', $product_id)->where('custom_option_id', $custom_option_id);
        $Attributes = $Attributes->select(
            'gwc_sizes.*',
            'gwc_products_attribute.size_id',
            'gwc_products_attribute.product_id',
            'gwc_products_attribute.custom_option_id',
            'gwc_products_attribute.id as attribute_id'
        );
        $Attributes = $Attributes->join("gwc_sizes", "gwc_sizes.id", "=", "gwc_products_attribute.size_id");
        $Attributes = $Attributes->where('gwc_products_attribute.size_id', '!=', 0)
//            ->where('gwc_products_attribute.quantity', '>', 0)
//            ->groupBy('gwc_products_attribute.size_id')
            ->get();
		return $Attributes;
	}

	//get color attribute
	public static function getColorByCustomIdProductId($custom_option_id, $product_id)
	{

		$Attributes = ProductAttribute::where('product_id', $product_id)->where('custom_option_id', $custom_option_id);
		$Attributes = $Attributes->select(
			'gwc_colors.*',
			'gwc_products_attribute.color_id',
			'gwc_products_attribute.product_id',
			'gwc_products_attribute.custom_option_id',
            'gwc_products_attribute.id as attribute_id'
		);
		$Attributes = $Attributes->join("gwc_colors", "gwc_colors.id", "=", "gwc_products_attribute.color_id");
		$Attributes = $Attributes->where('gwc_products_attribute.color_id', '!=', 0)
//			->where('gwc_products_attribute.quantity', '>', 0)
//			->groupBy('gwc_products_attribute.color_id')
			->get();
		return $Attributes;
	}
	/////////////////////////////////////////////////////end option////////////////////////////////////////////////////////////////////////
	//get ratings 
	public static function getProductRatings($product_id)
	{
		$ratings = 0;
		$reviewsCount = ProductReview::where('product_id', $product_id)->get()->count();
		if (!empty($reviewsCount)) {
			$reviewsSum   = ProductReview::where('product_id', $product_id)->get()->sum('ratings');
			$ratings = round(($reviewsSum / $reviewsCount), 1);
		}
		return $ratings;
	}
	//get gallery
	public static function getGalleries($product_id)
	{
		$settingInfo    = Settings::where("keyname", "setting")->first();
		$galleryLists   = ProductGallery::where('product_id', $product_id)->orderBy('display_order', $settingInfo->default_sort)->get();
		return $galleryLists;
	}
	//get attribute
	public static function getAttributes($custom_option_id, $product_id)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$attr_size = [];
		$attr_color = [];
		$Attributes_Sizes = ProductAttribute::where('custom_option_id', $custom_option_id)->groupBy('size_id')->where('product_id', $product_id)->where('size_id', '!=', 0)->get();
		if (!empty($Attributes_Sizes) && count($Attributes_Sizes) > 0) {
			foreach ($Attributes_Sizes as $Attributes_Size) {
				$attr_size[] = array(
					"id" => $Attributes_Size->id,
					"size_id" => $Attributes_Size->size_id,
					"size_name" => self::sizeName($Attributes_Size->size_id, $strLang),
					"retail_price" => $Attributes_Size->retail_price,
					"old_price" => $Attributes_Size->old_price,
					"colors" => self::getColorBySize($product_id, $Attributes_Size->size_id, $strLang)
				);
			}
		}

		//color
		$Attributes_Colors = ProductAttribute::where('product_id', $product_id)->where('color_id', '!=', 0)->where('quantity', '>', 0)->groupBy('color_id')->get();
		if (!empty($Attributes_Colors) && count($Attributes_Colors) > 0) {
			foreach ($Attributes_Colors as $Attributes_Color) {
				$colorDetails  = Color::where('id', $Attributes_Color->color_id)->first();
				if (!empty($colorDetails->image)) {
					$color_image = url('uploads/color/thumb/' . $colorDetails->image);
				} else {
					$color_image = '';
				}
				$attr_color[] = array(
					"id" => $Attributes_Color->id,
					"color_id" => $Attributes_Color->color_id,
					"color_name" => self::colorName($Attributes_Color->color_id, $strLang),
					"color_code" => !empty($colorDetails->color_code) ? $colorDetails->color_code : '',
					"color_image" => $color_image,
					"retail_price" => $Attributes_Color->retail_price,
					"old_price" => $Attributes_Color->old_price
				);
			}
		}


		return ["sizes" => $attr_size, "colors" => $attr_color];
//		return $attr_size;
	}


	//get colors for size
	public static function getColorBySize($product_id, $size_id, $strLang)
	{
		$attr_color = [];
        $productDetails = Product::FindOrFail($product_id);
		$Attributes_Colors = ProductAttribute::where('product_id', $product_id)->where('size_id', '=', $size_id)->where('color_id', '!=', 0)->get();
		if (!empty($Attributes_Colors) && count($Attributes_Colors) > 0) {
			foreach ($Attributes_Colors->groupBy('color_id') as $Attributes_Color) {
                $quantity = 0 ;
                foreach ($productDetails->getQuantity(-1, $Attributes_Color->pluck('id')->toArray(), null, true) as $quantities) {
                    $quantity = $quantity + $quantities->quantity;
                }
                if ($quantity <= 0  )
                    continue;
                $Attributes_Color = $Attributes_Color->first();
				$colorDetails  = Color::where('id', $Attributes_Color->color_id)->first();
				if (!empty($colorDetails->image)) {
					$color_image = url('uploads/color/thumb/' . $colorDetails->image);
				} else {
					$color_image = '';
				}
				if (!empty($Attributes_Color->color_image)) {
					$product_color_image = url('uploads/color/' . $Attributes_Color->color_image);
				} else {
					$product_color_image = '';
				}


				$attr_color[] = array(
					"id" => $Attributes_Color->id,
					"color_id" => $Attributes_Color->color_id,
					"color_name" => self::colorName($Attributes_Color->color_id, $strLang),
					"color_code" => !empty($colorDetails->color_code) ? $colorDetails->color_code : '',
					"color_image" => $color_image,
					"quantity" => $quantity,
					"product_color_image" => $product_color_image,
					"retail_price" => $Attributes_Color->retail_price,
					"old_price" => $Attributes_Color->old_price
				);
			}
		}
		return $attr_color;
	}
	//get color name
	public static function colorName($id, $strLang)
	{
		$txt = '--';
		$Details   = Color::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}
	//get size name 
	public static function sizeName($id, $strLang)
	{
		$txt = '--';
		$Details   = Size::where('id', $id)->first();
		if (!empty($Details['title_' . $strLang])) {
			$txt = $Details['title_' . $strLang];
		}
		return $txt;
	}
	//get product categories
	public function getCategories(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (!empty($request->parent_id)) {
			$parent = $request->parent_id;
		} else {
			$parent = 0;
		}

		$categories      = [];
		$settingInfo     = Settings::where("keyname", "setting")->first();
		$categoriesInfos = Categories::where("is_active", "1")->where('parent_id', $request->parent_id)->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($categoriesInfos) && count($categoriesInfos) > 0) {
			foreach ($categoriesInfos as $categoriesInfo) {
				if (!empty($categoriesInfo->image)) {
					$imageurl =  url('uploads/category/thumb/' . $categoriesInfo->image);
				} else {
					$imageurl =  url('uploads/no-image.png');
				}
				$category['id']      =  $categoriesInfo->id;
				$category['title']   =  $categoriesInfo['name_' . $strLang];
				$category['image']   =  $imageurl;
				$category['subcats']   =  self::getSubcategories($categoriesInfo->id);
				$categories[] = $category;
			}
		}

		$success['data'] = ['categories' => $categories,];
		return response()->json($success, $this->successStatus);
	}

	//get subcategories
	public static function getSubcategories($catid)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$categories      = [];
		$settingInfo     = Settings::where("keyname", "setting")->first();
		$categoriesInfos = Categories::where("is_active", "1")->where('parent_id', $catid)->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($categoriesInfos) && count($categoriesInfos) > 0) {
			foreach ($categoriesInfos as $categoriesInfo) {
				if (!empty($categoriesInfo->image)) {
					$imageurl =  url('uploads/category/thumb/' . $categoriesInfo->image);
				} else {
					$imageurl =  url('uploads/no-image.png');
				}
				$category['id']      =  $categoriesInfo->id;
				$category['title']   =  $categoriesInfo['name_' . $strLang];
				$category['image']   =  $imageurl;
				$category['subcats']   =  self::getSubcategories($categoriesInfo->id);
				$categories[] = $category;
			}
		}
		return $categories;
	}


	public static function ajax_post_categorycount($id)
	{
		if (!empty($id)) {
			$Categories = Categories::find($id);
			$Categories->app_views = ($Categories->app_views + 1);
			$Categories->save();
		}
	}


	//products listings
	public function listProducts(Request $request)
	{

		$limit = 20;
		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id


		if (empty($request->catid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}
		$catid = $request->catid;

		self::ajax_post_categorycount($catid);

		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$settingInfo     = Settings::where("keyname", "setting")->first();

		$categoryDetails = Categories::where('id', $catid)->select('gwc_categories.name_en', 'gwc_categories.name_ar', 'gwc_categories.id')->first();
		//get sorting option
		if (!empty($request->product_sort_by) && $request->product_sort_by == "popular") {
			$sortKeyName = 'most_visited_count';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "max-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "min-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "a-z") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "z-a") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'DESC';
		} else {
			$sortKeyName = 'id';
			$sortKey = 'DESC';
		}
		//load items per page
		if (!empty($request->product_per_page)) {
			$recordPerPage = $request->product_per_page;
		} else {
			$recordPerPage = $settingInfo->item_per_page_front;
		}
		//product listings
		if (!empty($request->filter_by_size) && empty($request->filter_by_color)) {
			$size_id = $request->filter_by_size;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['category_id' => $catid, 'gwc_products_attribute.size_id' => $size_id])->where('gwc_products.is_active', '!=', 0);
		} else if (empty($request->filter_by_size) && !empty($request->filter_by_color)) {
			$color_id = $request->filter_by_color;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['category_id' => $catid, 'gwc_products_attribute.color_id' => $color_id])->where('gwc_products.is_active', '!=', 0);
		} else if (!empty($request->filter_by_size) && !empty($request->filter_by_color)) {
			$color_id = $request->filter_by_color;
			$size_id = $request->filter_by_size;
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['category_id' => $catid, 'gwc_products_attribute.size_id' => $size_id, 'gwc_products_attribute.color_id' => $color_id])->where('gwc_products.is_active', '!=', 0);
		} else {
			$productLists = DB::table('gwc_products_category')
				->select(
					'gwc_products_category.product_id',
					'gwc_products_category.category_id',
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
				->where(['category_id' => $catid])->where('gwc_products.is_active', '!=', 0);
		}
		//filter by tags
		if (!empty($request->filter_by_tags)) {
			$search = $request->filter_by_tags;
			$productLists = $productLists->whereRaw("find_in_set('" . $search . "',gwc_products.tags_en)")
				->whereRaw("find_in_set('" . $search . "',gwc_products.tags_ar)");
		}
		//filter by price range
		if (!empty($request->rangeprice)) {
			$explodePrice = explode('-', $request->rangeprice);
			$productLists = $productLists->where('gwc_products.retail_price', '>=', $explodePrice[0])
				->where('gwc_products.retail_price', '<=', $explodePrice[1]);
		}
		//count total records
		$totalItems = $productLists->get()->count();
		//price range
		$retailPriceRanges = $productLists->max('gwc_products.retail_price');

		$productLists = $productLists->orderBy($sortKeyName, $sortKey)->offset($offset)->limit($limit)->get();


		//check subcategoris exit or not
		$productCategoriesLists = Categories::where('is_active', 1)
			->where('parent_id', $catid)
			->select('gwc_categories.name_en', 'gwc_categories.name_ar', 'gwc_categories.id')
			->orderBy('name_en', 'ASC')
			->get();

		//get sizes
		$prodSizes = DB::table('gwc_products_category')
			->select(
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_products.id as pid',
				'gwc_products_attribute.size_id',
				'gwc_products_attribute.product_id',
				'gwc_products_category.product_id',
				'gwc_sizes.id',
				'gwc_sizes.is_active',
				'gwc_sizes.title_en',
				'gwc_sizes.title_en'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_sizes', 'gwc_sizes.id', '=', 'gwc_products_attribute.size_id')
			->where(['gwc_products_category.category_id' => $catid, 'gwc_sizes.is_active' => 1])->where('gwc_products.is_active', '!=', 0)
			->where('size_id', '!=', 0)->groupBy('gwc_products_attribute.size_id')
			->get();

		//get colors
		$prodColors = DB::table('gwc_products_category')
			->select(
				'gwc_products.id as product_id',
				'gwc_products.is_active',
				'gwc_products_category.product_id',
				'gwc_products_category.category_id',
				'gwc_products.id as pid',
				'gwc_products_attribute.product_id',
				'gwc_products_attribute.color_id',
				'gwc_colors.id',
				'gwc_colors.title_en',
				'gwc_colors.title_ar',
				'gwc_colors.color_code',
				'gwc_colors.is_active'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_colors', 'gwc_colors.id', '=', 'gwc_products_attribute.color_id')
			->where(['gwc_products_category.category_id' => $catid, 'gwc_colors.is_active' => 1])->where('gwc_products.is_active', '!=', 0)
			->where('gwc_products_attribute.color_id', '!=', 0);

		if (!empty($request->filter_by_size)) {
			$prodColors->where('gwc_products_attribute.size_id', '=', $request->filter_by_size);
		}
		$prodColors = $prodColors->groupBy('gwc_products_attribute.color_id')->get();
		//customize color
		$prodColorsY = [];
		if (!empty($prodColors) && count($prodColors) > 0) {
			foreach ($prodColors as $prodColor) {
				if (!empty($prodColor->image)) {
					$colorImage = url('uploads/colors/thumb/' . $prodColor->image);
				} else {
					$colorImage = "";
				}
				$prodColorsY[] = [
					'id' => $prodColor->id,
					'color_name_en' => $prodColor->title_en,
					'color_name_ar' => $prodColor->title_ar,
					'color_code'    => (string)$prodColor->color_code,
					'colorImage'    => $colorImage
				];
			}
		}
		//end customize color

		//get most popular items 
		/*
			$mostpopularitems    = DB::table('gwc_products_category')
			->select('gwc_products_category.product_id','gwc_products_category.category_id','gwc_products.id','gwc_products.*')
			->join('gwc_products','gwc_products.id','=','gwc_products_category.product_id')
			->where(['category_id'=>$catid, 'is_active' =>1])
			->orderBy('most_visited_count','DESC')
			->limit(5)
			->get();
			*/
		//get product tags
		$cattags = [];

		$prodtags = DB::table('gwc_products_category')
			->select('gwc_products_category.product_id', 'gwc_products_category.category_id', 'gwc_products.id', 'gwc_products.tags_en', 'gwc_products.tags_ar', 'gwc_products.is_active')
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->where('gwc_products_category.category_id', '=', $catid)->where('gwc_products.is_active', '!=', 0)
			->get();
		if (!empty($prodtags) && count($prodtags) > 0) {
			$tags = '';
			foreach ($prodtags as $prodtag) {
				if ($strLang == 'en' && !empty($prodtag->tags_en)) {
					$tags .= $prodtag->tags_en . ',';
				} else if ($strLang == 'ar' && !empty($prodtag->tags_ar)) {
					$tags .= $prodtag->tags_ar . ',';
				}
			}
			$ftags = trim($tags, ',');
			$arrTags = explode(",", $ftags);
			$cattags = array_unique($arrTags);
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
				if (!empty($productList->rollover_image)) {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->rollover_image);
				} else {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->image);
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
					'image'          => (string)$imageUrl,
					'rolloverImage'  => (string)$imageRollOverUrl,
					'is_wish_item'   => self::getWhishStatus($productList->id, $userid),
					'countdown_datetime'  => date("Y-m-d") < date("Y-m-d", strtotime($productList->countdown_datetime)) ? $productList->countdown_datetime : null
				];
			}
			$prods = $prods;
		}
		//end

		//customize category
		$prodCategoryY = [];
		if (!empty($productCategoriesLists) && count($productCategoriesLists) > 0) {
			foreach ($productCategoriesLists as $prodCategory) {
				if (!empty($prodCategory->image)) {
					$catImage = url('uploads/category/thumb/' . $prodCategory->image);
				} else {
					$catImage = "";
				}
				$prodCategoryY[] = [
					'id'            => $prodCategory->id,
					'name_en'       => $prodCategory->name_en,
					'name_ar'       => $prodCategory->name_ar,
					'catImage'      => $catImage
				];
			}
		}
		//end customize category

		$success['data'] = [
			'productLists' => $prods,
			'categoryDetails' => $categoryDetails,
			'productCategoriesLists' => $prodCategoryY,
			'retailPriceRanges' => $retailPriceRanges,
			'prodSizes' => $prodSizes,
			'prodColors' => $prodColorsY,
			'totalItems' => $totalItems,
			'cattags' => $cattags
		];
		return response()->json($success, $this->successStatus);
	}

	//Section more items///////////////////////////////////////////////////////////////////////////////////////////////////
	public function listSectionsProducts(Request $request)
	{

		$limit = 20;
		if (!empty($request->offset)) {
			$offset = $request->offset;
		} else {
			$offset = 0;
		}

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id



		if (empty($request->secid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}
		$secid = $request->secid;


		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}


		$settingInfo     = Settings::where("keyname", "setting")->first();
		$sectionDetails  = Section::where('id', $secid)->first();
		//get sorting option
		if (!empty($request->product_sort_by) && $request->product_sort_by == "popular") {
			$sortKeyName = 'most_visited_count';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "max-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'DESC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "min-price") {
			$sortKeyName = 'retail_price';
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "a-z") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'ASC';
		} else if (!empty($request->product_sort_by) && $request->product_sort_by == "z-a") {
			$sortKeyName = 'title_' . $strLang;
			$sortKey = 'DESC';
		} else {
			$sortKeyName = 'id';
			$sortKey = 'DESC';
		}
		//load items per page
		if (!empty($request->product_per_page)) {
			$recordPerPage = $request->product_per_page;
		} else {
			$recordPerPage = $settingInfo->item_per_page_front;
		}
		//product listings
		if (!empty($request->filte_by_size) && empty($request->filte_by_color)) {
			$size_id = $request->filte_by_size;
			$productLists = Product::query()
				->select(
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.homesection' => $secid, 'gwc_products_attribute.size_id' => $size_id])->where('gwc_products.is_active', '!=', 0);
		} else if (empty($request->filte_by_size) && !empty($request->filte_by_color)) {
			$color_id = $request->filte_by_color;
			$productLists = Product::query()
				->select(
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color',
					DB::raw('sum(gwc_products_attribute.attribute_quantity) as p_qty', 'gwc_products_attribute.product_id=gwc_products.id')
				)
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.homesection' => $secid, 'gwc_products_attribute.color_id' => $color_id])->where('gwc_products.is_active', '!=', 0);
		} else if (!empty($request->filte_by_size) && !empty($request->filte_by_color)) {
			$color_id = $request->filte_by_color;
			$size_id = $request->filte_by_size;
			$productLists = Product::query()
				->select(
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'
				)
				->join('gwc_products_attribute', 'gwc_products_attribute.product_id', '=', 'gwc_products.id')
				->where(['gwc_products.homesection' => $secid, 'gwc_products_attribute.size_id' => $size_id, 'gwc_products_attribute.color_id' => $color_id])->where('gwc_products.is_active', '!=', 0);
		} else {
			$productLists = Product::query()
				->select(
					'gwc_products.id',
					'gwc_products.title_en',
					'gwc_products.title_ar',
					'gwc_products.sku_no',
					'gwc_products.item_code',
					'gwc_products.details_en',
					'gwc_products.details_ar',
					'gwc_products.retail_price',
					'gwc_products.old_price',
					'gwc_products.is_attribute',
					'gwc_products.quantity',
					'gwc_products.image',
					'gwc_products.rollover_image',
					'gwc_products.countdown_datetime',
					'gwc_products.countdown_price',
					'gwc_products.caption_en',
					'gwc_products.caption_ar',
					'gwc_products.caption_color'

				)
				->where(['homesection' => $secid])->where('gwc_products.is_active', '!=', 0);
		}
		//filter by tags
		if (!empty($request->filter_by_tags)) {
			$search = $request->filter_by_tags;
			$productLists = $productLists->whereRaw("find_in_set('" . $search . "',gwc_products.tags_en)")
				->whereRaw("find_in_set('" . $search . "',gwc_products.tags_ar)");
		}
		//filter by price range
		if (!empty($request->rangeprice)) {
			$explodePrice = explode('-', $request->rangeprice);
			$productLists = $productLists->where('gwc_products.retail_price', '>=', $explodePrice[0])
				->where('gwc_products.retail_price', '<=', $explodePrice[1]);
		}
		//price range
		$retailPriceRanges = $productLists->max('gwc_products.retail_price');

		//count total records
		$totalItems = $productLists->get()->count();

		$productLists = $productLists->orderBy($sortKeyName, $sortKey)->get(); //->offset($offset)->limit($limit)

		//check subcategoris exit or not

		$productCategoriesLists = Product::query()
			->select(
				'gwc_products.id as pid',
				'gwc_products.homesection',
				'gwc_products_category.category_id',
				'gwc_products_category.product_id',
				'gwc_categories.name_en',
				'gwc_categories.name_ar'
			)
			->join('gwc_products_category', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->join('gwc_categories', 'gwc_categories.id', '=', 'gwc_products_category.category_id')
			->where(['gwc_products.homesection' => $secid, 'gwc_categories.is_active' => 1])->where('gwc_products.is_active', '!=', 0)
			->groupBy('gwc_products_category.category_id')
			->get();

		//get sizes
		$prodSizes = Product::query()
			->select(
				'gwc_products.id',
				'gwc_products.homesection',
				'gwc_products.id as pid',
				'gwc_products_attribute.size_id',
				'gwc_products_attribute.product_id',
				'gwc_sizes.id',
				'gwc_sizes.is_active',
				'gwc_sizes.title_en',
				'gwc_sizes.title_en'
			)
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_sizes', 'gwc_sizes.id', '=', 'gwc_products_attribute.size_id')
			->where(['gwc_products.homesection' => $secid, 'gwc_sizes.is_active' => 1])
			->where('gwc_products.is_active', '!=', 0)
			->where('size_id', '!=', 0)->groupBy('gwc_products_attribute.size_id')
			->get();

		//get colors
		$prodColors = Product::query()
			->select(
				'gwc_products.id',
				'gwc_products.homesection',
				'gwc_products.id as pid',
				'gwc_products_attribute.product_id',
				'gwc_products_attribute.color_id',
				'gwc_colors.id',
				'gwc_colors.title_en',
				'gwc_colors.title_ar',
				'gwc_colors.color_code',
				'gwc_colors.is_active'
			)
			->join('gwc_products_attribute', 'gwc_products.id', '=', 'gwc_products_attribute.product_id')
			->join('gwc_colors', 'gwc_colors.id', '=', 'gwc_products_attribute.color_id')
			->where(['gwc_products.homesection' => $secid, 'gwc_colors.is_active' => 1])
			->where('gwc_products.is_active', '!=', 0)
			->where('gwc_products_attribute.color_id', '!=', 0);

		if (!empty($request->filte_by_size)) {
			$prodColors->where('gwc_products_attribute.size_id', '=', $request->filte_by_size);
		}
		$prodColors = $prodColors->groupBy('gwc_products_attribute.color_id')->get();

		//customize color
		$prodColorsY = [];
		if (!empty($prodColors) && count($prodColors) > 0) {
			foreach ($prodColors as $prodColor) {
				if (!empty($prodColor->image)) {
					$colorImage = url('uploads/colors/thumb/' . $prodColor->image);
				} else {
					$colorImage = "";
				}
				$prodColorsY[] = [
					'id' => $prodColor->id,
					'color_name_en' => $prodColor->title_en,
					'color_name_ar' => $prodColor->title_ar,
					'color_code'    => (string)$prodColor->color_code,
					'colorImage'    => $colorImage
				];
			}
		}
		//end customize color


		//get most popular items 
		/*$mostpopularitems    = Product::query()
		->select('gwc_products.*')
		->where(['homesection'=>$secid, 'is_active' =>1])
		->orderBy('most_visited_count','DESC')
		->limit(5)
		->get();*/
		//get product tags
		$cattags = [];
		$prodtags = Product::query()
			->select('gwc_products.*')
			->where('homesection', '=', $secid)->where('is_active', '=', 1)
			->get();
		if (!empty($prodtags) && count($prodtags) > 0) {
			$tags = '';
			foreach ($prodtags as $prodtag) {
				if ($strLang == 'en' && !empty($prodtag->tags_en)) {
					$tags .= $prodtag->tags_en . ',';
				} else if ($strLang == 'ar' && !empty($prodtag->tags_ar)) {
					$tags .= $prodtag->tags_ar . ',';
				}
			}
			$ftags = trim($tags, ',');
			$arrTags = explode(",", $ftags);
			$cattags = array_unique($arrTags);
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
				if (!empty($productList->rollover_image)) {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->rollover_image);
				} else {
					$imageRollOverUrl = url('uploads/product/thumb/' . $productList->image);
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
					'image'          => (string)$imageUrl,
					'rolloverImage'  => (string)$imageRollOverUrl,
					'is_wish_item'   => self::getWhishStatus($productList->id, $userid),
					'countdown_datetime'  => date("Y-m-d") < date("Y-m-d", strtotime($productList->countdown_datetime)) ? $productList->countdown_datetime : null
				];
			}
			$prods = $prods;
		}
		//end

		//customize category
		$prodCategoryY = [];
		if (!empty($productCategoriesLists) && count($productCategoriesLists) > 0) {
			foreach ($productCategoriesLists as $prodCategory) {
				if (!empty($prodCategory->image)) {
					$catImage = url('uploads/category/thumb/' . $prodCategory->image);
				} else {
					$catImage = "";
				}
				$prodCategoryY[] = [
					'id'            => $prodCategory->category_id,
					'name_en'       => $prodCategory->name_en,
					'name_ar'       => $prodCategory->name_ar,
					'catImage'      => $catImage
				];
			}
		}
		//end customize category

		$success['data'] = [
			'productLists' => $prods,
			'sectionDetails' => $sectionDetails,
			'productCategoriesLists' => $prodCategoryY,
			'retailPriceRanges' => $retailPriceRanges,
			'prodSizes' => $prodSizes,
			'prodColors' => $prodColorsY,
			'totalItems' => $totalItems,
			'cattags' => $cattags
		];
		return response()->json($success, $this->successStatus);
	}

	//end Section items
	public static function ajax_post_slidecount($id)
	{
		if (!empty($id)) {
			$slideshow = Slideshow::find($id);
			$slideshow->app_views = ($slideshow->app_views + 1);
			$slideshow->save();
		}
	}

	public static function ajax_post_bannercount($id)
	{
		if (!empty($id)) {
			$Banner = Banner::find($id);
			$Banner->app_views = ($Banner->app_views + 1);
			$Banner->save();
		}
	}

	//home api
	public function getHome(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$settingInfo = Settings::where("keyname", "setting")->first();

		//get user id
		if (!empty($this->isTokenValid($request->bearerToken()))) {
			$user = User::where('api_token', $request->bearerToken())->first();
			if (!empty($user->id)) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
		} else {
			$userid = 0;
		}
		//end get user id
		//start register device
		if (!empty($request->input('device_token')) && !empty($request->input('device_type'))) {
			Common::registerDevice($request->input('device_token'), $request->input('device_type'), $userid);
		}

		$offertext = '';
		if (!empty($settingInfo->top_header_note_en) && $strLang == "en") {
			$offertext = $settingInfo->top_header_note_en;
		} else if (!empty($settingInfo->top_header_note_ar) && $strLang == "ar") {
			$offertext = $settingInfo->top_header_note_ar;
		}
		//slideshow
		$slides = [];
		$slideInfos   = Slideshow::where("is_active", "1")->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($slideInfos) && count($slideInfos) > 0) {
			foreach ($slideInfos as $slideInfo) {
				self::ajax_post_slidecount($slideInfo->id);
				$slide['id']    =  $slideInfo->id;
				$slide['link']  =  (string)$slideInfo->link;
				$slide['link_id']    =  (string)$slideInfo->link_id;
				$slide['link_type']  =  (string)$slideInfo->link_type;
				$slide['image'] =  url('uploads/slideshow/' . $slideInfo->image);
				$slides[] = $slide;
			}
		}
		//banner
		$banners = [];
		$bannerInfos   = Banner::where("is_active", "1")->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($bannerInfos) && count($bannerInfos) > 0) {
			foreach ($bannerInfos as $bannerInfo) {
				self::ajax_post_bannercount($bannerInfo->id);
				$banner['id']    =  $bannerInfo->id;
				$banner['link']  =  (string)$bannerInfo->link;
				$banner['link_id']   =  (string)$bannerInfo->link_id;
				$banner['link_type'] =  (string)$bannerInfo->link_type;
				$banner['image'] =  url('uploads/banner/' . $bannerInfo->image);
				$banners[] = $banner;
			}
		}
		//section and items
		$sections = [];
		$sectionsInfos   = Section::where("is_active", "1")->where(function ($query) {
            $query->where("section_type", "regular")
                ->orWhere("key_name", "latest_product");
        })->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($sectionsInfos) && count($sectionsInfos) > 0) {
			foreach ($sectionsInfos as $sectionsInfo) {
				$sectInfo['id']    =  $sectionsInfo->id;
				$sectInfo['is_slideshow'] = (bool) $sectionsInfo->slideShow;
				$sectInfo['title'] =  $sectionsInfo['title_' . $strLang];
				$sectInfo['items'] =  self::getSectionsProducts($sectionsInfo->id, $strLang , Auth::id() ,$sectionsInfo->key_name == "latest_product" , $sectionsInfo->ordering  );
				$sections[] = $sectInfo;
			}
		}
		//other option
		$otheroptions['ios_old_version']    = $settingInfo->ios_old_version;
		$otheroptions['ios_new_version']    = $settingInfo->ios_new_version;
		$otheroptions['android_old_version'] = $settingInfo->android_old_version;
		$otheroptions['android_new_version'] = $settingInfo->android_new_version;
		$otheroptions['whatsapp'] = [
            'is_active' => $settingInfo->is_float_whatsapp,
            'number' => $settingInfo->social_whatsapp,
            'office_hours' => $settingInfo['office_hours_' . $strLang],
        ];
		$otheroptions['is_reviews_disabled'] = (bool) $settingInfo->is_review_active;

		$otheroptions['checkout_note']      = trans('webMessage.checkout_note');
		$otheroptions['is_discount_disabled']      = ! (bool) $settingInfo->is_discount;
		$otheroptions['has_express_delivery']      = (bool) $settingInfo->is_express;
        $shopBySellers = Admin::where(['is_active' => 1, 'is_home' => 1])->get();
		$success['data'] = ['shopBySellers' => $shopBySellers, 'slideshow' => $slides, 'banner' => $banners, 'sections' => $sections, 'offertext' => $offertext, 'otheroptions' => $otheroptions];
		return response()->json($success, $this->successStatus);
	}

	//get items from section\
	public static function getSectionsProducts($section_id, $strLang, $userid = 0 , $lastProduct = false,$section_order = 'random')
	{
		$items = [];
		$settingInfo   = Settings::where("keyname", "setting")->first();
        $isProductOutOfStock = $settingInfo->show_out_of_stock;
        $productQuantity = $isProductOutOfStock == 0 ? 0 : -1;
        if ( $lastProduct ){
            $itemInfos   = Product::where("is_active", "!=", "0")->where('quantity', '>', $productQuantity)->limit(20);
            if ( $section_order == "random" )
                $itemInfos = $itemInfos->inRandomOrder();
            else
                $itemInfos = $itemInfos->orderBy($section_order, 'DESC');
            $itemInfos = $itemInfos->get();
        } else {
            $itemInfos   = Product::where("is_active", "!=", "0")->where('quantity', '>', $productQuantity)->where('homesection', $section_id);
            if ( $section_order == "random" )
                $itemInfos = $itemInfos->inRandomOrder();
            else
                $itemInfos = $itemInfos->orderBy($section_order, 'DESC');
            $itemInfos = $itemInfos->get();

        }
		if (!empty($itemInfos) && count($itemInfos) > 0) {
			foreach ($itemInfos as $itemInfo) {
				$item['id']       =  $itemInfo->id;
				$item['image']    =  url('uploads/product/thumb/' . $itemInfo->image);
				$item['title']    =  $itemInfo['title_' . $strLang];
				$item['is_attribute']  =  $itemInfo['is_attribute'];
				$item['is_stock']      =  self::IsAvailableQuantity($itemInfo->id);
				$item['caption_title'] =  (string)$itemInfo['caption_' . $strLang];
				$item['caption_color'] =  (string)$itemInfo['caption_color'];
				//check price
				if (!empty($itemInfo->countdown_datetime) && strtotime($itemInfo->countdown_datetime) > strtotime(date('Y-m-d'))) {
					$item['countdown_datetime'] = $itemInfo->countdown_datetime;
					$item['retail_price']  = (float)$itemInfo->countdown_price;
					$item['old_price']     = (float)$itemInfo->retail_price;
				} else {
					$item['countdown_datetime'] = null;
					$item['retail_price']  = (float)$itemInfo->retail_price;
					$item['old_price']     = (float)$itemInfo->old_price;
				}

				$item['is_wish_item'] = self::getWhishStatus($itemInfo->id, $userid);

				$items[] = $item;
			}
		}
		return $items;
	}



	//get user wish status from product id 
	public static function getWhishStatus($product_id, $user_id)
	{
		$wishStatus = 0;

		if (!empty($product_id) && !empty($user_id)) {
			$wishItems = CustomersWish::where('customer_id', $user_id)->where('product_id', $product_id)->first();
			if (!empty($wishItems->id)) {
				$wishStatus = 1;
			}
		}
		return $wishStatus;
	}



	//checl item quantity
	public static function IsAvailableQuantity($product_id)
	{
		$qty = 0;
		$productDetails   = Product::where('id', $product_id)->first();
//		if (empty($productDetails['is_attribute'])) {
			$qty   = $productDetails['quantity'];
//		} else {
//			$qty   = ProductAttribute::where('product_id', $product_id)->get()->sum('quantity');
//		}
//		$qty = $qty + self::getOptionsQuantityTemp($product_id);

		return $qty;
	}

	///get option quantty
	public static function getOptionsQuantityTemp($productid)
	{
		$strOptions = ProductOptions::where("product_id", $productid)->sum("quantity");
		return $strOptions;
	}


	///release payment
	public function releasepayment(Request $request)
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		if (empty($request->payid)) {
			$success['data'] = 'invalid transaction id';
			return response()->json($success, $this->failedStatus);
		}
		if (empty($request->vendor) || $request->vendor != $settingInfo->gulfpay_key) {
			$success['data'] = 'invalid vendor id';
			return response()->json($success, $this->failedStatus);
		}
		$trans = Transaction::where('payment_id', $request->payid)->first();
		if (empty($trans->id)) {
			$success['data'] = 'invalid transaction id';
			return response()->json($success, $this->failedStatus);
		}
		$trans->release_pay = 1;
		$trans->release_date = date("Y-m-d H:i:s");
		$trans->save();
		//$success['data']='Status is updated successfully';
		//return response()->json($success,$this->successStatus);
	}

	//get social links
	public function getSocialLinks(Request $request)
	{
		$socialLinks = [];
		$settingInfo = Settings::where("keyname", "setting")->first();

		if (!empty($settingInfo->social_facebook)) {
			$socialLinks['facebook']     = trans('webMessage.facebook');
			$socialLinks['facebook_url'] = $settingInfo->social_facebook;
		}

		if (!empty($settingInfo->social_twitter)) {
			$socialLinks['twitter']     = trans('webMessage.twitter');
			$socialLinks['twitter_url'] = $settingInfo->social_twitter;
		}

		if (!empty($settingInfo->social_instagram)) {
			$socialLinks['instagram']     = trans('webMessage.instagram');
			$socialLinks['instagram_url'] = $settingInfo->social_instagram;
		}

		if (!empty($settingInfo->social_linkedin)) {
			$socialLinks['linkedin']     = trans('webMessage.linkedin');
			$socialLinks['linkedin_url'] = $settingInfo->social_linkedin;
		}

		if (!empty($settingInfo->social_youtube)) {
			$socialLinks['youtube']     = trans('webMessage.youtube');
			$socialLinks['youtube_url'] = $settingInfo->social_youtube;
		}

		if (!empty($settingInfo->social_whatsapp)) {
			$socialLinks['whatsapp']     = trans('webMessage.whatsapp');
			$socialLinks['whatsapp_url'] = $settingInfo->social_whatsapp;
		}

		$success['data'] = $socialLinks;
		return response()->json($success, $this->successStatus);
	}

	//subscribe newsletter
	public function postNewsLetter(Request $request)
	{
		if (empty($request->newsletter_email)) {
			$success['data'] = trans('webMessage.email_required');
			return response()->json($success, $this->failedStatus);
		}
		if (filter_var($request->newsletter_email, FILTER_VALIDATE_EMAIL) === false) {
			$success['data'] = trans('webMessage.email_valid_required');
			return response()->json($success, $this->failedStatus);
		}

		$newsletter = Newsletter::where("newsletter_email", $request->newsletter_email)->first();
		if (!empty($newsletter->id)) {
			$success['data'] = trans('webMessage.email_already_subscribed');
			return response()->json($success, $this->failedStatus);
		} else {
			$newsletter = new Newsletter;
			$newsletter->newsletter_email = $request->newsletter_email;
			$newsletter->save();
			$success['data'] = trans('webMessage.subscribed_successfully');
			return response()->json($success, $this->successStatus);
		}
	}

	//single page
	public function getSinglePage(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		if (empty($request->keyname)) {
			$success['data'] = trans('webMessage.invalidkeyname');
			return response()->json($success, $this->failedStatus);
		}

		$singleInfo    = SinglePages::where("is_active", "1")->where('slug', $request->keyname)->first();

		if (!empty($singleInfo->id)) {
			$singleInfos['id']      = $singleInfo->id;
			$singleInfos['title']   = $singleInfo['title_' . $strLang];
			$singleInfos['details'] = $singleInfo['details_' . $strLang];

			$success['data'] = $singleInfos;
			return response()->json($success, $this->successStatus);
		} else {
			$success['data'] = trans('webMessage.norecordfound');
			return response()->json($success, $this->failedStatus);
		}
	}

	//get faq
	public function getFAQ()
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$settingInfo = Settings::where("keyname", "setting")->first();
		$faqLists = Faq::where("is_active", 1)->orderBy('display_order', $settingInfo->default_sort)->get();
		$faqsInfos = [];
		if (!empty($faqLists) && count($faqLists) > 0) {
			foreach ($faqLists as $faqList) {
				$faq['id']      = $faqList->id;
				$faq['title']   = $faqList['title_' . $strLang];
				$faq['details'] = $faqList['details_' . $strLang];
				$faq['details_en'] = $faqList['details_en'];
				$faq['details_ar'] = $faqList['details_ar'];
				$faq['details_ios'] = strip_tags($faqList['details_' . $strLang]);
				$faqsInfos[] = $faq;
			}
			$success['data'] = $faqsInfos;
			return response()->json($success, $this->successStatus);
		} else {
			$success['data'] = trans('webMessage.norecordfound');
			return response()->json($success, $this->failedStatus);
		}
	}
	//get contact details
	public function getContactDetails()
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$contactAddress = [];
		$settingInfo = Settings::where("keyname", "setting")->first();

		if (!empty($settingInfo['address_' . $strLang])) {
			$contactAddress['address'] = $settingInfo['address_' . $strLang];
		}

		if (!empty($settingInfo['office_hours_' . $strLang])) {
			$contactAddress['address'] = $settingInfo['office_hours_' . $strLang];
		}

		if (!empty($settingInfo['email'])) {
			$contactAddress['email'] = $settingInfo['email'];
		}

		if (!empty($settingInfo['phone'])) {
			$contactAddress['phone'] = $settingInfo['phone'];
		}

		if (!empty($settingInfo['mobile'])) {
			$contactAddress['mobile'] = $settingInfo['mobile'];
		}
		//subject
		$subjectLists = Subjects::where('is_active', 1)->get();
		$subjects = [];
		if (!empty($subjectLists) && count($subjectLists) > 0) {
			foreach ($subjectLists as $subjectList) {
				$sub['id']      = $subjectList->id;
				$sub['title']   = $subjectList['title_' . $strLang];
				$subjects[] = $sub;
			}
		}

		$success['data'] = ['contactDetails' => $contactAddress, 'subjects' => $subjects];
		return response()->json($success, $this->successStatus);
	}

	//post contact us details

	public function postContactForm(Request $request)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$settingInfo      = Settings::where("keyname", "setting")->first();

		$validator = Validator::make(
			$request->all(),
			[
				'name'    => ['required', 'string', 'min:4', 'max:190', new Name],
				'email'   => 'required|email',
				'mobile'  => ['required', new Mobile],
				'subject' => 'required',
				'message' => 'required|string|min:10|max:900',
			],
			[
				'name.required'    => trans('webMessage.name_required'),
				'email.required'   => trans('webMessage.email_required'),
				'mobile.required'  => trans('webMessage.mobile_required'),
				'subject.required' => trans('webMessage.subject_required'),
				'message.required' => trans('webMessage.message_required')
			]
		);

		if ($validator->fails()) {
			$errmsg = '';
			$allError = [];
			foreach ($validator->errors()->messages() as $error) {
				array_push($allError, $error[0]);
			}
			$success['data'] = $allError[0];
			return response()->json($success, $this->failedStatus);
		}

		$contact = new Contactus;
		$contact->name      = $request->input('name');
		$contact->email     = $request->input('email');
		$contact->mobile    = $request->input('mobile');
		$contact->subject   = $request->input('subject');
		$contact->message   = strip_tags($request->input('message'));
		$contact->created_at = date("Y-m-d H:i:s");
		$contact->updated_at = date("Y-m-d H:i:s");
		$contact->save();
		//send email notification
		if (!empty($request->input('email'))) {
			$data = [
				'dear'            => trans('webMessage.dear') . ' ' . $request->input('name'),
				'footer'          => trans('webMessage.email_footer'),
				'message'         => trans('webMessage.contactus_body'),
				'subject'         => self::getSubjectName($request->input('subject')),
				'email_from'      => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($request->input('email'))->send(new SendGrid($data));
		}
		//
		if (!empty($settingInfo->email)) {
			$appendMessage = "";
			$appendMessage .= "<br><b>" . trans('webMessage.name') . " : </b>" . $request->input('name');
			$appendMessage .= "<br><b>" . trans('webMessage.email') . " : </b>" . $request->input('email');
			$appendMessage .= "<br><b>" . trans('webMessage.mobile') . " : </b>" . $request->input('mobile');
			$appendMessage .= "<br><b>" . trans('webMessage.subject') . " : </b>" . self::getSubjectName($request->input('subject'));
			$appendMessage .= "<br><b>" . trans('webMessage.message') . " : </b>" . strip_tags($request->input('message'));
			$dataadmin = [
				'dear'            => trans('webMessage.dearadmin'),
				'footer'          => trans('webMessage.email_footer'),
				'message'         => trans('webMessage.contactus_admin_body') . "<br><br>" . $appendMessage,
				'subject'         => self::getSubjectName($request->input('subject')),
				'email_from'      => $settingInfo->from_email,
				'email_from_name' => $settingInfo->from_name
			];
			Mail::to($settingInfo->email)->send(new SendGrid($dataadmin));
		}
		//end sending email	
		$success['data'] = trans('webMessage.contact_message_sent');
		return response()->json($success, $this->successStatus);
	}





	public static function getRelatedItems($productid)
	{
		$relatedProduct = [];
		$productDetails = Product::where('id', $productid)->first();
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		//english tags
		if ($strLang == "en" && !empty($productDetails->tags_en)) {
			$tags = $productDetails->tags_en;
			$explode_tags = explode(",", $tags);
			if (count($explode_tags) > 0) {
				$relatedProduct = Product::where('is_active', '!=', 0)->where('id', '<>', $productid)->where(function ($sq) use ($explode_tags) {
					foreach ($explode_tags as $searchword) {
						$sq->orwhereRaw("FIND_IN_SET('" . $searchword . "',tags_en)");
					}
				});
				$relatedProduct = $relatedProduct->orderBy('most_visited_count', 'DESC')->get();
			} else {
				if (!empty($explode_tags[0])) {
					$relatedProduct = Product::where('is_active', '!=', 0)->where('id', '<>', $productid)->whereRaw("FIND_IN_SET('" . $explode_tags[0] . "',tags_en)");
					$relatedProduct = $relatedProduct->orderBy('most_visited_count', 'DESC')->get();
				}
			}
		}
		//arabic tags													  

		if ($strLang == "ar" && !empty($productDetails->tags_ar)) {
			$tags = $productDetails->tags_ar;
			$explode_tags = explode(",", $tags);
			if (count($explode_tags) > 0) {
				$relatedProduct = Product::where('is_active', '!=', 0)->where('id', '<>', $productid)->where(function ($sq) use ($explode_tags) {
					foreach ($explode_tags as $searchword) {
						$sq->orwhereRaw("FIND_IN_SET('" . $searchword . "',tags_ar)");
					}
				});
				$relatedProduct = $relatedProduct->orderBy('most_visited_count', 'DESC')->get();
			} else {
				if (!empty($explode_tags[0])) {
					$relatedProduct = Product::where('is_active', '!=', 0)->where('id', '<>', $productid)->whereRaw("FIND_IN_SET('" . $explode_tags[0] . "',tags_ar)");
					$relatedProduct = $relatedProduct->orderBy('most_visited_count', 'DESC')->get();
				}
			}
		}

		return $relatedProduct;
	}


	//get subject name
	public static function getSubjectName($subjectid)
	{
		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}
		$recDetails = Subjects::where('id', $subjectid)->first();
		if (!empty($recDetails['title_' . $strLang])) {
			$subject = $recDetails['title_' . $strLang];
		} else {
			$subject = "Contact us";
		}
		return $subject;
	}



	//////////////////////////////////////////////////////////////////////////////////////

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
}
