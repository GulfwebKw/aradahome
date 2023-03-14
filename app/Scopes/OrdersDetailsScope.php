<?php

namespace App\Scopes;

use App\Country;
use App\Settings;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use function foo\func;

class OrdersDetailsScope implements Scope
{

    public $canNotView = [
        'gwc/payment-links',
        'gwc/orders/status/ajax',
        'gwc/payments',
        '{locale}/order-details',
        '{locale}/order-print',
        '{locale}/checkout',
        'checkout',
        'myfatoorah_response_accept',
        'knet_response',
        'knet_response_q8link_return',
        'paypal_return',
        'knet_response_accept',
        'tahseel_response_accept',
        'cbk_response_accept',
        'api',
        'masterCard_response'
    ];
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ( Route::current() != null ) {
            if (!(Str::startsWith(trim(Route::current()->uri(), '/'), $this->canNotView))) {
                $builder->whereNull('linkDescription');
            }
        }
    }
}
