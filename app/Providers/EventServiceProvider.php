<?php

namespace App\Providers;

use App\Inventory;
use App\Observers\InventoryObserver;
use App\Observers\ProductsQuantityObserver;
use App\ProductsQuantity;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Inventory::observe(InventoryObserver::class);
        ProductsQuantity::observe(ProductsQuantityObserver::class);
        //
    }
}
