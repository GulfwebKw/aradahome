<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\QrCode;
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
use App\ProductCategory;

use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChild;
use App\ProductOptionsCustomChosen;

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
use DB;
use Image;
use File;

class ImportController extends Controller
{
	public $successStatus       = 200;
	public $failedStatus        = 400;
	public $unauthorizedStatus  = 401;


	public function galleryProductImages()
	{
		$pro = DB::table('products')->select(['id', 'photos'])->whereNotNull('photos')->get();
		foreach ($pro as $p) {
			$this->getImageArray($p);
		}
	}

	public function getImageArray($arr)
	{
		$trim_path = "";
		$imagePathString = $arr->photos;
		$product_id = $arr->id;
		$images = explode(",", 	$imagePathString);

		foreach ($images as $image) {
			$imagePath = DB::table('uploads')->select('file_name')->where('id', $image)->first();
				$trim_path = str_replace("uploads/all/", "", $imagePath->file_name);
				$product_Gallery = new ProductGallery;
				$product_Gallery->product_id = $product_id;
				$product_Gallery->image = $trim_path;
				$product_Gallery->is_active = 1;
				$product_Gallery->save();
		}
	}


	//products listings
	public function listProducts($catid)
	{


		if (empty($catid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}


		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}


		$productLists = DB::table('gwc_products_category')
			->select(
				'gwc_products.id',
				'gwc_products.title_en',
				'gwc_products.title_ar',
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
				'gwc_products.slug'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->where(['category_id' => $catid])->where('gwc_products.is_active', '!=', 0);

		$productLists = $productLists->orderBy("gwc_products.id", "desc")->get();



		$success['data'] = ['productLists' => $productLists];
		return response()->json($success, $this->successStatus);
	}


	//get items from api
	public  function getItemsFromApi($catid, $mcatid)
	{

		$url = "https://www.hakumkw.com/getProductsOther/" . $catid;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		$curlData = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($curlData, TRUE);
		if (!empty($res['data']['productLists']) && count($res['data']['productLists']) > 0) {
			$lists = $res['data']['productLists'];
			foreach ($lists as $list) {
				self::storeItems(
					$mcatid,
					$list['id'],
					$list['slug'],
					$list['title_en'],
					$list['title_ar'],
					$list['details_en'],
					$list['details_ar'],
					$list['retail_price'],
					$list['old_price'],
					$list['image'],
					$list['rollover_image'],
					$list['is_attribute'],
					$list['quantity']
				);
			}
			return ["status" => 200, "message" => "Record is added"];
		} else {
			return ["status" => 400, "message" => "No Record Found"];
		}
	}

	//import mrk product
	public  function getItemsFromApiMrk()
	{

		$url = "https://www.mrk-q8.com/json.php";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		$curlData = curl_exec($curl);
		curl_close($curl);
		$lists = json_decode($curlData, TRUE);

		if (!empty($lists) && count($lists) > 0) {
			$mcatid = 1;
			foreach ($lists as $list) {

				self::storeItemsMrk(
					$mcatid,
					$list['link'],
					$list['product_code'],
					$list['title_en'],
					$list['title_ar'],
					$list['sdetails_en'],
					$list['sdetails_ar'],
					$list['details_en'],
					$list['details_ar'],
					$list['retail_price'],
					$list['retail_price_off'],
					$list['pimage'],
					$list['attribute'],
					$list['quantity']
				);
			}
			return ["status" => 200, "message" => "Record is added"];
		} else {
			return ["status" => 400, "message" => "No Record Found"];
		}
	}

	public static function storeItemsMrk($mcatid, $slug, $product_code, $title_en, $title_ar, $sdetails_en, $sdetails_ar, $details_en, $details_ar, $retail_price, $old_price, $imageName, $is_attribute, $quantity)
	{
		if (!empty($product_code)) {

			$settingInfo = Settings::where("keyname", "setting")->first();

			$product   = Product::where('item_code', $product_code)->first();
			if (empty($product->id)) {
				$product   = new Product;

				$product->slug          = $slug;

				$product->item_code     = $product_code;

				$product->title_en      = $title_en;
				$product->title_ar      = $title_ar;
				$product->sdetails_en   = $sdetails_en;
				$product->sdetails_ar   = $sdetails_ar;
				$product->details_en    = !empty($details_en) ? $details_en : $sdetails_en;
				$product->details_ar    = !empty($details_ar) ? $details_ar : $sdetails_ar;
				$product->retail_price  = !empty($old_price) ? $old_price : $retail_price;
				$product->old_price     = !empty($old_price) ? $retail_price : 0;
				$product->is_attribute  = $is_attribute;
				$product->quantity      = $quantity;

				if (!empty($old_price)) {
					$product->is_offer      = 1;
				} else {
					$product->is_offer      = 0;
				}

				$product->is_active     = "1";
				$product->display_order = '0';
				$product->image         = $imageName;
				$product->rollover_image = '';
				$product->save();
				//add category

				if (!empty($product->id)) {
					$productCat   = ProductCategory::firstOrNew(['product_id' => $product->id, "category_id" => $mcatid]);
					$productCat->product_id  = $product->id;
					$productCat->category_id = $mcatid;
					$productCat->save();
				}

				//generate QR 
				$qrtext = $product->id . '-' . $product_code;
				self::QrCodes($qrtext, $product_code);
			}
		}
	}

	//add items to products
	public static function storeItems($mcatid, $fromid, $slug, $title_en, $title_ar, $details_en, $details_ar, $retail_price, $old_price, $imageName, $imageName_roll, $is_attribute, $quantity)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		$product   = Product::firstOrNew(['title_en' => $title_en]);
		$item_code = self::serialNumber();
		$product->slug          = $slug;
		if (empty($product->item_code)) {
			$product->item_code     = $item_code;
		}
		$product->title_en      = $title_en;
		$product->title_ar      = $title_ar;
		$product->details_en    = $details_en;
		$product->details_ar    = $details_ar;
		$product->retail_price  = $retail_price;
		$product->old_price     = $old_price;
		$product->is_attribute  = $is_attribute;
		$product->quantity      = $quantity;

		if (!empty($old_price)) {
			$product->is_offer      = 1;
		} else {
			$product->is_offer      = 0;
		}

		$product->is_active     = "1";
		$product->display_order = '0';
		$product->image         = $imageName;
		$product->rollover_image = !empty($imageName_roll) ? $imageName_roll : '';
		$product->save();
		//add category

		if (!empty($product->id)) {
			$productCat   = ProductCategory::firstOrNew(['product_id' => $product->id, "category_id" => $mcatid]);
			$productCat->product_id  = $product->id;
			$productCat->category_id = $mcatid;
			$productCat->save();
			self::updateUpperCateory($product->id, $mcatid);
		}
		if (!empty($product->id)) {
			self::getItemsImagesFromApi($fromid, $product->id);
		}
		//generate QR 
		$qrtext = $product->id . '-' . $item_code;
		self::QrCodes($qrtext, $item_code);
	}


