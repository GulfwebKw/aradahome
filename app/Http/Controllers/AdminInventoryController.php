<?php

namespace App\Http\Controllers;



use App\Inventory;
use Illuminate\Http\Request;

use App\Settings;
use App\Product;
use Illuminate\Support\Facades\DB;


class AdminInventoryController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Settings::where("keyname","setting")->first();

        $totalProducts = 0;
        $totalAmount = 0;

        $Inventory = Inventory::when($request->inventory_id , function ($query) use($request) {
            $query->where('id' , $request->inventory_id );
        } , function ($query){
            $query->where('can_delete' , 0 );
        })->first();
//        DB::enableQueryLog();
        $products = Product::rightJoin('gwc_products_quantity','gwc_products.id', '=', 'gwc_products_quantity.product_id')
            ->where( 'gwc_products_quantity.inventory_id' , $Inventory->id)
            ->selectRaw('gwc_products.* , SUM(gwc_products_quantity.quantity) as quantities, "'.$Inventory->title.'" as inventoryTitle')
            ->groupBy('gwc_products.id')
            ->orderBy('quantities','DESC')
            ->when($request->searchCat , function ($query) use($request) {
                $query->where( function ($searchQuery) use($request) {
                    $searchQuery->where('item_code' , $request->searchCat )
                        ->orWhere('title_en' , 'like' , '%'.$request->searchCat.'%')
                        ->orWhere('title_ar' , 'like' , '%'.$request->searchCat.'%');
                });
            })
            ->paginate($settings->item_per_page_back);
//            dd(DB::getQueryLog());
        foreach($products as $product){
            $totalProducts++;
            $totalAmount += ($product->countdown_price) ?: ($product->retail_price);
        }

        return view('gwc.inventory.index',['products'=>$products,'inventorySelected' =>$Inventory->id, 'totalProducts'=>$totalProducts,'totalAmount'=>$totalAmount,'settings'=>$settings]);
    }


}