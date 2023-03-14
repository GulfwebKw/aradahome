<?php

namespace App\Observers;

use App\Product;
use App\ProductsQuantity;

class ProductsQuantityObserver
{

    /**
     * Handle the inventory "saved" event.
     *
     * @param  \App\ProductsQuantity  $productsQuantity
     * @return void
     */
    public function saved(ProductsQuantity $productsQuantity)
    {
        $this->updateProductsQuantity($productsQuantity );
    }


    /**
     * Handle the products quantity "deleted" event.
     *
     * @param  \App\ProductsQuantity  $productsQuantity
     * @return void
     */
    public function deleted(ProductsQuantity $productsQuantity)
    {
        $this->updateProductsQuantity($productsQuantity );
    }

    /**
     * Handle the products quantity "restored" event.
     *
     * @param  \App\ProductsQuantity  $productsQuantity
     * @return void
     */
    public function restored(ProductsQuantity $productsQuantity)
    {
        $this->updateProductsQuantity($productsQuantity );
    }


    private function updateProductsQuantity($ProductsQuantity ){
        $quantityDetails = ProductsQuantity::where('product_id' , $ProductsQuantity->product_id)
            ->where('is_qty_deduct' , 1)
            ->whereHas('inventory', function ($query) {
                return $query->where('is_active',  1);
            })->selectRaw('product_id , sum(quantity) as quantity')->first();
        if ( $quantityDetails->product_id != null ) {
            $product = Product::find($quantityDetails->product_id);
            if ( $product != null )
                $product->update(['quantity' => max($quantityDetails->quantity, 0)]);
        } else {
            $product = Product::find($ProductsQuantity->product_id);
            if ( $product != null )
                $product->update(['quantity' =>0]);
        }
    }
}
