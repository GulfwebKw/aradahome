<?php

namespace App\Observers;

use App\Inventory;
use App\Product;
use App\ProductsQuantity;

class InventoryObserver
{

    /**
     * Handle the inventory "deleted" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function deleted(Inventory $inventory)
    {
        ProductsQuantity::quantityUpdateAfterInventoryActivityChange($inventory , 'store');
    }

    /**
     * Handle the inventory "restored" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function restored(Inventory $inventory)
    {
        ProductsQuantity::quantityUpdateAfterInventoryActivityChange($inventory , 'store');
    }



    /**
     * Handle the inventory "saved" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function saved(Inventory $inventory)
    {
//        $this->updateProductQuantity($inventory);
    }



    /**
     * Handle the inventory "deleting" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function deleting(Inventory $inventory)
    {
//        dd('deleting!');
    }

}
