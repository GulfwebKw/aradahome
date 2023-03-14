<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

///////////////////////////////////////////////////////POS//////////////////////////////////////

Route::group(['prefix' => 'posv1', 'namespace' => 'posv1', 'middleware' => ['cors', 'json.response']], function () {
    Route::post('/login', 'PosAdminController@login')->name('login.api');
    Route::post('/logout', 'PosAdminController@logout')->name('logout.api');
    Route::get('/getSetting', 'PosAdminController@setting')->name('setting.api');


    Route::get('/knet_response', 'PosProductController@getKnetResponse')->name('knet_response.api');

});

Route::group(['prefix' => 'posv1', 'namespace' => 'posv1', 'middleware' => ['auth:api', 'cors', 'json.response']], function () {

    Route::post('/editProfile', 'PosAdminController@editProfile')->name('user.editProfile');
    Route::post('/supervisor_password', 'PosAdminController@supervisor_password');


    Route::post('/logout', 'PosAdminController@logout')->name('logout.api');
    Route::post('/category', 'PosProductController@category')->name('category.api');
    Route::post('/categoryChild', 'PosProductController@categoryChild')->name('categoryChild.api');
    Route::post('/products', 'PosProductController@products')->name('products.api');
    Route::post('/productsByCategory', 'PosProductController@productsByCategory')->name('productsByCategory.api');
    Route::post('/getProductDetails', 'PosProductController@getProductDetail');
    Route::get('/getAreas', 'PosProductController@getAreas')->name('getAreas.api');

    Route::post('/getCustomers', 'PosCustomerController@getCustomers')->name('customer.api');
    Route::post('/getOrders', 'PosCustomerController@getOrders')->name('order.list');
    Route::post('/getOrderItems', 'PosCustomerController@getOrderItems')->name('orderItems.list');
    Route::post('/AddNewCustomer', 'PosCustomerController@AddNewCustomer')->name('newcustomer.api');


    Route::post('/addtocart', 'PosProductController@addtocart')->name('addtocart.api');
    Route::post('/getTempOrders', 'PosProductController@getTempOrders')->name('getTemp.api');
    Route::post('/removeTempOrder', 'PosProductController@removeTempOrder')->name('removeCart.api');
    Route::post('/removeAllTempOrder', 'PosProductController@removeAllTempOrder')->name('removeAllCart.api');
    Route::post('/apply_coupon_to_cart', 'PosProductController@apply_coupon_to_cart')->name('coupon.api');
    Route::post('/addremovequantity', 'PosProductController@addremovequantity')->name('updateQty.api');
    Route::get('/getPaymentMethod', 'PosProductController@getPaymentMethod')->name('updateQty.api');

    Route::post('/checkoutConfirm', 'PosProductController@checkoutConfirm')->name('checkoutConfirm.api');
    Route::post('/order/{order}/refund', 'PosProductController@refundOrder')->name('checkoutConfirm.api');
    Route::post('/order/{order}/refund/item', 'PosProductController@refundItem')->name('checkoutConfirm.api');


    Route::get('/getCurrency', 'PosProductController@getDefaultCurrency')->name('getDefaultCurrency.api');

    Route::post('/shift/start', 'PosCashController@startShift')->name('startShift.api');
    Route::post('/shift/end', 'PosCashController@endShift')->name('endShift.api');
    Route::get('/shift/details', 'PosCashController@shiftDetails')->name('shiftDetails.api');
    Route::post('/cash/history', 'PosCashController@cashHistory')->name('cashHistory.api');
    Route::post('/cash/change', 'PosCashController@cashChange')->name('cashChange.api');
    Route::get('/user/{user}/address', 'AddressController@index');
    Route::post('/user/{user}/address', 'AddressController@store');
    Route::put('/user/{user}/address/{CustomersAddress}', 'AddressController@update');
    Route::delete('/user/{user}/address/{CustomersAddress}', 'AddressController@delete');

});

///////////////////////////////////////////////////////END POS//////////////////////////////////////


//dez order API
Route::get('getDezOrders', 'webCartController@getDezOrders')->middleware('localization');
Route::post('changeDezOrderStatus', 'webCartController@changeDezOrderStatus')->middleware('localization');



