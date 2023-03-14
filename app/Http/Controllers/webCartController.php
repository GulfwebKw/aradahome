<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
use App\Warranty;
use App\DeliveryTimes;
use App\Tags;
use App\CyberSource;
use Curl;
use Hash;
//rules
use App\Rules\Name;
use App\Rules\Mobile;
//email
use App\Mail\SendGrid;
use App\Mail\SendGridOrder;
use Mail;
use Image;
use File;

use App\Classes\Payment\CBKPay;

class webCartController extends Controller
{



    //get temp orders
    public static function loadTempOrders($priceConvert = true)
    {
        $session_id = session()->getId();
        $tempOrders = OrdersTemp::where('unique_sid', $session_id)->orderBy('created_at', 'DESC')->get()->each(function ($item, $key) use ($priceConvert) {
            if ($priceConvert) {
                $tempPrice = Currency::convertTCountry($item->unit_price);
                $showPrice = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $item->unit_price;
                $item->unit_price = $showPrice;
            }
        });
        return $tempOrders;
    }
    //get temp orders Bundle Discount
    public static function loadTempOrdersBundleDiscount($tempOrders)
    {
        $bundleSetting = new \App\bundleSetting();
        $totalBundlePrice = 0;
        $totalBundleItems = 0;
        foreach ($tempOrders as $tempOrder) {
            $productDetails = self::getProductDetails($tempOrder->product_id);
            if ($productDetails->isBundle()) {
                $totalBundlePrice += round(($tempOrder->unit_price * $tempOrder->quantity), 3);
                $totalBundleItems += $tempOrder->quantity ?? 1;
            }
        }
        if ($totalBundleItems >= $bundleSetting->quantity) {
            $bundleDiscountPercent = $bundleSetting->percent;
            if ($bundleDiscountPercent > 100)
                $bundleDiscountPercent = 100;
            if ($bundleDiscountPercent < 0)
                $bundleDiscountPercent = 0;
            $bundleDiscount = round($totalBundlePrice * $bundleDiscountPercent / 100, 3) + ($bundleSetting->price ?? 0);
            if ($bundleDiscount > $totalBundlePrice)
                $bundleDiscount = $totalBundlePrice;
            return $bundleDiscount;
        } else {
            return  0;
        }
    }

    //count temp order
    public static function countTempOrders()
    {
        $session_id = session()->getId();
        $tempOrders = OrdersTemp::where('unique_sid', $session_id)->get()->count();
        return $tempOrders;
    }
    //get product details
    public static function getProductDetails($id)
    {
        $prodDetails = Product::where('id', $id)->withoutGlobalScopes()->first();
        return $prodDetails;
    }