	public static function serialNumber()
	{
		$settingInfo = Settings::where("keyname", "setting")->first();
		$productInfo = Product::orderBy("id", "desc")->first();
		if (!empty($productInfo->id)) {
			$lastProdId = ($productInfo->id + 1);
		} else {
			$lastProdId = 1;
		}
		$seriamNum = $settingInfo->prefix . sprintf('%0' . $settingInfo->item_code_digits . 's', $lastProdId);
		return $seriamNum;
	}

	public static function QrCodes($qrtext, $itemcode)
	{
		$qrCode = new QrCode();
		$qrCode->setText($qrtext)
			->setSize(300)
			->setPadding(10)
			->setErrorCorrection('high')
			->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
			->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
			// Path to your logo with transparency
			->setLogo(public_path('assets/images/kash5a.png'))
			// Set the size of your logo, default is 48
			->setLogoSize(98)
			->setImageType(QrCode::IMAGE_TYPE_PNG);
		$qrCode->save(public_path('uploads/product/qr/' . $itemcode . ".png"));
	}

	public static function updateUpperCateory($id, $category)
	{

		$upperCategory = Categories::where("id", $category)->first();
		if (!empty($upperCategory->parent_id)) {
			$upperParentCategory = Categories::where("id", $upperCategory->parent_id)->first();
			if (empty(self::isCategoryExists($id, $upperParentCategory->id))) {
				$filerec = new ProductCategory;
				$filerec->product_id    = $id;
				$filerec->category_id   = $upperParentCategory->id;
				$filerec->save();
				//
				if (!empty($upperParentCategory->id)) {
					self::updateUpperCateory($id, $upperParentCategory->id);
				}
			}
		}
	}


	public static function isCategoryExists($product_id, $category_id)
	{
		$filerec = ProductCategory::where('product_id', $product_id)->where('category_id', $category_id)->first();
		if (!empty($filerec->id)) {
			return 1;
		} else {
			return 0;
		}
	}

