<?php

namespace App\Exports;

use App\Country;
use App\Currency;
use App\Product;
use App\ProductAttribute;
use App\ProductGallery;
use App\ProductOptions;
use App\ProductsQuantity;
use App\Settings;
use App\Brand;
use App\Categories;
use App\ProductCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExportHuawei implements FromCollection, WithHeadings
{

    public $country;
    public $lang;
    public $inventory;

    public function __construct(Country $country,$lang = "en",$inventory = null)
    {
        $this->country = $country;
        $this->lang = $lang;
        $this->inventory = $inventory;
    }

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{


        $country = $this->country;
        $strLang = $this->lang;
		$prods = [];
		$products =   Product::where('is_active', 1)->where('is_export_active', 1)
            ->when($country->exists, function ($builder) use ($country) {
                if (in_array($country->shipment_method  ,["zoneprice" , "dhl"])) {
                    $builder->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('gwc_products.height', '>', 0)
                                ->where('gwc_products.width', '>', 0)
                                ->where('gwc_products.depth', '>', 0);
                        })->orWhere('gwc_products.weight', '>', 0);
                    });
                }

            })->when($this->inventory != null , function ($query){
                $query->whereIn('id', function($sub){
                    $sub->select('product_id')
                        ->from(with(new ProductsQuantity())->getTable())
                        ->where(function ($f) {
                            $f->where('inventory_id', $this->inventory);
                        });
                });
            })
            ->get();
		if (!empty($products) && count($products) > 0) {
			$brand = '';
			foreach ($products as $product) {

				if (!empty($product->image)) {
					$imageUrl = url('uploads/product/thumb/' . $product->image);
				} else {
					$imageUrl = url('uploads/no-image.png');
				}
                $link = url($strLang.'/directdetails/' . $product->id . '/' . $product->slug);
                $additional_image_link = self::getGalleries($product->id);

				if ($strLang == "en") {
					$title       = substr(strip_tags($product->title_en), 0, 150);
					$description = substr(strip_tags($product->sdetails_en), 0, 5000);
					$htmldescription = substr($product->details_en, 0, 5000);
				} else {
					$title       = substr(strip_tags($product->title_ar), 0, 150);
					$description = substr(strip_tags($product->sdetails_ar), 0, 5000);
					$htmldescription = substr($product->details_ar, 0, 5000);
				}

				if (!empty($product->old_price)) {
					$price    =		 (float)$product->old_price;
					$salePrice       = (float)$product->retail_price;
				} else {
					$price    = (float)$product->retail_price;
					$salePrice      = '';
				}

				if (!empty($product->brand_id)) {
					$brandInfo   = Brand::where('id', $product->brand_id)->first();
					if (!empty($brandInfo->title_en) || !empty($brandInfo->title_ar)) {
						$brand = $strLang == "en" ? $brandInfo->title_en : $brandInfo->title_ar;
					} else {
						$brand = '';
					}
				}

				$aquantity = self::IsAvailableQuantity($product->id);
				$stocktxt = trans('webMessage.instock');
				if (empty($aquantity)) {
					$stocktxt = trans('webMessage.outofstock');
					$aquantity = 0;
				}
                if ( $country->exists ) {
                    $tprice = Currency::convert($price, $country->currency);
                    $price = $tprice['price'] ?? $tprice->price ?? $tprice[0]->price ?? $price;

                    $tprice = Currency::convert($salePrice, $country->currency);
                    $salePrice = $tprice['price'] ?? $tprice->price ?? $tprice[0]->price ?? $salePrice;

                    $imageUrl = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $imageUrl);
                    $link = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $link);
                    $additional_image_link = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $additional_image_link);
                }
				$prods[] = [
					'offerId'        => $product->id,
					'title'          => $title,
					'description'    => $description,
					'availability'   => $stocktxt,
					'condition'      => 'new',
					'price'          => $price. ' ' . ($country->exists ? \App\Currency::defaultCMS(true , $country->currency) :  \App\Currency::defaultCMS()),
					'salePrice'      => !empty($salePrice) ? $salePrice . ' ' . ($country->exists ? \App\Currency::defaultCMS(true , $country->currency) :  \App\Currency::defaultCMS()) : '',
					'link'           => $link,
					'image_link'     => (string)$imageUrl,
					'brand'          => $brand,
					'extImageLinks' => $additional_image_link,
					'gtin' => '',
					'mpn' => '',
					'productTypes' => '[' . $this->getCatTreeNameByPid($product->id) . ']',
				];
			}
		}
		return collect($prods);
	}


	public function headings(): array
	{
		return ['offerId', 'title', 'description', 'availability', 'condition', 'price', 'salePrice', 'webLink', 'imageLink', 'brand', 'extImageLinks', 'gtin', 'mpn', 'productTypes'];
	}


	public static function getGalleries($product_id)
	{
		$imageUrl = '';
		$settingInfo   = Settings::where("keyname", "setting")->first();
		$galleryLists   = ProductGallery::where('product_id', $product_id)->orderBy('display_order', $settingInfo->default_sort)->get();
		if (!empty($galleryLists) && count($galleryLists) > 0) {
			foreach ($galleryLists as $galleryList) {
				if (!empty($galleryList->image)) {
					$imageUrl .= url('uploads/product/thumb/' . $galleryList->image) . ',';
				}
			}
		}
		return trim($imageUrl, ',');
	}

	public static function IsAvailableQuantity($product_id)
	{
		$qty = 0;
		$productDetails   = Product::where('id', $product_id)->first();
		if (empty($productDetails['is_attribute'])) {
			$qty   = $productDetails['quantity'];
		} else {
			$qty     = ProductAttribute::where('product_id', $product_id)->get()->sum('quantity');
		}

		$optyQty = ProductOptions::where('product_id', $product_id)->get()->sum('quantity'); //option
		$qty     = $qty + $optyQty;

		if (empty($qty)) {
			$qty = 0;
		}
		return $qty;
	}

	//get categories
	public  function getCatTreeNameByPid($productid)
	{
		$txt = '';

		$catInfo1 = self::getProductCatName($productid);
		if (!empty($catInfo1->parent_id)) {
			$txt .= $this->getChildCatName($catInfo1->parent_id);
		}
		if (!empty($catInfo1->id)) {
			$txt .=  $catInfo1['name_' .$this->lang];
		}
		return $txt;
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
	public function getChildCatName($id)
	{
		$txt = '';
		$ProdCat = Categories::where("id", $id)->first();
		if (!empty($ProdCat->parent_id)) {
			$txt .= $this->getChildCatName($ProdCat->parent_id);
		}
		if (!empty($ProdCat->id)) {
			$txt .= $ProdCat['name_' . $this->lang] . ' > ';
		}
		return $txt;
	}
}