//////////////////////////////////////////////////////////IOSv1///////////////////////////////////////////////////////////////////////////
foreach (['iosv1' ,''] as $perfix)
Route::group(['prefix' => $perfix, 'namespace' => 'v1'], function () use ($perfix) {
    Route::post('createAccount', 'apiUserController@createNewAccount')->middleware('localization')->defaults('platform_prefix', ($perfix == "iosv1" ? "ios" : "android") );
    Route::post('loginAccount', 'apiUserController@loginAuthenticate')->middleware('localization');

//reset forgot pass

    Route::post('sendResetForgotPassCode', 'apiUserController@sendResetForgotPassCode')->middleware('localization');
    Route::post('resetForgotPassword', 'apiUserController@resetForgotPassword')->middleware('localization');

//regular api
    Route::get('getHome', 'apiController@getHome')->middleware('localization');
    Route::post('getHome', 'apiController@getHome')->middleware('localization');
    Route::post('getCategories', 'apiController@getCategories')->middleware('localization');
    Route::post('getProducts', 'apiController@listProducts')->middleware('localization');
    Route::post('getSectionsProducts', 'apiController@listSectionsProducts')->middleware('localization');
    Route::post('getProductDetails', 'apiController@getProductDetails')->middleware('localization');
    Route::post('postReview', 'apiUserAccountController@postReview')->middleware('localization');
    Route::post('postInquiry', 'apiUserAccountController@postInquiry')->middleware('localization');
    Route::post('getPushMessage', 'apiController@getPushMessage')->middleware('localization');
    Route::get('getPushMessage', 'apiController@getPushMessage')->middleware('localization');

    Route::post('searchResults', 'apiController@searchResults')->middleware('localization');
    Route::post('QuickSearchResults', 'apiController@QuickSearchResults')->middleware('localization');
//shopping cart
    Route::post('addtocart', 'apiCartController@addtocart')->middleware('localization');
    Route::post('getTempOrders', 'apiCartController@getTempOrders')->middleware('localization');
    Route::post('apply_coupon_to_cart', 'apiCartController@apply_coupon_to_cart')->middleware('localization');
    Route::post('removeTempOrder', 'apiCartController@removeTempOrder')->middleware('localization');
//post checkout confirm
    Route::post('orderConfirm', 'apiCartController@orderConfirm')->middleware('localization')->defaults('platform_prefix', ($perfix == "iosv1" ? "ios" : "android") );
    Route::post('addremovequantity', 'apiCartController@addremovequantity')->middleware('localization');


//user
    Route::get('userDetails', 'apiUserAccountController@userDetails')->middleware('localization');
    Route::post('editProfile', 'apiUserAccountController@postEditProfile')->middleware('localization');
    Route::post('changepass', 'apiUserAccountController@postChangePassword')->middleware('localization');
    Route::post('logout', 'apiUserAccountController@logout')->middleware('localization');
//payment
    Route::post('getTransactions', 'apiUserAccountController@getTransactionsLists')->middleware('localization');

//address
    Route::get('userAddress', 'apiUserAccountController@userAddress')->middleware('localization');
    Route::post('newAddress', 'apiUserAccountController@newAddress')->middleware('localization');
    Route::post('editAddress', 'apiUserAccountController@editAddress')->middleware('localization');
    Route::post('deleteAddress', 'apiUserAccountController@deleteAddress')->middleware('localization');
    Route::post('getCSA', 'apiController@getCSA')->middleware('localization');
    Route::get('getAreas', 'apiController@getAreasOnly')->middleware('localization');
//wish items
    Route::post('userWishItems', 'apiUserAccountController@getWishItems')->middleware('localization');
    Route::get('userWishItems', 'apiUserAccountController@getWishItems')->middleware('localization');
    Route::post('deleteWishItem', 'apiUserAccountController@deleteWishItem')->middleware('localization');
    Route::post('saveWishItem', 'apiUserAccountController@saveWishItem')->middleware('localization');
//user orders
    Route::post('userOrders', 'apiUserAccountController@getUserOrders')->middleware('localization');
    Route::get('userOrders', 'apiUserAccountController@getUserOrders')->middleware('localization');
    Route::post('userOrdersDetails', 'apiUserAccountController@getUserOrdersDetails')->middleware('localization');
    Route::post('releasepayment', 'apiController@releasepayment')->middleware('localization');

    Route::post('getSinglePage', 'apiController@getSinglePage')->middleware('localization');
    Route::post('postNewsLetter', 'apiController@postNewsLetter')->middleware('localization');
    Route::get('getSocialLinks', 'apiController@getSocialLinks')->middleware('localization');
    Route::get('getFAQ', 'apiController@getFAQ')->middleware('localization');
    Route::get('getContactDetails', 'apiController@getContactDetails')->middleware('localization');
    Route::post('postContactForm', 'apiController@postContactForm')->middleware('localization');
    Route::post('getUserReviews', 'apiUserAccountController@getUserReviews')->middleware('localization');

    Route::post('getOffers', 'apiController@getOffers')->middleware('localization');

    Route::get('getCurrency', 'apiController@getDefaultCurrency')->middleware('localization');

    Route::get('getGateways/{country_id?}' , 'apiController@getGateways')->middleware('localization')->defaults('platform_prefix', ($perfix == "iosv1" ? "ios" : "android") );
});