    //settings
    public static function settings()
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        return $settingInfo;
    }

    //get product galerries
    public function getGalleries($product_id)
    {
        $settingInfo   = Settings::where("keyname", "setting")->first();
        $galleryLists   = ProductGallery::where('product_id', $product_id)->orderBy('display_order', $settingInfo->default_sort)->get();
        return $galleryLists;
    }

    //get product category details
    public static function getProductCatName($productid)
    {
        $ProdCat = [];
        $ProdCatInfo = ProductCategory::where("product_id", $productid)->orderBy('category_id', 'desc')->first();
        if (!empty($ProdCatInfo->category_id)) {
            $ProdCat = Categories::where("id", $ProdCatInfo->category_id)->first();
        }
        return $ProdCat;
    }

    ///***********************DETAILS ADD TO CART *************//


    public function ajax_details_addtocart(Request $request)
    {

        $settingInfo   = Settings::where("keyname", "setting")->first();
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }


        if (empty($request->product_id)) {
            $message = trans('webMessage.product_id_required');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        if (empty($request->price)) {
            $message = trans('webMessage.price_required');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        if (empty($request->quantity_attr)) {
            $message = trans('webMessage.quantity_required');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        //check size/color attribute
        if (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 3) {
            if (empty($request->size_attr)) {
                $message = trans('webMessage.size_required');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
            if (empty($request->color_attr)) {
                $message = trans('webMessage.color_required');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
            $aquantity = self::getProductQuantity($request->product_id, $request->size_attr, $request->color_attr);
            if (!empty($request->quantity_attr) && $request->quantity_attr > $aquantity) {
                $message = trans('Given quantity exceeds available quantity');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
        } elseif (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 1) {
            if (empty($request->size_attr)) {
                $message = trans('webMessage.size_required');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
            $aquantity = self::getProductQuantity($request->product_id, $request->size_attr, 0);
            if (!empty($request->quantity_attr) && $request->quantity_attr > $aquantity) {
                $message = trans('Given quantity exceeds available quantity');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
        } elseif (isset($request->option_sc) && !empty($request->option_sc) && $request->option_sc == 2) {
            if (empty($request->color_attr)) {
                $message = trans('webMessage.color_required');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
            $aquantity = self::getProductQuantity($request->product_id, 0, $request->color_attr);
            if (!empty($request->quantity_attr) && $request->quantity_attr > $aquantity) {
                $message = trans('Given quantity exceeds available quantity');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
        }

        //check other field validation
        $flag = self::checkOptionsFields($request);
        if (!empty($flag) && $flag > 0) {
            $message = trans('webMessage.options_required');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        //end check other field validation

        //check item exit or not
        $productDetails   = Product::where('id', $request->product_id)->where('is_active', '!=', 0)->first();
        if (empty($productDetails->id)) {
            $message = trans('webMessage.item_not_found');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        $requiredQty = $request->quantity_attr;
        $productQuantityCheck = $productDetails->quantity;

        // Checking if Product Quantity is in -ive in db.
        if ($productQuantityCheck < 0) {
            $message = trans('Product is Out of Stock as of now');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }
        // Checking if the given quantity is less than the Qty in db.
        if ($requiredQty > $productQuantityCheck) {
            $message = trans('Given quantity exceeds available quantity');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        }

        $aquantity = $this->AvailableQuantity($productDetails->id);

        //get item image
        if (!empty($productDetails->image)) {
            $imgUrl = url('uploads/product/thumb/' . $productDetails->image);
        } else {
            $imgUrl = url('uploads/no-image.png');
        }

        $session_id = session()->getId();

        $whereClause[] = ["product_id", "=", $request->product_id];
        $whereClause[] = ["unique_sid", "=", $session_id];
        //size
        if (!empty($request->size_attr)) {
            $whereClause[] = ["size_id", "=", $request->size_attr];
        }
        //size
        if (!empty($request->color_attr)) {
            $whereClause[] = ["color_id", "=", $request->color_attr];
            $colorImageDetails = self::getColorImage($productDetails->id, $request->color_attr);
            if (!empty($colorImageDetails->color_image)) {
                $imgUrl = url('uploads/color/thumb/' . $colorImageDetails->color_image);
            }
        }

        //check countdown price
        if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
            $price = round($productDetails->getOriginal('countdown_price'), 3);
        } else {
            $price = self::getProductPrice($request->product_id, $request->size_attr, $request->color_attr);
            if (empty($price)) {
                $price = $request->price;
            }
            //check option price
            $price = self::getOptionsPrice($request, $price);

            $price = $this->getProductBrandDiscount($productDetails->brand_id, $price);
        }

        $productoptions = ProductOptionsCustomChosen::where('gwc_products_option_custom_chosen.product_id', $request->product_id)
            ->where('gwc_products_option_custom_chosen.custom_option_id', '>=', 4);

        $productoptions = $productoptions->select('gwc_products_option_custom.id', 'gwc_products_option_custom.option_type', 'gwc_products_option_custom_chosen.*');

        $productoptions = $productoptions->join('gwc_products_option_custom', 'gwc_products_option_custom.id', '=', 'gwc_products_option_custom_chosen.custom_option_id');
        $productoptions->groupBy('gwc_products_option_custom_chosen.custom_option_id');
        $productoptions = $productoptions->get();
        $selectOptions = [];
        foreach ($productoptions as $productoption) {
            if ($productoption->option_type == "select for each order") {
                $selectOptions[] = "select-" . $productoption->product_id . "-" . $productoption->custom_option_id;
            }
        }
        $tempOrdersObject = OrdersTemp::where($whereClause);
        if (count($selectOptions) > 0) {
            foreach ($selectOptions as $selectOption) {
                $tempData = explode('-', $request[$selectOption]);
                $tempOrdersObject->whereHas('options', function ($query) use ($request, $tempData) {
                    return $query->where('product_id',  $tempData[1] ?? "")
                        ->where('option_id',  $tempData[2] ?? "")
                        ->where('option_child_ids',  $tempData[3] ?? "");
                });
            }
        }

        $tempOrder  = $tempOrdersObject->first();

        if (!empty($tempOrder->id)) {
            $tempOrder->unit_price = $price;
            $tempOrder->quantity   = !empty($request->quantity_attr) ? $request->quantity_attr : '1';
            $tempOrder->save();

            $totalAmount = self::getTotalCartAmount();
            $countitems  = self::countTempOrders();
            $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
            //prepare box message
            $tempPrice =  Currency::convertTCountry(($tempOrder->unit_price * $request->quantity_attr));
            $totalShowPrice =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??  ($tempOrder->unit_price * $request->quantity_attr);

            $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
					</div>
			        <a href="' . url(app()->getLocale() . '/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img src="' . $imgUrl . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>' . $request->quantity_attr . '</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price">' . $totalShowPrice . '' . \App\Currency::default() . ' </span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price">' . $totalAmount . '' . \App\Currency::default() . ' </span>
								</div>
							</a>
							
							<a href="' . url(app()->getLocale() . '/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
            //end
            $message = !empty($settingInfo->is_cart_popup) ? $message : trans('webMessage.quantity_is_updated');
            return ["status" => 200, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        } else {

            $tempOrder  = new OrdersTemp;
            $tempOrder->product_id = $request->product_id;
            $tempOrder->size_id    = $request->size_attr;
            $tempOrder->color_id   = $request->color_attr;
            $tempOrder->quantity   = $request->quantity_attr;
            $tempOrder->unit_price = $price;
            $tempOrder->unique_sid = $session_id;
            $tempOrder->save();
            //add options
            self::detailsTempOrders($request, $tempOrder->id);
            //end

            $totalAmount = self::getTotalCartAmount();
            $countitems  = self::countTempOrders();
            $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
            //prepare box message
            $tempPrice =  Currency::convertTCountry(($price * $request->quantity_attr));
            $totalShowPrice =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??  ($price * $request->quantity_attr);

            $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
					</div>
			        <a href="' . url(app()->getLocale() . '/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img  src="' . $imgUrl . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>' . $request->quantity_attr . '</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalShowPrice . '' . \App\Currency::default() . '</span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalAmount . '' . \App\Currency::default() . '</span>
								</div>
							</a>
							
							<a href="' . url(app()->getLocale() . '/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
            //end
        }

        //remove all options from cookies
        self::removeAllCookiesOptions($request->product_id);
        //refresh discount
        self::ajax_apply_coupon_to_cart_refresh();

        $message = !empty($settingInfo->is_cart_popup) ? $message : trans('webMessage.item_is_added');
        //collect payload
        $categoryNametxt = self::getProductCatName($productDetails->id);
        if (!empty($productDetails->brand_id)) {
            $brandNametxt    = Brand::where("id", $productDetails->brand_id)->first();
            $brandNametxtt = $brandNametxt->title_en ?? 'no brand';
        } else {
            $brandNametxtt = '';
        }
        $currencyCode = Currency::default(false)->code;
        $dataLayerPayload = [
            "item_name"     => $productDetails->title_en ?? 'no name',
            "item_id"       => $productDetails->id ?? '0',
            "price"         => $price,
            "item_brand"    => $brandNametxtt,
            "item_category" => $categoryNametxt->name_en,
            "item_category2" => "",
            "item_category3" => "",
            "item_category4" => "",
            "item_variant"  => "",
            "item_list_name" => "",
            "item_list_id"  => "",
            "index"         => $tempOrder->id,
            "quantity"      => $request->quantity_attr ?? '1',
            'currency'      => @$currencyCode,
        ];



        return ["status" => 200, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup, "payload" => json_encode($dataLayerPayload)];
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
                    $explodeme = explode("-", $child_option);
                    $tempOrderOption = new OrdersTempOption;
                    $tempOrderOption->product_id       = $productid;
                    $tempOrderOption->oid              = $oid;
                    $tempOrderOption->option_id        = $productoption->custom_option_id;
                    $tempOrderOption->option_child_ids = $explodeme[3];
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

                //textarea
                if (!empty($request->input('textarea-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('textarea-' . $productid . '-' . $productoption->custom_option_id);
                    $tempOrderCheck = new OrdersTempOption;
                    $tempOrderCheck->product_id       = $productid;
                    $tempOrderCheck->oid              = $oid;
                    $tempOrderCheck->option_id        = $productoption->custom_option_id;
                    $tempOrderCheck->option_child_ids = 0;
                    $tempOrderCheck->comments         = $child_option;
                    $tempOrderCheck->save();
                }

                //datetime
                if (!empty($request->input('datetime-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('datetime-' . $productid . '-' . $productoption->custom_option_id);
                    $tempOrderCheck = new OrdersTempOption;
                    $tempOrderCheck->product_id       = $productid;
                    $tempOrderCheck->oid              = $oid;
                    $tempOrderCheck->option_id        = $productoption->custom_option_id;
                    $tempOrderCheck->option_child_ids = 0;
                    $tempOrderCheck->dates            = $child_option;
                    $tempOrderCheck->save();
                }

                //file
                if (!empty($request->file('file-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->file('file-' . $productid . '-' . $productoption->custom_option_id);
                    $tempOrderCheck = new OrdersTempOption;
                    $tempOrderCheck->product_id       = $productid;
                    $tempOrderCheck->oid              = $oid;
                    $tempOrderCheck->option_id        = $productoption->custom_option_id;
                    $tempOrderCheck->option_child_ids = 0;
                    $tempOrderCheck->attach_file      = self::uploadfiles($child_option);
                    $tempOrderCheck->save();
                }
            }
        }
    }

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

                if (!empty($productoption->is_required) && $productoption->option_type == 'file' && empty($request->file('file-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
                    return 1;
                }
                if (!empty($productoption->is_required) && $productoption->option_type == 'file' && !empty($request->file('file-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
                    $uploadedFile = $request->file('file-' . $productoption->product_id . '-' . $productoption->custom_option_id);
                    $mimeType     = $uploadedFile->getMimeType();
                    if (!in_array($mimeType, ["image/jpg", "image/jpeg", "image/png", "image/gif"])) {
                        return 1;
                    }
                    return 0;
                }

                if (!empty($productoption->is_required) && $productoption->option_type == 'datetime' && empty($request->input('datetime-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
                    return 1;
                }
                if (!empty($productoption->is_required) && $productoption->option_type == 'textarea' && empty($request->input('textarea-' . $productoption->product_id . '-' . $productoption->custom_option_id))) {
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
            ->groupBy('custom_option_id')
            ->orderBy('custom_option_id', 'ASC')->get();

        if (!empty($productoptions) && count($productoptions) > 0) {
            foreach ($productoptions as $productoption) {
                //option start
                $oidOps = $request->input('option-' . $request->product_id . '-' . $productoption->custom_option_id);
                if (!empty($oidOps)) {
                    $prodOption  = ProductOptions::where('id', $oidOps)->first();
                    if ($prodOption->is_price_add == 1) {
                        $retailPriceOption += $prodOption->getOriginal('retail_price');
                    } else if ($prodOption->is_price_add == 2) {
                        $retailPriceOption -= $prodOption->getOriginal('retail_price');
                    }
                }
                //end option
                //select start
                $oidSel = $request->input('select-' . $request->product_id . '-' . $productoption->custom_option_id);
                if (!empty($oidSel)) {
                    $explodeSelect = explode("-", $oidSel);
                    $prodSelect  = ProductOptions::where('id', $explodeSelect[3])->first();
                    if ($prodSelect->is_price_add == 1) {
                        $retailPriceSelect += $prodSelect->getOriginal('retail_price');
                    } else if ($prodSelect->is_price_add == 2) {
                        $retailPriceSelect -= $prodSelect->getOriginal('retail_price');
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
                $retailPriceCheck += $prodOption->getOriginal('retail_price');
            } else if ($prodOption->is_price_add == 2) {
                $retailPriceCheck -= $prodOption->getOriginal('retail_price');
            }
        }
        return $retailPriceCheck;
    }

    //option details with price - temp
    public static function getOptionsDtails($oid)
    {
        $optionDetailstxt = '';
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $optionDetails = OrdersTempOption::where("oid", $oid)->get();
        if (!empty($optionDetails) && count($optionDetails) > 0) {
            foreach ($optionDetails as $optionDetail) {
                $optionParentDetails = ProductOptionsCustom::where("id", $optionDetail->option_id)->first();
                if (!empty($optionParentDetails->id)) {
                    $option_name = $strLang == "en" ? $optionParentDetails->option_name_en : $optionParentDetails->option_name_ar;
                    if (!empty($optionDetail->attach_file)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(<a href="' . url('uploads/attachcart/' . $optionDetail->attach_file) . '" target="_blank">' . trans('webMessage.attach') . '</a>)</li>';
                    } elseif (!empty($optionDetail->dates)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . $optionDetail->dates . ')</li>';
                    } elseif (!empty($optionDetail->comments)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . $optionDetail->comments . ')</li>';
                    } else {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . self::getChildOptionsDtails($optionDetail->option_child_ids) . ')</li>';
                    }
                }
            }
        }
        return $optionDetailstxt;
    }

    public static function getOptionsDtailsBr($oid)
    {
        $optionDetailstxt = '';
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $optionDetails = OrdersTempOption::where("oid", $oid)->get();
        if (!empty($optionDetails) && count($optionDetails) > 0) {
            foreach ($optionDetails as $optionDetail) {
                $optionParentDetails = ProductOptionsCustom::where("id", $optionDetail->option_id)->first();
                if (!empty($optionParentDetails->id)) {
                    $option_name = $strLang == "en" ? $optionParentDetails->option_name_en : $optionParentDetails->option_name_ar;
                    if (!empty($optionDetail->attach_file)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(<a href="' . url('uploads/attachcart/' . $optionDetail->attach_file) . '" target="_blank">' . trans('webMessage.attach') . '</a>)';
                    } elseif (!empty($optionDetail->dates)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . $optionDetail->dates . ')';
                    } elseif (!empty($optionDetail->comments)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . $optionDetail->comments . ')';
                    } else {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . self::getChildOptionsDtails($optionDetail->option_child_ids) . ')';
                    }
                }
            }
        }
        return $optionDetailstxt;
    }

    public static function getOptionsDtailsOrder($oid)
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
                    if (!empty($optionDetail->attach_file)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(<a href="' . url('uploads/attachcart/' . $optionDetail->attach_file) . '" target="_blank">' . trans('webMessage.attach') . '</a>)</li>';
                    } elseif (!empty($optionDetail->dates)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . $optionDetail->dates . ')</li>';
                    } elseif (!empty($optionDetail->comments)) {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . $optionDetail->comments . ')</li>';
                    } else {
                        $optionDetailstxt .= '<li>' . $option_name . ':(' . self::getChildOptionsDtails($optionDetail->option_child_ids) . ')</li>';
                    }
                }
            }
        }
        return $optionDetailstxt;
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
                    if (!empty($optionDetail->attach_file)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(<a href="' . url('uploads/attachcart/' . $optionDetail->attach_file) . '" target="_blank">' . trans('webMessage.attach') . '</a>)';
                    } elseif (!empty($optionDetail->dates)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . $optionDetail->dates . ')';
                    } elseif (!empty($optionDetail->comments)) {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . $optionDetail->comments . ')';
                    } else {
                        $optionDetailstxt .= '<br>' . $option_name . ':(' . self::getChildOptionsDtails($optionDetail->option_child_ids) . ')';
                    }
                }
            }
        }
        return $optionDetailstxt;
    }

    //get child
    public static function getChildOptionsDtails($ids)
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

    //
    public static function getJoinOptions($id)
    {
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
                $optionName .= ($strLang == "en" ? $option->option_value_name_en : $option->option_value_name_ar) . ',';
            }
        }
        return $optionName;
    }



    ///***********************END DETAILS ADD TO CART *************//
    //load quick view


    public function ajax_quickview(Request $request)
    {
        //check lang
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $productDetails   = Product::where('id', $request->id)->first();
        if (!empty($productDetails->id)) {
            if (!empty($productDetails->image)) {
                $gallerylist = '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/product/' . $productDetails->image) . '" alt="' . $productDetails['title_' . $strLang] . '"></div>';
                $gallerylist_thumb = '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/product/thumb/' . $productDetails->image) . '" alt="' . $productDetails['title_' . $strLang] . '"></div>';
            } else {
                $gallerylist = '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/no-image.png') . '" alt="' . $productDetails['title_' . $strLang] . '"></div>';
                $gallerylist_thumb = '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/no-image.png') . '" alt="' . $productDetails['title_' . $strLang] . '"></div>';
            }
            //get gallery if available
            $galleries = $this->getGalleries($productDetails->id);

            if (!empty($galleries)) {
                foreach ($galleries as $gallery) {
                    $gallerylist .= '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/product/' . $gallery->image) . '" alt="' . $gallery['title_' . $strLang] . '"></div>';
                    $gallerylist_thumb .= '<div><img src="' . url('assets/images/loader.svg') . '" data-src="' . url('uploads/product/thumb/' . $gallery->image) . '" alt="' . $gallery['title_' . $strLang] . '"></div>';
                }
            }
            //get available qty
            $availableQty = $this->AvailableQuantity($productDetails->id);
            if (!empty($productDetails->item_code)) {
                $attributeList = '<li>' . trans('webMessage.item_code') . ' : ' . $productDetails->item_code . '</li>';
            }
            if (!empty($productDetails->sku_no)) {
                $attributeList .= '<li>' . trans('webMessage.sku_no') . ' : ' . $productDetails->sku_no . '</li>';
            }
            if (!empty($availableQty)) {
                $attributeList .= '<li>' . trans('webMessage.availability') . ' : ' . $availableQty . ' <font color="#ff0000">' . trans('webMessage.instock') . '</font></li>';
            } else {
                $attributeList .= '<li>' . trans('webMessage.availability') . ' : <font color="#ff0000">' . trans('webMessage.outofstock') . '</font></li>';
            }

            if (!empty($productDetails->warranty)) {
                $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                if ($strLang == "en") {
                    $wtitle = $warrantyDetails->title_en;
                } else {
                    $wtitle = $warrantyDetails->title_ar;
                }
                $attributeList .= '<li>' . trans('webMessage.warranty') . ' : ' . $wtitle . '</li>';
            }
            //price
            if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
                $price_lists = '<span class="new-price">' . round($productDetails['countdown_price'], 3) . '' . \App\Currency::default() . '</span>';
                if (!empty($productDetails['retail_price'])) {
                    $price_lists .= '&nbsp;&nbsp;<span class="old-price"><small>' . round($productDetails['retail_price'], 3) . '' . \App\Currency::default() . '</small></span>';
                }
            } else {
                $price_lists = '<span class="new-price"> <span id="display_price_' . $productDetails->id . '">' . round($productDetails['retail_price'], 3) . '</span>' . \App\Currency::default() . '</span>';
                if (!empty($productDetails['old_price'])) {
                    $price_lists .= '&nbsp;&nbsp;<span class="old-price" id="oldprices' . $productDetails->id . '"><small> <span id="display_oldprice_' . $productDetails->id . '">' . round($productDetails['old_price'], 3) . '</span>' . \App\Currency::default() . '</small></span>';
                }
            }

            //attributes size & color


            //review & ratings
            $productRatings = self::getProductRatings($productDetails->id);
            $reviewCounts   = ProductReview::where('product_id', $productDetails->id)->get()->count();
            //check qty exist or not

            $message = '
				<div class="tt-modal-quickview desctope">
					<div class="row">
						<div class="col-12 col-md-5 col-lg-6">
							<div class="tt-mobile-product-slider arrow-location-center">
								' . $gallerylist . '
							</div>
							<div class="slider-nav">
							' . $gallerylist_thumb . '
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-6">
							<div class="tt-product-single-info">
								<div class="tt-add-info">
									<ul>' . $attributeList . '</ul>
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-price">' . $price_lists . '</div>
								<div class="tt-review">
									<div class="tt-rating">
										' . $productRatings . '
									</div>
									<a href="javascript:;">(' . $reviewCounts . ' ' . trans('webMessage.customerreview') . ')</a>
								</div>
							    <div class="tt-review">
								' . $productDetails['details_' . $strLang] . '
								</div>
								
							</div>
							<div class="tt-review" align="center"><a class="btn btn-lg" href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . trans('webMessage.details') . '</a></div>
							
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="unit_price_' . $productDetails->id . '" id="unit_price_' . $productDetails->id . '" value="' . $productDetails->retail_price . '">
			';
        } else {
            $message = trans('webMessage.productisnotactive');
        }
        return ["status" => 200, "message" => $message];
    }


    //quick view add to cart
    public function ajax_quickview_addtocart(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        if (empty($request->product_id) || empty($request->price) || empty($request->quantity)) {
            $message = '<div class="alert-danger">' . trans('webMessage.invalid_request_tryagain') . '</div>';
        } else {
            $productDetails   = Product::where('id', $request->product_id)->first();
            $aquantity = self::getProductQuantity($request->product_id, $request->size_attribute, $request->color_attribute);
            if (!empty($request->quantity) && $request->quantity > $aquantity) {
                $message = '<div class="alert-danger">' . trans('webMessage.quantity_is_exceeded') . '</div>';
                return ["status" => 400, "message" => $message];
            }
            //check option exit or not

            if (!empty($request->option_id) && $request->option_id != 'undefined' && empty($request->child_option)) {
                $message = '<div class="alert-danger">' . trans('webMessage.please_choose_your_option') . '</div>';
                return ["status" => 400, "message" => $message];
            }
            $option_id = !empty($request->option_id) ? $request->option_id : '';
            $child_option = !empty($request->child_option) ? $request->child_option : '';

            $session_id = session()->getId();

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

            $price = self::getProductPrice($request->product_id, $request->size_attribute, $request->color_attribute);
            if (empty($price)) {
                $price = $request->price;
            }
            $tempOrder  = OrdersTemp::where($whereClause)->first();
            if (!empty($tempOrder->id)) {
                $tempOrder->quantity = $request->quantity;
                $tempOrder->save();
                $totalAmount = self::getTotalCartAmount();
                $countitems  = self::countTempOrders();
                $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
                //prepare box message
                $tempPrice =  Currency::convertTCountry(($tempOrder->unit_price * $request->quantity));
                $totalShowPrice =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??  ($tempOrder->unit_price * $request->quantity);

                $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
					</div>
			        <a href="' . url('/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url('/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img src="' . url('uploads/product/thumb/' . $productDetails->image) . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>' . $request->quantity . '</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalShowPrice . '' . \App\Currency::default() . '</span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalAmount . '' . \App\Currency::default() . '</span>
								</div>
							</a>
							
							<a href="' . url('/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url('/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
                //end
            } else {
                //get option price
                if (!empty($option_id) && !empty($child_option)) {
                    $optionPrice = self::getOptionsPrice($child_option);
                    $price = $price + $optionPrice['optionPrice'];
                }
                //end
                $tempOrder  = new OrdersTemp;
                $tempOrder->product_id = $request->product_id;
                $tempOrder->size_id    = $request->size_attribute;
                $tempOrder->color_id   = $request->color_attribute;
                $tempOrder->quantity   = $request->quantity;
                $tempOrder->unit_price = $price;
                $tempOrder->unique_sid = $session_id;
                $tempOrder->save();
                //add option


                if (!empty($option_id) && !empty($child_option)) {
                    $tempOrderOption = new OrdersTempOption;
                    $tempOrderOption->product_id       = $request->product_id;
                    $tempOrderOption->oid              = $tempOrder->id;
                    $tempOrderOption->option_id        = $option_id;
                    $tempOrderOption->option_child_ids = $child_option;
                    $tempOrderOption->save();
                }

                $totalAmount = self::getTotalCartAmount();
                $countitems  = self::countTempOrders();
                $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
                //prepare box message
                $tempPrice =  Currency::convertTCountry(($price * $request->quantity));
                $totalShowPrice =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??  ($price * $request->quantity);

                $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
					</div>
			        <a href="' . url('/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url('/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img src="' . url('uploads/product/thumb/' . $productDetails->image) . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>' . $request->quantity . '</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price">' . $totalShowPrice . '' . \App\Currency::default() . ' </span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalAmount . '' . \App\Currency::default() . '</span>
								</div>
							</a>
							
							<a href="' . url('/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url('/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
                //end
            }
        }
        return ["status" => 200, "message" => $message];
    }
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
                foreach ($attributes as $attribute)
                    foreach (($productDetails->getQuantity(-1, $attribute->id, null, true)) as $InventoryHave)
                        $quantity = $quantity + $InventoryHave->quantity;
            } else if (!empty($size_id) && empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
                foreach ($attributes as $attribute) {
                    foreach (($productDetails->getQuantity(-1, $attribute->id, null, true)) as $InventoryHave)
                        $quantity = $quantity + $InventoryHave->quantity;
                }
            } else if (empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
                foreach ($attributes as $attribute)
                    foreach (($productDetails->getQuantity(-1, $attribute->id, null, true)) as $InventoryHave)
                        $quantity = $quantity + $InventoryHave->quantity;
            }
            if ($tempOrder != null) {
                if (get_class($tempOrder) == OrdersTemp::class)
                    $tempOrderOptions = OrdersTempOption::where("oid", $tempOrder->id)->get();
                else
                    $tempOrderOptions = OrdersOption::where("oid", $tempOrder->id)->get();
                if (!empty($tempOrderOptions) && count($tempOrderOptions) > 0) {
                    foreach ($tempOrderOptions as $tempOrderOption) {
                        $Option = ProductOptions::find($tempOrderOption->option_child_ids);
                        $Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
                        foreach ($Options as $option)
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



    //get product prices
    public static function getProductPrice($product_id, $size_id = 0, $color_id = 0)
    {
        $price = 0;
        $productDetails   = Product::where('id', $product_id)->first();
        if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
            $price = $productDetails->getOriginal('countdown_price');
        } else {
            if (empty($productDetails['is_attribute'])) {
                $price = $productDetails->getOriginal('retail_price');
            } else {
                if (!empty($size_id) && !empty($color_id)) {
                    $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->first();
                    if (!empty($attributes->id)) {
                        $price = $attributes->getOriginal('retail_price');
                    }
                } else if (!empty($size_id) && empty($color_id)) {
                    $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->first();
                    if (!empty($attributes->id)) {
                        $price = $attributes->getOriginal('retail_price');
                    }
                } else if (empty($size_id) && !empty($color_id)) {
                    $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->first();
                    if (!empty($attributes->id)) {
                        $price = $attributes->getOriginal('retail_price');
                    }
                }
                if ($price == 0)
                    $price = $productDetails->getOriginal('retail_price');
            }
        }
        return $price;
    }
    public static function checkBrandDiscount($brand_id)
    {
        if (!empty($brand_id)) {
            $brand = Brand::where('id', $brand_id)->first();
            $isDisc = $brand->is_discount;
            $discVal = $brand->discount;
            if ($isDisc == '1' && $discVal > 0) {
                return true;
            }
        }
        return false;
    }

    public static function getProductBrandDiscount($brand_id, $productPrice)
    {
        if (!empty($brand_id)) {
            $brand = Brand::where('id', $brand_id)->first();
            $isDisc = $brand->is_discount;
            $discVal = $brand->discount;
            if ($isDisc == 1 && $discVal > 0) {
                $disc = $discVal / 100 * $productPrice;
                $discPrice = $productPrice - $disc;
                return round($discPrice, 3);
            }
            return $productPrice;
        }
        return  $productPrice;
    }


    //add to cart - single
    public function ajax_addtocart_single(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();

        if (empty($request->product_id)) {
            $message = trans('webMessage.invalid_request_tryagain');
            return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
        } else {
            $productDetails   = Product::where('id', $request->product_id)->first();
            $aquantity = self::getProductQuantity($request->product_id, 0, 0);
            if (empty($aquantity)) {
                $message = trans('webMessage.quantity_is_exceeded');
                return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
            }
            $session_id = session()->getId();

            $whereClause[] = ["product_id", "=", $request->product_id];
            $whereClause[] = ["unique_sid", "=", $session_id];

            if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
                $price = round($productDetails->getOriginal('countdown_price'), 3);
            } else {
                $price = $productDetails->getOriginal('retail_price');
                //Applying Brand discount (if not applied,will return old price itself)
                $price = $this->getProductBrandDiscount($productDetails->brand_id, $price);
            }

            $tempOrder  = OrdersTemp::where($whereClause)->first();
            if (!empty($tempOrder->id)) {
                $nqty = $tempOrder->quantity + 1;
                if (!empty($aquantity) && $nqty > $aquantity) {
                    $message = trans('webMessage.quantity_is_exceeded');
                    return ["status" => 400, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
                } else {
                    $newQuantity = ($tempOrder->quantity + 1);
                    $tempOrder->quantity = $newQuantity;
                    $tempOrder->save();
                    $totalAmount = self::getTotalCartAmount();
                    $countitems  = self::countTempOrders();
                    $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
                    //prepare box message
                    $tempPrice =  Currency::convertTCountry(($price * $newQuantity));
                    $totalShowPrice =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??   ($price * $newQuantity);

                    $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
					</div>
			        <a href="' . url(app()->getLocale() . '/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.quantity_is_updated') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img src="' . url('uploads/product/thumb/' . $productDetails->image) . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>' . $newQuantity . '</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalShowPrice . '' . \App\Currency::default() . '</span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalAmount . '' . \App\Currency::default() . '</span>
								</div>
							</a>
							
							<a href="' . url(app()->getLocale() . '/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
                    //end
                    $message  = !empty($settingInfo->is_cart_popup) ? $message : trans('webMessage.quantity_is_updated');
                    return ["status" => 200, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup];
                }
            } else {
                $tempOrder  = new OrdersTemp;
                $tempOrder->product_id = $request->product_id;
                $tempOrder->size_id    = 0;
                $tempOrder->color_id   = 0;
                $tempOrder->quantity   = 1;
                $tempOrder->unit_price = $price;
                $tempOrder->unique_sid = $session_id;
                $tempOrder->save();
                $totalAmount = self::getTotalCartAmount();
                $countitems  = self::countTempOrders();
                $item_text   = str_replace('[QTY]', $countitems, trans('webMessage.item_text_message'));
                //prepare box message
                $tempPrice =  Currency::convertTCountry($price);
                $priceShow =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??   $price;

                $message = '<div class="tt-modal-addtocart mobile">
					<div class="tt-modal-messages">
						<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
					</div>
			        <a href="' . url(app()->getLocale() . '/cart') . '" class="btn-link">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			        <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn-link">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
				</div>
				<div class="tt-modal-addtocart desctope">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="tt-modal-messages">
								<i class="icon-f-68"></i> ' . trans('webMessage.item_is_added') . '
							</div>
							<div class="tt-modal-product">
								<div class="tt-img">
									<img src="' . url('uploads/product/thumb/' . $productDetails->image) . '" alt="">
								</div>
								<h2 class="tt-title"><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '">' . $productDetails['title_' . $strLang] . '</a></h2>
								<div class="tt-qty">
									' . trans('webMessage.quantity') . ': <span>1</span>
								</div>
							</div>
							<div class="tt-product-total">
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $priceShow . '' . \App\Currency::default() . '</span>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<a href="javascript:;" class="tt-cart-total">
								' . $item_text . '
								<div class="tt-total">
									' . strtoupper(trans('webMessage.total')) . ': <span class="tt-price"> ' . $totalAmount . '' . \App\Currency::default() . '</span>
								</div>
							</a>
							
							<a href="' . url(app()->getLocale() . '/cart') . '" class="btn btn-border">' . strtoupper(trans('webMessage.viewcart')) . '</a>
			                <a href="' . url(app()->getLocale() . '/checkout') . '" class="btn">' . strtoupper(trans('webMessage.proceedtocheckout')) . '</a>
						</div>
					</div>
				</div>';
                //end
            }
        }

        //refresh discount
        self::ajax_apply_coupon_to_cart_refresh();

        //collect payload
        $categoryNametxt = self::getProductCatName($productDetails->id);
        if (!empty($productDetails->brand_id)) {
            $brandNametxt    = Brand::where("id", $productDetails->brand_id)->first();
            $brandNametxtt = $brandNametxt->title_en ?? 'no brand';
        } else {
            $brandNametxtt = '';
        }

        $dataLayerPayload = [
            "item_name"     => $productDetails->title_en ?? 'no name',
            "item_id"       => $productDetails->id ?? '0',
            "price"         => $price,
            "item_brand"    => $brandNametxtt,
            "item_category" => !empty($categoryNametxt->name_en) ? $categoryNametxt->name_en : '',
            "item_category2" => "",
            "item_category3" => "",
            "item_category4" => "",
            "item_variant"  => "",
            "item_list_name" => "",
            "item_list_id"  => "",
            "index"         => $tempOrder->id,
            "quantity"      => $request->quantity_attr ?? '1',
        ];



        $message  = !empty($settingInfo->is_cart_popup) ? $message : trans('webMessage.item_is_added');
        return ["status" => 200, "message" => $message, "is_cart_popup" => $settingInfo->is_cart_popup, "payload" => json_encode($dataLayerPayload)];
    }

    //get color by size
    public function ajax_quickview_getColor_BySize(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $ColorAttributes = ProductAttribute::where('product_id', $request->product_id)->where('size_id', $request->size_id)->where('quantity', '>', 0)->where('color_id', '!=', 0)->get();
        $color_opt = '';
        if (!empty($ColorAttributes) && count($ColorAttributes) > 0) {
            $color_opt .= '<div class="tt-wrapper">';
            $color_opt .= '<div class="tt-title-options">' . trans('webMessage.texture') . ':</div>';
            $color_opt .= '<ul class="tt-options-swatch options-large">';
            foreach ($ColorAttributes as $ColorAttribute) {
                $colorInfo = $this->colorDetails($ColorAttribute->color_id);
                if (!empty($colorInfo['image'])) {
                    $colorbg = '<span class="swatch-img"><img src="' . url('uploads/color/thumb/' . $colorInfo['image']) . '" alt=""></span>';
                } else {
                    $colorbg = '<div class="swatch-img" style="background-color:' . $colorInfo['color_code'] . ';width:30px;height:30px;border-radius:100%;">&nbsp;</div>';
                }
                $color_opt .= '<li title="' . $colorInfo['title_' . $strLang] . ':' . $ColorAttribute->quantity . '"><div style="width:30px;height:30px;" for="color_attribute' . $ColorAttribute->id . '"><input type="radio" name="color_attribute_' . $request->product_id . '" id="color_attribute_' . $request->product_id . '_' . $ColorAttribute->id . '" style="position:absolute;margin-left:10px;margin-top:8px;" value="' . $ColorAttribute->color_id . '" class="color_attribute">' . $colorbg . '<span class="swatch-label color-black"></span></div></li>';
            }
            $color_opt .= '</ul>';
            $color_opt .= '</div>';
        }
        return ["status" => 200, "message" => $color_opt];
    }
    //get price by size
    public function ajax_quickview_getprice_by_size(Request $request)
    {
        $productDetails   = Product::where('id', $request->product_id)->first();
        $Attributes = ProductAttribute::where('product_id', $request->product_id)->where('size_id', $request->size_id)->first();
        if (!empty($Attributes['retail_price'])) {
            $price     = $Attributes['retail_price'];
            $old_price = $Attributes['old_price'] <> 0 ? $Attributes['old_price'] : '0';
        } else {
            $price     = $productDetails['retail_price'];
            $old_price = $productDetails['old_price'] <> 0 ? $productDetails['old_price'] : '0';
        }
        return ["status" => 200, "message" => $price, "old_price" => $old_price];
    }
    //get price by color
    public function ajax_quickview_getprice_by_color(Request $request)
    {
        $productDetails   = Product::where('id', $request->product_id)->first();
        $Attributes = ProductAttribute::where('product_id', $request->product_id)->where('color_id', $request->color_id)->first();
        if (!empty($Attributes['retail_price'])) {
            $price     = $Attributes['retail_price'];
            $old_price = $Attributes['old_price'] <> 0 ? $Attributes['old_price'] : '0';
        } else {
            $price     = $productDetails['retail_price'];
            $old_price = $productDetails['old_price'] <> 0 ? $productDetails['old_price'] : '0';
        }
        return ["status" => 200, "message" => $price, "otherPrice" => \App\Currency::table($price), "old_price" => $old_price];
    }
    //get attribute size
    public static function getSizeByCustomIdProductId($custom_option_id, $product_id)
    {

        $Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $product_id)->where('gwc_products_attribute.custom_option_id', $custom_option_id);
        $Attributes = $Attributes->select(
            'gwc_sizes.*',
            'gwc_products_attribute.size_id',
            'gwc_products_attribute.product_id',
            'gwc_products_attribute.custom_option_id'
        );
        $Attributes = $Attributes->join("gwc_sizes", "gwc_sizes.id", "=", "gwc_products_attribute.size_id");
        $Attributes = $Attributes->join("gwc_products_quantity", function ($join) use ($product_id) {
            $join->where("gwc_products_quantity.product_id", "=", $product_id)
                ->on("gwc_products_quantity.attribute_id", "=", "gwc_products_attribute.id");
        });
        $Attributes = $Attributes->where('gwc_products_attribute.size_id', '!=', 0)
            ->where('gwc_products_quantity.quantity', '>', 0)
            ->groupBy('gwc_products_attribute.size_id')
            ->get();
        return $Attributes;
    }

    //get color attribute
    public static function getColorByCustomIdProductId($custom_option_id, $product_id)
    {

        $Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $product_id)->where('gwc_products_attribute.custom_option_id', $custom_option_id);
        $Attributes = $Attributes->select(
            'gwc_colors.*',
            'gwc_products_attribute.color_id',
            'gwc_products_attribute.product_id',
            'gwc_products_attribute.custom_option_id'
        );
        $Attributes = $Attributes->join("gwc_colors", "gwc_colors.id", "=", "gwc_products_attribute.color_id");
        $Attributes = $Attributes->join("gwc_products_quantity", function ($join) use ($product_id) {
            $join->where("gwc_products_quantity.product_id", "=", $product_id)
                ->on("gwc_products_quantity.attribute_id", "=", "gwc_products_attribute.id");
        });
        $Attributes = $Attributes->where('gwc_products_attribute.color_id', '!=', 0)
            ->where('gwc_products_quantity.quantity', '>', 0)
            ->groupBy('gwc_products_attribute.color_id')
            ->get();
        return $Attributes;
    }



    //get color image by color/prodid
    public function ajax_get_color_image(Request $request)
    {
        if (empty($request->product_id) || empty($request->color_id)) {
            return ["status" => 400, "message" => trans('webMessage.idmissing')];
        }
        $imagePath = url('uploads/no-image.png');
        $productDetails = Product::where('id', $request->product_id)->first();
        if (!empty($productDetails->image)) {
            $imagePath = url('uploads/product/' . $productDetails->image);
        }
        $Attributes     = ProductAttribute::where('product_id', $request->product_id);
        if (!empty($request->size_id)) {
            $Attributes     = $Attributes->where('size_id', $request->size_id);
        }
        $Attributes     = $Attributes->where('color_id', $request->color_id);
        $AllAttributes     = $Attributes->get();
        $Attributes     = $Attributes->first();
        if (!empty($Attributes->color_image)) {
            $imagePath = url('uploads/color/' . $Attributes->color_image);
        }

        //get price
        if (!empty($Attributes['retail_price'])) {
            $price     = $Attributes['retail_price'];
            $old_price = $Attributes['old_price'] <> 0 ? $Attributes['old_price'] : '0';
        } else {
            $price     = $productDetails['retail_price'];
            $old_price = $productDetails['old_price'] <> 0 ? $productDetails['old_price'] : '0';
        }

        //countdown price
        if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
            $old_price = round($price, 3);
            $price     = round($productDetails->countdown_price, 3);
        }

        Cookie::queue("unit_price", $price, 3600);
        //remove all option from cookies
        self::removeAllCookiesOptions($request->product_id);
        $quantity = 0;
        foreach ($AllAttributes as $allAttribute) {
            $tempQ = $productDetails->getQuantity(-1, $allAttribute->id);
            if ($tempQ != null)
                $quantity += $tempQ->quantity;
        }

        $colorObject = Color::find($request->color_id);
        $colorDetails = $colorObject == null ? null : $colorObject['details_' . (app()->getLocale())];
        return ["status" => 200, "details" => $colorDetails, "message" => $imagePath, "price" => $price, "otherPrice" => \App\Currency::table($price), "old_price" => $old_price, "quantity" => $quantity];
    }

    //check product has qty or not
    public function AvailableQuantity($product_id)
    {
        $qty = 0;
        $productDetails   = Product::where('id', $product_id)->first();
        //		if (empty($productDetails['is_attribute'])) {
        $qty   = $productDetails['quantity'];
        //		} else {
        //			$qty     = ProductAttribute::where('product_id', $product_id)->get()->sum('quantity');
        //			$optyQty = ProductOptions::where('product_id', $product_id)->get()->sum('quantity'); //option
        //			$qty = $qty + $optyQty;
        //			//save qty
        //			$productDetails->quantity = $qty;
        //			$productDetails->save();
        //		}

        return $qty;
    }
    public static function IsAvailableQuantity($product_id)
    {
        $qty = 0;
        $productDetails   = Product::where('id', $product_id)->first();
        try {
            //		if (empty($productDetails['is_attribute'])) {
            $qty   = $productDetails['quantity'];
            //		} else {
            //			$qty     = ProductAttribute::where('product_id', $product_id)->get()->sum('quantity');
            //			$optyQty = ProductOptions::where('product_id', $product_id)->get()->sum('quantity'); //option
            //			$qty = $qty + $optyQty;
            //			//save qty
            //			$productDetails->quantity = $qty;
            //			$productDetails->save();
            //		}
        } catch (\Exception $e) {
            dd('IsAvailableQuantity', $product_id, $productDetails, $e->getMessage());
        }
        return $qty;
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

    public static function getColorImage($productid, $colorid)
    {
        $Attributes     = ProductAttribute::where('product_id', $productid)->where('color_id', $colorid)->first();
        return $Attributes;
    }

    //auto laod count
    //count temp order
    public function countTempOrdersAjax()
    {
        $session_id = session()->getId();
        $tempOrders = OrdersTemp::where('unique_sid', $session_id)->get()->count();
        return ["status" => 200, "message" => $tempOrders];
    }

    public function deleteTempOrdersAjax(Request $request)
    {
        $tempOrders = OrdersTemp::find($request->id);
        $productDetails = Product::find($tempOrders->product_id);
        $price = $tempOrders->unit_price ?? '0';
        $quantity = $tempOrders->quantity ?? '0';

        if (!empty($tempOrders->id)) {
            $optionsboxs = OrdersTempOption::where("oid", $request->id)->get();
            if (!empty($optionsboxs) && count($optionsboxs) > 0) {
                foreach ($optionsboxs as $optionsbox) {
                    $tempOrdersOption = OrdersTempOption::find($optionsbox->id);
                    $tempOrdersOption->delete();
                }
            }
            $tempOrders->delete();
            //delete discount
            $tempOrdersDiscount = self::loadTempOrders();
            if (empty($tempOrdersDiscount) || count($tempOrdersDiscount) == 0) {
                //remove coupon
                Cookie::queue('gb_coupon_code', 0, 0);
                Cookie::queue('gb_coupon_discount', '', 0);
                Cookie::queue('gb_coupon_discount_text', '', 0);
                Cookie::queue('gb_coupon_free', 0, 0);
                //remove area
                Cookie::queue('country', '', 0);
                Cookie::queue('state', '', 0);
                Cookie::queue('area', '', 0);
                Cookie::queue('area_id', '', 0);
            }

            //refresh discount
            self::ajax_apply_coupon_to_cart_refresh();

            //collect payload

            $categoryNametxt = self::getProductCatName($productDetails->id);
            if (!empty($productDetails->brand_id)) {
                $brandNametxt    = Brand::where("id", $productDetails->brand_id)->first();
                $brandNametxtt = $brandNametxt->title_en ?? 'no brand';
            } else {
                $brandNametxtt = '';
            }
            $dataLayerPayload = [];
            if (!empty($productDetails->id)) {
                $dataLayerPayload = [
                    "item_name"     => $productDetails->title_en ?? 'no name',
                    "item_id"       => $productDetails->id ?? '0',
                    "price"         => $price,
                    "item_brand"    => $brandNametxtt,
                    "item_category" => $categoryNametxt->name_en,
                    "item_category2" => "",
                    "item_category3" => "",
                    "item_category4" => "",
                    "item_variant"  => "",
                    "item_list_name" => "",
                    "item_list_id"  => "",
                    "index"         => "1",
                    "quantity"      => $quantity ?? '1',
                ];
            }

            return ["status" => 200, "message" => trans('webMessage.itemisremovedsuccess'), "payload" => json_encode($dataLayerPayload)];
        } else {
            return ["status" => 400, "message" => trans('webMessage.yourcartisempty')];
        }
    }

    //auto reload temp order by using ajax
    public static function ajax_reload_temp_order_box()
    {
        if (app()->getLocale() == 'en') {
            $strLang = "en";
        } else {
            $strLang = "ar";
        }
        $tempOrdersCount = self::countTempOrders();
        $tempOrders = self::loadTempOrders();

        $temp = '<div class="tt-dropdown-inner" id="TempOrderBoxDiv">';
        $temp .= '<div class="tt-cart-layout">';
        if (empty($tempOrders) || count($tempOrders) == 0) {
            //remove coupon
            Cookie::queue('gb_coupon_code', 0, 0);
            Cookie::queue('gb_coupon_discount', '', 0);
            Cookie::queue('gb_coupon_discount_text', '', 0);
            Cookie::queue('gb_coupon_free', 0, 0);
            //remove area
            Cookie::queue('country', '', 0);
            Cookie::queue('state', '', 0);
            Cookie::queue('area', '', 0);
            Cookie::queue('area_id', '', 0);
            $temp .= '<a href="javascript:;" class="tt-cart-empty"><i class="icon-f-39"></i><p>' . trans('webMessage.yourcartisempty') . '</p></a>';
        } else {
            $temp .= '<div class="tt-cart-content">';
            $temp .= '<div class="tt-cart-list">';
            $subTotalAmount = 0;
            $grandTotalAmount = 0;
            $attrtxt = '';
            $t = 1;
            foreach ($tempOrders as $tempOrder) {
                $prodDetails = self::getProductDetails($tempOrder->product_id);
                if ($prodDetails->image) {
                    $imgurl = url('uploads/product/thumb/' . $prodDetails->image);
                } else {
                    $imgurl = url('uploads/no-image.png');
                }
                if (!empty($tempOrder->size_id)) {
                    $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                    $attrtxt .= '<li>' . trans('webMessage.size') . ': ' . $sizeName . '</li>';
                }
                if (!empty($tempOrder->color_id)) {
                    $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                    $attrtxt .= '<li>' . trans('webMessage.color') . ': ' . $colorName . '</li>';
                    $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                    if (!empty($colorImageDetails->color_image)) {
                        $imgurl = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                    }
                }
                $optionsDetails = self::getOptionsDtails($tempOrder->id);

                $subTotalAmount += ($tempOrder->quantity * $tempOrder->unit_price);

                if ($t > 3) {
                    $hidecart = 'style="display:none;"';
                } else {
                    $hidecart = '';
                }
                $temp .= '<div class="tt-item" ' . $hidecart . '>';
                $temp .= '<a href="' . url(app()->getLocale() . '/details/' . $prodDetails->id . '/' . $prodDetails->slug) . '">';
                $temp .= '<div class="tt-item-img">';

                $temp .= '<img src="' . $imgurl . '" alt="' . $prodDetails['title_' . $strLang] . '"></div>';
                $temp .= '<div class="tt-item-descriptions">';
                $temp .= '<h2 class="tt-title">' . $prodDetails['title_' . $strLang] . '</h2>';
                $temp .= '<ul class="tt-add-info">' . $attrtxt . $optionsDetails . '</ul>';
                $temp .= '<div class="tt-quantity">' . $tempOrder->quantity . ' X</div> <div class="tt-price">' . $tempOrder->unit_price . '' . \App\Currency::default() . ' </div></div>';
                $temp .= '</a>';
                $temp .= '<div class="tt-item-close">';
                $temp .= '<a href="javascript:;" id="' . $tempOrder->id . '" class="tt-btn-close deleteFromTemp"></a>';
                $temp .= '</div>';
                $temp .= '</div>';
                $attrtxt = '';
                $t++;
            }
            if ($t >= 4) {
                $temp .= '<div class="tt-item" align="center"><a href="' . url('/cart') . '" >' . trans('webMessage.viewall') . '(+' . ($t - 4) . ')</a></div>';
            }
            $temp .= '</div>';
            $temp .= '<div class="tt-cart-total-row">';
            $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
            if ($bundleDiscount > 0) {
                $subTotalAmount = $subTotalAmount - $bundleDiscount;

                $tempPrice =  Currency::convertTCountry($bundleDiscount);
                $bundleDiscount =  $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ??  $bundleDiscount;

                $temp .= '<div class="tt-cart-total-title">' . trans('webMessage.bundles.BundleDiscount') . '</div>';
                $temp .= '<div class="tt-cart-total-price" style="color: #FF0000;" id="BundleDiscountJSGet">-' . $bundleDiscount  . \App\Currency::default() . '</div>';
                $temp .= '</div>';
                $temp .= '<div class="tt-cart-total-row" style="    border-top: 0px solid #e9e7e7;margin-top: 0px;padding-top: 10px;">';
            }
            $temp .= '<div class="tt-cart-total-title">' . trans('webMessage.subtotal') . ':</div>';
            $temp .= '<div class="tt-cart-total-price" id="subtotalJSget">' . round($subTotalAmount, 3) . '' . \App\Currency::default() . '</div>';
            $temp .= '</div>';
            $temp .= '<div class="tt-cart-btn">';
            $temp .= '<div class="tt-item">';
            $temp .= '<a href="' . url(app()->getLocale() . '/checkout') . '" class="btn">' . trans('webMessage.checkout') . '</a>';
            $temp .= '</div>';
            $temp .= '<div class="tt-item">';
            $temp .= '<a href="' . url(app()->getLocale() . '/cart') . '" class="btn-link-02 tt-hidden-mobile">' . trans('webMessage.viewcart') . '</a>';
            $temp .= '<a href="' . url(app()->getLocale() . '/cart') . '" class="btn btn-border tt-hidden-desctope">' . trans('webMessage.viewcart') . '</a>';
            $temp .= '</div>';
            $temp .= '</div>';
            $temp .= '</div>';
        }
        $temp .= '</div>';
        $temp .= '</div>';


        return ["status" => 200, "message" => $temp];
    }

    public function directdetails(Request $request)
    {
        $id = $request->id;
        $id  = !empty($id) ? $id : $request->id;
        $slug  = $request->slug;

        // if (!empty($request->lang)) {
        // 	$locale = $request->lang == "en" ? "en" : "ar";
        // 	Session::put('locale', $locale);
        // }

        return redirect(app()->getLocale() . '/details/' . $id . '/' . $slug);
    }
    ///product details
    public function viewProductDetails(Request $request)
    {


        $settingInfo = Settings::where("keyname", "setting")->first();

        $id  = $request->id;

        $productDetails = Product::where("id", $id)->where('is_active', '!=', 0)->first();
        if (empty($productDetails->id)) {
            abort(404);
        }
        //remove all options from cookies
        self::removeAllCookiesOptions($id);
        //get gallery
        $prodGalleries   = $this->getGalleries($id);
        //quantity
        $availableQty    = $this->AvailableQuantity($id);
        //list reviews
        $ReviewsLists    = ProductReview::where('is_active', 1)->where('product_id', $id)->orderBy('created_at', 'DESC')->get();

        $newCount = $productDetails->most_visited_count + 1;
        $productDetails->most_visited_count = $newCount;
        $productDetails->save();
        //get product options

        $productoptions = ProductOptionsCustomChosen::with('mainoption')->where('product_id', $id)->orderBy('custom_option_id', 'ASC')->groupBy('custom_option_id')->get();
        //brand
        $brandDetails = [];
        if (!empty($productDetails->brand_id)) {
            $brandDetails = Brand::where("id", $productDetails->brand_id)->first();
        }

        ///get related product
        $relatedProducts = self::getRelatedItems($productDetails->id);


        $tagsDetails = '';
        if (!empty($productDetails->tags_en)) {
            $tagsDetails = self::getTagsName($productDetails->tags_en, $productDetails->tags_ar);
        }

        if ($settingInfo->theme == 9) {

            return view('website.details.details9', compact("productDetails", "prodGalleries", "availableQty", "ReviewsLists", "productoptions", "brandDetails", "relatedProducts", "tagsDetails"));
        } else {

            return view('website.details.details1', compact("productDetails", "prodGalleries", "availableQty", "ReviewsLists", "productoptions", "brandDetails", "relatedProducts", "tagsDetails"));
        }
    }

    //remove all cookies option
    public static function removeAllCookiesOptions($id)
    {
        //remove cookies
        Cookie::queue("unit_price", 0, 0);
        $customOptionChildsOthers  = ProductOptions::where('product_id', $id)->get();
        //radio
        if (!empty($customOptionChildsOthers) && count($customOptionChildsOthers) > 0) {
            foreach ($customOptionChildsOthers as $customOptionChildsOther) {
                $radioCookie = "option-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id;
                $radioCookies = "option-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookie))) {
                    Cookie::queue($radioCookie, 0, 0);
                }
                if (!empty(Cookie::get($radioCookies))) {
                    Cookie::queue($radioCookies, 0, 0);
                }
                //select
                $radioCookie = "select-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id;
                $radioCookies = "select-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookie))) {
                    Cookie::queue($radioCookie, 0, 0);
                }
                if (!empty(Cookie::get($radioCookies))) {
                    Cookie::queue($radioCookies, 0, 0);
                }
                //check box
                $radioCookiec = "checkbox-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id;
                $radioCookiesc = "checkbox-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookiec))) {
                    Cookie::queue($radioCookiec, 0, 0);
                }
                if (!empty(Cookie::get($radioCookiesc))) {
                    Cookie::queue($radioCookiesc, 0, 0);
                }
            }
        }
    }

    //get Size A

    public static function getChildCatName($id)
    {
        $txt = '';
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $ProdCat = Categories::where("id", $id)->first();
        if (!empty($ProdCat->parent_id)) {
            $txt .= self::getChildCatName($ProdCat->parent_id);
        }
        if (!empty($ProdCat->id)) {
            $txt .= '<li><a href="javascript:;">' . $ProdCat['name_' . $strLang] . '</a></li>';
        }
        return $txt;
    }

    //get categories
    public  static function getCatTreeNameByPid($productid)
    {
        $txt = '';
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $catInfo1 = self::getProductCatName($productid);
        if (!empty($catInfo1->parent_id)) {
            $txt .= self::getChildCatName($catInfo1->parent_id);
        }
        if (!empty($catInfo1->id)) {
            $txt .= '<li><a href="javascript:;">' . $catInfo1['name_' . $strLang] . '</a></li>';
        }
        return $txt;
    }

    //get color for details
    public function ajax_details_getColor_BySize(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }


        $Attributes = ProductAttribute::where('gwc_products_attribute.product_id', $request->product_id)
            ->where('gwc_products_attribute.custom_option_id', $request->custom_option_id)
            ->where('gwc_products_attribute.size_id', $request->size_id);
        $Attributes = $Attributes->select(
            'gwc_colors.*',
            'gwc_products_attribute.color_id',
            'gwc_products_attribute.product_id',
            'gwc_products_attribute.custom_option_id'
        );
        $Attributes = $Attributes->join("gwc_products_quantity", function ($join) use ($request) {
            $join->where("gwc_products_quantity.product_id", "=", $request->product_id)
                ->on("gwc_products_quantity.attribute_id", "=", "gwc_products_attribute.id");
        });
        $Attributes = $Attributes->join("gwc_colors", "gwc_colors.id", "=", "gwc_products_attribute.color_id");
        $Attributes = $Attributes->where('gwc_products_attribute.color_id', '!=', 0)
            ->where('gwc_products_quantity.quantity', '>', 0)
            ->groupBy('gwc_products_attribute.color_id')
            ->get();

        $color_opt = '';
        if (!empty($Attributes) && count($Attributes) > 0) {
            $color_opt .= '<div class="tt-wrapper">';
            $color_opt .= '<div class="tt-title-options">' . trans('webMessage.texture') . ':</div>';
            $color_opt .= '<ul  class="tt-options-swatch options-large">';
            foreach ($Attributes as $ColorAttribute) {
                if ($ColorAttribute->color_code) {
                    $colorcode = $ColorAttribute->color_code;
                } else {
                    $colorcode = 'none';
                }

                if (!empty($ColorAttribute->image)) {
                    $color_opt .= '<li id="li-' . $ColorAttribute->color_id . '">';
                    $color_opt .= '<a class="options-color  mx-auto"  href="javascript:;" id="' . $ColorAttribute->color_id . '">';
                    $color_opt .= '<span class="swatch-img">';
                    $color_opt .= '<img src="' . url('uploads/color/thumb/' . $ColorAttribute->image) . '" alt="">';
                    $color_opt .= '</span>';
                    $color_opt .= '<span class="swatch-label color-black"></span>';
                    $color_opt .= '</a>';
                    $color_opt .= '<h6 class="color-label">' . $ColorAttribute['title_' . app()->getLocale()] . '</h6>';
                    $color_opt .= '</li>';
                } else {
                    $color_opt .= '<li id="li-' . $ColorAttribute->color_id . '"><a href="javascript:;" class="options-color  mx-auto" style="background-color:' . $colorcode . ';" id="' . $ColorAttribute->color_id . '" ></a><h6 class="color-label">' . $ColorAttribute['title_' . app()->getLocale()] . '</h6></li>';
                }
            }
            $color_opt .= '</ul>';
            $color_opt .= '</div>
	<script>
	$(".options-color").click(function(){
	//remove all check options							  
	$("input:checked").removeAttr("checked");
	//end remove all checked options
	
	var product_id = $("#product_id").val();
	if($("#size_attr_"+product_id).is(":visible")==true){
	var size_id = $("#size_attr_"+product_id).val();	 
	}else{
	var size_id = "";
	}
	var colorid = $(this).attr("id");
	$("#color_attr").val(colorid);
	var obj = $("#tt-pageContent").find(".tt-options-swatch");
	initSwatch(obj);
	
	$.ajax({
	 type: "GET",
	 url: "' . url('ajax_get_color_image') . '",
	 data: "color_id="+colorid+"&product_id="+product_id+"&size_id="+size_id,
	 dataType: "json",
	 cache: false,
	 processData:false,
	 success: function(msg){
	  if(msg.status==200){
	  $(".zoomWindowContainer").remove();
	  $(".zoomContainer").remove();
	  $("#displayd-"+product_id).attr("src", msg.message);
	  $("#displayd-"+product_id).attr("data-zoom-image", msg.message);
	  $("#displaya-"+product_id).attr("data-image", msg.message);
	  $("#displaym-"+product_id).attr("src", msg.message);
	  $(".zoomWindowContainer div").stop().css("background-image",msg.message);
	  $("#quantity_attr").attr("size", msg.quantity);
      $("#display_qty").html(msg.quantity);
	  $("#display_price").html(msg.price);	
	  $("#unit_price").val(msg.price);
	  if(msg.old_price!="0"){
	  $("#display_oldprice").html(msg.old_price);		 
	  }else{
	  $("#oldprices").hide();	 
	  }
	  
      }else{		 
	  $("#quickresponse").html("<div class=\'alert-danger\'>"+msg.message+"</div>");
	  }
	 },
	 error: function(msg){
	 $("#quickresponse").html("<div class=\'alert-danger\'>Oops! There was something wrong.</div>");	 
	 } 
	 });
	});
	function initSwatch($obj){
        $obj.each(function(){
            var $this = $(this),
                jsChangeImg = $this.hasClass("js-change-img"),
                optionsColorImg = $this.find(".options-color-img");

            $this.on("click", "li", function(e) {
                var $this = $(this);
                $this.addClass("active").siblings().removeClass("active");
                if(jsChangeImg){
                    addImg($this);
                };
                return false;
            });
            if (optionsColorImg.length) {
                addBg(optionsColorImg);
            };
        });
    };
    function addBg(optionsColorImg){
      $(optionsColorImg).each(function() {
            $(this).css({
              "background-image": "url("+$(this).attr("data-src") + ")"
            });
      });
    };
	</script>';
        }
        return ["status" => 200, "message" => $color_opt];
    }




    //add to wish list
    public function ajax_add_to_wish_list(Request $request)
    {
        if (empty(Auth::guard('webs')->user()->id)) {
            $message = trans('webMessage.pleaseloginfirst');
            return ["status" => 400, "message" => $message];
        }
        if (empty($request->product_id)) {
            $message = trans('webMessage.idmissing');
            return ["status" => 400, "message" => $message];
        }
        $wish = CustomersWish::where("product_id", $request->product_id)->where("customer_id", Auth::guard('webs')->user()->id)->first();
        if (!empty($wish->id)) {
            $message = trans('webMessage.itemisalreadyaddedtowish');
            return ["status" => 400, "message" => $message];
        }

        $wish = new CustomersWish;
        $wish->product_id = $request->product_id;
        $wish->customer_id = Auth::guard('webs')->user()->id;
        $wish->save();
        $message = trans('webMessage.itemaddedtowishlist');

        $productDetails = Product::find($request->product_id);

        $categoryNametxt = self::getProductCatName($productDetails->id);
        if (!empty($productDetails->brand_id)) {
            $brandNametxt    = Brand::where("id", $productDetails->brand_id)->first();
            $brandNametxtt = $brandNametxt->title_en ?? 'no brand';
        } else {
            $brandNametxtt = '';
        }
        $dataLayerPayload = [
            "item_name"     => $productDetails->title_en ?? 'no name',
            "item_id"       => $productDetails->id ?? '0',
            "price"         => $productDetails->retail_price ?? '0',
            "item_brand"    => $brandNametxtt,
            "item_category" => $categoryNametxt->name_en,
            "item_category2" => "",
            "item_category3" => "",
            "item_category4" => "",
            "item_variant"  => "",
            "item_list_name" => "",
            "item_list_id"  => "",
            "index"         => $wish->id,
            "quantity"      => $productDetails->quantity ?? '0',
        ];


        return ["status" => 200, "message" => $message, "payload" => json_encode($dataLayerPayload)];
    }


    //
    public static function getProductRatings($product_id)
    {
        $ratings = 0;
        $reviewsCount = ProductReview::where('product_id', $product_id)->get()->count();
        if (!empty($reviewsCount)) {
            $reviewsSum   = ProductReview::where('product_id', $product_id)->get()->sum('ratings');
            $ratings = round(($reviewsSum / $reviewsCount), 1);
        }
        $ratingtxt = self::getRatings($ratings);
        return $ratingtxt;
    }

    //get ratings
    public static function getRatings($ratings)
    {
        $ratingstxt = '<i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';

        if ($ratings >= 5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i>';
        }
        if ($ratings >= 4.5 && $ratings < 5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half"></i>';
        }
        if ($ratings >= 4 && $ratings < 4.5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 3.5 && $ratings < 4) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 3 && $ratings < 3.5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 2.5 && $ratings < 3) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 2 && $ratings < 2.5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 1.5 && $ratings < 2) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star-half"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings >= 1 && $ratings < 1.5) {
            $ratingstxt = '<i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        if ($ratings > 0 && $ratings < 1) {
            $ratingstxt = '<i class="icon-star-half"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i>';
        }
        return $ratingstxt;
    }

    //show autocomplete search
    public static function ajax_product_quick_search(Request $request)
    {

        $settingInfo = Settings::where("keyname", "setting")->first();

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        if (!empty($request->keyname) && strlen($request->keyname) >= 2) {
            $search = $request->keyname;
            $theme    = $settingInfo->theme;
            $products = Product::where('is_active', '!=', 0)->where(function ($sq) use ($search, $theme) {
                if ($theme == 2) {
                    $sq->where('title_en', 'like', '%' . $search . '%')->orwhere('title_ar', 'like', '%' . $search . '%');
                } else {
                    $sq->where('title_en', 'like', '%' . $search . '%')->orwhere('title_ar', 'like', '%' . $search . '%')->orwhere('item_code', 'like', '%' . $search . '%');
                }
            });


            $products = $products->where('is_active', '!=', 0)->limit(6)
                ->orderBy('most_visited_count', 'DESC')
                ->get();


            if (!empty($products) && count($products) > 0) {
                $result = '<ul>';
                $title = '';
                $price = '';
                foreach ($products as $product) {
                    $title = $strLang == "en" ? $product->title_en : $product->title_ar;
                    if (!empty($product->old_price)) {
                        $oldprice = '<span class="old-price price_black">' . $product->old_price . ' ' . \App\Currency::default() . '</span>';
                        $cl      = 'price_red';
                        $price   = $product->retail_price;
                    } else {
                        $oldprice = '';
                        $cl = '';
                        $price   = $product->retail_price;
                    }

                    if (!empty($product->countdown_datetime) && strtotime($product->countdown_datetime) > strtotime(date('Y-m-d'))) {
                        $oldprice  = '<span class="old-price price_black">' . round($product->retail_price, 3) . ' ' . \App\Currency::default() . '</span>';
                        $price     = round($product->countdown_price, 3);
                        $cl      = 'price_red';
                    } else	if (static::checkBrandDiscount($product->brand_id)) {
                        $oldprice  = '<span class="old-price price_black">' . round($product->retail_price, 3) . ' ' . \App\Currency::default() . '</span>';
                        $price     = static::getProductBrandDiscount($product->brand_id, $product->retail_price);
                        $cl      = 'price_red';
                    }

                    if ($product->image) {
                        $imgurl = url('uploads/product/thumb/' . $product->image);
                    } else {
                        $imgurl = url('uploads/no-image.png');
                    }
                    $result .= '<li><a href="' . url(app()->getLocale() . '/details/' . $product->id . '/' . $product->slug) . '"><div class="thumbnail"><img src="' . $imgurl . '" alt=""></div><div class="tt-description"><div class="tt-title">' . $title . '</div><div class="tt-price"><span class="new-price ' . $cl . '">' . $price . ' ' . \App\Currency::default() . '</span>' . $oldprice . '</div></div></a></li>';
                }
                $result .= '</ul>';
                $message = $result;
            } else {
                $message = trans('webMessage.noresultfound');
            }
        } else {
            $message = trans('webMessage.noresultfound');
        }
        return ["status" => 200, "message" => $message];
    }

    ///////////////////////////////////shopping cart////////////////////////////
    public function cartview()
    {
        $userAddress = [];
        if (!empty(Auth::guard('webs')->user()->id)) {
            $userid = Auth::guard('webs')->user()->id;
            $userAddress = CustomersAddress::where('customer_id', $userid)->where('is_default', '1')->first();
            if (!empty($userAddress->area_id)) {
                Cookie::queue('area', $userAddress->area_id, 3600);
            }
        }

        $tempOrders = self::loadTempOrders();
        if (empty($tempOrders) || count($tempOrders) == 0) {
            //remove coupon
            Cookie::queue('gb_coupon_code', 0, 0);
            Cookie::queue('gb_coupon_discount', '', 0);
            Cookie::queue('gb_coupon_discount_text', '', 0);
            Cookie::queue('gb_coupon_free', 0, 0);
            //remove area
            Cookie::queue('country', 0, 0);
            Cookie::queue('state', 0, 0);
            Cookie::queue('area', 0, 0);
            Cookie::queue('area_id', 0, 0);
        }
        return view('website.cart', compact('tempOrders', 'userAddress'));
    }
    //update shopping cart qty
    public static function ajax_change_cart_quantity(Request $request)
    {
        if (empty($request->id)) {
            return ["status" => 400, "message" => trans('webMessage.errorfound')];
        }
        $tempOrders = OrdersTemp::where('id', $request->id)->first();
        $aquantity = self::getProductQuantity($tempOrders->product_id, $tempOrders->size_id, $tempOrders->color_id, $tempOrders);
        if ($request->type == 'a') {
            if (!empty($request->quantity)) {
                $nquantity = $request->quantity;
            } else {
                $nquantity = 1;
            }
            if ($nquantity > $aquantity) {
                return ["status" => 400, "message" => trans('webMessage.quantity_is_exceeded')];
            }
        } else {
            if (!empty($request->quantity) && $request->quantity > 0) {
                $nquantity = $request->quantity;
            } else {
                $nquantity = 1;
            }
        }
        $tempOrders->quantity = $nquantity;
        $tempOrders->save();
        //subtotal
        $subtotal = round(($tempOrders->unit_price * $tempOrders->quantity), 3);
        $subtotal = Currency::convertTCountry($subtotal)->price;
        //total price
        $total = self::getTotalCartAmount();
        return ["status" => 200, "message" => trans('webMessage.quantityupdatedsuccess'), 'subtotal' => $subtotal, 'total' => $total];
    }

    //get shopping cart total price
    public static function getTotalCartAmount()
    {
        $total = 0;
        $tempOrders = self::loadTempOrders();
        if (!empty($tempOrders) && count($tempOrders) > 0) {
            foreach ($tempOrders as $tempOrder) {
                $total += ($tempOrder->quantity * $tempOrder->unit_price);
            }
        }
        return round($total, 3);
    }
    //remove cart items
    public static function ajax_remove_my_cart()
    {
        $tempOrders = self::loadTempOrders();

        if (!empty($tempOrders) && count($tempOrders) > 0) {
            foreach ($tempOrders as $tempOrder) {
                $tempOrder = OrdersTemp::find($tempOrder->id);
                self::removeOptions($tempOrder->id);
                $tempOrder->delete();
            }
        }
        //check record exist or not - delete coupon and delivery charge
        $tempOrders = self::loadTempOrders();
        if (empty($tempOrders) || count($tempOrders) == 0) {
            //remove coupon
            Cookie::queue('gb_coupon_code', 0, 0);
            Cookie::queue('gb_coupon_discount', '', 0);
            Cookie::queue('gb_coupon_discount_text', '', 0);
            Cookie::queue('gb_coupon_free', 0, 0);
            //remove area
            Cookie::queue('country', 0, 0);
            Cookie::queue('state', 0, 0);
            Cookie::queue('area', 0, 0);
            Cookie::queue('area_id', 0, 0);
        }
        //end
        return ["status" => 200, "message" => '<div class="alert-success">' . trans('webMessage.itemsareremovedfromcart') . '</div>'];
    }
    //removed option
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
    //remove item from cart
    public static function ajax_remove_my_cart_item(Request $request)
    {
        $tempOrder = OrdersTemp::find($request->id);
        self::removeOptions($tempOrder->id);
        $tempOrder->delete();
        //total price
        $total = self::getTotalCartAmount();
        //check record exist or not - delete coupon and delivery charge
        $tempOrders = self::loadTempOrders();
        if (empty($tempOrders) || count($tempOrders) == 0) {
            //remove coupon
            Cookie::queue('gb_coupon_code', 0, 0);
            Cookie::queue('gb_coupon_discount', '', 0);
            Cookie::queue('gb_coupon_discount_text', '', 0);
            Cookie::queue('gb_coupon_free', 0, 0);
            //remove area
            Cookie::queue('country', '', 0);
            Cookie::queue('state', '', 0);
            Cookie::queue('area', '', 0);
            Cookie::queue('area_id', 0, 0);
        }
        //end
        //refresh discount
        self::ajax_apply_coupon_to_cart_refresh();

        return ["status" => 200, "message" => trans('webMessage.itemisremovedsuccess'), 'total' => $total];
    }
    //get last shopping link
    public static function getShoppingLink($productid)
    {
        $links = DB::table('gwc_products_category')
            ->select('gwc_categories.*', 'gwc_products_category.category_id', 'gwc_products_category.*')
            ->join('gwc_categories', 'gwc_products_category.category_id', '=', 'gwc_categories.id')
            ->where(['gwc_products_category.product_id' => $productid])->first();
        if (!empty($links->category_id)) {
            $linkstxt = 'products/' . $links->category_id . '/' . $links->friendly_url;
        } else {
            $linkstxt = '';
        }
        return $linkstxt;
    }

    //checkout view
    public function checkout()
    {

        $tempOrders = self::loadTempOrders();
        $userDetailsCheckout = [];
        $address = [];
        if (!empty(Auth::guard('webs')->user()->id) && empty(Auth::guard('webs')->user()->is_seller)) {
            $userDetailsCheckout = Auth::guard('webs')->user();
            $minutes = 3600;
            $address =     CustomersAddress::where('customer_id', Auth::guard('webs')->user()->id)->where('is_default', 1)->first();
            if (!empty(Auth::guard('webs')->user()->name)) {
                Cookie::queue('name', Auth::guard('webs')->user()->name, $minutes);
            }
            if (!empty(Auth::guard('webs')->user()->email)) {
                Cookie::queue('email', Auth::guard('webs')->user()->email, $minutes);
            }
            if (!empty(Auth::guard('webs')->user()->mobile)) {
                Cookie::queue('mobile', Auth::guard('webs')->user()->mobile, $minutes);
            }

            if (!empty($address->area_id)) {
                Cookie::queue('area', $address->area_id, $minutes);
            }
            if (!empty($address->block)) {
                Cookie::queue('block', $address->block, $minutes);
            }
            if (!empty($address->street)) {
                Cookie::queue('street', $address->street, $minutes);
            }
            if (!empty($address->avenue)) {
                Cookie::queue('avenue', $address->avenue, $minutes);
            }
            if (!empty($address->house)) {
                Cookie::queue('house', $address->house, $minutes);
            }
            if (!empty($address->floor)) {
                Cookie::queue('floor', $address->floor, $minutes);
            }
        }
        return view('website.checkout', compact('tempOrders', 'userDetailsCheckout', 'address'));
    }

    //get confirm view
    public function confirm()
    {
        $tempOrders = self::loadTempOrders();
        if (empty($tempOrders) || count($tempOrders) == 0) {
            return redirect(app()->getLocale() . '/cart');
        }
        if (empty(Cookie::get('is_checkout'))) {
            return redirect(app()->getLocale() . '/checkout');
        }
        return view('website.confirm', compact('tempOrders'));
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
    public static function OrderSidNumber()
    {
        $orderInfo = OrdersDetails::orderBy("id", "desc")->first();
        if (!empty($orderInfo->id)) {
            $lastProdId = ($orderInfo->id + 1);
        } else {
            $lastProdId = 1;
        }
        return $lastProdId;
    }
    //change qty from option
    public static function changeOptionQuantity($product_id, $mode, $ids, $quantity, $inventories = null)
    {
        if ($inventories != null) {
            $inventories = json_decode($inventories, true);
        }
        $inventoryUse = [];
        if (!empty($ids)) {
            $explodechildids = explode(",", $ids);
            $productDetails = Product::find($product_id);
            for ($i = 0; $i < count($explodechildids); $i++) {
                $Option = ProductOptions::find($explodechildids[$i]);
                $Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
                $tempQuantity = $quantity;
                if ($mode == "d") {
                    $productQuantities = $productDetails->getQuantity(-1, null, $Options->pluck('id')->toArray(), true);
                    foreach ($productQuantities as $productQuantity) {
                        if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                            if ($tempQuantity > $productQuantity->quantity) {
                                $tempQuantity = $tempQuantity - $productQuantity->quantity;
                                $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
                                $productQuantity->quantity = 0;
                                $productQuantity->save();
                            } else {
                                $productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
                                $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
                                $productQuantity->save();
                                break;
                            }
                        }
                    }
                } elseif ($inventories != null) {
                    foreach ($inventories as $inventory) {
                        $productQuantities = $productDetails->getQuantity($inventory['IID'], null, $explodechildids[$i], true);
                        foreach ($productQuantities as $productQuantity) {
                            if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                                $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                                $productQuantity->save();
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $inventoryUse;
    }
    //check qty exist or not while order confirmation
    public static function isQuantityExistForOrder()
    {
        $flag = 0;
        $tempOrders = self::loadTempOrders();
        if (!empty($tempOrders) && count($tempOrders) > 0) {
            foreach ($tempOrders as $tempOrder) {
                $existQty = self::getProductQuantity($tempOrder->product_id, $tempOrder->size_id, $tempOrder->color_id, $tempOrder);
                if ($existQty >= $tempOrder->quantity) {
                    $flag = 1;
                }
            }
        }
        return $flag;
    }

    //update track seen status
    public static function updateSeendStatus($trackid)
    {
        $track = OrdersTrack::where("id", $trackid)->first();
        $track->is_seen = 1;
        $track->save();
        return true;
    }
    //save order
    public function saveconfirm(Request $request)
    {
        if (!empty(Auth::guard('webs')->user()->is_seller)) {
            $userType = 1;
        } else {
            $userType = 0;
        }

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();

        $customer_id = 0;

        $tempOrders = self::loadTempOrders(false);
        if (empty($tempOrders) || count($tempOrders) == 0) {
            return redirect(app()->getLocale() . '/cart');
        }
        //check quantity exiot or not
        $tempQuantityExist = self::isQuantityExistForOrder();
        if (empty($tempQuantityExist)) {
            return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.oneoftheitemqtyexceeded'));
        }

        //check min order amount
        $totalAmtchk = self::getTotalCartAmount();

        if (!empty($settingInfo->min_order_amount) && !empty($totalAmtchk) && $settingInfo->min_order_amount >  $totalAmtchk && empty($userType)) {

            return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.minimumordermessage') . ' ' . number_format($settingInfo->min_order_amount, 3) . ' ' . \App\Currency::default());
        }

        $checkName = $settingInfo->validate_cust_name == 1 ? 'required' : 'nullable';
        $checkMob = $settingInfo->validate_cust_mob == 1 ? 'required' : 'nullable';
        $checkArea = $settingInfo->validate_cust_area == 1 ? 'required' : 'nullable';
        $checkBlock = $settingInfo->validate_cust_block == 1 ? 'required' : 'nullable';
        $checkSt = $settingInfo->validate_cust_st == 1 ? 'required' : 'nullable';
        $checkHno = $settingInfo->validate_cust_hno == 1 ? 'required' : 'nullable';


        if (!empty(Auth::guard('webs')->user()->id) && !empty(Auth::guard('webs')->user()->is_seller)) {

            $validator = Validator::make(
                $request->all(),
                [
                    'name'    => [$checkName, 'string', 'min:4', 'max:190', new Name],
                    'email'   => 'nullable|email|min:3|max:150|string',
                    'mobile'  => [$checkMob, new Mobile],
                    'area'    => $checkArea . '|numeric|gte:0',
                    'block'   => $checkBlock,
                    'street'  => $checkSt,
                    'house'   => $checkHno,
                    'payment_method' => 'required'
                ],
                [
                    'name.required'    => trans('webMessage.name_required'),
                    'name.min'         => trans('webMessage.min_name_chars_required'),
                    'name.max'         => trans('webMessage.max_name_chars_required'),
                    'email.required'   => trans('webMessage.email_required'),
                    'email.string'     => trans('webMessage.string_chars_required'),
                    'mobile.required'  => trans('webMessage.mobile_required'),
                    'mobile.digits'    => trans('webMessage.mobile_digits_required'),
                    'mobile.numeric'   => trans('webMessage.mobile_numeric_required'),
                    'country.required' => trans('webMessage.country_required'),
                    'state.required'   => trans('webMessage.state_required'),
                    'area.required'    => trans('webMessage.area_required'),
                    'country.numeric'  => trans('webMessage.country_required'),
                    'state.numeric'    => trans('webMessage.state_required'),
                    'area.numeric'     => trans('webMessage.area_required'),
                    'country.gt'       => trans('webMessage.country_required'),
                    'state.gt'         => trans('webMessage.state_required'),
                    'area.gt'          => trans('webMessage.area_required'),
                    'block.required'   => trans('webMessage.block_required'),
                    'street.required'  => trans('webMessage.street_required'),
                    'house.required'   => trans('webMessage.house_required'),
                    'payment_method.required' => trans('webMessage.payment_method_required'),
                ]
            );
            if ($validator->fails()) {
                return redirect($strLang . '/checkout')
                    ->withErrors($validator)
                    ->withInput();
            }

            $isValidMobile = Common::checkMobile($request->mobile);
            if (!empty($request->mobile) && empty($isValidMobile)) {
                return redirect($strLang . '/checkout')
                    ->withErrors(['mobile' => trans('webMessage.mobile_invalid')])
                    ->withInput();
            }
        } else {


            $validator = Validator::make(
                $request->all(),
                [
                    'name'    => [$checkName, 'string', 'min:4', 'max:190', new Name],
                    'email'   => 'nullable|email|min:3|max:150|string',
                    'mobile'  => [$checkMob, new Mobile],
                    'area'    => $checkArea . '|numeric|gte:0',
                    'block'   => $checkBlock,
                    'street'  => $checkSt,
                    'house'   => $checkHno,
                    'payment_method' => 'required'
                ],
                [
                    'name.required'    => trans('webMessage.name_required'),
                    'name.min'         => trans('webMessage.min_name_chars_required'),
                    'name.max'         => trans('webMessage.max_name_chars_required'),
                    'email.required'   => trans('webMessage.email_required'),
                    'email.string'     => trans('webMessage.string_chars_required'),
                    'mobile.required'  => trans('webMessage.mobile_required'),
                    'mobile.digits'    => trans('webMessage.mobile_digits_required'),
                    'mobile.numeric'   => trans('webMessage.mobile_numeric_required'),
                    'country.required' => trans('webMessage.country_required'),
                    'state.required'   => trans('webMessage.state_required'),
                    'area.required'    => trans('webMessage.area_required'),
                    'country.numeric'  => trans('webMessage.country_required'),
                    'state.numeric'    => trans('webMessage.state_required'),
                    'area.numeric'     => trans('webMessage.area_required'),
                    'country.gt'       => trans('webMessage.country_required'),
                    'state.gt'         => trans('webMessage.state_required'),
                    'area.gt'          => trans('webMessage.area_required'),
                    'block.required'   => trans('webMessage.block_required'),
                    'street.required'  => trans('webMessage.street_required'),
                    'house.required'   => trans('webMessage.house_required'),
                    'payment_method.required' => trans('webMessage.payment_method_required'),
                ]
            );
            if ($validator->fails()) {
                return redirect($strLang . '/checkout')
                    ->withErrors($validator)
                    ->withInput();
            }

            $isValidMobile = Common::checkMobile($request->mobile);
            if (!empty($request->mobile) && empty($isValidMobile)) {
                return redirect($strLang . '/checkout')
                    ->withErrors(['mobile' => trans('webMessage.mobile_invalid')])
                    ->withInput();
            }


            ///check if register is true
            if (!empty($request->register_me)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'email'        => 'nullable|email|min:3|max:150|string|unique:gwc_customers,email',
                        'mobile'       => 'required|digits_between:' . config('MOBILE_VALIDATION_MIN', 3) . ',' . config('MOBILE_VALIDATION_MAX', 10) . '|unique:gwc_customers,mobile',
                        'username'     => 'required|min:3|max:20|string|unique:gwc_customers,username',
                        'password'     => 'required|min:3|max:150|string',
                    ],
                    [
                        'email.required' => trans('webMessage.email_required'),
                        'email.min' => trans('webMessage.min_name_chars_required'),
                        'email.max' => trans('webMessage.max_name_chars_required'),
                        'email.string' => trans('webMessage.string_chars_required'),
                        'email.unique' => trans('webMessage.email_unique_required'),
                        'mobile.required' => trans('webMessage.mobile_required'),
                        'mobile.min' => trans('webMessage.min_name_chars_required'),
                        'mobile.max' => trans('webMessage.mobile_max_name_chars_required'),
                        'mobile.string' => trans('webMessage.string_chars_required'),
                        'mobile.unique' => trans('webMessage.mobile_unique_required'),
                        'username.required' => trans('webMessage.username_required'),
                        'username.min' => trans('webMessage.min_name_chars_required'),
                        'username.max' => trans('webMessage.mobile_max_name_chars_required'),
                        'username.string' => trans('webMessage.string_chars_required'),
                        'username.unique' => trans('webMessage.username_unique_required'),
                        'password.required' => trans('webMessage.password_required'),
                        'password.min' => trans('webMessage.min_name_chars_required'),
                        'password.max' => trans('webMessage.max_name_chars_required'),
                        'password.string' => trans('webMessage.string_chars_required'),
                    ]
                );
                if ($validator->fails()) {
                    return redirect($strLang . '/checkout')
                        ->withErrors($validator)
                        ->withInput();
                }

                $customers = new User;
                $customers->name    = !empty($request->input('name')) ? $request->input('name') : 'Guest';
                $customers->email   = $request->input('email');
                $customers->mobile  = $request->input('mobile');
                $customers->username = $request->input('username');
                $customers->password = bcrypt($request->input('password'));
                $customers->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '1';
                $customers->register_from = "web";
                $customers->register_ip   = $_SERVER['REMOTE_ADDR'];
                $customers->save();
                $customer_id = $customers->id;
                //save address
                $custaddress = new CustomersAddress;
                $custaddress->customer_id = $customers->id;
                $custaddress->title = 'My Address';
                $custaddress->country_id = self::get_country_of_area($request->input('area'));;
                $custaddress->state_id = self::state($request->input('area'));
                $custaddress->area_id = $request->input('area');
                $custaddress->block = $request->input('block');
                $custaddress->street = $request->input('street');
                $custaddress->avenue = $request->input('avenue');
                $custaddress->house = $request->input('house');
                $custaddress->floor = $request->input('floor');
                $custaddress->is_default = 1;
                $custaddress->save();

                //send email notification
                if (!empty($request->email)) {
                    $appendMessage  = "<b>" . trans('webMessage.username') . " : </b>" . $request->input('username');
                    $appendMessage .= "<br><b>" . trans('webMessage.password') . " : </b>" . $request->input('password');
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
            }
        } //else part for seller

        $orderid  = strtolower($settingInfo->prefix) . $this->OrderserialNumber();
        $ordersid = $this->OrderSidNumber();
        $orderDetails = new OrdersDetails;
        $uid = 0;
        if (!empty(Auth::guard('webs')->user()->id)) {
            $orderDetails->customer_id = Auth::guard('webs')->user()->id;
            $uid = Auth::guard('webs')->user()->id;
        } else if (!empty($customer_id)) {
            $orderDetails->customer_id = $customer_id;
            $uid = $customer_id;
        }
        //default lat/long
        $latitude = '';
        $longitude = '';
        if (!empty($request->area)) {
            $AreaLatLong   = self::get_csa_info($request->area);
            $latitude   = !empty($AreaLatLong->latitude) ? $AreaLatLong->latitude : '';
            $longitude  = !empty($AreaLatLong->longitude) ? $AreaLatLong->longitude : '';
        }
        //store in session
        Session::put('orderid', md5($orderid));

        $orderDetails->order_id     = $orderid;
        $orderDetails->sid          = $ordersid;
        $orderDetails->order_id_md5 = md5($orderid);
        $orderDetails->latitude     = !empty(Cookie::get('latitude')) ? Cookie::get('latitude') : $latitude;
        $orderDetails->longitude    = !empty(Cookie::get('longitude')) ? Cookie::get('longitude') : $longitude;
        $orderDetails->is_express_delivery  =  Cookie::get('is_express_delivery', 0);
        $orderDetails->name         = !empty($request->name) ? $request->name : 'Guest';
        $orderDetails->email        = !empty($request->email) ? $request->email : '';
        $orderDetails->mobile       = !empty($request->mobile) ? $request->mobile : '';
        $orderDetails->country_id   = self::get_country_of_area($request->input('area'));
        $orderDetails->state_id     = self::state($request->input('area'));
        $orderDetails->area_id      = !empty($request->area) ? $request->area : '0';
        $orderDetails->block        = !empty($request->block) ? $request->block : '';
        $orderDetails->street       = !empty($request->street) ? $request->street : '';
        $orderDetails->avenue       = !empty($request->avenue) ? $request->avenue : '';
        $orderDetails->house        = !empty($request->house) ? $request->house : '';
        $orderDetails->floor        = !empty($request->floor) ? $request->floor : '';
        $orderDetails->landmark     = !empty($request->landmark) ? $request->landmark : '';
        $orderDetails->device_type  = 'web';
        $orderDetails->order_ip     = $_SERVER['REMOTE_ADDR'];
        $orderDetails->pay_mode     = !empty($request->payment_method) ? $request->payment_method : '';
        //delivery time
        $deliverytimetxt = '';
        if (!empty($request->delivery_time)) {
            $delivryDetailsInfo = self::getDeliberyTimeDetails($request->delivery_time);
            $orderDetails->delivery_time_id = $delivryDetailsInfo->id;
            $orderDetails->delivery_time_en = $delivryDetailsInfo->title_en;
            $orderDetails->delivery_time_ar = $delivryDetailsInfo->title_ar;
            $deliverytimetxt = $strLang == "en" ? $delivryDetailsInfo->title_en : $delivryDetailsInfo->title_ar;
        }
        //seller discount
        if (!empty(Cookie::get('gb_seller_discount'))) {
            $orderDetails->seller_discount = Cookie::get('gb_seller_discount');
            $orderDetails->delivery_date   = Cookie::get('gb_delivery_date');
        } else {
            $curdate = date("Y-m-d");
            $orderDetails->delivery_date   = !empty($request->delivery_date) ? $request->delivery_date : date("Y-m-d", strtotime($curdate . '+1 day'));
        }

        $couponCode = !empty(Cookie::get('gb_coupon_code')) ? Cookie::get('gb_coupon_code') : '';

        if (!empty(Cookie::get('gb_coupon_code'))) {
            $orderDetails->is_coupon_used = 1;
            $orderDetails->coupon_code    = $couponCode;
            $orderDetails->coupon_amount  = !empty(Cookie::get('gb_coupon_discount')) ? Cookie::get('gb_coupon_discount') : '0';
            $orderDetails->coupon_free    = !empty(Cookie::get('gb_coupon_free')) ? Cookie::get('gb_coupon_free') : '0';
            $decrementCouponUsage = Coupon::where('coupon_code', $couponCode)->first();
            $decrementCouponUsage->usage_limit = $decrementCouponUsage->usage_limit - 1;
            $decrementCouponUsage->save();
        }


        $deliveryCharge = self::get_delivery_charge($request->area ?? null, session()->getId(), false);

        $orderDetails->delivery_charges    = !empty(Cookie::get('gb_coupon_free')) ? 0 : $deliveryCharge;

        $orderDetails->strLang = $strLang;
        $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
        $orderDetails->bundle_discount = $bundleDiscount;

        $orderDetails->save();

        // Unique order id fix
        $ordersid = $orderDetails->id;
        $orderid  = strtolower($settingInfo->prefix) . $ordersid;
        Session::put('orderid', md5($orderid));
        $orderDetails->order_id     = $orderid;
        $orderDetails->sid          = $ordersid;
        $orderDetails->order_id_md5 = md5($orderid);
        $orderDetails->save();

        //import temp order to order table
        $ordertxt_child = '';
        $subtotalprice = 0;
        $grandtotal = 0;
        $totalprice = 0;
        foreach ($tempOrders as $tempOrder) {
            $productDetails = self::getProductDetails($tempOrder->product_id);
            if ($productDetails->image) {
                $prodImage = url('uploads/product/thumb/' . $productDetails->image);
            } else {
                $prodImage = url('uploads/no-image.png');
            }


            if (!empty($tempOrder->size_id)) {
                $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
            } else {
                $sizeName = '';
            }
            if (!empty($tempOrder->color_id)) {
                $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                //color image
                $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                if (!empty($colorImageDetails->color_image)) {
                    $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                }
            } else {
                $colorName = '';
            }
            $orderOptions = self::getOptionsDtails($tempOrder->id);
            //deduct quantity
            $orderInventory1 = $this->deductQuantity($tempOrder->product_id, $tempOrder->quantity, $tempOrder->size_id, $tempOrder->color_id);
            $unitprice = $tempOrder->getOriginal('unit_price');
            $subtotalprice = $unitprice * $tempOrder->quantity;
            $title = $productDetails['title_' . $strLang];

            $warrantyTxt = '';
            if (!empty($productDetails->warranty)) {
                $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
            }

            $tempPrice = Currency::convertTCountry($unitprice);
            $unitpriceShow = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $unitprice;
            $subtotalpriceShow = $unitpriceShow * $tempOrder->quantity;
            $ordertxt_child .= '<tr>
						<td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
						</td>
						<td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
						<td>' . \App\Currency::default() . ' ' . $unitpriceShow . '</td>
						<td align="center">' . $tempOrder->quantity . '</td>
						<td>' . \App\Currency::default() . ' ' . $subtotalpriceShow . '</td>
						</tr>';
            $orders = new Orders;
            $orders->oid       = $orderDetails->id;
            $orders->order_id  = $orderid;
            $orders->product_id = $tempOrder->product_id;
            $orders->size_id   = $tempOrder->size_id;
            $orders->color_id  = $tempOrder->color_id;
            $orders->unit_price = $unitprice;
            $orders->quantity  = $tempOrder->quantity;
            $orders->save();
            //add option
            $orderInventory2 = [];
            $tempOrderOptions = OrdersTempOption::where("oid", $tempOrder->id)->get();
            if (!empty($tempOrderOptions) && count($tempOrderOptions) > 0) {
                foreach ($tempOrderOptions as $tempOrderOption) {
                    //deduct qty from option
                    $orderInventory2 = self::changeOptionQuantity($tempOrderOption->product_id, 'd', $tempOrderOption->option_child_ids, $tempOrder->quantity);
                    //initialize object
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $tempOrderOption->product_id;
                    $OrderOption->oid              = $orders->id;
                    $OrderOption->option_id        = $tempOrderOption->option_id;
                    $OrderOption->option_child_ids = $tempOrderOption->option_child_ids;
                    $OrderOption->attach_file      = $tempOrderOption->attach_file;
                    $OrderOption->dates            = $tempOrderOption->dates;
                    $OrderOption->comments         = $tempOrderOption->comments;
                    $OrderOption->save();
                    //remove option
                    $tempOrds = OrdersTempOption::find($tempOrderOption->id);
                    $tempOrds->delete();
                }
            }
            $orderInventories = $tempOrderInventories = array_merge($orderInventory1, $orderInventory2);
            foreach ($tempOrderInventories as $i => $orderInventory)
                if (!($orderInventory > 0))
                    unset($orderInventories[$i]);
            $orders->inventory  =  json_encode($orderInventories);
            $orders->save();

            //remove temp record
            $tempOrd = OrdersTemp::find($tempOrder->id);
            $tempOrd->delete();

            //plus sub total price
            $totalprice += $subtotalprice;
        }
        //reset order cookies

        Cookie::queue('name', '', 0);
        Cookie::queue('email', '', 0);
        Cookie::queue('mobile', '', 0);
        Cookie::queue('country', '', 0);
        Cookie::queue('state', '', 0);
        Cookie::queue('area', '', 0);
        Cookie::queue('area_id', '', 0);
        Cookie::queue('block', '', 0);
        Cookie::queue('street', '', 0);
        Cookie::queue('avenue', '', 0);
        Cookie::queue('house', '', 0);
        Cookie::queue('floor', '', 0);
        Cookie::queue('landmark', '', 0);
        Cookie::queue('payment_method', '', 0);
        Cookie::queue('is_checkout', 0, 0);
        Cookie::queue('gb_coupon_code', 0, 0);
        Cookie::queue('gb_coupon_discount', 0, 0);
        Cookie::queue('gb_coupon_discount_text', 0, 0);
        Cookie::queue('gb_coupon_free', 0, 0);
        Cookie::queue('latitude', 0, 0);
        Cookie::queue('longitude', 0, 0);
        Cookie::queue('gb_seller_discount', 0, 0);
        Cookie::queue('gb_delivery_date', 0, 0);
        Cookie::queue('is_express_delivery', 0, 0);


        $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
						<tr>
						<td class="headertd">' . trans('webMessage.image') . '</td>
						<td class="headertd">' . trans('webMessage.details') . '</td>
						<td class="headertd">' . trans('webMessage.unit_price') . '</td>
						<td class="headertd">' . trans('webMessage.quantity') . '</td>
						<td class="headertd">' . trans('webMessage.subtotal') . '</td>
						</tr>';
        $orderDetailsTxt .= $ordertxt_child;

        $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
        //bundle discount
        if ($bundleDiscount > 0) {
            $tempPrice = Currency::convertTCountry($bundleDiscount);
            $bundleDiscountShow = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $bundleDiscount;
            $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscountShow . '</font></td></tr>';
            $totalprice = $totalprice - $bundleDiscount;
        }
        //seller discount
        if (!empty($orderDetails->seller_discount)) {
            $tempPrice = Currency::convertTCountry($orderDetails->seller_discount);
            $seller_discountShow = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $orderDetails->seller_discount;
            $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $seller_discountShow . '</font></td></tr>';
            $totalprice = $totalprice - $orderDetails->seller_discount;
        }
        //show discount if available but not free delivery
        if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_amount)) {
            $tempPrice = Currency::convertTCountry($orderDetails->coupon_amount);
            $coupon_amountShow = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $orderDetails->coupon_amount;
            $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $coupon_amountShow . '</font></td></tr>';
            $totalprice = $totalprice - $orderDetails->coupon_amount;
        }
        if (!empty($settingInfo->is_free_delivery) && $totalprice >= $settingInfo->free_delivery_amount) {
            $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
            //update delivery charge 0 if its free
            $orderDetailsDelivery = OrdersDetails::where('order_id', $orderid)->first();
            $orderDetailsDelivery->delivery_charges = 0;
            $orderDetailsDelivery->save();
        } else {

            if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
            }

            if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                $deliveryCharge = $orderDetails->getOriginal('delivery_charges');
                $tempPrice = Currency::convertTCountry($deliveryCharge);
                $deliveryChargeShow = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $deliveryCharge;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryChargeShow . '</td></tr>';
                $totalprice = $totalprice + $deliveryCharge;
            }
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
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $deliverytimetxt . '</td></tr>';
            }
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
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.deliverytime') . '</strong></td><td>' . $deliverytimetxt . '</td></tr>';
            }
            $invoiceDetailsTxt .= '</table>';
        }




        $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
        if (!empty($orderDetails->name)) {
            $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
        }

        if (!empty($orderDetails->area_id)) {
            $areaInfo    = self::get_csa_info($orderDetails->area_id);
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
        }
        if (!empty($orderDetails->block)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
        }
        if (!empty($orderDetails->street)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
        }
        if (!empty($orderDetails->avenue)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
        }
        if (!empty($orderDetails->house)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
        }
        if (!empty($orderDetails->floor)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
        }
        if (!empty($orderDetails->landmark)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
        }

        if (!empty($orderDetails->email)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
        }
        if (!empty($orderDetails->mobile)) {
            $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
        }

        $customerDetailsTxt .= '</table>';

        //update total amount
        self::UpdateOrderAmounts($orderDetails->id, $totalprice);



        //        $tempPrice = Currency::convertTCountry( $totalprice);
        //        $totalprice = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $totalprice;

        //track url
        $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderid);
        $paymentDetailsTxt = '';
        //send email notification if COD
        if ($request->payment_method == "COD" || $request->payment_method == "POSTKNET") {
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
                //send mrk print
                if ($settingInfo->theme == 8) {
                    $printEmail = '8578cmh76dfqm@hpeprint.com';
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
                    Mail::to($printEmail)->send(new SendGridOrder($data));
                }
                //send mrk print end
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
                $to      = $orderDetails->mobile;
                $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                Common::SendSms($to, $sms_msg);
            }
            //end sending sms for cod

            return redirect(app()->getLocale() . '/order-details/' . md5($orderid))->with('session_msg', trans('webMessage.yourorderisplacedsucces'));
        } elseif ($request->payment_method == "GKNET" || $request->payment_method == "GTPAY") {

            if (empty($settingInfo->gulfpay_key) || empty($settingInfo->gulfpay_token)) {
                return redirect($strLang . '/cart')->with('session_msg', trans('webMessage.dezpaycredentialserror'));
            }

            if ($request->payment_method == "GKNET") {
                $payType = 1; // KNET
            } else {
                $payType = 2; //TPAY
            }
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
                return Redirect::to($jsdecode['payurl']);
            } else {
                $emsg = $jsdecode['message'];
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
            //end prepare payment
        } elseif ($request->payment_method == "MasterCard") {
            $mastercard = Common::MasterCard_payment_processing($orderid, $totalprice, $uid, app()->getLocale());
            if ($mastercard['status'])
                return $mastercard['payurl'];
            return redirect(app()->getLocale() . '/order-details/' . md5($orderid))->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $mastercard['message'] . '</strong>');
        } elseif ($request->payment_method == "Q8LINK") {



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
                return Redirect::to($jsdecode['data']);
            } else {
                $emsg = $jsdecode['info'];
                return redirect($strLang . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
            //end prepare payment
        } elseif ($request->payment_method == "KNET" || $request->payment_method == "TPAY") {

            //     if (!empty($settingInfo->is_knet_live)) {
            //         $paytype = 1;
            //     } else {
            //         $paytype = 0;
            //     }
            //     $payprocessDetails = Common::knet_payment_processing($orderid, $totalprice, $uid, $strLang, $paytype);
            //     if ($payprocessDetails['status'] == 1) {
            //         return Redirect::to($payprocessDetails['payurl']);
            //     } else {
            //         $emsg = $payprocessDetails['message'];
            //         return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            //     }
            // } elseif ($request->payment_method == "CBK_KNET" || $request->payment_method == "CBK_TPAY") {
            $paytype = $request->payment_method == "KNET" ? 1 : 2;
            $payprocessDetails = Common::cbk_payment_processing($orderid, $totalprice, $uid, $strLang, $paytype);
            if ($payprocessDetails['status'] == 1) {
                return $payprocessDetails['form'];
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "TAH") {

            if (!empty($settingInfo->is_tah_live)) {
                $paytype = 1;
            } else {
                $paytype = 0;
            }
            $email = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $name = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $payprocessDetails = Common::tahseel_payment_initialize($name, $email, $totalprice, $orderid, 1, $uid, $strLang, 0, $paytype);

            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect($strLang . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "MF") {

            if (!empty($settingInfo->is_mf_live)) {
                $paytype = 1;
            } else {
                $paytype = 0;
            }
            $email   = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $mobile  = !empty($orderDetails->mobile) ? $orderDetails->mobile : $settingInfo->mobile;
            $name    = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $block   = !empty($orderDetails->block) ? $orderDetails->block : '';
            $street  = !empty($orderDetails->street) ? $orderDetails->street : '';

            $accessToken       =  Common::getToken();
            $payprocessDetails = Common::initPayment($name, $block, $street, '', '', '', $mobile, $email, $accessToken[0], $totalprice, $orderid, $uid, $strLang);

            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "PAYPAL") {
            $PayDetails = "Purachasing items from " . $settingInfo->name_en;
            $PayName    = "Item Name";
            $ReturnUrl  = url('paypal_return');
            $totalpriceUSD = Common::currencyconverter($totalprice, 'KWD', 'USD');
            $payprocessDetails = Common::postPaymentWithpaypal($orderid, $totalpriceUSD, $totalprice, $PayDetails, $PayName, $ReturnUrl, $strLang);
            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "CS") {

            if (!empty($settingInfo->is_tah_live)) {
                $paytype = 1;
            } else {
                $paytype = 0;
            }
            $email  = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $name   = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $mobile = !empty($orderDetails->mobile) ? $orderDetails->mobile : $settingInfo->mobile;
            $explodeName = [];
            if (!empty($name)) {
                $explodeName = explode(' ', $name);
            }
            $cust_surname = !empty($explodeName[1]) ? $explodeName[1] : 'No Name';
            $areaname = !empty($areaInfo->name_en) ? $areaInfo->name_en : 'No Name';
            $address  = '';
            $address .= $areaname;
            if (!empty($orderDetails->block)) {
                $address  .= ',block-' . $orderDetails->block;
            }
            if (!empty($orderDetails->street)) {
                $address  .= ',street-' . $orderDetails->street;
            }
            if (!empty($orderDetails->avenue)) {
                $address  .= ',avenue-' . $orderDetails->avenue;
            }
            if (!empty($orderDetails->house)) {
                $address  .= ',house-' . $orderDetails->house;
            }
            /*if(!empty($orderDetails->floor)){
		$address  .=',floor-'.$orderDetails->floor;
		}*/
            /*if(!empty($orderDetails->landmark)){
		$address  .=',landmark-'.$orderDetails->landmark;
		}*/

            $payprocessDetails = Common::prepareCSPayment($orderid, $totalprice, $strLang, $address, $areaname, 'KW', $email, $mobile, $name, $cust_surname);


            if ($payprocessDetails['status'] == 1) {
                $fields = $payprocessDetails['message'];
                return view('website.cs_payments', compact('fields'));
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect($strLang . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        }
    }
    //accept encrypted trans data
    public function knet_response_accept(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();

        if (empty($settingInfo->is_knet_live)) {
            $TERM_RESOURCE_KEY = config('services.knet_test.TERM_RESOURCE_KEY');
        } else {
            $TERM_RESOURCE_KEY = config('services.knet_live.TERM_RESOURCE_KEY');
        }

        $paymentid = !empty($request->paymentid) ? $request->paymentid : '';
        $result    = !empty($request->result) ? $request->result : '';
        $postdate  = !empty($request->postdate) ? $request->postdate : '';
        $tranid    = !empty($request->tranid) ? $request->tranid : '';
        $auth      = !empty($request->auth) ? $request->auth : '';
        $ref       = !empty($request->ref) ? $request->ref : '';
        $trackid   = !empty($request->trackid) ? $request->trackid : '';
        $udf1      = !empty($request->udf1) ? $request->udf1 : ''; //userid
        $udf2      = !empty($request->udf2) ? $request->udf2 : ''; //orderid
        $udf3      = !empty($request->udf3) ? $request->udf3 : '';
        $udf4      = !empty($request->udf4) ? $request->udf4 : '';
        $udf5      = !empty($request->udf5) ? $request->udf5 : '';
        $ErrorText = !empty($request->ErrorText) ? $request->ErrorText : '';
        $Error     = !empty($request->Error) ? $request->Error : '';
        $avr       = !empty($request->avr) ? $request->avr : '';
        $amt       = !empty($request->amt) ? $request->amt : '';

        if ($ErrorText == null && $Error == null) {

            /*IMPORTANT NOTE - MERCHANT SHOULD UPDATE
							TRANACTION PAYMENT STATUS IN MERCHANT DATABASE AT THIS POSITION
							AND THEN REDIRECT CUSTOMER ON RESULT PAGE*/
            $ResTranData = !empty($request->trandata) ? $request->trandata : '';
            if (!empty($ResTranData)) {
                //dd($ResTranData);
                //Decryption logice starts
                $decrytedData   = Common::decrypt($ResTranData, $TERM_RESOURCE_KEY);
                $decryptedDatas = Common::splitData($decrytedData);

                if (!empty($decryptedDatas) && count($decryptedDatas) > 0) {
                    $knetMessage = '';
                    if (!empty($decryptedDatas['result']) && $decryptedDatas['result'] == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                    }
                    $res = self::knetresponseSaveData($decryptedDatas);
                    if (!empty($res['status']) && $res['status'] == 1) {
                        return redirect(app()->getLocale() . '/order-details/' . md5($decryptedDatas['udf1']))->with('session_msg', $knetMessage);
                    } else if (!empty($res['status']) && $res['status'] == 2) {
                        return redirect(app()->getLocale() . '/order-details/' . md5($decryptedDatas['udf1']))->with('session_msg_error', $knetMessage);
                    } else {
                        return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.splitdataerror'));
                    }
                } else {
                    return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.splitdataerror'));
                }
            } else {
                return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.transdataempty'));
            }
        } else {
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.errorfound') . '<br>' . $ErrorText);
        }
    }

    public function cbk_response_accept(Request $request)
    {
        try {
            $cbkPay = new CBKPay();
            $paymentStatus = $cbkPay->getPaymentStatusDetails(@$request->encrp);

            if (!@$paymentStatus || (!@$paymentStatus->Status && !@$paymentStatus->ErrorCode)) {
                throw new \Exception(trans('webMessage.errorfound') . '<br>' . 'Unable to verify Payment!');
            }
            if (@$paymentStatus->Status) {
                $message = $cbkPay->getPaymentResultMsg($paymentStatus->Status);
            } elseif (@$paymentStatus->ErrorCode) {
                $message = $cbkPay->getCBKError($paymentStatus->ErrorCode);
            }

            if (isset($paymentStatus->Status) && isset($paymentStatus->MerchUdf1)) {
                $res = null;
                if (
                    @$paymentStatus->Status === '1'
                    && (@$paymentStatus->PayId || @$decryptedDatas->PayTrackID)
                ) {
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                }
                $res = self::cbkresponseSaveData($paymentStatus);

                if (!empty($res['status']) && $res['status'] === 1) {
                    return redirect(app()->getLocale() . '/order-details/' . md5($paymentStatus->MerchUdf1))->with('session_msg', $knetMessage);
                }
                $knetMessage = trans('webMessage.yourorderisplacedwithfailed') . '<br>' . @$message;
                return redirect(app()->getLocale() . '/order-details/' . md5($paymentStatus->MerchUdf1))->with('session_msg_error', @$paymentStatus->Message ?? @$paymentStatus->CCMessage ?? @$res['message'] ?? @$knetMessage ?? trans('webMessage.errorfound'));
            } else {
                throw new \Exception(@$message ?? trans('webMessage.errorfound'));
            }
        } catch (\Exception $e) {
            $message = @$e->getMessage() ??  trans('webMessage.errorfound');
            return redirect('/knet_failed')->with('session_msg_error',  @$message ?? trans('webMessage.errorfound'));
        }
    }

    public function getMasterCardResponse(Request $request, $transactionId)
    {
        try {
            $settingInfo = Settings::where("keyname", "setting")->first();
            $resultIndicator = !empty($request->resultIndicator) ? $request->resultIndicator : '';
            $orderDetails = OrdersDetails::where('order_id_md5', @$transactionId)->latest()->first();
            $transactionDetails = Transaction::where('trackid', @$orderDetails->order_id)->latest()->first();
            $strLang = app()->getLocale();
            if (@$orderDetails->id) {
                if ($resultIndicator == @$transactionDetails['successIndicator']) {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                    $transactionDetails->presult = 'CAPTURED';
                    $transactionDetails->save();

                    $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                    if (!empty($orderDetails->name)) {
                        $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                    }
                    if (!empty($orderDetails->area_id)) {
                        $areaInfo = self::get_csa_info($orderDetails->area_id);
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                    }
                    if (!empty($orderDetails->block)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                    }
                    if (!empty($orderDetails->street)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                    }
                    if (!empty($orderDetails->avenue)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                    }
                    if (!empty($orderDetails->house)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                    }
                    if (!empty($orderDetails->floor)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                    }
                    if (!empty($orderDetails->landmark)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                    }

                    if (!empty($orderDetails->email)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                    }
                    if (!empty($orderDetails->mobile)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                    }

                    $customerDetailsTxt .= '</table>';
                    //invoice details
                    $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                    if (!empty($transactionDetails->presult) && $transactionDetails->presult == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    }
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';
                    
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';
                    
                    if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                    } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                    }
                    
                    $invoiceDetailsTxt .= '</table>';
                    
                    //list order
                    $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                    $ordertxt_child = '';
                    $subtotalprice = 0;
                    $grandtotal = 0;
                    $totalprice = 0;
                    foreach ($tempOrders as $tempOrder) {
                        $productDetails = self::getProductDetails($tempOrder->product_id);
                        if ($productDetails->image) {
                            $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                        } else {
                            $prodImage = url('uploads/no-image.png');
                        }
                        if (!empty($tempOrder->size_id)) {
                            $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                            $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                        } else {
                            $sizeName = '';
                        }
                        if (!empty($tempOrder->color_id)) {
                            $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                            $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                            //color image
                            $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                            if (!empty($colorImageDetails->color_image)) {
                                $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                            }
                        } else {
                            $colorName = '';
                        }
                        $orderOptions = self::getOptionsDtailsOrderBr($tempOrder->id);
                        $unitprice = $tempOrder->unit_price;
                        $subtotalprice = $unitprice * $tempOrder->quantity;
                        $title = $productDetails['title_' . $strLang];

                        $warrantyTxt = '';
                        if (!empty($productDetails->warranty)) {
                            $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                            $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                        }


                        $ordertxt_child .= '<tr>
                            <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
                            </td>
                            <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                            <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                            <td align="center">' . $tempOrder->quantity . '</td>
                            <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                            </tr>';

                        $totalprice += $subtotalprice;
                    }
                    //order details
                    $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                    $orderDetailsTxt .= $ordertxt_child;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                    //bundle discount
                    $bundleDiscount = self::loadTempOrdersBundleDiscount($tempOrders);
                    if ($bundleDiscount > 0) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                        $totalprice = $totalprice - $bundleDiscount;
                    }
                    //seller discount
                    if (!empty($orderDetails->seller_discount)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->seller_discount;
                    }
                    //show discount if available but not free delivery
                    if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->coupon_amount;
                    }
                    if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                    }

                    if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                        $deliveryCharge = $orderDetails->delivery_charges;
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                        $totalprice = $totalprice + $deliveryCharge;
                    }
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                    $orderDetailsTxt .= '</table>';
                    
                    //payment temp
                    $paymentDetails = '';
                    $knetMessage = '';
                    if (!empty($transactionDetails->presult) && $transactionDetails->presult == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                    }
                    
                    $transid = '--';
                    if (isset($transactionDetails->tranid) && !empty(@$transactionDetails->tranid)) {
                        $transid = @$transactionDetails->tranid;
                    }

                    $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
                            <tr>
                            <td>' . trans('webMessage.result') . '</td>
                            <td>' . $txtpaid . '</td>
                            </tr>
                            <tr>
                            <td>' . trans('webMessage.date') . '</td>
                            <td>' . date('Y-m-d H:i:s') . '</td>
                            </tr>
                            <tr>
                            <td>' . trans('webMessage.transid') . '</td>
                            <td>' . @$transactionDetails->tranid . '</td>
                            </tr>
                            <tr>
                            <td>' . trans('webMessage.paymentid') . '</td>
                            <td>' . @$transactionDetails['payment_id'] . '</td>
                            </tr>
                            <tr>
                            <td>' . trans('webMessage.amount') . '</td>
                            <td>' . number_format(@$orderDetails->getOriginal('total_amount'), 3) . ' ' . \App\Currency::defaultCMS() . '</td>
                            </tr>
                            </table>';
                            
                            $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
                            //send email to admins
                            $adminNotifications = NotificationEmails::where('is_active', 1)->get();
                   
                    if (!empty($adminNotifications) && count($adminNotifications) > 0) {
                        foreach ($adminNotifications as $adminNotification) {
                            $deartxt = !empty($adminNotification->name) ? trans('webMessage.dear') . ' ' . $adminNotification->name : trans('webMessage.dear') . ' ' . trans('webMessage.admin');
                            $data = [
                                'deartxt' => $deartxt,
                                'bodytxt' => trans('webMessage.admin_order_msg_cod'),
                                'customerDetails' => $customerDetailsTxt,
                                'invoiceDetails' => $invoiceDetailsTxt,
                                'orderDetails' => $orderDetailsTxt,
                                'paymentDetails' => $paymentDetails,
                                'trackYourOrder' => $trackYourOrderTxt,
                                'subject' => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                                'email_from' => $settingInfo->from_email,
                                'email_from_name' => $settingInfo->from_name
                            ];
                            Mail::to($adminNotification->email)->send(new SendGridOrder($data));
                        }

                        //send mrk print
                        if ($settingInfo->theme == 8 && !empty($transactionDetails->presult) && $transactionDetails->presult == 'CAPTURED') {
                            $printEmail = '8578cmh76dfqm@hpeprint.com';
                            $data = [
                                'deartxt' => $deartxt,
                                'bodytxt' => trans('webMessage.admin_order_msg_cod'),
                                'customerDetails' => $customerDetailsTxt,
                                'invoiceDetails' => $invoiceDetailsTxt,
                                'orderDetails' => $orderDetailsTxt,
                                'paymentDetails' => $paymentDetails,
                                'trackYourOrder' => $trackYourOrderTxt,
                                'subject' => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                                'email_from' => $settingInfo->from_email,
                                'email_from_name' => $settingInfo->from_name
                            ];
                            Mail::to($printEmail)->send(new SendGridOrder($data));
                        }
                        //send mrk print end


                    }
                    //send email to user
                    if (!empty($orderDetails->email)) {
                        $deartxt = !empty($orderDetails->name) ? trans('webMessage.dear') . ' ' . $orderDetails->name : trans('webMessage.dear') . ' ' . trans('webMessage.buyer');
                        $data = [
                            'deartxt' => $deartxt,
                            'bodytxt' => trans('webMessage.user_order_msg_cod'),
                            'customerDetails' => $customerDetailsTxt,
                            'invoiceDetails' => $invoiceDetailsTxt,
                            'orderDetails' => $orderDetailsTxt,
                            'paymentDetails' => $paymentDetails,
                            'trackYourOrder' => $trackYourOrderTxt,
                            'subject' => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                            'email_from' => $settingInfo->from_email,
                            'email_from_name' => $settingInfo->from_name
                        ];
                        Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                    }
                    if (!empty($transactionDetails->presult) && $transactionDetails->presult == 'CAPTURED') {

                        //send sms notification for cod
                        $isValidMobile = Common::checkMobile($orderDetails->mobile);
                        if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                            if ($orderDetails->strLang == "en") {
                                $smsMessage = $settingInfo->sms_text_knet_en;
                            } else {
                                $smsMessage = $settingInfo->sms_text_knet_ar;
                            }
                            $to      = $orderDetails->mobile;
                            $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                            Common::SendSms($to, $sms_msg);
                        }
                        // end sending sms for cod
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                        return redirect(app()->getLocale() . '/order-details/' .  @$orderDetails->order_id_md5)->with('session_msg', $knetMessage);
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                        return redirect(app()->getLocale() . '/order-details/' .  $orderDetails->order_id_md5)->with('session_msg_error', $knetMessage);
                    }
                } else {
                    return redirect(url(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5))->with('session_msg_error', trans('webMessage.invalidpayment'));
                }
            } else {
                return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment') . '<br>' . trans('webMessage.errorfound'));
            }
        } catch (\Exception $e) {
            // dd($e);
            $log = \DB::table('gwc_logs')->insert([
                'key_name' => 'mc',
                'key_id'    => '1',
                'message'  => json_encode(['exception' => @$e]),
                'created_by' => '1'
            ]);
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment') . '<br>' . (@$e->getMessage() ?? trans('webMessage.errorfound')));
        }
    }

    public function knet_failed(Request $request)
    {
        return view('website.knet_failed');
    }
    //save knet response data
    public static function knetresponseSaveData($decryptedDatas)
    {

        if (!empty($decryptedDatas['trackid'])) {
            if (!empty(app()->getLocale())) {
                $strLang = app()->getLocale();
            } else {
                $strLang = "en";
            }
            $settingInfo = Settings::where("keyname", "setting")->first();

            $transactionDetails = Transaction::where('id', $decryptedDatas['trackid'])->first();
            $orderDetails = OrdersDetails::where('order_id', $transactionDetails ? $transactionDetails->trackid : '-1')->first();
            if ($orderDetails->id) {
                if ($decryptedDatas['result'] == 'CAPTURED') {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                }
                //update trans
                $transactionDetails->presult = $decryptedDatas['result'];
                if (!empty($decryptedDatas['paymentid'])) {
                    $transactionDetails->payment_id = $decryptedDatas['paymentid'];
                }
                if (!empty($decryptedDatas['ref'])) {
                    $transactionDetails->ref = $decryptedDatas['ref'];
                }
                if (isset($decryptedDatas['tranid']) && !empty($decryptedDatas['tranid'])) {
                    $transactionDetails->tranid = $decryptedDatas['tranid'];
                }
                if (!empty($decryptedDatas['auth'])) {
                    $transactionDetails->auth = $decryptedDatas['auth'];
                }
                if (!empty($decryptedDatas['amt'])) {
                    $transactionDetails->amt = $decryptedDatas['amt'];
                }
                $transactionDetails->PayType = 'KNET';
                $transactionDetails->save();

                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($decryptedDatas['result']) && $decryptedDatas['result'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice     = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                $knetMessage = '';
                if (!empty($decryptedDatas['result']) && $decryptedDatas['result'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $transid = '--';
                if (isset($decryptedDatas['tranid']) && !empty($decryptedDatas['tranid'])) {
                    $transid = $decryptedDatas['tranid'];
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
                    <tr>
                    <td>' . trans('webMessage.result') . '</td>
                    <td>' . $txtpaid . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.date') . '</td>
                    <td>' . date('Y-m-d H:i:s') . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.transid') . '</td>
                    <td>' . $transid . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.paymentid') . '</td>
                    <td>' . $decryptedDatas['paymentid'] . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.amount') . '</td>
                    <td>' . number_format($decryptedDatas['amt'], 3) . ' ' . \App\Currency::defaultCMS() . '</td>
                    </tr>
                </table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                            'email_from'      => $settingInfo->from_email,
                            'email_from_name' => $settingInfo->from_name
                        ];
                        Mail::to($adminNotification->email)->send(new SendGridOrder($data));
                    }

                    //send mrk print
                    if ($settingInfo->theme == 8 && !empty($decryptedDatas['result']) && $decryptedDatas['result'] == 'CAPTURED') {
                        $printEmail = '8578cmh76dfqm@hpeprint.com';
                        $data = [
                            'deartxt'         => $deartxt,
                            'bodytxt'         => trans('webMessage.admin_order_msg_cod'),
                            'customerDetails' => $customerDetailsTxt,
                            'invoiceDetails'  => $invoiceDetailsTxt,
                            'orderDetails'    => $orderDetailsTxt,
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                            'email_from'      => $settingInfo->from_email,
                            'email_from_name' => $settingInfo->from_name
                        ];
                        Mail::to($printEmail)->send(new SendGridOrder($data));
                    }
                    //send mrk print end


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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }

                if (!empty($decryptedDatas['result']) && $decryptedDatas['result'] == 'CAPTURED') {

                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod


                    return ["status" => 1, "message" => $knetMessage];
                } else {
                    return ["status" => 2, "message" => $knetMessage];
                }
            } else { //order exist or not
                return ["status" => 0, "message" => trans('webMessage.invalidpayment')];
            }
        } else { //track id not empty
            return ["status" => 0, "message" => trans('webMessage.invalidpayment')];
        }
    }


    public static function cbkresponseSaveData($paymentStatus)
    {
        try {
            $decryptedDatas = (array)$paymentStatus;
            $payment_result = ($decryptedDatas['Status'] === '1') ? "CAPTURED" : "NOT CAPTURED";
            if (!empty($decryptedDatas['PayId']) || !empty($decryptedDatas['PayTrackID'])) {
                if (!empty(app()->getLocale())) {
                    $strLang = app()->getLocale();
                } else {
                    $strLang = "en";
                }
                $settingInfo = Settings::where("keyname", "setting")->first();

                $transactionDetails = Transaction::where('id', @$decryptedDatas['PayId'] ?? @$decryptedDatas['PayTrackID'])->first();

                $orderDetails = OrdersDetails::where('order_id', @$transactionDetails ? $transactionDetails->trackid : '-1')->first();

                if (@$orderDetails->id) {
                    if ($payment_result == 'CAPTURED') {
                        $orderDetails->is_paid = 1;
                        $orderDetails->save();
                    }
                    //update trans
                    $transactionDetails->presult = @$payment_result;
                    if (!empty($decryptedDatas['PaymentId'])) {
                        $transactionDetails->payment_id = @$decryptedDatas['PaymentId'];
                    }
                    if (!empty($decryptedDatas['ReferenceId'])) {
                        $transactionDetails->ref = @$decryptedDatas['ReferenceId'];
                    }
                    if (isset($decryptedDatas['TransactionId']) && !empty($decryptedDatas['TransactionId'])) {
                        $transactionDetails->tranid = @$decryptedDatas['TransactionId'];
                    }
                    if (!empty($decryptedDatas['AuthCode'])) {
                        $transactionDetails->auth = @$decryptedDatas['AuthCode'];
                    }
                    if (!empty($decryptedDatas['Amount'])) {
                        $transactionDetails->amt = @$decryptedDatas['Amount'];
                    }
                    if (!empty($decryptedDatas['TrackId'])) {
                        $transactionDetails->MfTrackId = @$decryptedDatas['TrackId'];
                    }
                    $transactionDetails->PayType = 'KNET';
                    $transactionDetails->save();

                    $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                    if (!empty($orderDetails->name)) {
                        $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                    }

                    if (!empty($orderDetails->area_id)) {
                        $areaInfo    = self::get_csa_info($orderDetails->area_id);
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                    }
                    if (!empty($orderDetails->block)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                    }
                    if (!empty($orderDetails->street)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                    }
                    if (!empty($orderDetails->avenue)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                    }
                    if (!empty($orderDetails->house)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                    }
                    if (!empty($orderDetails->floor)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                    }
                    if (!empty($orderDetails->landmark)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                    }

                    if (!empty($orderDetails->email)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                    }
                    if (!empty($orderDetails->mobile)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                    }

                    $customerDetailsTxt .= '</table>';

                    //invoice details
                    $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                    if (!empty($payment_result) && $payment_result == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    }
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                    if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                    } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                    }

                    $invoiceDetailsTxt .= '</table>';

                    //list order
                    $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                    $ordertxt_child = '';
                    $subtotalprice = 0;
                    $grandtotal = 0;
                    $totalprice = 0;
                    foreach ($tempOrders as $tempOrder) {
                        $productDetails = self::getProductDetails($tempOrder->product_id);
                        if ($productDetails->image) {
                            $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                        } else {
                            $prodImage = url('uploads/no-image.png');
                        }
                        if (!empty($tempOrder->size_id)) {
                            $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                            $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                        } else {
                            $sizeName = '';
                        }
                        if (!empty($tempOrder->color_id)) {
                            $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                            $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                            //color image
                            $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                            if (!empty($colorImageDetails->color_image)) {
                                $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                            }
                        } else {
                            $colorName = '';
                        }
                        $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                        $unitprice     = $tempOrder->unit_price;
                        $subtotalprice = $unitprice * $tempOrder->quantity;
                        $title = $productDetails['title_' . $strLang];

                        $warrantyTxt = '';
                        if (!empty($productDetails->warranty)) {
                            $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                            $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                        }


                        $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                        $totalprice += $subtotalprice;
                    }
                    //order details
                    $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                    $orderDetailsTxt .= $ordertxt_child;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                    //bundle discount
                    $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                    if ($bundleDiscount > 0) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                        $totalprice = $totalprice - $bundleDiscount;
                    }
                    //seller discount
                    if (!empty($orderDetails->seller_discount)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->seller_discount;
                    }
                    //show discount if available but not free delivery
                    if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->coupon_amount;
                    }
                    if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                    }

                    if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                        $deliveryCharge = $orderDetails->delivery_charges;
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                        $totalprice = $totalprice + $deliveryCharge;
                    }
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                    $orderDetailsTxt .= '</table>';

                    //payment temp
                    $paymentDetails = '';
                    $knetMessage = '';
                    if (!empty($payment_result) && $payment_result == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                    }

                    $transid = '--';
                    if (isset($decryptedDatas['TransactionId']) && !empty($decryptedDatas['TransactionId'])) {
                        $transid = $decryptedDatas['TransactionId'];
                    }

                    $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
                    <tr>
                    <td>' . trans('webMessage.result') . '</td>
                    <td>' . $txtpaid . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.date') . '</td>
                    <td>' . date('Y-m-d H:i:s') . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.transid') . '</td>
                    <td>' . $transid . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.paymentid') . '</td>
                    <td>' . $decryptedDatas['PaymentId'] . '</td>
                    </tr>
                    <tr>
                    <td>' . trans('webMessage.amount') . '</td>
                    <td>' . number_format($decryptedDatas['Amount'], 3) . ' ' . \App\Currency::defaultCMS() . '</td>
                    </tr>
                </table>';
                    $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                                'paymentDetails'  => $paymentDetails,
                                'trackYourOrder'  => $trackYourOrderTxt,
                                'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                                'email_from'      => $settingInfo->from_email,
                                'email_from_name' => $settingInfo->from_name
                            ];
                            Mail::to($adminNotification->email)->send(new SendGridOrder($data));
                        }

                        //send mrk print
                        if ($settingInfo->theme == 8 && !empty($payment_result) && $payment_result == 'CAPTURED') {
                            $printEmail = '8578cmh76dfqm@hpeprint.com';
                            $data = [
                                'deartxt'         => $deartxt,
                                'bodytxt'         => trans('webMessage.admin_order_msg_cod'),
                                'customerDetails' => $customerDetailsTxt,
                                'invoiceDetails'  => $invoiceDetailsTxt,
                                'orderDetails'    => $orderDetailsTxt,
                                'paymentDetails'  => $paymentDetails,
                                'trackYourOrder'  => $trackYourOrderTxt,
                                'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                                'email_from'      => $settingInfo->from_email,
                                'email_from_name' => $settingInfo->from_name
                            ];
                            Mail::to($printEmail)->send(new SendGridOrder($data));
                        }
                        //send mrk print end


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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                            'email_from'      => $settingInfo->from_email,
                            'email_from_name' => $settingInfo->from_name
                        ];
                        Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                    }

                    if (!empty($payment_result) && $payment_result == 'CAPTURED') {

                        //send sms notification for cod
                        $isValidMobile = Common::checkMobile($orderDetails->mobile);
                        if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                            if ($orderDetails->strLang == "en") {
                                $smsMessage = $settingInfo->sms_text_knet_en;
                            } else {
                                $smsMessage = $settingInfo->sms_text_knet_ar;
                            }
                            $to      = $orderDetails->mobile;
                            $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                            Common::SendSms($to, $sms_msg);
                        }
                        //end sending sms for cod


                        return ["status" => 1, "message" => $knetMessage];
                    } else {
                        return ["status" => 2, "message" => $knetMessage];
                    }
                } else { //order exist or not
                    return ["status" => 0, "message" => trans('webMessage.invalidpayment')];
                }
            } else { //track id not empty
                return ["status" => 0, "message" => trans('webMessage.invalidpayment')];
            }
        } catch (\Exception $e) {
            return ["status" => 0, "message" => trans('webMessage.invalidpayment') . '<br>' . @$e->getMessage() ?? ''];
        }
    }


    /////////////////////////////////////Payment payment response/////////////////////////////////////////////

    public function paypal_response_accept(Request $request)
    {

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();
        if (empty($request->paymentId) || empty($request->PayerID) || empty($request->token)) {
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.paypalpaymentcanceled'));
        }

        $paymentId   = $request->paymentId;
        $PayerID     = $request->PayerID;
        $token       = $request->token;
        $response    = Common::getPaymentStatus($paymentId, $PayerID, $token);

        if (empty($response['status'])) {
            return redirect('/knet_failed')->with('session_msg_error', $response['message']);
        }


        if (!empty($response['message'])) {

            $orderDetails = OrdersDetails::where('order_id', $response['message'])->first();
            //get trans
            $transactionDetails = Transaction::where('payment_id', $paymentId)->first();

            if ($orderDetails->id) {

                if (!empty($response['is_paid'])) {
                    $orderDetails->is_paid = 1;
                    $orderDetails->total_amount_dollar = !empty($transactionDetails->amt_dollar) ? $transactionDetails->amt_dollar : '0';
                    $orderDetails->save();
                }


                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($response['is_paid'])) {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice     = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                $knetMessage = '';
                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $transid = '--';
                if (isset($transactionDetails['paypal_cart']) && !empty($transactionDetails['paypal_cart'])) {
                    $transid = $transactionDetails['paypal_cart'];
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
	    <tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . date('Y-m-d H:i:s') . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . $transid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $transactionDetails['payment_id'] . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td>' . trans('webMessage.usd') . '' . number_format($transactionDetails['amt_dollar'], 2) . '(' . \App\Currency::defaultCMS() . '' . number_format($transactionDetails['amt'], 3) . ')</td>
        </tr>
      </table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }

                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {

                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod

                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg', $knetMessage);
                } else {
                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg_error', $knetMessage);
                }
            } else { //order exist or not
                return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
            }
        } else { //track id not empty
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
        }
    }

    /////////////////////////////////////Tahseel payment response/////////////////////////////////////////////

    public function tahseel_response_accept(Request $request)
    {

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();

        $paytype     = !empty($settingInfo->is_tah_live) ? 1 : 0;

        if (!empty($request->cancelled)) {
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.tahseelpaymentcanceled'));
        }

        $hash   = $request->hash;
        $inv_id = $request->order_id;
        $response = Common::tahseel_payment_confirm($hash, $inv_id, $paytype, $request);

        if (empty($response['status'])) {
            return redirect('/knet_failed')->with('session_msg_error', $response['message']);
        }

        if (!empty($response['message'])) {
            if (!empty(app()->getLocale())) {
                $strLang = app()->getLocale();
            } else {
                $strLang = "en";
            }
            $transactionDetails = Transaction::where('id', $response['message'])->first();
            $orderDetails = OrdersDetails::where('order_id', $transactionDetails ? $transactionDetails->trackid : '-1')->first();
            if ($orderDetails->id) {

                if (!empty($response['is_paid'])) {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                }
                //get trans
                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($response['is_paid'])) {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice     = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                $knetMessage = '';
                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $transid = '--';
                if (isset($transactionDetails['tranid']) && !empty($transactionDetails['tranid'])) {
                    $transid = $transactionDetails['tranid'];
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
	    <tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . date('Y-m-d H:i:s') . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . $transid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $transactionDetails['payment_id'] . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td>' . number_format($transactionDetails['amt'], 3) . ' ' . \App\Currency::defaultCMS() . '</td>
        </tr>
      </table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }

                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {

                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod

                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg', $knetMessage);
                } else {
                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg_error', $knetMessage);
                }
            } else { //order exist or not
                return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
            }
        } else { //track id not empty
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
        }
    }



    /////////////////////////////////////myfatoorah payment response/////////////////////////////////////////////

    public function myfatoorah_response_accept(Request $request)
    {

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();

        $paytype     = !empty($settingInfo->is_tah_live) ? 1 : 0;

        if (empty($request->paymentId)) {
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.myfatoorahpaymentcanceled'));
        }

        $response = Common::callBackPayment($request->paymentId);

        if (empty($response['status'])) {
            return redirect('/knet_failed')->with('session_msg_error', $response['message']);
        }
        //dd($response);
        if (!empty($response['message'])) {
            if (!empty(app()->getLocale())) {
                $strLang = app()->getLocale();
            } else {
                $strLang = "en";
            }

            $orderDetails = OrdersDetails::where('order_id', $response['message'])->first();
            if ($orderDetails->id) {

                if (!empty($response['is_paid'])) {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                }
                //get trans
                $transactionDetails = Transaction::where('trackid', $orderDetails->order_id)->first();

                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($response['is_paid'])) {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice     = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                $knetMessage = '';
                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $transid = '--';
                if (isset($transactionDetails['tranid']) && !empty($transactionDetails['tranid'])) {
                    $transid = $transactionDetails['tranid'];
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
	    <tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . date('Y-m-d H:i:s') . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . $transid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $transactionDetails['payment_id'] . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td>' . number_format($transactionDetails['amt'], 3) . ' ' . \App\Currency::defaultCMS() . '</td>
        </tr>
      </table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }

                if (!empty($transactionDetails['presult']) && $transactionDetails['presult'] == 'CAPTURED') {

                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod

                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg', $knetMessage);
                } else {
                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg_error', $knetMessage);
                }
            } else { //order exist or not
                return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
            }
        } else { //track id not empty
            return redirect('/knet_failed')->with('session_msg_error', trans('webMessage.invalidpayment'));
        }
    }

    //update total amount
    public static function UpdateOrderAmounts($id, $amount)
    {
        $orderDetails = OrdersDetails::Where('id', $id)->first();
        $orderDetails->total_amount = $amount;
        $orderDetails->save();
    }

    public static function getOrderAmounts($id)
    {
        $totalAmt = 0;
        $orderDetails = OrdersDetails::Where('id', $id)->first();
        $listOrders   = Orders::where('oid', $id)->get();
        if (!empty($listOrders) && count($listOrders) > 0) {
            foreach ($listOrders as $listOrder) {
                $totalAmt += ($listOrder->quantity * $listOrder->unit_price);
            }
            //apply coupon if its not free
            if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                $totalAmt = $totalAmt - $orderDetails->coupon_amount;
            }
            //apply delivery charges if coupon is empty
            if (empty($orderDetails->coupon_free)) {
                $totalAmt = $totalAmt + $orderDetails->delivery_charges;
            }
        }
        $orderDetails->total_amount = $totalAmt;
        $orderDetails->save();
        //return $totalAmt;
    }
    //deduct quantity
    public static function deductQuantity($product_id, $quantity, $size_id = 0, $color_id = 0)
    {
        $inventoryUse = [];
        $productDetails   = Product::where('id', $product_id)->first();
        if (empty($productDetails['is_attribute'])) {
            $productQuantities = $productDetails->getQuantity(-1, null, null, true);
            $tempQuantity = $quantity;
            foreach ($productQuantities as $productQuantity) {
                if ($productQuantity->is_qty_deduct == 1) {
                    if ($tempQuantity > $productQuantity->quantity) {
                        $tempQuantity = $tempQuantity - $productQuantity->quantity;
                        $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
                        $productQuantity->quantity = 0;
                        $productQuantity->save();
                    } else {
                        $productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
                        $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
                        $productQuantity->save();
                        break;
                    }
                }
            }
        } else {
            if (!empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
            } else if (!empty($size_id) && empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
            } else if (empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
            }
            if (isset($attributes)) {
                $tempQuantity = $quantity;
                $productQuantities = $productDetails->getQuantity(-1, $attributes->pluck('id')->toArray(), null, true);
                foreach ($productQuantities as $productQuantity) {
                    if ($productQuantity->is_qty_deduct == 1) {
                        if ($tempQuantity > $productQuantity->quantity) {
                            $tempQuantity = $tempQuantity - $productQuantity->quantity;
                            $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $productQuantity->quantity];
                            $productQuantity->quantity = 0;
                            $productQuantity->save();
                        } else {
                            $productQuantity->quantity = $productQuantity->quantity - $tempQuantity;
                            $inventoryUse[] = ['IID' => $productQuantity->inventory_id, 'q' => $tempQuantity];
                            $productQuantity->save();
                            break;
                        }
                    }
                }
            }
        }
        //change qty in product table for attribute only
        //		self::ChangeUpdateQuantity($product_id);
        //end
        return $inventoryUse;
    }

    //show order result page
    public function ordercompleted(Request $request)
    {
        $orderLists = [];
        $trackLists = [];
        $orderDetails = OrdersDetails::where('order_id_md5', $request->orderid)->first();
        if (empty($orderDetails->id)) {
            abort(404);
        }
        if (!empty($orderDetails->order_id)) {
            $orderLists = Orders::where('order_id', $orderDetails->order_id)->get();
        }
        if (!empty($orderDetails->id)) {
            $trackLists = OrdersTrack::where('oid', $orderDetails->id)->orderBy('display_order', 'DESC')->get();
        }

        $paymentLink = true;
        return view('website.ordercompleted', compact('orderDetails', 'orderLists', 'trackLists', 'paymentLink'));
    }

    //show order result page
    public function orderCompletedPay(Request $request)
    {
        $orderDetails = OrdersDetails::where('order_id_md5', $request->orderid)->first();
        if (empty($orderDetails->id) or $orderDetails->is_paid)
            abort(404);

        $settingInfo = Settings::where("keyname", "setting")->first();
        if ($request->payment_method == "COD" || $request->payment_method == "POSTKNET") {
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
            return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.yourorderisplacedsucces'));
        } elseif ($request->payment_method == "GKNET" || $request->payment_method == "GTPAY") {

            if (empty($settingInfo->gulfpay_key) || empty($settingInfo->gulfpay_token)) {
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.dezpaycredentialserror'));
            }

            if ($request->payment_method == "GKNET") {
                $payType = 1; // KNET
            } else {
                $payType = 2; //TPAY
            }
            $transaction = new Transaction;
            $transaction->presult  = 'INITIALIZED';
            $transaction->postdate = date("md");
            $transaction->udf1     = $orderDetails->order_id;
            $transaction->udf2     = $orderDetails->getOriginal('total_amount');
            $transaction->udf3     = app()->getLocale();
            $transaction->udf4     = $orderDetails->customer_id ?? 0;
            $transaction->udf5     = $settingInfo->name_en;
            $transaction->trackid  = $orderDetails->order_id;
            $transaction->pay_mode  = $request->payment_method;
            $transaction->save();
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
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
                    'amount'       => $orderDetails->getOriginal('total_amount'),
                    'paytype'      => $payType,
                    'item_details' => $item_details
                ])->post();
            $jsdecode = json_decode($response, true);
            if ($jsdecode['status'] == 'success') {
                return Redirect::to($jsdecode['payurl']);
            } else {
                $emsg = $jsdecode['message'];
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
            //end prepare payment
        } elseif ($request->payment_method == "MasterCard") {
            $orderDetails->pay_mode = "MasterCard";
            $orderDetails->save();
            $mastercard = Common::MasterCard_payment_processing($orderDetails->order_id, $orderDetails->getOriginal('total_amount'), $orderDetails->customer_id ?? 0, app()->getLocale());
            if ($mastercard['status'])
                return $mastercard['payurl'];
            return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $mastercard['message'] . '</strong>');
        } elseif ($request->payment_method == "Q8LINK") {
            $transaction = new Transaction;
            $transaction->presult  = 'INITIALIZED';
            $transaction->postdate = date("md");
            $transaction->udf1     = $orderDetails->order_id;
            $transaction->udf2     = $orderDetails->getOriginal('total_amount');
            $transaction->udf3     = app()->getLocale();
            $transaction->udf4     = $orderDetails->customer_id ?? 0;
            $transaction->udf5     = $settingInfo->name_en;
            $transaction->trackid  = $orderDetails->order_id;
            $transaction->pay_mode  = $request->payment_method;
            $transaction->save();
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
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
                    'pay_amount'    => $orderDetails->getOriginal('total_amount')
                ])->post();
            $jsdecode = json_decode($response, true);
            if ($jsdecode['status'] == 200) {
                return Redirect::to($jsdecode['data']);
            } else {
                $emsg = $jsdecode['info'];
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
            //end prepare payment
        } elseif ($request->payment_method == "KNET" || $request->payment_method == "TPAY") {

            // if (!empty($settingInfo->is_knet_live)) {
            //     $paytype = 1;
            // } else {
            //     $paytype = 0;
            // }
            // $orderDetails->pay_mode = $request->payment_method;
            // $orderDetails->save();
            // $payprocessDetails = Common::knet_payment_processing($orderDetails->order_id, $orderDetails->getOriginal('total_amount'), $orderDetails->customer_id ?? 0, app()->getLocale(), $paytype);
            // if ($payprocessDetails['status'] == 1) {
            //     return Redirect::to($payprocessDetails['payurl']);
            // } else {
            //     $emsg = $payprocessDetails['message'];
            //     return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg_error', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            // }

            $paytype = $request->payment_method == "KNET" ? 1 : 2;
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
            $payprocessDetails = Common::cbk_payment_processing($orderDetails->order_id, $orderDetails->getOriginal('total_amount'), $orderDetails->customer_id ?? 0, app()->getLocale(), $paytype);
            if ($payprocessDetails['status'] == 1) {
                return $payprocessDetails['form'];
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "TAH") {

            if (!empty($settingInfo->is_tah_live)) {
                $paytype = 1;
            } else {
                $paytype = 0;
            }
            $email = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $name = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
            $payprocessDetails = Common::tahseel_payment_initialize($name, $email, $orderDetails->getOriginal('total_amount'), $orderDetails->order_id, 1, $orderDetails->customer_id ?? 0, app()->getLocale(), 0, $paytype);

            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "MF") {

            if (!empty($settingInfo->is_mf_live)) {
                $paytype = 1;
            } else {
                $paytype = 0;
            }
            $email   = !empty($orderDetails->email) ? $orderDetails->email : $settingInfo->email;
            $mobile  = !empty($orderDetails->mobile) ? $orderDetails->mobile : $settingInfo->mobile;
            $name    = !empty($orderDetails->name) ? $orderDetails->name : $settingInfo->name_en;
            $block   = !empty($orderDetails->block) ? $orderDetails->block : '';
            $street  = !empty($orderDetails->street) ? $orderDetails->street : '';

            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
            $accessToken       =  Common::getToken();
            $payprocessDetails = Common::initPayment($name, $block, $street, '', '', '', $mobile, $email, $accessToken[0], $orderDetails->getOriginal('total_amount'), $orderDetails->order_id, $orderDetails->customer_id ?? 0, app()->getLocale());

            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        } elseif ($request->payment_method == "PAYPAL") {
            $PayDetails = "Purachasing items from " . $settingInfo->name_en;
            $PayName    = "Item Name";
            $ReturnUrl  = url('paypal_return');
            $totalpriceUSD = Common::currencyconverter($orderDetails->getOriginal('total_amount'), 'KWD', 'USD');
            $orderDetails->pay_mode = $request->payment_method;
            $orderDetails->save();
            $payprocessDetails = Common::postPaymentWithpaypal($orderDetails->order_id, $totalpriceUSD, $orderDetails->getOriginal('total_amount'), $PayDetails, $PayName, $ReturnUrl, app()->getLocale());
            if ($payprocessDetails['status'] == 1) {
                return Redirect::to($payprocessDetails['payurl']);
            } else {
                $emsg = $payprocessDetails['message'];
                return redirect(app()->getLocale() . '/order-details/' . $orderDetails->order_id_md5)->with('session_msg', trans('webMessage.paymentprocessingerrorfound') . '<br><strong>' . $emsg . '</strong>');
            }
        }
    }

    public function orderCompletedStartToPay(Request $request)
    {
        $orderDetails = OrdersDetails::where('order_id_md5', $request->orderid)->first();
        if (empty($orderDetails->id) or $orderDetails->is_paid)
            abort(404);
        $request->merge(['payment_method' => $request->gateway]);

        return $this->orderCompletedPay($request);
    }



    //print invoice
    public function orderprint(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }

        if (
            $strLang == "en"
        ) {
            $align = 'left';
        } else {
            $align = 'right';
        }

        $settingInfo = Settings::where("keyname", "setting")->first();

        $orderDetails = OrdersDetails::where('order_id_md5', $request->orderid)->orwhere('order_id', $request->orderid)->first();

        $deliverytimetxt = '';
        //        if (!empty($request->delivery_time)) {
        //            $delivryDetailsInfo = self::getDeliberyTimeDetails($request->delivery_time);
        //            $orderDetails->delivery_time_id = $delivryDetailsInfo->id;
        //            $orderDetails->delivery_time_en = $delivryDetailsInfo->title_en;
        //            $orderDetails->delivery_time_ar = $delivryDetailsInfo->title_ar;
        //            $deliverytimetxt = $strLang == "en" ? $delivryDetailsInfo->title_en : $delivryDetailsInfo->title_ar;
        //        }
        if (!empty($orderDetails->delivery_time_en)) {
            $deliverytimetxt = $strLang == "en" ? $orderDetails->delivery_time_en : $orderDetails->delivery_time_ar;
        }
        //customer details
        $customerDetailsTxt = '';
        if ($settingInfo->invoice_template == 2) {

            $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
            if (!empty($orderDetails->name)) {
                $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
            }

            if (!empty($orderDetails->area_id)) {
                $areaInfo    = self::get_csa_info($orderDetails->area_id);
                $stateInfo    = self::get_csa_info($areaInfo->parent_id);
                $countryInfo    = self::get_csa_info($stateInfo->parent_id);
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . ($countryInfo != null ? $countryInfo['name_' . $strLang] . ', ' : '') . $stateInfo['name_' . $strLang] . ', ' . $areaInfo['name_' . $strLang] . '</td></tr>';
            }
            if (!empty($orderDetails->block)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
            }
            if (!empty($orderDetails->street)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
            }
            if (!empty($orderDetails->avenue)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
            }
            if (!empty($orderDetails->house)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
            }
            if (!empty($orderDetails->floor)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
            }
            if (!empty($orderDetails->landmark)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
            }

            if (!empty($orderDetails->email)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
            }
            if (!empty($orderDetails->mobile)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
            }

            $customerDetailsTxt .= '</table>';
        } else {

            if (!empty($orderDetails->name)) {
                $customerDetailsTxt .= '<b>' . $orderDetails->name . '</b><br>';
            }

            if (!empty($orderDetails->area_id)) {
                $areaInfo    = self::get_csa_info($orderDetails->area_id);
                $stateInfo    = self::get_csa_info($areaInfo->parent_id);
                $countryInfo    = self::get_csa_info($stateInfo->parent_id);
                $customerDetailsTxt .= ($countryInfo != null ? $countryInfo['name_' . $strLang] . ', ' : '') . $stateInfo['name_' . $strLang] . ', ' . $areaInfo['name_' . $strLang] . ',<br>';
            }
            if (!empty($orderDetails->block)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.block') . ' : </b>' . $orderDetails->block . ',';
            }
            if (!empty($orderDetails->street)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.street') . ' : </b>' . $orderDetails->street . ',';
            }
            if (!empty($orderDetails->avenue)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.avenue') . ' : </b>' . $orderDetails->avenue . ',<br>';
            }
            if (!empty($orderDetails->house)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.house') . ' : </b>' . $orderDetails->house . ',';
            }
            if (!empty($orderDetails->floor)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.floor') . ' : </b>' . $orderDetails->floor . ',';
            }
            if (!empty($orderDetails->landmark)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.landmark') . ' : </b>' . $orderDetails->landmark;
            }
            if (!empty($orderDetails->email)) {
                $customerDetailsTxt .= '<br><b>' . trans('webMessage.email') . ' : </b>' . $orderDetails->email;
            }
            if (!empty($orderDetails->mobile)) {
                $customerDetailsTxt .= '<br><b>' . trans('webMessage.mobile') . ' : </b>' . $orderDetails->mobile;
            }
            if ($orderDetails->is_express_delivery) {
                $customerDetailsTxt .= '<br><b>' . trans('webMessage.isSendByExpress') . '</b>';
            }
        }

        $customerDetails = $customerDetailsTxt;
        $resultColor = 'style="' . ($orderDetails->order_status == 'completed' ?  'color:#009900' : 'color:#FF0000') . '"';
        //invoice details
        if ($settingInfo->invoice_template == 1) {
            $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
            if (@$orderDetails->coupon_code) {
                $invoiceDetailsTxt .= '<tr><td >' . trans('webMessage.couponCode') . '</td><td >' . @$orderDetails->coupon_code . '</td></tr>';
            }
            if (!empty($orderDetails->is_paid)) {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td ' . $resultColor . '>' . strtoupper(trans('webMessage.' . $orderDetails->order_status)) . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $deliverytimetxt . '</td></tr>';
            }
            $invoiceDetailsTxt .= '</table>';
        } else {
            $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $invoiceDetailsTxt .= '<tr><td rowspan="4"><strong>' . trans('webMessage.orderid') . '</strong></td><td rowspan="4"><img src="https://barcode.tec-it.com/barcode.ashx?data=' . $orderDetails->order_id . '&code=Code128&translate-esc=on"></td><td><strong>' . trans('webMessage.paymentmethod') . '</strong></td><td>' . $orderDetails->pay_mode . '</td></tr>';
            if (@$orderDetails->coupon_code) {
                $invoiceDetailsTxt .= '<tr><td rowspan="4"><strong>' . trans('webMessage.couponCode') . '</strong></td><td rowspan="4">' . @$orderDetails->coupon_code . '</td></tr>';
            }
            if (!empty($orderDetails->is_paid)) {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.payment_status') . '</strong></td><td>' . $txtpaid . '</td></tr><tr><td><strong>' . trans('webMessage.date') . '</strong></td><td>' . $orderDetails->created_at . '</td></tr>';
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.deliverytime') . '</strong></td><td>' . $deliverytimetxt . '</td></tr>';
            }
            $invoiceDetailsTxt .= '</table>';
        }

        $invoiceDetails = $invoiceDetailsTxt;
        //list order
        $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
        $ordertxt_child = '';
        $subtotalprice = 0;
        $grandtotal = 0;
        $totalprice = 0;
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
            $unitprice = $tempOrder->unit_price;
            $subtotalprice = $unitprice * $tempOrder->quantity;
            $title = $productDetails['title_' . $strLang];

            $warrantyTxt = '';
            if (!empty($productDetails->warranty)) {
                $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                $warrantyTxt = '<br>' . $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
            }
            $color = $productDetails->attributes->where('color_id', $tempOrder->color_id ?? 0)->first();
            $optionColumn = ($sizeName . $colorName . $orderOptions .  $warrantyTxt != "") ? $sizeName . $colorName . $orderOptions .  $warrantyTxt : '--';
            $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . url('uploads/product/thumb/' . (@$color && @$color->color_image ? $color->color_image : $productDetails['image'])) . '" alt="' . $title . '" width="50"></a>
					</td>
					<td>
					    ' . $productDetails->item_code . '
					    ' . ($productDetails->sku_no ? '<br>' . trans('webMessage.sku_no') . ':' . $productDetails->sku_no : '') . '
					</td>
                    <td style="text-align:' . $align . ';">' . $title . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td>' . $optionColumn . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>';
            if (auth('admin')->user()) {
                $ordertxt_child .= '<td>';
                $inventories = (array) json_decode($tempOrder->inventory, true);
                foreach ($inventories as $inventory) {
                    $ordertxt_child .= Inventory::findOrFail($inventory['IID'])->title . ' → ' . $inventory['q'] . '<br>';
                }
                $ordertxt_child .= '</td>';
            }
            $ordertxt_child .= '
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

            $totalprice += $subtotalprice;
        }
        //order details
        $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.item_code') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.options') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    ' . (auth('admin')->user() ? '<td class="headertd">' . trans('adminMessage.inventory.inventory') . '</td>' : '') . '
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
        $orderDetailsTxt .= $ordertxt_child;
        $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
        //bundle discount
        $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
        if ($bundleDiscount > 0) {
            $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
            $totalprice = $totalprice - $bundleDiscount;
        }
        //seller discount
        if (!empty($orderDetails->seller_discount)) {
            $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
            $totalprice = $totalprice - $orderDetails->seller_discount;
        }
        //show discount if available but not free delivery
        if (!empty($orderDetails->coupon_code)) {
            $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
            $totalprice = $totalprice - $orderDetails->coupon_amount;
        }

        if (empty($orderDetails->delivery_charges)) {
            $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
        }

        if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
            $deliveryCharge = $orderDetails->delivery_charges;
            $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
            $totalprice = $totalprice + $deliveryCharge;
        }
        $orderDetailsTxt .= '<tr><td colspan="' . (auth('admin')->user() ? '7' : '6') . '" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
        $orderDetailsTxt .= '</table>';

        if ($orderDetails->linkDescription != null) {
            $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd" width="1%">#</td>
                    <td class="headertd" width="49%">' . trans('webMessage.details') . '</td>
                    <td class="headertd" width="20%">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd" width="10%">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd" width="20%">' . trans('webMessage.subtotal') . '</td>
                    </tr><tr><td align="center">1</td><td>' . $orderDetails->linkDescription . '</td><td>' . \App\Currency::default() . ' ' . number_format($orderDetails->total_amount, 3) . '</td><td align="center">1</td><td>' . \App\Currency::default() . ' ' . number_format($orderDetails->total_amount, 3) . '</td></tr>';
            $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . ' ' . number_format($orderDetails->total_amount, 3) . '</td></tr>';
            $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . ' ' . number_format($orderDetails->total_amount, 3) . '</td></tr>';
            $orderDetailsTxt .= '</table>';
        }
        $orderid = $orderDetails->order_id;
        $orderDetails = $orderDetailsTxt;

        $paymentDetails = '';
        $transDetails = self::TransDetails($orderid);
        if (!empty($transDetails->id)) {
            if (!empty($transDetails->presult) && $transDetails->presult == 'CAPTURED') {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
	    <tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . $transDetails->created_at . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . (!empty($transDetails->tranid) ? $transDetails->tranid : (!empty($transDetails->paypal_cart) ? $transDetails->paypal_cart : '')) . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $transDetails->payment_id . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td dir="ltr">' . number_format($transDetails->udf2, 3) . ' ' . \App\Currency::defaultCMS() . (!empty($transDetails->amt_dollar) ? '(' . trans('webMessage.usd') . '' . $transDetails->amt_dollar . ')' : '') . '</td>
        </tr>
      </table>';
        }
        if ($settingInfo->invoice_template == 1) {
            $bladeView = "emails.template_order_print";
        } else {
            $bladeView = "emails.template_order_print_2";
        }
        return view($bladeView, compact('settingInfo', 'customerDetails', 'invoiceDetails', 'orderDetails', 'paymentDetails', 'orderid'));
    }

    //get country
    public static function ajax_get_country_state_area_request(Request $request)
    {
        if ($request->type == 'all') {

            $gateways = Country::getGateways($request->parentid);
            $gatewaysHtml = "";
            $paytxt = '';
            foreach ($gateways as $p => $payment) {
                if ($payment == 'COD') {
                    $paytxt = trans('webMessage.payment_COD');
                } else if ($payment == 'KNET') {
                    $paytxt = trans('webMessage.payment_KNET');
                } else if ($payment == 'TPAY') {
                    $paytxt = trans('webMessage.payment_TPAY');
                } else if ($payment == 'GKNET') {
                    $paytxt = trans('webMessage.payment_GKNET');
                } else if ($payment == 'GTPAY') {
                    $paytxt = trans('webMessage.payment_GTPAY');
                } else if ($payment == 'TAH') {
                    $paytxt = trans('webMessage.payment_TAH');
                } else if ($payment == 'MF') {
                    $paytxt = trans('webMessage.payment_MF');
                } else if ($payment == 'PAYPAL') {
                    $paytxt = trans('webMessage.payment_PAYPAL');
                } else if ($payment == 'POSTKNET') {
                    $paytxt = trans('webMessage.payment_POSTKNET');
                } else if ($payment == 'CS') {
                    $paytxt = trans('webMessage.payment_CS');
                } else if ($payment == 'MasterCard') {
                    $paytxt = trans('webMessage.payment_MasterCard');
                } else if ($payment == 'Q8LINK') {
                    $paytxt = trans('webMessage.payment_Q8LINK');
                }

                $gatewaysHtml .= '<div class="col-xs-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                    <label class="radio" for="' . $payment . '"><input ' . ($p == 0 ? 'checked' : "") . ' type="radio" name="payment_method"  id="' . $payment . '"  value="' . $payment . '"><span class="outer"><span class="inner"></span></span><img src="' . url('uploads/paymenticons/' . strtolower($payment) . '.png') . '" height="30" alt="' . __('webMessage.payment_' . $payment) . '">&nbsp;' . $paytxt . '</label>
									
									</div>
                                    </div>';
            }
            return ["status" => 200, "message" => self::get_Country_areas(0, true, $request->parentid), "gateways" => $gatewaysHtml];
        }
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $country = Country::where('is_active', 1)->where('parent_id', $request->parentid)->get();
        if ($request->type == 'state') {
            $opt = '<select name="state"  class="form-control state_checkout" id="state" >';
            $opt .= '<option value="0">' . trans('webMessage.choosestate') . '</option>';
            $opt2 = '<select name="area"  class="form-control" id="area" >';
            $opt2 .= '<option value="0">' . trans('webMessage.choosearea') . '</option>';
            $opt2 .= '</select>';
        } else if ($request->type == 'area') {
            $opt = '<select name="area"  class="form-control" id="area" >';
            $opt .= '<option value="0">' . trans('webMessage.choosearea') . '</option>';
            $opt2 = "";
            if ($country->count() == 0) {
                $opt2 = '<select name="area"  class="form-control" id="area" >';
                $opt2 .= '</select>';
                return ["status" => 200, "message" => 'close-area', "area" => $opt2];
            }
        }
        if (!empty($country)) {
            foreach ($country as $countryv) {
                $opt .= '<option value="' . $countryv->id . '">' . $countryv['name_' . $strLang] . '</option>';
            }
        }
        $opt .= '</select>';
        return ["status" => 200, "message" => $opt, "area" => $opt2];
    }
    //
    public static function get_csa_info($id)
    {
        $country = Country::where('id', $id)->first();
        return $country;
    }
    //
    public static function get_country($parent)
    {
        $country = Country::where('is_active', 1)->where('parent_id', $parent)->get();
        return $country;
    }

    public static function get_country_of_area($id)
    {
        $areaSelected = Country::where('id', $id)->first();
        if ($areaSelected == null)
            return 2;
        if ($areaSelected->parent_id != 0) {
            $areaSelected = Country::where('id', $areaSelected->parent_id)->first();
        }
        if ($areaSelected->parent_id != 0) {
            $areaSelected = Country::where('id', $areaSelected->parent_id)->first();
        }
        return $areaSelected->id;
    }
    public static function get_Country_areas($id = 0, $selectChooseArea = true, $country_id = null)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        if ($id != 0 and $country_id == null) {
            $country_id = self::get_country_of_area($id);
        }
        if ($country_id == null) {
            $country_id = 2;
        }
        $stateLists = Country::where('is_active', 1)->where('parent_id', $country_id)->get();
        $optionstxt = '<option ' . ($selectChooseArea or env('SELECTED_CHOOSE_AREA', false) ? 'value="0"' : 'disabled="disabled"') . '>' . __('webMessage.choosearea') . '</option>';
        if ($settingInfo->validate_cust_area != 1)
            $optionstxt = '<option value="0">None</option>';
        $sel = '';
        if (!empty($stateLists) && count($stateLists) > 0) {
            foreach ($stateLists as $stateList) {
                $areaLists = Country::where('is_active', 1)->where('parent_id', $stateList->id)->get();
                $optionstxt .= '<optgroup label="' . Common::getLangString($stateList->name_en, $stateList->name_ar) . '">';
                if (!empty($areaLists) && count($areaLists) > 0) {
                    foreach ($areaLists as $areaList) {
                        $sel = $areaList->id == $id ? 'selected' : '';
                        $optionstxt .= '<option value="' . $areaList->id . '" ' . $sel . '>' . Common::getLangString($areaList->name_en, $areaList->name_ar) . '</option>';
                    }
                } else {
                    $sel = $stateList->id == $id ? 'selected' : '';
                    $optionstxt .= '<option value="' . $stateList->id . '" ' . $sel . '>' . Common::getLangString($stateList->name_en, $stateList->name_ar) . '</option>';
                }
                $optionstxt .= '</optgroup>';
            }
        }

        return $optionstxt;
    }


    //get delivery fee
    public static function get_delivery_charge($areaid, $order = null, $convertPrice = true)
    {
        if ($order == null)
            $order = session()->getId();
        $settingInfo = Settings::where("keyname", "setting")->first();
        $fees = round($settingInfo->flat_rate, 3);
        if (!empty($areaid)) {
            //            $areaInfo = Country::where('id', $areaid)->first();
            //            if (!empty($areaInfo->id) && !empty($areaInfo->delivery_fee)) {
            //                $fees = round($areaInfo->delivery_fee, 3);
            //            }
            $fees = round(ShipmentController::getPrice($areaid, $order, $convertPrice, Cookie::get('is_express_delivery', 0)), 3);
        }
        return $fees;
    }
    //apply coupon
    public static function ajax_apply_coupon_to_cart(Request $request)
    {
        //empty seller discount
        Cookie::queue('gb_seller_discount', 0, 0);

        $total = self::getTotalCartAmount();
        $settingInfo = Settings::where("keyname", "setting")->first();
        if (empty($request->coupon_code)) {
            return ["status" => 200, "message" => '<div class="alert-danger small">' . trans('webMessage.coupon_required') . '</div>'];
        }
        $curDate = date("Y-m-d");
        $coupon = Coupon::where('is_active', 1)
            ->where('coupon_code', $request->coupon_code)
            ->where('is_for', 'web')
            ->first();
        if (empty($coupon->id)) {
            return ["status" => 200, "message" => '<div class="alert-danger ">' . trans('webMessage.invalid_coupon_code') . '</div>'];
        }
        $tempPrice = Currency::convertTCountry($coupon->price_start);
        $coupon->price_start = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $coupon->price_start;

        $tempPrice = Currency::convertTCountry($coupon->price_end);
        $coupon->price_end = $tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $coupon->price_end;
        if (!empty($coupon->id) && strtotime($curDate) < strtotime($coupon->start_date)) {
            return ["status" => 200, "message" => '<div class="alert-danger ">' . trans('webMessage.coupon_can_be_used_from') . $coupon->start_date . '</div>'];
        }
        if (!empty($coupon->id) && strtotime($curDate) > strtotime($coupon->end_date)) {
            return ["status" => 200, "message" => '<div class="alert-danger ">' . trans('webMessage.coupon_is_expired_on') . $coupon->end_date . '</div>'];
        }
        if (!empty($coupon->id) && ($total < $coupon->price_start || $total > $coupon->price_end)) {
            return ["status" => 200, "message" => '<div class="alert-danger "> ' . trans('webMessage.coupon_can_be_apply_for_price_range') . \App\Currency::default() . ' ' . $coupon->price_start . ' - ' . \App\Currency::default() . ' ' . $coupon->price_end . '---' . $total . '</div>'];
        }
        if (!empty($coupon->id) && empty($coupon->usage_limit)) {
            return ["status" => 200, "message" => '<div class="alert-danger ">' . trans('webMessage.usage_limit_exceeded') . '</div>'];
        }
        $totalWithOurOldPrice = 0;
        $tempOrders = self::loadTempOrders();
        if (!empty($tempOrders) && count($tempOrders) > 0) {
            foreach ($tempOrders as $tempOrder) {
                $pro = Product::find($tempOrder->product_id);
                $brand = Brand::where('id', $pro->brand_id)->first();
                if ($pro != null and !($pro->old_price >  $pro->retail_price or ($brand != null and $brand->is_discount == 1 && $brand->discount > 0) or (!empty($pro->countdown_datetime) && strtotime($pro->countdown_datetime) > strtotime(date('Y-m-d'))))) {
                    $totalWithOurOldPrice += ($tempOrder->quantity * $tempOrder->unit_price);
                }
            }
        }
        $totalKD = Currency::getOriginalPrice(round($totalWithOurOldPrice, 3));
        $gb_coupon_free = 0;
        if (!empty($coupon->id) && !empty($coupon->is_free) and $totalKD > 0) {
            $domainCountry = Country::$countryInDomainModel;
            if (!(empty($coupon->is_zone_free) and in_array($domainCountry->shipment_method, ["zoneprice", "dhl"]))) {
                $gb_coupon_free = 1;
            }
        }
        $discountAmttxt = '';
        $discountAmt = 0;
        if (!empty($coupon->id) && $coupon->coupon_type == "amt") {
            $discountAmt = $coupon->coupon_value;
        } else {
            $discountAmt = round(($totalKD * $coupon->coupon_value) / 100, 3);
        }
        if ($discountAmt > $totalKD)
            $discountAmt = $totalKD;
        $tempPrice = Currency::convertTCountry($discountAmt);
        $discountAmttxt =     \App\Currency::default() . ' ' .  ($tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $discountAmt);

        if (!empty(Cookie::get('area'))) {
            $deliveryCharge = self::get_delivery_charge(Cookie::get('area'), session()->getId());
            $deliveryCharge   = $gb_coupon_free ? 0 : $deliveryCharge;
        }

        //save coupon
        $minutes = 3600;
        Cookie::queue('gb_coupon_code', $request->coupon_code, $minutes);
        Cookie::queue('gb_coupon_discount', $discountAmt, $minutes);
        Cookie::queue('gb_coupon_discount_text', $discountAmttxt, $minutes);
        Cookie::queue('gb_coupon_free',  $gb_coupon_free, $minutes);
        ///show ajax
        $deliverytxt = '';
        $totalprice  = $total;

        $deliverytxt .= '<table class="tt-shopcart-table01">';
        $deliverytxt .= '<tbody>';
        $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.subtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>';



        if (!empty($request->coupon_code) and $discountAmt > 0) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.coupon_discount')) . '</th><td><font color="#FF0000">-' . $discountAmttxt . '</font></td>
									</tr>';
            $totalprice = $totalprice - $discountAmt;
        }
        if (!empty($request->coupon_code) && $gb_coupon_free) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
                   <td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td>
                   </tr>';
        }
        if (!empty($deliveryCharge) && !$gb_coupon_free) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
				   <td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td>
				   </tr>';
            $totalprice = $totalprice + $deliveryCharge;
        } elseif (empty($coupon->is_free)) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th><td> -- </td></tr>';
        }
        $deliverytxt .= '</tbody>';
        $deliverytxt .= '<tfoot>
                   <tr>
                   <th>' . strtoupper(trans('webMessage.grandtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>
                   </tfoot>';
        $deliverytxt .= '</table>';
        $deliverytxt .= '<a href="' . url(app()->getLocale() . '/checkout') . '" class="btn btn-lg"><span class="icon icon-check_circle"></span>' . strtoupper(__('webMessage.proceedtocheckout')) . '</a>';

        return ["status" => 200, "message" => '<div class="alert-success ">' . trans('webMessage.yourdiscountis') . $discountAmttxt . '</div>', 'cartbox' => $deliverytxt];
    }

    //apply seller coupon
    public static function ajax_apply_seller_discount_to_cart(Request $request)
    {
        $total = self::getTotalCartAmount();
        $settingInfo = Settings::where("keyname", "setting")->first();
        $curdate = date("Y-m-d");
        if (empty($request->delivery_date)) {
            return ["status" => 200, "message" => '<div class="alert-danger small">' . trans('webMessage.delivery_date_required') . '</div>'];
        }
        if (!empty($request->delivery_date) && strtotime($request->delivery_date) < strtotime($curdate)) {
            return ["status" => 200, "message" => '<div class="alert-danger small">' . trans('webMessage.delivery_date_invalid') . '</div>'];
        }

        if (empty($request->seller_discount)) {
            return ["status" => 200, "message" => '<div class="alert-danger small">' . trans('webMessage.seller_discount_required') . '</div>'];
        }
        if (!empty($request->seller_discount) && $request->seller_discount >= $total) {
            return ["status" => 200, "message" => '<div class="alert-danger small">' . trans('webMessage.invalid_seller_discount_price') . '</div>'];
        }


        //save coupon
        $minutes = 3600;
        Cookie::queue('gb_delivery_date', $request->delivery_date, $minutes);
        Cookie::queue('gb_seller_discount', (float)$request->seller_discount, $minutes);

        ///show ajax
        $deliverytxt = '';
        $totalprice  = $total;

        $deliverytxt .= '<table class="tt-shopcart-table01">';
        $deliverytxt .= '<tbody>';
        $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.subtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>';

        if (!empty($request->seller_discount)) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.seller_discount')) . '</th><td><font color="#FF0000">-' . \App\Currency::default() . '' . (float)$request->seller_discount . '</font></td>
									</tr>';
            $totalprice = $totalprice - (float)$request->seller_discount;
        }
        $deliveryCharge = 0;
        if (!empty(Cookie::get('area'))) {
            $deliveryCharge = self::get_delivery_charge(Cookie::get('area'), session()->getId());
            $deliveryCharge   = !empty(Cookie::get('gb_coupon_free')) ? 0 : $deliveryCharge;
        }

        if (!empty($deliveryCharge)) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
				   <td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td>
				   </tr>';
            $totalprice = $totalprice + $deliveryCharge;
        }
        $deliverytxt .= '</tbody>';
        $deliverytxt .= '<tfoot>
                   <tr>
                   <th>' . strtoupper(trans('webMessage.grandtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>
                   </tfoot>';
        $deliverytxt .= '</table>';


        return ["status" => 200, "message" => '<div class="alert-success ">' . trans('webMessage.discountapplied') . '</div>', 'cartbox' => $deliverytxt];
    }
    //get customer address
    public static function customerAddress()
    {
        $listaddress = [];
        if (!empty(Auth::guard('webs')->user()->id)) {
            $listaddress = CustomersAddress::where('customer_id', Auth::guard('webs')->user()->id)->get();
        }
        return $listaddress;
    }
    public static function ajax_get_customer_address(Request $request)
    {
        if (!empty($request->id)) {
            $minutes = 3600;
            $address =     CustomersAddress::where('id', $request->id)->first();
            if (!empty($address->id)) {
                Cookie::queue('address_id', $address->id, $minutes);
            }
            if (!empty($address->country_id)) {
                Cookie::queue('country', $address->country_id, $minutes);
            }
            if (!empty($address->state_id)) {
                Cookie::queue('state', $address->state_id, $minutes);
            }
            if (!empty($address->area_id)) {
                Cookie::queue('area', $address->area_id, $minutes);
            }
            if (!empty($address->block)) {
                Cookie::queue('block', $address->block, $minutes);
            }
            if (!empty($address->street)) {
                Cookie::queue('street', $address->street, $minutes);
            }
            if (!empty($address->avenue)) {
                Cookie::queue('avenue', $address->avenue, $minutes);
            }
            if (!empty($address->house)) {
                Cookie::queue('house', $address->house, $minutes);
            }
            if (!empty($address->floor)) {
                Cookie::queue('floor', $address->floor, $minutes);
            }
        }
        return ["status" => 200, "message" => ''];
    }
    //my orders
    public function viewmyorders(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        //check search queries
        if (!empty($request->get('q'))) {
            $q = $request->get('q');
        } else {
            $q = $request->q;
        }

        if (!empty($q)) {
            $myorderLists = OrdersDetails::where('is_removed', 0);

            if (!empty(Auth::guard('webs')->user()->id) && empty(Auth::guard('webs')->user()->is_seller)) {
                $myorderLists = $myorderLists->where('customer_id', Auth::guard('webs')->user()->id);
            }

            $myorderLists = $myorderLists->where(function ($sq) use ($q) {
                $sq->where('order_id', '=', $q)
                    ->orWhere('name', 'LIKE', '%' . $q . '%')
                    ->orWhere('email', 'LIKE', '%' . $q . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $q . '%')
                    ->orWhere('order_status', 'LIKE', '%' . $q . '%');
            });
            if ($request->filter_date) {
                $myorderLists = $myorderLists->where('delivery_date', $request->filter_date);
            }
            $myorderLists = $myorderLists->orderBy('created_at', 'DESC')
                ->paginate($settingInfo->item_per_page_back);

            $myorderLists->appends(['q' => $q]);
        } else {
            $myorderLists = OrdersDetails::where('is_removed', 0);
            if (!empty(Auth::guard('webs')->user()->id) && empty(Auth::guard('webs')->user()->is_seller)) {
                $myorderLists = $myorderLists->where('customer_id', Auth::guard('webs')->user()->id);
            }
            if ($request->filter_date) {
                $myorderLists = $myorderLists->where('delivery_date', $request->filter_date);
            }
            $myorderLists = $myorderLists->orderBy('created_at', 'DESC')->paginate($settingInfo->item_per_page_back);
        }
        return view('website.myorders', compact('myorderLists'));
    }
    //get my order details
    //show order result page
    public function myorderdetails(Request $request)
    {
        $orderDetails = OrdersDetails::where('order_id', $request->orderid)->where('customer_id', Auth::guard('webs')->user()->id)->where('is_removed', 0)->first();
        $orderLists = Orders::where('order_id', $request->orderid)->get();
        $trackLists = [];
        if (!empty($orderDetails->id)) {
            $trackLists = OrdersTrack::where('oid', $orderDetails->id)->orderBy('display_order', 'DESC')->get();
        }
        return view('website.orderdetails', compact('orderDetails', 'orderLists', 'trackLists'));
    }
    //get my order properties
    public static function getMyOrdersProperties($id)
    {
        $totalAmt = 0;
        $orderDetails = OrdersDetails::Where('id', $id)->first();
        $listOrders   = Orders::where('oid', $id)->get();
        if (!empty($listOrders) && count($listOrders) > 0) {
            foreach ($listOrders as $listOrder) {
                $totalAmt += ($listOrder->quantity * $listOrder->unit_price);
            }
            //apply coupon if its not free
            if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                $totalAmt = $totalAmt - $orderDetails->coupon_amount;
            }
            //seller discount
            if (!empty($orderDetails->seller_discount)) {
                $totalAmt = $totalAmt - $orderDetails->seller_discount;
            }
            //apply delivery charges if coupon is empty
            if (empty($orderDetails->coupon_free)) {
                $totalAmt = $totalAmt + $orderDetails->delivery_charges;
            }
        }

        return  ['totalAmt' => $totalAmt, 'totalAmt_dollar' => $orderDetails->total_amount_dollar];
    }

    //rollbacked qty
    public static function rollbackedQuantity($product_id, $quantity, $size_id = 0, $color_id = 0, $inventories = "")
    {
        $inventories = json_decode($inventories, true);
        $productDetails   = Product::where('id', $product_id)->first();
        if (empty($productDetails['is_attribute'])) {
            foreach ($inventories as $inventory) {
                $productQuantity = $productDetails->getQuantity($inventory['IID'], null, null, false, false, true);
                if ($productQuantity->is_qty_deduct == 1) {
                    $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                    $productQuantity->save();
                }
            }
        } else {
            if (!empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
                foreach ($inventories as $inventory) {
                    $productQuantities = $productDetails->getQuantity($inventory['IID'], $attributes->pluck('id')->toArray(), null, true, false, true);
                    foreach ($productQuantities as $productQuantity) {
                        if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                            $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                            $productQuantity->save();
                        }
                    }
                }
            } else if (!empty($size_id) && empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
                foreach ($inventories as $inventory) {
                    $productQuantities = $productDetails->getQuantity($inventory['IID'], $attributes->pluck('id')->toArray(), null, true, false, true);
                    foreach ($productQuantities as $productQuantity) {
                        if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                            $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                            $productQuantity->save();
                        }
                    }
                }
            } else if (empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
                foreach ($inventories as $inventory) {
                    $productQuantities = $productDetails->getQuantity($inventory['IID'], $attributes->pluck('id')->toArray(), null, true, false, true);
                    foreach ($productQuantities as $productQuantity) {
                        if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                            $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                            $productQuantity->save();
                        }
                    }
                }
            }
        }
        //change qty to product table for attribute
        //		self::ChangeUpdateQuantity($product_id);
        //end
    }

    public static function ajax_remove_my_order(Request $request)
    {
        if (empty($request->id)) {
            return ["status" => 200, "message" => '<div class="alert-danger">' . trans('webMessage.idmissing') . '</div>'];
        }
        $orderDetails = OrdersDetails::where('id', $request->id)->where('customer_id', Auth::guard('webs')->user()->id)->first();
        if (empty($orderDetails->id)) {
            return ["status" => 200, "message" => '<div class="alert-danger">' . trans('webMessage.norecordfound') . '</div>'];
        }
        $listOrders   = Orders::where('oid', $orderDetails->id)->get();
        if (!empty($listOrders) && count($listOrders) > 0) {
            foreach ($listOrders as $listOrder) {
                //option
                $OrderOptions = OrdersOption::where("oid", $listOrder->id)->get();
                if (!empty($OrderOptions) && count($OrderOptions) > 0) {
                    foreach ($OrderOptions as $OrderOption) {
                        self::changeOptionQuantity($OrderOption->product_id, 'a', $OrderOption->option_child_ids, $listOrder->quantity, $listOrder->inventory); //deduct qty
                    }
                }
                //end option
                self::rollbackedQuantity($listOrder->product_id, $listOrder->quantity, $listOrder->size_id, $listOrder->color_id, $listOrder->inventory);
            }
        }
        $orderDetails->is_qty_rollbacked = 1;
        $orderDetails->is_removed = 1;
        $orderDetails->save();
        return ["status" => 200, "message" => '<div class="alert-success">' . trans('webMessage.orderremoved') . '</div>'];
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
    //knet response
    public function getKnetResponse(Request $request)
    {
        if ($request->trackid) {
            if (!empty(app()->getLocale())) {
                $strLang = app()->getLocale();
            } else {
                $strLang = "en";
            }
            $settingInfo = Settings::where("keyname", "setting")->first();

            $transactionDetails = Transaction::where('id', $request->trackid)->first();
            if ($transactionDetails->id) {
                $orderDetails = OrdersDetails::where('order_id', $transactionDetails->trackid)->first();
                if ($orderDetails->id) {
                    if ($request->presult == 'CAPTURED') {
                        $orderDetails->is_paid = 1;
                        $orderDetails->save();
                    }
                    //update trans
                    $transactionDetails->presult = $request->presult;
                    if ($request->payment_id) {
                        $transactionDetails->payment_id = $request->payment_id;
                    }
                    if ($request->ref) {
                        $transactionDetails->ref = $request->ref;
                    }
                    if ($request->tranid) {
                        $transactionDetails->tranid = $request->tranid;
                    }
                    if ($request->auth) {
                        $transactionDetails->auth = $request->auth;
                    }
                    if ($request->amount) {
                        $transactionDetails->amt = $request->amount;
                    }
                    if ($request->PayType) {
                        $transactionDetails->PayType = $request->PayType;
                    }
                    $transactionDetails->save();

                    $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                    if (!empty($orderDetails->name)) {
                        $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                    }

                    if (!empty($orderDetails->area_id)) {
                        $areaInfo    = self::get_csa_info($orderDetails->area_id);
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                    }
                    if (!empty($orderDetails->block)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                    }
                    if (!empty($orderDetails->street)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                    }
                    if (!empty($orderDetails->avenue)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                    }
                    if (!empty($orderDetails->house)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                    }
                    if (!empty($orderDetails->floor)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                    }
                    if (!empty($orderDetails->landmark)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                    }

                    if (!empty($orderDetails->email)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                    }
                    if (!empty($orderDetails->mobile)) {
                        $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                    }

                    $customerDetailsTxt .= '</table>';

                    //invoice details
                    $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                    if (!empty($request->presult) && $request->presult == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    }
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                    if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                    } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                        $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                    }

                    $invoiceDetailsTxt .= '</table>';

                    //list order
                    $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                    $ordertxt_child = '';
                    $subtotalprice = 0;
                    $grandtotal = 0;
                    $totalprice = 0;
                    foreach ($tempOrders as $tempOrder) {
                        $productDetails = self::getProductDetails($tempOrder->product_id);
                        if ($productDetails->image) {
                            $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                        } else {
                            $prodImage = url('uploads/no-image.png');
                        }
                        if (!empty($tempOrder->size_id)) {
                            $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                            $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                        } else {
                            $sizeName = '';
                        }
                        if (!empty($tempOrder->color_id)) {
                            $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                            $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                            //color image
                            $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                            if (!empty($colorImageDetails->color_image)) {
                                $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                            }
                        } else {
                            $colorName = '';
                        }
                        $orderOptions = self::getOptionsDtailsOrderBr($tempOrder->id);
                        $unitprice = $tempOrder->unit_price;
                        $subtotalprice = $unitprice * $tempOrder->quantity;
                        $title = $productDetails['title_' . $strLang];

                        $warrantyTxt = '';
                        if (!empty($productDetails->warranty)) {
                            $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                            $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                        }


                        $ordertxt_child .= '<tr>
                        <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
                        </td>
                        <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                        <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                        <td align="center">' . $tempOrder->quantity . '</td>
                        <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                        </tr>';

                        $totalprice += $subtotalprice;
                    }
                    //order details
                    $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                        <tr>
                        <td class="headertd">' . trans('webMessage.image') . '</td>
                        <td class="headertd">' . trans('webMessage.details') . '</td>
                        <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                        <td class="headertd">' . trans('webMessage.quantity') . '</td>
                        <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                        </tr>';
                    $orderDetailsTxt .= $ordertxt_child;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                    //bundle discount
                    $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                    if ($bundleDiscount > 0) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                        $totalprice = $totalprice - $bundleDiscount;
                    }
                    //seller discount
                    if (!empty($orderDetails->seller_discount)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->seller_discount;
                    }
                    //show discount if available but not free delivery
                    if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                        $totalprice = $totalprice - $orderDetails->coupon_amount;
                    }
                    if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                    }

                    if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                        $deliveryCharge = $orderDetails->delivery_charges;
                        $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                        $totalprice = $totalprice + $deliveryCharge;
                    }
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                    $orderDetailsTxt .= '</table>';

                    //payment temp
                    $paymentDetails = '';
                    if (!empty($request->presult) && $request->presult == 'CAPTURED') {
                        $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                    } else {
                        $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                        $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                    }
                    $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
            <tr>
              <td>' . trans('webMessage.result') . '</td>
              <td>' . $txtpaid . '</td>
            </tr>
            <tr>
              <td>' . trans('webMessage.date') . '</td>
              <td>' . date('Y-m-d H:i:s') . '</td>
            </tr>
            <tr>
              <td>' . trans('webMessage.transid') . '</td>
              <td>' . $request->tranid . '</td>
            </tr>
            <tr>
              <td>' . trans('webMessage.paymentid') . '</td>
              <td>' . $request->payment_id . '</td>
            </tr>
            <tr>
              <td>' . trans('webMessage.amount') . '</td>
              <td>' . number_format($request->amount, 3) . ' ' . \App\Currency::defaultCMS() . '</td>
            </tr>
          </table>';
                    $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                                'paymentDetails'  => $paymentDetails,
                                'trackYourOrder'  => $trackYourOrderTxt,
                                'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                            'email_from'      => $settingInfo->from_email,
                            'email_from_name' => $settingInfo->from_name
                        ];
                        Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                    }
                    if (!empty($request->presult) && $request->presult == 'CAPTURED') {

                        //send sms notification for cod
                        $isValidMobile = Common::checkMobile($orderDetails->mobile);
                        if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                            if ($orderDetails->strLang == "en") {
                                $smsMessage = $settingInfo->sms_text_knet_en;
                            } else {
                                $smsMessage = $settingInfo->sms_text_knet_ar;
                            }
                            $to      = $orderDetails->mobile;
                            $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                            Common::SendSms($to, $sms_msg);
                        }
                        //end sending sms for cod


                        return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg', $knetMessage);
                    } else {
                        return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg_error', $knetMessage);
                    }
                } else { //order exist or not
                    return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
                }
            } else { //order exist or not
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
            }
        } else { //track id not empty
            return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
        }
    }

    //get trans details
    public static function TransDetails($orderid)
    {
        $transactionDetails = Transaction::where('trackid', $orderid)->orderByDesc('id')->first();
        return $transactionDetails;
    }
    //get delivery charges
    public static function getCheckoutDelivery($areaid)
    {
        $deliverytxt = '';
        if (!empty($areaid)) {
            $deliveryCharge = self::get_delivery_charge($areaid, session()->getId());
            $deliveryCharge   = !empty(Cookie::get('gb_coupon_free')) ? 0 : $deliveryCharge;

            $deliverytxt .= '<table class="tt-shopcart-table01">';
            $deliverytxt .= '<tbody>';
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.subtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>';
            if (!empty(Cookie::get('gb_coupon_code')) &&  floatval(preg_replace('/[^0-9\/.\-]/', '', Cookie::get('gb_coupon_discount_text'))) > 0) {
                $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.coupon_discount')) . '</th><td><font color="#FF0000">-' . Cookie::get('gb_coupon_discount_text') . '</font></td>
									</tr>';
                $totalprice = $totalprice - Cookie::get('gb_coupon_discount');
            }
            if (!empty(Cookie::get('gb_coupon_code')) && !empty(Cookie::get('gb_coupon_free'))) {
                $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
                   <td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td>
                   </tr>';
            } elseif (!empty($deliveryCharge) && empty(Cookie::get('gb_coupon_free'))) {
                $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
				   <td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td>
				   </tr>';
                $totalprice = $totalprice + $deliveryCharge;
            } else {
                $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th><td> -- </td></tr>';
            }
            $deliverytxt .= '</tbody>';
            $deliverytxt .= '<tfoot>
                   <tr>
                   <th>' . strtoupper(trans('webMessage.grandtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>
                   </tfoot>';
            $deliverytxt .= '</table>';

            return $deliverytxt;
        } else {
            return $deliverytxt;
        }
    }

    //ajax delivery
    public static function ajax_get_area_delivery(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        $totalprice = $request->totalprice;
        $deliverytxt = '<table class="tt-shopcart-table01">';
        $deliverytxt .= '<tbody>';
        $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.subtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>';

        if ($request->bundleDiscount > 0) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.bundles.BundleDiscount')) . '</th><td><font color="#FF0000">-' . $request->bundleDiscount . ' ' . \App\Currency::default() . '</font></td>
									</tr>';
        }

        if (!empty(Cookie::get('gb_seller_discount'))) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.seller_discount')) . '</th><td><font color="#FF0000">-' . \App\Currency::default() . '' . Cookie::get('gb_seller_discount') . '</font></td>
									</tr>';
            $totalprice = $totalprice - Cookie::get('gb_seller_discount');
        }

        if (!empty(Cookie::get('gb_coupon_code'))  &&  floatval(preg_replace('/[^0-9\/.\-]/', '', Cookie::get('gb_coupon_discount_text'))) > 0) {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.coupon_discount')) . '</th><td><font color="#FF0000">-' . Cookie::get('gb_coupon_discount_text') . '</font></td>
									</tr>';
            $totalprice = $totalprice - Cookie::get('gb_coupon_discount');
        }
        if (!empty($request->areaid) and $request->areaid != "Choose Area") {

            if (!empty($request->stateid)) {
                Cookie::queue('area', $request->areaid, 3600);
            }
            if (!empty($request->stateid)) {
                Cookie::queue('state', $request->stateid, 3600);
            }
            if (!empty($request->stateid)) {
                Cookie::queue('country', $request->countryid, 3600);
            }

            $deliveryCharge   = self::get_delivery_charge($request->areaid, session()->getId());
            $deliveryCharge   = !empty(Cookie::get('gb_coupon_free')) ? 0 : $deliveryCharge;



            if (!empty($settingInfo->is_free_delivery) && $totalprice >= $settingInfo->free_delivery_amount) {
                $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
                   <td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td>
                   </tr>';
            } else {
                if (!empty(Cookie::get('gb_coupon_code')) && !empty(Cookie::get('gb_coupon_free'))) {
                    $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
                   <td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td>
                   </tr>';
                } elseif (!empty($deliveryCharge) && empty(Cookie::get('gb_coupon_free'))) {
                    $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th>
				   <td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td>
				   </tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                } else {
                    $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th><td> -- </td></tr>';
                }
            }
        } else {
            $deliverytxt .= '<tr><th>' . strtoupper(trans('webMessage.delivery_charge')) . '</th><td> -- </td></tr>';
        }
        $deliverytxt .= '</tbody>';
        $deliverytxt .= '<tfoot>
                   <tr>
                   <th>' . strtoupper(trans('webMessage.grandtotal')) . '</th>
                   <td>' . \App\Currency::default() . ' <span class="total_result">' . $totalprice . '</span></td>
                   </tr>
                   </tfoot>';
        $deliverytxt .= '</table>';

        if (!empty($request->t) && $request->t == 'cart') {
            $deliverytxt .= '<a href="' . url(app()->getLocale() . '/checkout') . '" class="btn btn-lg"><span class="icon icon-check_circle"></span>' . strtoupper(__('webMessage.proceedtocheckout')) . '</a>';
        }
        return ['status' => 200, 'message' => $deliverytxt];
    }

    //ajax delivery
    public static function ajax_is_express_delivery(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        if ($settingInfo->is_express)
            Cookie::queue('is_express_delivery', (bool) $request->is_express_delivery, 3600);
        return ['status' => 200, 'message' => ""];
    }


    //get track order id
    public static function ajax_get_track_orderid(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        if (empty($request->orderid)) {
            return ["status" => 400, "message" => "<div class='alert-danger'>" . trans('webMessage.idmissing') . "</div>"];
        }
        $orderdetails = OrdersDetails::where('order_id', $request->orderid)->first();
        if (!empty($orderdetails->id)) {
            return ["status" => 200, "message" => '', 'url' => app()->getLocale() . '/order-details/' . $orderdetails->order_id_md5];
        } else {
            return ["status" => 400, "message" => "<div class='alert-danger'>" . trans('webMessage.norecordfound') . "</div>"];
        }
    }

    public static function ChangeUpdateQuantity($product_id)
    {
        //		$qty = 0;
        //		$productUpdate   = Product::where('id', $product_id)->first();
        //		if (!empty($productUpdate->is_attribute)) {
        //			$qty   = ProductAttribute::where('product_id', $productUpdate->id)->get()->sum('quantity');
        //			$productUpdate->quantity = $qty;
        //			$productUpdate->save();
        //		}
    }
    //get related items

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

    ///option sectio start /////////////////////////////////
    public static function getCustomOptions($custom_option_id, $product_id)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $customOptionDetails = ProductOptionsCustom::where('id', $custom_option_id)->first();
        //        DB::enableQueryLog();
        $customOptionChilds  = ProductOptions::where('gwc_products_options.product_id', $product_id)
            ->where('gwc_products_quantity.quantity', '>', 0)
            ->where('gwc_inventories.is_active', 1)
            ->where('gwc_products_options.custom_option_id', $custom_option_id);
        $customOptionChilds  = $customOptionChilds->select(
            'gwc_products_option_custom_chosen.custom_option_id',
            'gwc_products_option_custom_chosen.product_id',
            'gwc_products_option_custom_chosen.is_required',
            'gwc_products_option_custom_child.*',
            'gwc_products_options.*'

        );
        $customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_child', 'gwc_products_option_custom_child.id', '=', 'gwc_products_options.option_value_id');
        $customOptionChilds  = $customOptionChilds->join('gwc_products_quantity', function ($join) {
            $join->on('gwc_products_quantity.option_id', '=', 'gwc_products_options.id')
                ->On('gwc_products_quantity.product_id', '=', 'gwc_products_options.product_id')
                //                ->where('gwc_products_quantity.is_qty_deduct', '=', 1)
                ->where('gwc_products_quantity.quantity', '>', 0);
        });
        $customOptionChilds  = $customOptionChilds->join('gwc_inventories', 'gwc_products_quantity.inventory_id', '=', 'gwc_inventories.id');

        $customOptionChilds  = $customOptionChilds->join('gwc_products_option_custom_chosen', ['gwc_products_option_custom_chosen.product_id' => 'gwc_products_options.product_id', 'gwc_products_option_custom_chosen.custom_option_id' => 'gwc_products_options.custom_option_id']);

        $customOptionChilds = $customOptionChilds->groupBy('gwc_products_option_custom_child.id');

        //        $customOptionChilds = $customOptionChilds->limit(1);
        $customOptionChilds  = $customOptionChilds->get();

        //        $querys = DB::getQueryLog();
        //        dd($querys);

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


    ///get option price
    public function ajax_get_option_price(Request $request)
    {
        if (empty($request->ids)) {
            return ["status" => 400, "message" => trans('webMessage.idmissing')];
        }
        if (empty($request->unit_price)) {
            return ["status" => 400, "message" => trans('webMessage.pricemissing')];
        }

        $minutes = 3600;

        if (!empty(Cookie::get('unit_price'))) {
            $unitprice = round(Cookie::get('unit_price'), 3);
        } else {
            $unitprice = round($request->unit_price, 3);
        }



        $explodeids         = explode("-", $request->ids);
        $customOptionChilds = ProductOptions::where('id', $explodeids[3])->first();
        $radioCookie        = $request->ids;

        if (empty(Cookie::get($radioCookie))) {
            if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 1) {
                $unitprice = round(($customOptionChilds->retail_price + $unitprice), 3);
                Cookie::queue($radioCookie, $customOptionChilds->retail_price, $minutes);
                Cookie::queue("unit_price", $unitprice, $minutes);
            } else if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 2) {
                $unitprice = round(($unitprice - $customOptionChilds->retail_price), 3);
                Cookie::queue($radioCookie, $customOptionChilds->retail_price, $minutes);
                Cookie::queue("unit_price", $unitprice, $minutes);
            }

            $unitprice = self::deductoptionprice($request->ids, $unitprice);
        }

        return ["status" => 200, "message" => $unitprice, "otherPrice" => \App\Currency::table($unitprice)];
    }

    //deduct previous
    public static function deductoptionprice($ids, $unitprice)
    {
        $minutes = 3600;
        //update other option if exist in cookies
        $explodeids  = explode("-", $ids);

        $customOptionChildsOthers  = ProductOptions::where('product_id', $explodeids[1])
            ->where('custom_option_id', $explodeids[2])
            ->where('id', '!=', $explodeids[3])
            ->get();



        if (!empty($customOptionChildsOthers) && count($customOptionChildsOthers) > 0) {
            foreach ($customOptionChildsOthers as $customOptionChildsOther) {
                $radioCookie = "option-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookie))) {
                    if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 1) {
                        $unitprice = round(($unitprice - Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    } else if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 2) {
                        $unitprice = round(($unitprice + Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    }
                    Cookie::queue($radioCookie, 0, 0);
                }
            }
        }

        return $unitprice;
        //exist end
    }

    //get select option price
    public function ajax_get_option_select_price(Request $request)
    {
        if (empty($request->val)) {
            $unitprice = self::removeEmptySelectedOption($request);
            return ["status" => 200, "message" => $unitprice, "otherPrice" => \App\Currency::table($unitprice)];
        }
        if (empty($request->unit_price)) {
            return ["status" => 400, "message" => trans('webMessage.pricemissing')];
        }


        $minutes = 3600;
        if (!empty(Cookie::get('unit_price'))) {
            $unitprice = round(Cookie::get('unit_price'), 3);
        } else {
            $unitprice = round($request->unit_price, 3);
        }


        $explodeids  = explode("-", $request->val);
        $radioCookie = "select-" . $explodeids[1] . "-" . $explodeids[2] . "-" . $explodeids[3];

        $customOptionChilds  = ProductOptions::where('id', $explodeids[3])->first();
        if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 1) {
            $unitprice = round(($unitprice + $customOptionChilds->retail_price), 3);

            Cookie::queue("unit_price", $unitprice, $minutes);
            Cookie::queue($radioCookie, $customOptionChilds->retail_price, $minutes); //store in option
        } else if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 2) {
            $unitprice = round(($unitprice - $customOptionChilds->retail_price), 3);
            Cookie::queue("unit_price", $unitprice, $minutes);
            Cookie::queue($radioCookie, $customOptionChilds->retail_price, $minutes); //store in option
        }

        $unitprice = self::deductselectedprice($radioCookie, $unitprice);


        $quantity = 0;
        $productDetails   = Product::where('id', $explodeids[1])->first();
        $Option = ProductOptions::find($customOptionChilds->id);
        $Options = ProductOptions::where("custom_option_id", $Option->custom_option_id)->where("option_value_id", $Option->option_value_id)->where("is_active", 1)->get();
        foreach ($Options as $option) {
            $productQuantities = $productDetails->getQuantity(-1, null, $option->id, true);
            foreach ($productQuantities as $productQuantity) {
                if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                    $quantity = $quantity + $productQuantity->quantity;
                }
            }
        }


        return ["status" => 200, "message" => $unitprice, "quantity" => $quantity, "otherPrice" => \App\Currency::table($unitprice)];
    }

    ///deduct selected price
    public static function deductselectedprice($radioCookie, $unitprice)
    {
        $minutes = 3600;
        //update other option if exist in cookies
        $explodeids  = explode("-", $radioCookie);

        $customOptionChildsOthers  = ProductOptions::where('product_id', $explodeids[1])
            ->where('custom_option_id', $explodeids[2])
            ->where('id', '!=', $explodeids[3])
            ->get();



        if (!empty($customOptionChildsOthers) && count($customOptionChildsOthers) > 0) {
            foreach ($customOptionChildsOthers as $customOptionChildsOther) {
                $radioCookie = "select-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookie))) {
                    if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 1) {
                        $unitprice = round(($unitprice - Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    } else if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 2) {
                        $unitprice = round(($unitprice + Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    }
                    Cookie::queue($radioCookie, 0, 0);
                }
            }
        }

        return $unitprice;
        //exist end
    }

    //remove all select opton if its empty
    public static function removeEmptySelectedOption($request)
    {
        $minutes = 3600;
        if (!empty(Cookie::get('unit_price'))) {
            $unitprice = round(Cookie::get('unit_price'), 3);
        } else {
            $unitprice = round($request->unit_price, 3);
        }
        //update other option if exist in cookies
        $explodeids  = explode("-", $request->ids);

        $customOptionChildsOthers  = ProductOptions::where('product_id', $explodeids[1])
            ->where('custom_option_id', $explodeids[2])
            ->get();



        if (!empty($customOptionChildsOthers) && count($customOptionChildsOthers) > 0) {
            foreach ($customOptionChildsOthers as $customOptionChildsOther) {
                $radioCookie = "select-" . $customOptionChildsOther->product_id . "-" . $customOptionChildsOther->custom_option_id . "-" . $customOptionChildsOther->id;
                if (!empty(Cookie::get($radioCookie))) {
                    if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 1) {
                        $unitprice = round(($unitprice - Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    } else if (!empty($customOptionChildsOther->is_price_add) && $customOptionChildsOther->is_price_add == 2) {
                        $unitprice = round(($unitprice + Cookie::get($radioCookie)), 3);
                        Cookie::queue("unit_price", $unitprice, $minutes);
                    }
                    Cookie::queue($radioCookie, 0, 0);
                }
            }
        }
        return $unitprice;
    }



    //get check box option
    public function ajax_get_option_check_price(Request $request)
    {
        if (empty($request->ids)) {
            return ["status" => 400, "message" => trans('webMessage.idmissing')];
        }
        if (empty($request->unit_price)) {
            return ["status" => 400, "message" => trans('webMessage.pricemissing')];
        }


        $minutes = 3600;
        if (!empty(Cookie::get('unit_price'))) {
            $unitprice = round(Cookie::get('unit_price'), 3);
        } else {
            $unitprice = round($request->unit_price, 3);
        }

        $explodeids  = explode("-", $request->ids);
        $radioCookie = "checkbox-" . $explodeids[1] . "-" . $explodeids[2] . "-" . $explodeids[3];
        $customOptionChilds  = ProductOptions::where('id', $explodeids[3])->first();
        if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 1) {
            if (!empty($request->isChecked)) {
                $unitprice = round(($unitprice + $customOptionChilds->retail_price), 3);
            } else {
                $unitprice = round(($unitprice - $customOptionChilds->retail_price), 3);
            }
            Cookie::queue("unit_price", $unitprice, $minutes);
        } else if (!empty($customOptionChilds->is_price_add) && $customOptionChilds->is_price_add == 2) {
            if (!empty($request->isChecked)) {
                $unitprice = round(($unitprice - $customOptionChilds->retail_price), 3);
            } else {
                $unitprice = round(($unitprice + $customOptionChilds->retail_price), 3);
            }
            Cookie::queue("unit_price", $unitprice, $minutes);
        }

        Cookie::queue($radioCookie, 0, 0);


        return ["status" => 200, "message" => $unitprice, "otherPrice" => \App\Currency::table($unitprice)];
    }

    ///get price by size
    public function ajax_details_getPrice_BySize(Request $request)
    {
        $productDetails   = Product::where('id', $request->product_id)->first();
        $Attributes = ProductAttribute::where('product_id', $request->product_id)->where('size_id', $request->size_id)->first();
        $AllAttributes = ProductAttribute::where('product_id', $request->product_id)->where('size_id', $request->size_id)->get();
        $sizeObject = Size::find($request->size_id);
        $sizeDetails = $sizeObject == null ? null : $sizeObject['details_' . (app()->getLocale())];


        if (!empty($Attributes['retail_price'])) {
            $price     = $Attributes['retail_price'];
            $old_price = $Attributes['old_price'] <> 0 ? $Attributes['old_price'] : '0';
        } else {
            $price     = $productDetails['retail_price'];
            $old_price = $productDetails['old_price'] <> 0 ? $productDetails['old_price'] : '0';
        }

        if (!empty($productDetails->countdown_datetime) && strtotime($productDetails->countdown_datetime) > strtotime(date('Y-m-d'))) {
            $old_price = round($price, 3);
            $price     = round($productDetails->countdown_price, 3);
        }

        Cookie::queue("unit_price", $price, 3600);
        //remove all option from cookies
        self::removeAllCookiesOptions($request->product_id);
        $quantity = 0;
        foreach ($AllAttributes as $allAttribute) {
            $tempQ = $productDetails->getQuantity(-1, $allAttribute->id);
            if ($tempQ != null)
                $quantity += $tempQ->quantity;
        }

        return ["status" => 200, "details" => $sizeDetails, "message" => $price, "otherPrice" => \App\Currency::table($price), "old_price" => $old_price, "quantity" => $quantity];
    }



    //get warranty
    public static function getWarrantyDetails($id)
    {
        $w = Warranty::where('id', $id)->first();
        return $w;
    }

    //////////////////////////////////////////Dez Order Api ////////////////////////////////////////
    public function getDezOrders(Request $request)
    {
        $curDate = date("Y-m-d");
        $ordersDetailsApi = [];
        $ordersDetails = OrdersDetails::where("delivery_date", $curDate)->whereIn("order_status", ['pending', 'received'])->where("is_for_dezorder", 1)->get();
        if (!empty($ordersDetails) && count($ordersDetails) > 0) {
            foreach ($ordersDetails as $ordersDetail) {
                $ordersDetailsApi[] = [
                    "id"          => $ordersDetail->id,
                    "order_id"    => $ordersDetail->order_id,
                    "name"        => $ordersDetail->name,
                    "email"       => $ordersDetail->email,
                    "mobile"      => $ordersDetail->mobile,
                    "country_id"  => $ordersDetail->country_id,
                    "state_id"    => $ordersDetail->state_id,
                    "area_id"     => $ordersDetail->area_id,
                    "block"       => $ordersDetail->block,
                    "street"      => $ordersDetail->street,
                    "avenue"      => $ordersDetail->avenue,
                    "house"       => $ordersDetail->house,
                    "floor"       => $ordersDetail->floor,
                    "landmark"    => $ordersDetail->landmark,
                    "coupon_amount"    => round($ordersDetail->coupon_amount, 3),
                    "is_free_coupon"   => $ordersDetail->coupon_free,
                    "delivery_charges" => $ordersDetail->delivery_charges,
                    "total_amount"     => round($ordersDetail->total_amount, 3),
                    "latitude"      => $ordersDetail->latitude,
                    "longitude"     => $ordersDetail->longitude,
                    "delivery_date" => $ordersDetail->delivery_date,
                    "delivery_time_en" => $ordersDetail->delivery_time_en,
                    "delivery_time_ar" => $ordersDetail->delivery_time_ar,
                    "seller_discount"  => round($ordersDetail->seller_discount, 3),
                    "bundle_discount"  => round($ordersDetail->bundle_discount, 3),
                    "pay_mode"    => $ordersDetail->pay_mode,
                    "order_items" => self::getDezOrdersItems($ordersDetail->order_id)
                ];
            }
        }
        if (!empty($ordersDetailsApi)) {
            return response()->json(['result_status' => 200, 'data' => $ordersDetailsApi], 200);
        } else {
            return response()->json(['result_status' => 400, 'data' => 'No Record Found'], 400);
        }
    }
    //get dez order items
    public static function getDezOrdersItems($orderid)
    {
        $strLang = "en";
        $tempOrders = Orders::where('order_id', $orderid)->get();
        $storechild = [];
        $subtotalprice = 0;
        $grandtotal = 0;
        $totalprice = 0;
        foreach ($tempOrders as $tempOrder) {
            $productDetails = self::getProductDetails($tempOrder->product_id);
            $suborders['item_name'] = !empty($productDetails['title_' . $strLang]) ? $productDetails['title_' . $strLang] : 'Not available';
            if (!empty($tempOrder->size_id)) {
                $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
            } else {
                $sizeName = '';
            }
            $suborders['size_name'] = $sizeName;
            if (!empty($tempOrder->color_id)) {
                $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
            } else {
                $colorName = '';
            }
            $suborders['options'] = self::getOptionsDtails($tempOrder->id);
            $subtotalprice = $tempOrder->unit_price * $tempOrder->quantity;
            $suborders['unit_price']   = $tempOrder->unit_price;
            $suborders['quantity']     = $tempOrder->quantity;
            $suborders['sub_total']    = $subtotalprice;
            $storechild[] = $suborders;
        }
        return $storechild;
    }

    //change order status from dezorder
    public  function changeDezOrderStatus(Request $request)
    {

        $dezAuth  = env("DEZ_ORDER_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9");

        if (empty($request->token)) {
            return response()->json(['result_status' => 400, 'data' => 'Token is missing'], 400);
        }
        if (!empty($request->token) && $request->token != $dezAuth) {
            return response()->json(['result_status' => 400, 'data' => 'You have invalid token'], 400);
        }
        if (empty($request->order_status)) {
            return response()->json(['result_status' => 400, 'data' => 'Order status is missing'], 400);
        }
        if (empty($request->order_id)) {
            return response()->json(['result_status' => 400, 'data' => 'Order ID is missing'], 400);
        }
        $ordersDetails = OrdersDetails::where("order_id", $request->order_id)->first();
        if (empty($ordersDetails->id)) {
            return response()->json(['result_status' => 400, 'data' => 'No Record Found'], 400);
        }

        $order_status = strtolower($request->order_status);

        $ordersDetails->order_status = $order_status == 'started' ? 'outfordelivery' : $order_status;
        if ($request->order_status == 'completed') {
            $ordersDetails->is_paid = 1;
        }
        $ordersDetails->save();
        return response()->json(['result_status' => 200, 'data' => 'Status is changed successfully'], 200);
    }
    //get delivery times

    public static function listDeliveryTimes()
    {
        $list = DeliveryTimes::where('is_active', 1)->orderBy('display_order', 'ASC')->get();
        return $list;
    }
    public static function getDeliberyTimeDetails($id)
    {
        return DeliveryTimes::where('id', $id)->first();
    }

    /////refresh coupon amount
    public static function ajax_apply_coupon_to_cart_refresh()
    {
        //empty seller discount
        if (!empty(Cookie::get('gb_coupon_code'))) {

            $total = self::getTotalCartAmount();
            $settingInfo = Settings::where("keyname", "setting")->first();

            $curDate = date("Y-m-d");
            $coupon = Coupon::where('is_active', 1)
                ->where('coupon_code', Cookie::get('gb_coupon_code'))
                ->first();


            $totalWithOurOldPrice = 0;
            $tempOrders = self::loadTempOrders();
            if (!empty($tempOrders) && count($tempOrders) > 0) {
                foreach ($tempOrders as $tempOrder) {
                    $pro = Product::find($tempOrder->product_id);
                    $brand = Brand::where('id', $pro->brand_id)->first();
                    if ($pro != null and !($pro->old_price >  $pro->retail_price or ($brand != null and $brand->is_discount == 1 && $brand->discount > 0)  or (!empty($pro->countdown_datetime) && strtotime($pro->countdown_datetime) > strtotime(date('Y-m-d'))))) {
                        $totalWithOurOldPrice += ($tempOrder->quantity * $tempOrder->unit_price);
                    }
                }
            }
            $totalKD = Currency::getOriginalPrice(round($totalWithOurOldPrice, 3));
            $isFreeDelivery = false;
            if (!empty($coupon->id) && !empty($coupon->is_free) and $totalKD > 0) {
                $domainCountry = Country::$countryInDomainModel;
                if (!(empty($coupon->is_zone_free) and in_array($domainCountry->shipment_method, ["zoneprice", "dhl"]))) {
                    $isFreeDelivery = true;
                }
            }
            $discountAmttxt = '';
            $discountAmt = 0;
            if (!empty($coupon->id) && $coupon->coupon_type == "amt") {
                $discountAmt    = $coupon->coupon_value;
            } else {
                $discountAmt    = round(($totalKD * $coupon->coupon_value) / 100, 3);
            }
            if ($discountAmt > $totalKD)
                $discountAmt = $totalKD;
            $tempPrice = Currency::convertTCountry($discountAmt);
            $discountAmttxt =     \App\Currency::default() . ' ' .  ($tempPrice['price'] ?? $tempPrice->price ?? $tempPrice[0]->price ?? $discountAmt);



            $minutes = 3600;
            Cookie::queue('gb_coupon_code', Cookie::get('gb_coupon_code'), $minutes);
            Cookie::queue('gb_coupon_discount', $discountAmt, $minutes);
            Cookie::queue('gb_coupon_discount_text', $discountAmttxt, $minutes);
            Cookie::queue('gb_coupon_free', $isFreeDelivery, $minutes);
        }
    }

    public static function state($id)
    {
        $parentId = 0;
        if (!empty($id)) {
            $areaInfo = self::get_csa_info($id);
            $parentId = $areaInfo->parent_id;
            if ($parentId != 0) {
                $stateInfo = self::get_csa_info($parentId);
                $parentStateId = $stateInfo->parent_id;
                if ($parentStateId == 0) {
                    return $id;
                }
            }
        }
        return $parentId;
    }

    //get tags names or image

    public static function getTagsName($tags_en, $tags_ar)
    {
        $txtTags = '';
        $settingInfo = Settings::where("keyname", "setting")->first();
        if (empty($settingInfo->is_show_tags)) {
            return $txtTags;
        }

        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        if (!empty($tags_en)) {
            $explodeTags = explode(",", $tags_en);
            foreach ($explodeTags as $tags) {
                $txtTags .= self::getTagsImage($tags);
            }
        }
        /*
	if($strLang=="ar" && !empty($tags_ar)){
	$explodeTags = explode(",",$tags_ar);
	foreach($explodeTags as $tags){
	$txtTags.= self::getTagsImage($tags);
	}
	}
	*/

        return $txtTags;
    }

    public static function getTagsImage($tags)
    {
        $tagsInfo = Tags::where('tag_name_en', $tags)->orwhere('tag_name_ar', $tags)->first();
        if (!empty($tagsInfo->image)) {
            $tags = '<a href="' . url('product-tag/' . $tags) . '"><img style="width:20px;height:20px;" width="20" height="20" class="tagimg" id="' . $tags . '" src="' . url('uploads/product/' . $tagsInfo->image) . '"></a>';
        } else {
            //$tags = '<a href="'.url('product-tag/'.$tags).'"><span class="tagtxt" id="'.$tags.'">'.$tags.'</span></a>';
            $tags = '';
        }
        return $tags;
    }

    //cyber res
    public function cs_response_accept(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }
        $settingInfo = Settings::where("keyname", "setting")->first();

        if (!empty($request->req_transaction_uuid)) {

            $payments = new CyberSource;

            $payments->auth_cv_result            = !empty($request->auth_cv_result) ? $request->auth_cv_result : 'NA';
            $payments->req_locale                = !empty($request->req_locale) ? $request->req_locale : 'NA';
            $payments->decision_case_priority    = !empty($request->decision_case_priority) ? $request->decision_case_priority : 'NA';
            $payments->req_card_type_selection_indicator = !empty($request->req_card_type_selection_indicator) ? $request->req_card_type_selection_indicator : 'NA';
            $payments->auth_trans_ref_no         = !empty($request->auth_trans_ref_no) ? $request->auth_trans_ref_no : 'NA';
            $payments->payer_authentication_enroll_veres_enrolled = !empty($request->payer_authentication_enroll_veres_enrolled) ? $request->payer_authentication_enroll_veres_enrolled : 'NA';
            $payments->req_bill_to_surname       = !empty($request->req_bill_to_surname) ? $request->req_bill_to_surname : 'NA';
            $payments->payer_authentication_proof_xml = !empty($request->payer_authentication_proof_xml) ? $request->payer_authentication_proof_xml : 'NA';
            $payments->req_card_expiry_date      = !empty($request->req_card_expiry_date) ? $request->req_card_expiry_date : 'NA';
            $payments->score_rflag               = !empty($request->score_rflag) ? $request->score_rflag : 'NA';
            $payments->score_card_issuer         = !empty($request->score_card_issuer) ? $request->score_card_issuer : 'NA';
            $payments->score_rcode               = !empty($request->score_rcode) ? $request->score_rcode : 'NA';
            $payments->card_type_name            = !empty($request->card_type_name) ? $request->card_type_name : 'NA';
            $payments->auth_amount               = !empty($request->auth_amount) ? $request->auth_amount : 'NA';
            $payments->auth_response             = !empty($request->auth_response) ? $request->auth_response : 'NA';
            $payments->bill_trans_ref_no         = !empty($request->bill_trans_ref_no) ? $request->bill_trans_ref_no : 'NA';
            $payments->req_payment_method        = !empty($request->req_payment_method) ? $request->req_payment_method : 'NA';
            $payments->auth_time                 = !empty($request->auth_time) ? $request->auth_time : 'NA';
            $payments->decision_early_return_code = !empty($request->decision_early_return_code) ? $request->decision_early_return_code : 'NA';
            $payments->payer_authentication_enroll_e_commerce_indicator = !empty($request->payer_authentication_enroll_e_commerce_indicator) ? $request->payer_authentication_enroll_e_commerce_indicator : 'NA';
            $payments->transaction_id            = !empty($request->transaction_id) ? $request->transaction_id : 'NA';
            $payments->req_card_type             = !empty($request->req_card_type) ? $request->req_card_type : 'NA';
            $payments->auth_avs_code             = !empty($request->auth_avs_code) ? $request->auth_avs_code : 'NA';
            $payments->auth_code                 = !empty($request->auth_code) ? $request->auth_code : 'NA';
            $payments->score_address_info        = !empty($request->score_address_info) ? $request->score_address_info : 'NA';
            $payments->score_factors             = !empty($request->score_factors) ? $request->score_factors : 'NA';
            $payments->score_model_used          = !empty($request->score_model_used) ? $request->score_model_used : 'NA';
            $payments->req_bill_to_address_country = !empty($request->req_bill_to_address_country) ? $request->req_bill_to_address_country : 'NA';
            $payments->auth_cv_result_raw        = !empty($request->auth_cv_result_raw) ? $request->auth_cv_result_raw : 'NA';
            $payments->decision_rmsg             = !empty($request->decision_rmsg) ? $request->decision_rmsg : 'NA';
            $payments->req_profile_id            = !empty($request->req_profile_id) ? $request->req_profile_id : 'NA';
            $payments->decision_rcode            = !empty($request->decision_rcode) ? $request->decision_rcode : 'NA';
            $payments->score_rmsg                = !empty($request->score_rmsg) ? $request->score_rmsg : 'NA';
            $payments->decision_rflag            = !empty($request->decision_rflag) ? $request->decision_rflag : 'NA';
            $payments->signed_date_time          = !empty($request->signed_date_time) ? $request->signed_date_time : 'NA';
            $payments->req_bill_to_address_line1 = !empty($request->req_bill_to_address_line1) ? $request->req_bill_to_address_line1 : 'NA';
            $payments->req_card_number           = !empty($request->req_card_number) ? $request->req_card_number : 'NA';
            $payments->signature                 = !empty($request->signature) ? $request->signature : 'NA';
            $payments->score_card_scheme         = !empty($request->score_card_scheme) ? $request->score_card_scheme : 'NA';
            $payments->score_bin_country         = !empty($request->score_bin_country) ? $request->score_bin_country : 'NA';
            $payments->req_bill_to_address_city  = !empty($request->req_bill_to_address_city) ? $request->req_bill_to_address_city : 'NA';
            $payments->auth_cavv_result          = !empty($request->auth_cavv_result) ? $request->auth_cavv_result : 'NA';
            $payments->score_reason_code         = !empty($request->score_reason_code) ? $request->score_reason_code : 'NA';
            $payments->reason_code               = !empty($request->reason_code) ? $request->reason_code : 'NA';
            $payments->req_bill_to_forename      = !empty($request->req_bill_to_forename) ? $request->req_bill_to_forename : 'NA';
            $payments->score_identity_info       = !empty($request->score_identity_info) ? $request->score_identity_info : 'NA';
            $payments->request_token             = !empty($request->request_token) ? $request->request_token : 'NA';
            $payments->req_device_fingerprint_id = !empty($request->req_device_fingerprint_id) ? $request->req_device_fingerprint_id : 'NA';
            $payments->auth_cavv_result_raw      = !empty($request->auth_cavv_result_raw) ? $request->auth_cavv_result_raw : 'NA';
            $payments->score_card_account_type   = !empty($request->score_card_account_type) ? $request->score_card_account_type : 'NA';
            $payments->req_amount                = !empty($request->req_amount) ? $request->req_amount : 'NA';
            $payments->req_bill_to_email         = !empty($request->req_bill_to_email) ? $request->req_bill_to_email : 'NA';
            $payments->payer_authentication_reason_code = !empty($request->payer_authentication_reason_code) ? $request->payer_authentication_reason_code : 'NA';
            $payments->auth_avs_code_raw         = !empty($request->auth_avs_code_raw) ? $request->auth_avs_code_raw : 'NA';
            $payments->req_currency              = !empty($request->req_currency) ? $request->req_currency : 'NA';
            $payments->decision                  = !empty($request->decision) ? $request->decision : 'NA';
            $payments->decision_return_code      = !empty($request->decision_return_code) ? $request->decision_return_code : 'NA';
            $payments->message                   = !empty($request->message) ? $request->message : 'NA';
            $payments->signed_field_names        = !empty($request->signed_field_names) ? $request->signed_field_names : 'NA';
            $payments->req_transaction_uuid      = !empty($request->req_transaction_uuid) ? $request->req_transaction_uuid : 'NA';
            $payments->decision_reason_code      = !empty($request->decision_reason_code) ? $request->decision_reason_code : 'NA';
            $payments->payer_authentication_eci  = !empty($request->payer_authentication_eci) ? $request->payer_authentication_eci : 'NA';
            $payments->score_time_local          = !empty($request->score_time_local) ? $request->score_time_local : 'NA';
            $payments->score_return_code         = !empty($request->score_return_code) ? $request->score_return_code : 'NA';
            $payments->score_host_severity       = !empty($request->score_host_severity) ? $request->score_host_severity : 'NA';
            $payments->req_transaction_type      = !empty($request->req_transaction_type) ? $request->req_transaction_type : 'NA';
            $payments->req_access_key            = !empty($request->req_access_key) ? $request->req_access_key : 'NA';
            $payments->score_internet_info       = !empty($request->score_internet_info) ? $request->score_internet_info : 'NA';
            $payments->decision_early_reason_code = !empty($request->decision_early_reason_code) ? $request->decision_early_reason_code : 'NA';
            $payments->req_reference_number      = !empty($request->req_reference_number) ? $request->req_reference_number : 'NA';
            $payments->decision_early_rcode      = !empty($request->decision_early_rcode) ? $request->decision_early_rcode : 'NA';
            $payments->save();
            ////////////////////////////save logs payments//////////////////////
            $trackid = trim($request->req_transaction_uuid);
            $result  = trim($request->decision);

            $orderDetails = OrdersDetails::where('order_id', $trackid)->first();
            if ($orderDetails->id) {

                if ($result == 'ACCEPT') {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                }
                //save trans logs
                $transactionDetails = new Transaction;
                if (!empty($request->auth_trans_ref_no)) {
                    $transactionDetails->payment_id = $request->auth_trans_ref_no;
                }
                if (!empty($request->score_identity_info)) {
                    $transactionDetails->ref = $request->score_identity_info;
                }
                if (!empty($request->transaction_id)) {
                    $transactionDetails->tranid = $request->transaction_id;
                }
                if (!empty($request->auth_code)) {
                    $transactionDetails->auth = $request->auth_code;
                }
                if (!empty($request->req_amount)) {
                    $transactionDetails->amt = $request->req_amount;
                }
                if (!empty($request->req_amount)) {
                    $transactionDetails->udf2 = $request->req_amount;
                }

                if (!empty($orderDetails->order_id)) {
                    $transactionDetails->trackid = $orderDetails->order_id;
                }
                if (!empty($orderDetails->order_id)) {
                    $transactionDetails->udf1 = $orderDetails->order_id;
                }
                if (!empty($orderDetails->id)) {
                    $transactionDetails->udf4 = $orderDetails->id;
                }
                $transactionDetails->PayType = 'CS';
                if ($result == 'ACCEPT') {
                    $transactionDetails->presult = 'CAPTURED';
                } else {
                    $transactionDetails->presult = $result;
                }
                $transactionDetails->postdate = date('md');
                $transactionDetails->udf3     = $strLang;
                $transactionDetails->udf5     = 'Payment from CyberSource';
                $transactionDetails->save();

                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . Common::getLangString($areaInfo->title_en, $areaInfo->title_ar) . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($result) && $result == 'ACCEPT') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions  = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice     = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
						<td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
						</td>
						<td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
						<td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
						<td align="center">' . $tempOrder->quantity . '</td>
						<td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
						</tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
						<tr>
						<td class="headertd">' . trans('webMessage.image') . '</td>
						<td class="headertd">' . trans('webMessage.details') . '</td>
						<td class="headertd">' . trans('webMessage.unit_price') . '</td>
						<td class="headertd">' . trans('webMessage.quantity') . '</td>
						<td class="headertd">' . trans('webMessage.subtotal') . '</td>
						</tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                $knetMessage = '';
                if (!empty($result) && $result == 'ACCEPT') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $transid = '--';
                if (isset($request->transaction_id) && !empty($request->transaction_id)) {
                    $transid = $request->transaction_id;
                }
                $authcode = '--';
                if (isset($request->auth_code) && !empty($request->auth_code)) {
                    $authcode = $request->auth_code;
                }
                $amount = 0;
                if (isset($request->req_amount) && !empty($request->req_amount)) {
                    $amount = $request->req_amount;
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
			<tr>
			  <td>' . trans('webMessage.result') . '</td>
			  <td>' . $txtpaid . '</td>
			</tr>
			<tr>
			  <td>' . trans('webMessage.date') . '</td>
			  <td>' . date('Y-m-d H:i:s') . '</td>
			</tr>
			<tr>
			  <td>' . trans('webMessage.transid') . '</td>
			  <td>' . $transid . '</td>
			</tr>
			<tr>
			  <td>' . trans('webMessage.authcode') . '</td>
			  <td>' . $authcode . '</td>
			</tr>
			<tr>
			  <td>' . trans('webMessage.amount') . '</td>
			  <td>' . number_format($amount, 3) . ' ' . \App\Currency::defaultCMS() . '</td>
			</tr>
		  </table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }

                if (!empty($result) && $result == 'ACCEPT') {
                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod

                    //return redirect('/order-details/'.md5($orderDetails->order_id))->with('session_msg',$knetMessage);
                } else {
                    //return redirect('/order-details/'.md5($orderDetails->order_id))->with('session_msg_error',$knetMessage);
                }
            } else { //order does not exist
                //return redirect('/knet_failed')->with('session_msg_error',trans('webMessage.orderdetailsnotexist'));
            }
        } else { // payment is empty
            //return redirect('/knet_failed')->with('session_msg_error',trans('webMessage.paymentdetailsareempty'));
        }
    }

    public function csreturnurl()
    {
        $sid = !empty(Session::get('orderid')) ? Session::get('orderid') : '0';
        return redirect(app()->getLocale() . '/order-details/' . $sid);
    }

    public static function uploadfiles($request)
    {
        $imageName = '';
        if (!empty($request)) {
            $imageName = 'attach-' . md5(time()) . '.' . $request->getClientOriginalExtension();
            $request->move(public_path('uploads/attachcart'), $imageName);
        }

        return $imageName;
    }

    ///***********************DETAILS ADD TO CART *************//


    public function ajaxAddProductToOrder(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }

        $order_id = $request->order_id;
        $order = Orders::where('order_id', $order_id)->first();
        $oid = $order->oid;

        //check size/color attribute
        if (isset($request->option_sc) && !empty($request->option_sc)) {
            if ($request->option_sc == 3) {
                $optionSc = 3;
                if (empty($request->size_attr)) {
                    return ["status" => 400, "message" => "please choose the size"];
                }
                if (empty($request->color_attr)) {
                    return ["status" => 400, "message" => "please choose the color"];
                }
                $aquantity = self::getProductQuantity($request->product_id, $request->size_attr, $request->color_attr);
                if (1 > $aquantity) {
                    $message = trans('Given quantity exceeds available quantity');
                    return ["status" => 400, "message" => $message];
                }
            } elseif ($request->option_sc == 1) {
                $optionSc = 1;
                if (empty($request->size_attr)) {
                    return ["status" => 400, "message" => "please choose the size"];
                }
                $aquantity = self::getProductQuantity($request->product_id, $request->size_attr, 0);
                if (1 > $aquantity) {
                    $message = trans('Given quantity exceeds available quantity');
                    return ["status" => 400, "message" => $message];
                }
            } elseif ($request->option_sc == 2) {
                $optionSc = 2;
                if (empty($request->color_attr)) {
                    return ["status" => 400, "message" => "please choose the color"];
                }
                $aquantity = self::getProductQuantity($request->product_id, 0, $request->color_attr);
                if (1 > $aquantity) {
                    $message = trans('Given quantity exceeds available quantity');
                    return ["status" => 400, "message" => $message];
                }
            }
        } else {
            $optionSc = 0;
        }

        //check other field validation
        $flag = self::checkOptionsFields($request);
        if (!empty($flag) && $flag > 0) {
            return ["status" => 400, "message" => "please choose the options"];
        }

        //check item exit or not
        $product = Product::where('id', $request->product_id)->where('is_active', '!=', 0)->first();
        if (empty($product->id)) {
            return ["status" => 400, "message" => "item not found"];
        }

        $aquantity = $this->AvailableQuantity($product->id);

        $size_id = $request->size_attr ?: 0;
        $color_id = $request->color_attr ?: 0;

        $new_order = new Orders();
        $new_order->oid = $oid;
        $new_order->order_id = $order_id;
        $new_order->product_id = $request->product_id;
        $new_order->quantity = 1;
        $new_order->unit_price = $request->price;
        $new_order->size_id = $size_id;
        $new_order->color_id = $color_id;
        $new_order->save();

        $orderDetails = OrdersDetails::where('order_id', $order_id)->first();
        $oldTotal = $orderDetails->total_amount;
        $newTotal = $oldTotal + $request->price;
        $orderDetails->total_amount = $newTotal;
        $orderDetails->save();

        $productid = $request->product_id;
        $productoptions = ProductOptionsCustomChosen::where('product_id', $productid)
            ->where('custom_option_id', '>=', 4)
            ->groupBy('custom_option_id')
            ->orderBy('custom_option_id', 'ASC')->get();


        $orderInventory1 = $this->deductQuantity($request->product_id, 1, $size_id, $color_id);
        $orderInventory2 = [];

        if (!empty($productoptions) && count($productoptions) > 0) {
            foreach ($productoptions as $productoption) {
                //option
                if (!empty($request->input('option-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('option-' . $productid . '-' . $productoption->custom_option_id);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = $child_option;
                    $OrderOption->save();
                }
                //select
                if (!empty($request->input('select-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('select-' . $productid . '-' . $productoption->custom_option_id);
                    $explodeme = explode("-", $child_option);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = $explodeme[3];
                    $OrderOption->save();
                }
                //checkbox
                if (!empty($request->input('checkbox-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('checkbox-' . $productid . '-' . $productoption->custom_option_id);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = implode(",", $child_option);
                    $OrderOption->save();
                }

                //textarea
                if (!empty($request->input('textarea-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('textarea-' . $productid . '-' . $productoption->custom_option_id);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = 0;
                    $OrderOption->comments         = $child_option;
                    $OrderOption->save();
                }

                //datetime
                if (!empty($request->input('datetime-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->input('datetime-' . $productid . '-' . $productoption->custom_option_id);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = 0;
                    $OrderOption->dates            = $child_option;
                    $OrderOption->save();
                }

                //file
                if (!empty($request->file('file-' . $productid . '-' . $productoption->custom_option_id))) {
                    $child_option = $request->file('file-' . $productid . '-' . $productoption->custom_option_id);
                    $OrderOption = new OrdersOption;
                    $OrderOption->product_id       = $request->product_id;
                    $OrderOption->oid              = $order_id;
                    $OrderOption->option_id        = $productoption->custom_option_id;
                    $OrderOption->option_child_ids = 0;
                    $OrderOption->attach_file      = self::uploadfiles($child_option);
                    $OrderOption->save();
                }

                $orderInventory2 = self::changeOptionQuantity($OrderOption->product_id, 'd', $OrderOption->option_child_ids, 1);
            }
        }

        $orderInventories = $tempOrderInventories = array_merge($orderInventory1, $orderInventory2);
        foreach ($tempOrderInventories as $i => $orderInventory)
            if (!($orderInventory > 0))
                unset($orderInventories[$i]);
        $new_order->inventory  =  json_encode($orderInventories);
        $new_order->save();

        return ["status" => 200, "message" => "product added successfully"];
    }

    //customize product
    public function customizeProduct(Request $request, $id)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();

        $product = Product::find($id);
        if ($product) {
            if ($product->customizable == 0) {
                return redirect(app()->getLocale() . '/details/' . $id . '/' . $product->slug);
            }

            //calculating the price
            if (!empty($product->countdown_datetime) && strtotime($product->countdown_datetime) > strtotime(date('Y-m-d'))) {
                $price = round($product->countdown_price, 3);
            } else {
                $price = round($product->retail_price, 3);
            }

            //finding the category
            $categories = ProductCategory::where('product_id', $id)->get();
            if ($categories) {
                foreach ($categories as $category) {
                    if ($category) {
                        $categoryName = $category->name_en;
                        break;
                    }
                }
            }
            if (empty($categoryName)) {
                $categoryName = "default";
            }

            $images = "";

            if ($product->image) {

                $images .= '/uploads/product/' . $product->image . '"';

                $json = [
                    "status" => true,
                    "products" => [
                        [
                            "id" => $id,
                            "name" => $product->title_en,
                            "price" => $price,
                            "category" => $categoryName,
                            "currency" => \App\Currency::default(),
                            "image" => '/uploads/product/' . $product->image,
                            "sub_images" => [],
                            "description" => $product->sdetails_en
                        ]
                    ],
                    "message" => "Products Loaded Successfully"
                ];

                $json = json_encode($json, JSON_PRETTY_PRINT);
                $domainUrl = str_replace("www.", "", $request->getSchemeAndHttpHost());
                if (!empty($request->getSchemeAndHttpHost()) && $domainUrl == ENV('APP_URL')) {
                    $path = base_path();
                    $explo = explode("/", $path);
                    if ($settingInfo->theme == 15) {
                        $detination =  '/home/u582104647/domains/aggrokw.com/public_html/product_cus/dummy_data/products/load_products.json';
                    } else {
                        $detination =  '/home/' . $explo[2] . '/public_html/product_cus/dummy_data/products/load_products.json';
                    }
                    file_put_contents($detination, stripslashes($json));
                } else {
                    file_put_contents(public_path('product_cus/dummy_data/products/load_products.json'), stripslashes($json));
                }
            }

            $galleries = ProductGallery::where('product_id', $id)->where('is_active', 1)->get();
            if ($galleries) {
                $i = 1;
                foreach ($galleries as $gallery) {

                    if ($i == 1) {
                        $images .= ',"';
                    }
                    if ($i != count($galleries)) {
                        $images .= '/uploads/product/' . $gallery->image . '","';
                    } else {
                        $images .= '/uploads/product/' . $gallery->image;
                    }

                    $i++;
                }
                //making the json file (the name will be the id of the product)
                $json = [
                    "status" => true,
                    "images" => [$images],
                    "message" => "Images Loaded Successfully"
                ];

                $json = json_encode($json, JSON_PRETTY_PRINT);
                $domainUrl = str_replace("www.", "", $request->getSchemeAndHttpHost());
                if (!empty($request->getSchemeAndHttpHost()) && $domainUrl == ENV('APP_URL')) {
                    $path = base_path();
                    $explo = explode("/", $path);
                    if ($settingInfo->theme == 15) {
                        $detination =  '/home/u582104647/domains/aggrokw.com/public_html/product_cus/dummy_data/products/' . $id . '.json';
                    } else {
                        $detination =  '/home/' . $explo[2] . '/public_html/product_cus/dummy_data/products/' . $id . '.json';
                    }
                    file_put_contents($detination, stripslashes($json));
                } else {
                    file_put_contents(public_path('product_cus/dummy_data/products/' . $id . '.json'), stripslashes($json));
                }
            }


            return view('website.customize.index');
        }
    }

    //print vendor order details//////////////////////////////////////////
    public function vendororderprint(Request $request)
    {
        if (!empty(app()->getLocale())) {
            $strLang = app()->getLocale();
        } else {
            $strLang = "en";
        }

        if ($strLang == "en") {
            $align = 'left';
        } else {
            $align = 'right';
        }

        $settingInfo = Settings::where("keyname", "setting")->first();

        $orderDetails = OrdersDetails::where('order_id', $request->orderid)->first();
        //customer details
        $customerDetailsTxt = '';
        if ($settingInfo->invoice_template == 2) {

            $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
            if (!empty($orderDetails->name)) {
                $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
            }

            if (!empty($orderDetails->area_id)) {
                $areaInfo    = self::get_csa_info($orderDetails->area_id);
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
            }
            if (!empty($orderDetails->block)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
            }
            if (!empty($orderDetails->street)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
            }
            if (!empty($orderDetails->avenue)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
            }
            if (!empty($orderDetails->house)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
            }
            if (!empty($orderDetails->floor)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
            }
            if (!empty($orderDetails->landmark)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
            }

            if (!empty($orderDetails->email)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
            }
            if (!empty($orderDetails->mobile)) {
                $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
            }

            $customerDetailsTxt .= '</table>';
        } else {

            if (!empty($orderDetails->name)) {
                $customerDetailsTxt .= '<b>' . $orderDetails->name . '</b><br>';
            }

            if (!empty($orderDetails->area_id)) {
                $areaInfo    = self::get_csa_info($orderDetails->area_id);
                $customerDetailsTxt .= $areaInfo['name_' . $strLang] . ',<br>';
            }
            if (!empty($orderDetails->block)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.block') . ' : </b>' . $orderDetails->block . ',';
            }
            if (!empty($orderDetails->street)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.street') . ' : </b>' . $orderDetails->street . ',';
            }
            if (!empty($orderDetails->avenue)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.avenue') . ' : </b>' . $orderDetails->avenue . ',<br>';
            }
            if (!empty($orderDetails->house)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.house') . ' : </b>' . $orderDetails->house . ',';
            }
            if (!empty($orderDetails->floor)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.floor') . ' : </b>' . $orderDetails->floor . ',';
            }
            if (!empty($orderDetails->landmark)) {
                $customerDetailsTxt .= '<b>' . trans('webMessage.landmark') . ' : </b>' . $orderDetails->landmark;
            }
            if (!empty($orderDetails->email)) {
                $customerDetailsTxt .= '<br><b>' . trans('webMessage.email') . ' : </b>' . $orderDetails->email;
            }
            if (!empty($orderDetails->mobile)) {
                $customerDetailsTxt .= '<br><b>' . trans('webMessage.mobile') . ' : </b>' . $orderDetails->mobile;
            }
        }

        $customerDetails = $customerDetailsTxt;

        //invoice details
        if ($settingInfo->invoice_template == 1) {
            $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
            if (!empty($orderDetails->is_paid)) {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';
            $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $deliverytimetxt . '</td></tr>';
            }
            $invoiceDetailsTxt .= '</table>';
        } else {
            $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.orderid') . '</strong></td><td>' . $orderDetails->order_id . '</td><td><strong>' . trans('webMessage.paymentmethod') . '</strong></td><td>' . $orderDetails->pay_mode . '</td></tr>';
            if (!empty($orderDetails->is_paid)) {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.payment_status') . '</strong></td><td>' . $txtpaid . '</td><td><strong>' . trans('webMessage.date') . '</strong></td><td>' . $orderDetails->created_at . '</td></tr>';
            //delivery time
            if (!empty($deliverytimetxt)) {
                $invoiceDetailsTxt .= '<tr><td><strong>' . trans('webMessage.deliverytime') . '</strong></td><td>' . $deliverytimetxt . '</td></tr>';
            }
            $invoiceDetailsTxt .= '</table>';
        }

        $invoiceDetails = $invoiceDetailsTxt;
        //list order
        $tempOrders  = DB::table('gwc_orders')->where('gwc_orders.order_id', $orderDetails->order_id);
        $tempOrders  = $tempOrders->select('gwc_orders.*', 'gwc_products.id', 'gwc_products.manufacturer_id');
        $tempOrders  = $tempOrders->join('gwc_products', 'gwc_products.id', 'gwc_orders.product_id');
        $tempOrders  = $tempOrders->where('gwc_products.manufacturer_id', $request->vendorid);
        $tempOrders  = $tempOrders->groupBy('gwc_orders.id')->get();

        $ordertxt_child = '';
        $subtotalprice = 0;
        $grandtotal = 0;
        $totalprice = 0;
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
            $unitprice = $tempOrder->unit_price;
            $subtotalprice = $unitprice * $tempOrder->quantity;
            $title = $productDetails['title_' . $strLang];

            $warrantyTxt = '';
            if (!empty($productDetails->warranty)) {
                $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
            }


            $color = $productDetails->attributes->where('color_id', $tempOrder->color_id ?? 0)->first();
            $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . url('uploads/product/thumb/' . (@$color && @$color->color_image ? $color->color_image : $productDetails['image'])) . '" alt="' . $title . '" width="50"></a>
					</td>
                    <td style="text-align:' . $align . ';">(#' . $productDetails->item_code . ')' . $title . '<small>' . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</small></td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

            $totalprice += $subtotalprice;
        }
        //order details
        $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
        $orderDetailsTxt .= $ordertxt_child;
        $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.total') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';

        $orderDetailsTxt .= '</table>';
        $orderid = $orderDetails->order_id;
        $orderDetails = $orderDetailsTxt;

        $paymentDetails = '';
        $transDetails = self::TransDetails($orderid);
        if (!empty($transDetails->id)) {
            if (!empty($transDetails->presult) && $transDetails->presult == 'CAPTURED') {
                $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
            } else {
                $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
            }
            $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">
	    <tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . $transDetails->created_at . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . (!empty($transDetails->tranid) ? $transDetails->tranid : (!empty($transDetails->paypal_cart) ? $transDetails->paypal_cart : '')) . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $transDetails->payment_id . '</td>
        </tr>
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td dir="ltr">' . number_format($transDetails->udf2, 3) . ' ' . \App\Currency::defaultCMS() . (!empty($transDetails->amt_dollar) ? '(' . trans('webMessage.usd') . '' . $transDetails->amt_dollar . ')' : '') . '</td>
        </tr>
      </table>';
        }

        $bladeView = "emails.template_vendor_order_print";

        return view($bladeView, compact('settingInfo', 'customerDetails', 'invoiceDetails', 'orderDetails', 'paymentDetails'));
    }

    //**********************Q8link response *************************/
    //knet response
    public function knet_response_q8link_return(Request $request)
    {
        if ($request->refid) {
            $refid  = $request->refid;
            $verifyurl = "https://www.q8link.net/clientpay/checkpayment.php";
            $response = Curl::to($verifyurl)
                ->withData([
                    'refid'    => $refid
                ])->post();
            $jsdecode = json_decode($response, true);
            if (empty($jsdecode['trackid'])) {
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
            }



            if (!empty(app()->getLocale())) {
                $strLang = app()->getLocale();
            } else {
                $strLang = "en";
            }
            $settingInfo = Settings::where("keyname", "setting")->first();

            $transactionDetails = Transaction::where('id', $refid)->first();
            $orderDetails = OrdersDetails::where('order_id', $transactionDetails ? $transactionDetails->trackid : '-1')->first();
            if ($orderDetails->id) {
                if ($jsdecode['presult'] == 'CAPTURED') {
                    $orderDetails->is_paid = 1;
                    $orderDetails->save();
                }
                //update trans
                $transactionDetails->presult = $jsdecode['presult'];
                if ($jsdecode['payment_id']) {
                    $transactionDetails->payment_id = $jsdecode['payment_id'];
                }
                if ($jsdecode['ref']) {
                    $transactionDetails->ref = $jsdecode['ref'];
                }
                if ($jsdecode['tranid']) {
                    $transactionDetails->tranid = $jsdecode['tranid'];
                }
                if ($jsdecode['auth']) {
                    $transactionDetails->auth = $jsdecode['auth'];
                }
                if ($jsdecode['udf2']) {
                    $transactionDetails->amt = $jsdecode['udf2'];
                }

                $transactionDetails->PayType = 'KNET';

                $transactionDetails->save();

                $customerDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0">';
                if (!empty($orderDetails->name)) {
                    $customerDetailsTxt .= '<tr><td width="150">' . trans('webMessage.name') . '</td><td>' . $orderDetails->name . '</td></tr>';
                }

                if (!empty($orderDetails->area_id)) {
                    $areaInfo    = self::get_csa_info($orderDetails->area_id);
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.area') . '</td><td>' . $areaInfo['name_' . $strLang] . '</td></tr>';
                }
                if (!empty($orderDetails->block)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.block') . '</td><td>' . $orderDetails->block . '</td></tr>';
                }
                if (!empty($orderDetails->street)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.street') . '</td><td>' . $orderDetails->street . '</td></tr>';
                }
                if (!empty($orderDetails->avenue)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.avenue') . '</td><td>' . $orderDetails->avenue . '</td></tr>';
                }
                if (!empty($orderDetails->house)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.house') . '</td><td>' . $orderDetails->house . '</td></tr>';
                }
                if (!empty($orderDetails->floor)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.floor') . '</td><td>' . $orderDetails->floor . '</td></tr>';
                }
                if (!empty($orderDetails->landmark)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.landmark') . '</td><td>' . $orderDetails->landmark . '</td></tr>';
                }

                if (!empty($orderDetails->email)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.email') . '</td><td>' . $orderDetails->email . '</td></tr>';
                }
                if (!empty($orderDetails->mobile)) {
                    $customerDetailsTxt .= '<tr><td>' . trans('webMessage.mobile') . '</td><td>' . $orderDetails->mobile . '</td></tr>';
                }

                $customerDetailsTxt .= '</table>';

                //invoice details
                $invoiceDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.orderid') . '</td><td>' . $orderDetails->order_id . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.paymentmethod') . '</td><td>' . $orderDetails->pay_mode . '</td></tr>';
                if (!empty($jsdecode['presult']) && $jsdecode['presult'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                }
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.payment_status') . '</td><td>' . $txtpaid . '</td></tr>';
                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.order_status') . '</td><td>' . strtoupper(trans('webMessage.pending')) . '</td></tr>';

                $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.date') . '</td><td>' . $orderDetails->created_at . '</td></tr>';

                if ($strLang == "en" && !empty($orderDetails->delivery_time_en)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_en . '</td></tr>';
                } else if ($strLang == "ar" && !empty($orderDetails->delivery_time_ar)) {
                    $invoiceDetailsTxt .= '<tr><td>' . trans('webMessage.deliverytime') . '</td><td>' . $orderDetails->delivery_time_ar . '</td></tr>';
                }

                $invoiceDetailsTxt .= '</table>';

                //list order
                $tempOrders = Orders::where('order_id', $orderDetails->order_id)->get();
                $ordertxt_child = '';
                $subtotalprice = 0;
                $grandtotal = 0;
                $totalprice = 0;
                foreach ($tempOrders as $tempOrder) {
                    $productDetails = self::getProductDetails($tempOrder->product_id);
                    if ($productDetails->image) {
                        $prodImage = url('uploads/product/thumb/' . $productDetails->image);
                    } else {
                        $prodImage = url('uploads/no-image.png');
                    }
                    if (!empty($tempOrder->size_id)) {
                        $sizeName = self::sizeNameStatic($tempOrder->size_id, $strLang);
                        $sizeName = '<br>' . trans('webMessage.size') . ':' . $sizeName;
                    } else {
                        $sizeName = '';
                    }
                    if (!empty($tempOrder->color_id)) {
                        $colorName = self::colorNameStatic($tempOrder->color_id, $strLang);
                        $colorName = '<br>' . trans('webMessage.color') . ':' . $colorName;
                        //color image
                        $colorImageDetails = self::getColorImage($tempOrder->product_id, $tempOrder->color_id);
                        if (!empty($colorImageDetails->color_image)) {
                            $prodImage = url('uploads/color/thumb/' . $colorImageDetails->color_image);
                        }
                    } else {
                        $colorName = '';
                    }
                    $orderOptions = self::getOptionsDtailsOrderBr($tempOrder->id);
                    $unitprice = $tempOrder->unit_price;
                    $subtotalprice = $unitprice * $tempOrder->quantity;
                    $title = $productDetails['title_' . $strLang];

                    $warrantyTxt = '';
                    if (!empty($productDetails->warranty)) {
                        $warrantyDetails = self::getWarrantyDetails($productDetails->warranty);
                        $warrantyTxt = $strLang == "en" ? $warrantyDetails->title_en : $warrantyDetails->title_ar;
                    }


                    $ordertxt_child .= '<tr>
                    <td><a href="' . url(app()->getLocale() . '/details/' . $productDetails->id . '/' . $productDetails->slug) . '"><img src="' . $prodImage . '" alt="' . $title . '" width="50"><br>' . $productDetails->item_code . '</a>
					</td>
                    <td>' . $title . $sizeName . $colorName . $orderOptions . '<br>' . $warrantyTxt . '</td>
                    <td>' . \App\Currency::default() . ' ' . $unitprice . '</td>
                    <td align="center">' . $tempOrder->quantity . '</td>
                    <td>' . \App\Currency::default() . ' ' . $subtotalprice . '</td>
                    </tr>';

                    $totalprice += $subtotalprice;
                }
                //order details
                $orderDetailsTxt = '<table cellpadding="0" cellspacing="0" border="0" class="pro_table">
                    <tr>
                    <td class="headertd">' . trans('webMessage.image') . '</td>
                    <td class="headertd">' . trans('webMessage.details') . '</td>
                    <td class="headertd">' . trans('webMessage.unit_price') . '</td>
                    <td class="headertd">' . trans('webMessage.quantity') . '</td>
                    <td class="headertd">' . trans('webMessage.subtotal') . '</td>
                    </tr>';
                $orderDetailsTxt .= $ordertxt_child;
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.subtotal') . '&nbsp;:&nbsp;&nbsp;</b></td><td>' . \App\Currency::default() . '' . $totalprice . '</td></tr>';
                //bundle discount
                $bundleDiscount =  self::loadTempOrdersBundleDiscount($tempOrders);
                if ($bundleDiscount > 0) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.bundles.BundleDiscount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $bundleDiscount . '</font></td></tr>';
                    $totalprice = $totalprice - $bundleDiscount;
                }
                //seller discount
                if (!empty($orderDetails->seller_discount)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.seller_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->seller_discount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->seller_discount;
                }
                //show discount if available but not free delivery
                if (!empty($orderDetails->coupon_code) && empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;(' . ($orderDetails->coupon_code) . '):&nbsp;&nbsp;</td><td><font color="#FF0000">-' . \App\Currency::default() . ' ' . $orderDetails->coupon_amount . '</font></td></tr>';
                    $totalprice = $totalprice - $orderDetails->coupon_amount;
                }
                if (!empty($orderDetails->coupon_code) && !empty($orderDetails->coupon_free)) {
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.coupon_discount') . '&nbsp;:&nbsp;&nbsp;</td><td><font color="#FF0000">' . strtoupper(trans('webMessage.free_delivery')) . '</font></td></tr>';
                }

                if (!empty($orderDetails->delivery_charges) && empty($orderDetails->coupon_free)) {
                    $deliveryCharge = $orderDetails->delivery_charges;
                    $orderDetailsTxt .= '<tr><td colspan="4" align="right">' . trans('webMessage.delivery_charge') . '&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $deliveryCharge . '</td></tr>';
                    $totalprice = $totalprice + $deliveryCharge;
                }
                $orderDetailsTxt .= '<tr><td colspan="4" align="right"><b>' . trans('webMessage.grandtotal') . '</b>&nbsp;:&nbsp;&nbsp;</td><td>' . \App\Currency::default() . ' ' . $totalprice . '</td></tr>';
                $orderDetailsTxt .= '</table>';

                //payment temp
                $paymentDetails = '';
                if (!empty($jsdecode['presult']) && $jsdecode['presult'] == 'CAPTURED') {
                    $txtpaid = '<font color="#009900">' . strtoupper(trans('webMessage.paid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithsuccess');
                } else {
                    $txtpaid = '<font color="#FF0000">' . strtoupper(trans('webMessage.notpaid')) . '</font>';
                    $knetMessage = trans('webMessage.yourorderisplacedwithfailed');
                }

                $paymentDetails .= '<table cellpadding="0" cellspacing="0" border="0" class="payment">';
                $paymentDetails .= '<tr>
	      <td>' . trans('webMessage.result') . '</td>
	      <td>' . $txtpaid . '</td>
        </tr>';
                $paymentDetails .= '<tr>
	      <td>' . trans('webMessage.date') . '</td>
	      <td>' . date('Y-m-d H:i:s') . '</td>
        </tr>';
                $paymentDetails .= '
	    <tr>
	      <td>' . trans('webMessage.transid') . '</td>
	      <td>' . $jsdecode['tranid'] . '</td>
        </tr>';
                if (!empty($jsdecode['payment_id'])) {
                    $paymentDetails .= '
	    <tr>
	      <td>' . trans('webMessage.paymentid') . '</td>
	      <td>' . $jsdecode['payment_id'] . '</td>
        </tr>';
                }
                if (!empty($jsdecode['ref'])) {
                    $paymentDetails .= '
	    <tr>
	      <td>' . trans('webMessage.refid') . '</td>
	      <td>' . $jsdecode['ref'] . '</td>
        </tr>';
                }
                if (!empty($jsdecode['auth'])) {
                    $paymentDetails .= '
	    <tr>
	      <td>' . trans('webMessage.authcode') . '</td>
	      <td>' . $jsdecode['auth'] . '</td>
        </tr>';
                }
                $paymentDetails .= '
	    <tr>
	      <td>' . trans('webMessage.amount') . '</td>
	      <td>' . number_format($jsdecode['udf2'], 3) . ' ' . \App\Currency::defaultCMS() . '</td>
        </tr>';
                $paymentDetails .= '</table>';
                $trackYourOrderTxt = trans('webMessage.trackyourorderhistory') . '<br>' . url(app()->getLocale() . '/order-details') . '/' . md5($orderDetails->order_id);
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
                            'paymentDetails'  => $paymentDetails,
                            'trackYourOrder'  => $trackYourOrderTxt,
                            'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
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
                        'paymentDetails'  => $paymentDetails,
                        'trackYourOrder'  => $trackYourOrderTxt,
                        'subject'         => "Order Notification From " . $settingInfo->name_en . " #" . $orderDetails->order_id,
                        'email_from'      => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($orderDetails->email)->send(new SendGridOrder($data));
                }
                if (!empty($jsdecode['presult']) && $jsdecode['presult'] == 'CAPTURED') {

                    //send sms notification for cod
                    $isValidMobile = Common::checkMobile($orderDetails->mobile);
                    if (!empty($settingInfo->sms_text_knet_active) && !empty($settingInfo->sms_text_knet_en) && !empty($settingInfo->sms_text_knet_ar) && !empty($isValidMobile)) {
                        if ($orderDetails->strLang == "en") {
                            $smsMessage = $settingInfo->sms_text_knet_en;
                        } else {
                            $smsMessage = $settingInfo->sms_text_knet_ar;
                        }
                        $to      = $orderDetails->mobile;
                        $sms_msg = $smsMessage . " #" . $orderDetails->order_id;
                        Common::SendSms($to, $sms_msg);
                    }
                    //end sending sms for cod


                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg', $knetMessage);
                } else {
                    return redirect(app()->getLocale() . '/order-details/' . md5($orderDetails->order_id))->with('session_msg_error', $knetMessage);
                }
            } else { //order exist or not
                return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
            }
        } else { //track id not empty
            return redirect(app()->getLocale() . '/cart')->with('session_msg', trans('webMessage.invalidpayment'));
        }
    }
}
