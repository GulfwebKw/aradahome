<?php

namespace App\Http\Controllers;

use App\Country;
use App\Currency;
use App\Orders;
use App\OrdersDetails;
use App\OrdersOption;
use App\OrdersTemp;
use App\OrdersTempOption;
use App\Product;
use App\ProductAttribute;
use App\ProductOptions;
use App\ProductOptionsCustom;
use App\ProductOptionsCustomChosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use DHL\Entity\AM\GetQuote;
use DHL\Datatype\AM\PieceType;
use DHL\Client\Web as WebserviceClient;
use Mockery\Undefined;

class ShipmentController extends Controller
{
    public static function getPrice($country, $order = null, $convertPrice = true, $isExpress = false)
    {
        $settingInfo = \App\Settings::where("keyname", "setting")->first();
        if (!$country instanceof Country) {
            $country = Country::findOrFail($country);
        }
        $mainCountry = $country;
        $state = $city = null;
        $countries = [];
        while ($country->parent_id != 0) {
            $countries[] = $country;
            $country = Country::findOrFail($country->parent_id);
        }
        if (count($countries) == 0) {
            $state = Country::where('is_active', 1)->where('parent_id', $country->id)->first();
            $city = Country::where('is_active', 1)->where('parent_id', $state->id)->first();
        } elseif (count($countries) == 1) {
            $state = $countries[0];
            $city = Country::where('is_active', 1)->where('parent_id', $state->id)->first();
        } elseif (count($countries) == 2) {
            $state = $countries[1];
            $city = $countries[0];
        }
        if ($country->shipment_method == "flatrate") {
            $fee = ($isExpress and $settingInfo->is_express) ?  $mainCountry->express_delivery_fee : $mainCountry->delivery_fee;
            if (
                $convertPrice and
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods())  and Route::current()->uri() == "{locale}/checkout")
            ) {
                $price = Currency::convertTCountry($fee);
                return $price['price'] ?? $price->price ?? $price[0]->price ?? $fee;
            } else {
                return $fee;
            }
        }
        if ($country->shipment_method == "dhl") {
            if (
                $convertPrice and
                !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
                and
                !(in_array('POST', Route::current()->methods())  and Route::current()->uri() == "{locale}/checkout")
            ) {
                $price = Currency::convertTCountry(self::calculateDHL($mainCountry, $country, $order, $state, $city));
                return $price['price'] ?? $price->price ?? $price[0]->price ?? self::calculateDHL($mainCountry, $country, $order, $state, $city);
            } else {
                return self::calculateDHL($mainCountry, $country, $order, $state, $city);
            }
        }
        if (
            $convertPrice and
            !(Str::startsWith(trim(Route::current()->uri(), '/'), \App\Http\Controllers\Common::noRedirectWildCard()))
            and
            !(in_array('POST', Route::current()->methods())  and Route::current()->uri() == "{locale}/checkout")
        ) {
            $price = Currency::convertTCountry(self::calculateZone($mainCountry, $country, $order));
            return $price['price'] ?? $price->price ?? $price[0]->price ??  self::calculateZone($mainCountry, $country, $order);
        } else {
            return  self::calculateZone($mainCountry, $country, $order);
        }
    }

    private static function calculateZone($mainCountry, $country, $order)
    {
        try {
            if ($order instanceof OrdersDetails) {
                $orders = Orders::where('oid', $order->id)->get();
            } elseif ($order instanceof Orders) {
                $orders = Orders::where('oid', $order->oid)->get();
            } elseif ($order instanceof OrdersTemp) {
                $orders = OrdersTemp::where('unique_sid', $order->unique_sid)->get();
            } else {
                $orders = OrdersTemp::where('unique_sid', $order)->get();
            }
            if ($orders->count() == 0)
                return $mainCountry->delivery_fee;

            $shipmentPrice = 0;
            $TotalShipmentWeightAndMass = 0;
            foreach ($orders as $order) {
                $product = Product::findOrFail($order->product_id);
                $maxWeight = self::getMaxWeight($product, $order);
                $mass = floatval($product->height) * floatval($product->width) * floatval($product->depth) / 5000;
                $valueOfWeightAndMass = max($maxWeight, $mass);
                $TotalShipmentWeightAndMass += $valueOfWeightAndMass;
                //            $price = $country->zones->prices()->where('from' , '<=' , $valueOfWeightAndMass)
                //                                            ->where('to' , '>' , $valueOfWeightAndMass)->first();
                //            $shipmentPrice += $valueOfWeightAndMass * $price->price ;
            }
            if ($country->zones == null)
                return $mainCountry->delivery_fee;
            $price = $country->zones->prices()->where('from', '<=', $TotalShipmentWeightAndMass)
                ->where('to', '>', $TotalShipmentWeightAndMass)->first();
            return $price->price * $order->quantity  ?? $mainCountry->delivery_fee;
            //        return  $shipmentPrice;
        } catch (\Exception $exception) {
            return $mainCountry->delivery_fee;
        }
    }

    private static function getMaxWeight($product, $order)
    {
        $maxWeight = $product->weight;
        if ($product->is_attribute) {
            $atterbute = ProductAttribute::where('product_id', $product->id)
                ->when($order->size_id > 0, function ($query) use ($order) {
                    $query->where('size_id', $order->size_id);
                })
                ->when($order->color_id > 0, function ($query) use ($order) {
                    $query->where('color_id', $order->color_id);
                })
                ->selectRaw('MAX(weight) as weight')->first();
            $maxWeight = max($maxWeight, $atterbute->weight);
            if ($order instanceof OrdersTemp) {
                $optionDetails = OrdersTempOption::where('oid', $order->id)->get();
            } else {
                $optionDetails = OrdersOption::where("oid", $order->id)->get();
            }
            if (!empty($optionDetails) && count($optionDetails) > 0) {
                foreach ($optionDetails as $optionDetail) {
                    $explode = explode(",", $optionDetail->option_child_ids);
                    for ($i = 0; $i < count($explode); $i++) {
                        $option = ProductOptions::find($explode[$i]);
                        $maxWeight = max($maxWeight, $option->weight);
                    }
                }
            }
        }
        return floatval($maxWeight);
    }
    
    private static function calculateDHL($mainCountry, $country, $order, $state, $city)
    {
        try {
            if ($order instanceof OrdersDetails) {
                $orders = Orders::where('oid', $order->id)->get();
            } elseif ($order instanceof Orders) {
                $orders = Orders::where('oid', $order->oid)->get();
            } elseif ($order instanceof OrdersTemp) {
                $orders = OrdersTemp::where('unique_sid', $order->unique_sid)->get();
            } else {
                $orders = OrdersTemp::where('unique_sid', $order)->get();
            }
            if ($orders->count() == 0 or !(env('DHL_SITE_ID', false) and env('DHL_PASSWORD', false) and env('DHL_PRODUCT_SHORT_NAME', false) and env('DHL_FROM', false)))
                return self::calculateZone($mainCountry, $country, $order);
            $dt = date("Y-m-d");
            // Test a getQuote using DHL XML API
            $sample = new GetQuote();
            $sample->SiteID = env('DHL_SITE_ID');
            $sample->Password = env('DHL_PASSWORD');
            // Set values of the request
            $sample->MessageTime = date('c');
            $sample->MessageReference = ((string)time()) . ((string)time()) . ((string)time());
            $sample->BkgDetails->Date = date('Y-m-d', strtotime("$dt +0 day"));
            $totalAmount = 0;
            foreach ($orders as $order) {
                $product = Product::findOrFail($order->product_id);
                $piece = new PieceType();
                $piece->PieceID = $product->id;
                $piece->Height = $product->height * $order->quantity;
                $piece->Depth = $product->depth * $order->quantity;
                $piece->Width = $product->width * $order->quantity;
                $piece->Weight = self::getMaxWeight($product, $order) * $order->quantity;
                // $piece->Height = 1;
                // $piece->Depth = 1;
                // $piece->Width = 1;
                // $piece->Weight = 0.5;
                $sample->BkgDetails->addPiece($piece);
                $totalAmount += $order->unit_price * $order->quantity;
            }
            $sample->BkgDetails->ReadyTime = 'PT10H21M';
            $sample->BkgDetails->ReadyTimeGMTOffset = '+01:00';
            $sample->BkgDetails->DimensionUnit = 'CM';
            $sample->BkgDetails->WeightUnit = 'KG';
            $sample->BkgDetails->PaymentCountryCode = 'KW';
            // $sample->BkgDetails->QtdShp->GlobalProductCode = 'P';
            // $sample->BkgDetails->QtdShp->LocalProductCode = 'P';
            $sample->BkgDetails->IsDutiable = 'Y';

            if (env('DHL_ACCOUNT_NUMBER')) {
                $sample->BkgDetails->PaymentAccountNumber =  env('DHL_ACCOUNT_NUMBER');
            }
            $sample->From->CountryCode = 'KW';
            $sample->From->City = env('DHL_FROM');
            $sample->To->CountryCode = strtoupper($country->code);
            $sample->To->City = $city->name_en;
            // $sample->To->CountryCode = 'SA'; 
            // $sample->To->City = "JEDAH";

            $sample->Dutiable->DeclaredCurrency = strtoupper($country->currency);
            $sample->Dutiable->DeclaredValue = $totalAmount;


            $client = new WebserviceClient('production');
            $xml = $client->call($sample);
            $xml = simplexml_load_string($xml);
            $xml = json_decode(json_encode($xml), true);

            $allShippingPrice = [];
            foreach ($xml['GetQuoteResponse']['BkgDetails']['QtdShp'] as $item) {
                // dump([$item['ProductShortName'] => $item['ShippingCharge']]);
                if (in_array($item['ProductShortName'], ["EXPRESS WORLDWIDE", "ECONOMY SELECT"])) {
                    array_push($allShippingPrice, (float) $item['ShippingCharge']);
                }
            }
            // dd([self::getMaxWeight($product, $order) => $totalAmount], @$xml);
            // die;
            return min($allShippingPrice);
        } catch (\Exception $exception) {
            return  self::calculateZone($mainCountry, $country, $order);
        }
    }
}