	//update images
	public static function uploadItemGallery($id, $imageName, $imageName_roll)
	{
		//original
		/*$path_original     = 'https://www.hakumkw.com/uploads/product/original/'.$imageName;
        $filename_original = basename($path_original);
		if(!File::exists(public_path('uploads/product/original/' . $filename_original))){
        Image::make($path_original)->save(public_path('uploads/product/original/' . $filename_original));
		}*/
		//product
		$path_product     = 'https://www.hakumkw.com/uploads/product/' . $imageName;
		$filename_product = basename($path_product);
		if (!File::exists(public_path('uploads/product/' . $filename_product))) {
			Image::make($path_product)->save(public_path('uploads/product/' . $filename_product));
		}
		//thumb
		$path_thumb     = 'https://www.hakumkw.com/uploads/product/thumb/' . $imageName;
		$filename_thumb = basename($path_thumb);
		if (!File::exists(public_path('uploads/product/thumb/' . $filename_thumb))) {
			Image::make($path_thumb)->save(public_path('uploads/product/thumb/' . $filename_thumb));
		}

		if (!empty($imageName_roll)) {
			//original
			$path_original     = 'https://www.hakumkw.com/uploads/product/original/' . $imageName_roll;
			$filename_original = basename($path_original);
			if (!File::exists(public_path('uploads/product/original/' . $filename_original))) {
				Image::make($path_original)->save(public_path('uploads/product/original/' . $filename_original));
			}
			//product
			$path_product     = 'https://www.hakumkw.com/uploads/product/' . $imageName_roll;
			$filename_product = basename($path_product);
			if (!File::exists(public_path('uploads/product/' . $filename_product))) {
				Image::make($path_product)->save(public_path('uploads/product/' . $filename_product));
			}
			//thumb
			$path_thumb     = 'https://www.hakumkw.com/uploads/product/thumb/' . $imageName_roll;
			$filename_thumb = basename($path_thumb);
			if (!File::exists(public_path('uploads/product/thumb/' . $filename_thumb))) {
				Image::make($path_thumb)->save(public_path('uploads/product/thumb/' . $filename_thumb));
			}
		}

		//get gallery images
		$galleryImages = ProductGallery::where("product_id", $id)->get();
		if (!empty($galleryImages) && count($galleryImages) > 0) {
			foreach ($galleryImages as $galleryImage) {
				self::uploadItemGalleryPics($galleryImage->image);
			}
		}
	}


	public static function uploadItemGalleryPics($imageName)
	{
		//original
		/*$path_original     = 'https://www.hakumkw.com/uploads/product/original/'.$imageName;
        $filename_original = basename($path_original);
		if(!File::exists(public_path('uploads/product/original/' . $filename_original))){
        Image::make($path_original)->save(public_path('uploads/product/original/' . $filename_original));
		}*/
		//product
		$path_product     = 'https://www.hakumkw.com/uploads/product/' . $imageName;
		$filename_product = basename($path_product);
		if (!File::exists(public_path('uploads/product/' . $filename_product))) {
			Image::make($path_product)->save(public_path('uploads/product/' . $filename_product));
		}
		//thumb
		$path_thumb     = 'https://www.hakumkw.com/uploads/product/thumb/' . $imageName;
		$filename_thumb = basename($path_thumb);
		if (!File::exists(public_path('uploads/product/thumb/' . $filename_thumb))) {
			Image::make($path_thumb)->save(public_path('uploads/product/thumb/' . $filename_thumb));
		}
	}

	public function listProductsImages($catid)
	{

		if (empty($catid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}


		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}


		$productLists = DB::table('gwc_products_category')
			->select(
				'gwc_products.id',
				'gwc_products.image',
				'gwc_products.rollover_image'
			)
			->join('gwc_products', 'gwc_products.id', '=', 'gwc_products_category.product_id')
			->where(['category_id' => $catid])->where('gwc_products.is_active', '!=', 0);

		$productLists = $productLists->orderBy("gwc_products.id", "desc")->get();
		if (!empty($productLists) && count($productLists) > 0) {
			foreach ($productLists as $product) {
				$id = $product->id;
				$imageName = $product->image;
				$imageName_roll = !empty($product->rollover_image) ? $product->rollover_image : '';
				self::uploadItemGallery($id, $imageName, $imageName_roll);
			}
		}


		$success['data'] = ['productLists' => $productLists];
		return response()->json($success, $this->successStatus);
	}




	public function getProductsGalleryImages($productid)
	{

		if (empty($productid)) {
			$success['data'] = trans('webMessage.idmissing');
			return response()->json($success, $this->failedStatus);
		}

		if (!empty(app()->getLocale())) {
			$strLang = app()->getLocale();
		} else {
			$strLang = "en";
		}

		$productLists = DB::table('gwc_products_gallery')->where(['product_id' => $productid])->get();

		$success['data'] = ['productLists' => $productLists];
		return response()->json($success, $this->successStatus);
	}

	public static function getItemsImagesFromApi($productid_from, $productid_to)
	{

		$url = "https://www.hakumkw.com/getProductsGalleryImages/" . $productid_from;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		$curlData = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($curlData, TRUE);
		if (!empty($res['data']['productLists']) && count($res['data']['productLists']) > 0) {
			$lists = $res['data']['productLists'];
			foreach ($lists as $list) {
				self::storeItemsImages($productid_to, $list['image']);
			}
			return ["status" => 200, "message" => "Record is added"];
		} else {
			return ["status" => 400, "message" => "No Record Found"];
		}
	}

	public static function storeItemsImages($productid, $image)
	{

		$productGallery = ProductGallery::firstOrNew(['product_id' => $productid, 'image' => $image]);
		$productGallery->product_id = $productid;
		$productGallery->image      = $image;
		$productGallery->save();
	}
}
