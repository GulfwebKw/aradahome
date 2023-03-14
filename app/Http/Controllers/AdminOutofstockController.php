<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Settings;
use App\Product;
use App\Orders;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class AdminOutofstockController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname","setting")->first();

        $totalOrders = [];
        $totalPrice = [];
        $outOfStocks = [];
        $totalProducts = 0;

        $products = Product::where('quantity',0)->get();

        foreach($products as $product){
            $totalProducts++;
            $totalOrders[$product->id] = 0;
            $totalPrice[$product->id] = 0;

            $orders = Orders::where('product_id',$product->id)->get();
            if($orders){
                foreach($orders as $order){
                    //calculating the total orders of the product
                    $totalOrders[$product->id] += $order->quantity;

                    //calculating the total price of the product
                    $totalPrice[$product->id] += $order->quantity * $order->unit_price;
                }
            }

            //making the response
            $outOfStocks[] = [
                'product' => $product,
                'totalOrders' => $totalOrders[$product->id],
                'totalPrice' => $totalPrice[$product->id]
            ];
        }

        //making a collection from the array and paginating the array
        $page = null;
        $perPage = $settings->item_per_page_back;
        $options = ['path' => $request->url(), 'query' => $request->query()];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $outOfStocks instanceof Collection ? $outOfStocks : Collection::make($outOfStocks);
        $outOfStocks = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        return view('gwc.outofstock.index',['products'=>$outOfStocks, 'totalProducts'=>$totalProducts, 'settings'=>$settings]);
    }


    public function updateQty(Request $request)
    {
        $id = explode("-", $request->id);
        $product = Product::find($id[1]);
        if ($product) {
            $product->quantity = $request->quantity;
            $product->save();
            $defaultInventory = Inventory::where('can_delete', 0)->first();
            $pQuantity = ProductsQuantity::where('product_id', $product->id)
                ->where('inventory_id', @$defaultInventory->id)
                ->first();
            if ($pQuantity) {
                $pQuantity->quantity = $request->quantity;
                $pQuantity->save();
            }

            return ["status" => 200, "message" => ""];
        }
        return ["status" => 404, "message" => ""];
    }


}
