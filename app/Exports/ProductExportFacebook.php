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
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExportFacebook implements FromCollection, WithHeadings
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
        $products = Product::where('is_active', 1)->where('is_export_active', 1)
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
                $link = url($strLang.'/details/' . $product->id . '/' . $product->slug);
                $additional_image_link = self::getGalleries($product->id);

                if ($strLang == "en") {
                    $title = substr(strip_tags($product->title_en), 0, 150);
                    $description = substr(strip_tags($product->sdetails_en), 0, 5000);
                    $htmldescription = substr($product->details_en, 0, 5000);
                } else {
                    $title = substr(strip_tags($product->title_ar), 0, 150);
                    $description = substr(strip_tags($product->sdetails_ar), 0, 5000);
                    $htmldescription = substr($product->details_ar, 0, 5000);
                }

                if (!empty($product->countdown_datetime) && strtotime($product->countdown_datetime) > strtotime(date('Y-m-d'))) {
                    $retail_price = (float)$product->countdown_price;
                    $old_price = (float)$product->retail_price;
                } else {
                    $retail_price = (float)$product->retail_price;
                    $old_price = (float)$product->old_price;
                }

                if (!empty($product->brand_id)) {
                    $brandInfo = Brand::where('id', $product->brand_id)->first();
                    if (!empty($brandInfo->title_en) || !empty($brandInfo->title_ar)) {
                        $brand = $strLang == "en" ? $brandInfo->title_en : $brandInfo->title_ar;
                    } else {
                        $brand = '';
                    }
                }

                $aquantity = self::IsAvailableQuantity($product->id);
                $stocktxt = 'in stock';
                if (empty($aquantity)) {
                    $stocktxt = 'out of stock';
                    $aquantity = 0;
                }
                if ( $country->exists ) {
                    $tprice = Currency::convert($retail_price, $country->currency);
                    $retail_price = $tprice['price'] ?? $tprice->price ?? $tprice[0]->price ?? $retail_price;

                    //$tprice = Currency::convert($old_price, $country->currency);
                    //$old_price = $tprice['price'] ?? $tprice->price ?? $tprice[0]->price ?? $retail_price;

                    $imageUrl = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $imageUrl);
                    $link = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $link);
                    $additional_image_link = str_replace(config('app.url'), strtolower($country->code) . '.' . config('app.url'), $additional_image_link);
                }
                $prods[] = [
                    'id' => $product->id,
                    'title' => $title,
                    'description' => $description,
                    'rich_text_description' => $htmldescription,
                    'availability' => $stocktxt,
                    'condition' => 'new',
                    'price' => $retail_price . ' ' . ($country->exists ? \App\Currency::defaultCMS(true , $country->currency) :  \App\Currency::defaultCMS()),
                    'link' => $link,
                    'image_link' => (string)$imageUrl,
                    'brand' => $brand,
                    'additional_image_link' => $additional_image_link,
                    'inventory' => $aquantity
                ];

            }
        }
        return collect($prods);
    }


    public function headings(): array
    {
        return ['id', 'title', 'description', 'rich_text_description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand', 'additional_image_link', 'inventory'];
    }


    public static function getGalleries($product_id)
    {
        $imageUrl = '';
        $settingInfo = Settings::where("keyname", "setting")->first();
        $galleryLists = ProductGallery::where('product_id', $product_id)->orderBy('display_order', $settingInfo->default_sort)->get();
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
        $productDetails = Product::where('id', $product_id)->first();
        $qty = $productDetails['quantity'];
        return $qty;
    }

}