<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('testing', function () {
// 	$curl = curl_init();
// 	curl_setopt_array(
// 		$curl,
// 		array(
// 			CURLOPT_URL => 'https://ap-gateway.mastercard.com/api/rest/version/69/merchant/Alaradah/order/89e73ccf085778abdbf1b14c8028d4a1/transaction/TXN89e73ccf085778abdbf1b14c8028d4a1',
// 			CURLOPT_RETURNTRANSFER => true,
// 			CURLOPT_ENCODING => '',
// 			CURLOPT_MAXREDIRS => 10,
// 			CURLOPT_TIMEOUT => 0,
// 			CURLOPT_FOLLOWLOCATION => true,
// 			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 			// CURLOPT_CUSTOMREQUEST => 'GET',
// 			CURLOPT_HTTPHEADER => array(
// 				'Authorization: Basic ' . base64_encode("merchant.Alaradah:8e1aa2ff8a3f1acdafb04d810455ee56"),
// 			),
// 		)
// 	);
// 	$response = curl_exec($curl);
// 	curl_close($curl);
// 	parse_str($response, $output);
// 	dd($output, $response);
// });

// Route::get('/convertImage', 'webController@convertImage');
Route::get('/optimize', 'webController@optimize');

Auth::routes();


//Used for redirecting Old Links that are still remaining in Search Engines(Live)
Route::group(
    [
        'domain' => '{countrySubDomainCode}.'.config('app.url')
    ],
    function () {
        Route::get('/details/{id}/{slug}', 'RedirectController@detailsSlug');
        Route::get('/search', 'RedirectController@search');
        Route::get('/directdetails/{id}/{slug}', 'RedirectController@directDetailsSlug');
        Route::get('/directdetails/{id}/{slug}/{locale}', 'RedirectController@directDetails');
        Route::get('/details/{id}', 'RedirectController@details');
        Route::get('/products/{catid}/{slug}', 'RedirectController@products');
    });
Route::get('/details/{id}/{slug}', 'RedirectController@detailsNoLocale');
Route::get('/search', 'RedirectController@searchNoLocale');
Route::get('/directdetails/{id}/{slug}', 'RedirectController@directDetailsNoLocale');
Route::get('/directdetails/{id}/{slug}/{locale}', 'RedirectController@directDetailsNoLocaleAlt');
Route::get('/details/{id}', 'RedirectController@detailsNoLocaleNoSlug');
Route::get('/products/{catid}/{slug}', 'RedirectController@productsNoLocale');
//End Redirecting Product related links.


Route::view('/download-apps', 'website.download-app')->name('downloadApp');

Route::group(['middleware' => ['admin'] ] , function (){
   Route::get('/gwc/bundles/setting' , 'AdminBundleSettingController@edit')->name('bundle.edit');
   Route::put('/gwc/bundles/setting' , 'AdminBundleSettingController@update')->name('bundle.update');
});
// bundle Categories
Route::group(['middleware' => ['admin']], function () {
    Route::post('/gwc/bundles/category/{id}', 'AdminBundleCategoriesController@update');
    Route::get('/gwc/bundles/category/deleteImage/{id}', 'AdminBundleCategoriesController@deleteImage');
    Route::get('/gwc/bundles/category/deleteHImage/{id}', 'AdminBundleCategoriesController@deleteHeaderImage');
    Route::get('/gwc/bundles/category/delete/{id}', 'AdminBundleCategoriesController@destroy');
    Route::get('/gwc/bundles/category/csv', 'AdminBundleCategoriesController@downloadCSV');
    Route::get('/gwc/bundles/category/{id}/view', 'AdminBundleCategoriesController@view');
    Route::post('/gwc/bundles/category/{id}/view', 'AdminBundleCategoriesController@updateOffer')->name('bundleCategoryoffer');
    Route::get('/gwc/bundles/highlighted/ajax/{id}', 'AdminBundleCategoriesController@updateHighLightedStatusAjax');
    Route::get('/gwc/bundles/category/ajax/{id}', 'AdminBundleCategoriesController@updateStatusAjax');
    Route::resource('gwc/bundles/category', 'AdminBundleCategoriesController', ['names' => 'bundleCategory']);

    Route::get('/gwc/product/{id}/bundle', 'AdminBundleCategoriesController@productCategory');
    Route::post('/gwc/product/{id}/bundle', 'AdminBundleCategoriesController@productCategoryUpdate')->name('uploadBundleCategory');
    Route::get('/gwc/product/{id}/deleteBundleCategory/{cid}', 'AdminBundleCategoriesController@deleteProdcategory');


    Route::prefix('/gwc/blog')
        ->namespace('Blog')
        ->as('admin.blog.')
        ->Group(function () {
            Route::resource('post', AdminPostController::class);
            Route::get('comment', [\App\Http\Controllers\Blog\AdminCommentController::class , 'index']); 
            Route::get('comment/{id}', [\App\Http\Controllers\Blog\AdminCommentController::class , 'status']); 
        });
});
// inventory
Route::group(['prefix' => '/gwc/','middleware' => ['admin']], function () {
    Route::resource('payment-links' , 'PaymentsLinkController');
    Route::get('payment-links/delete/{id}' , 'PaymentsLinkController@destroy');
    Route::resource('inventories', 'AdminInventoriesController');
    Route::get('/inventories/ajax/{id}', 'AdminInventoriesController@updateStatusAjax');
    Route::get('/inventories/{id}/restore', 'AdminInventoriesController@restore');
    Route::get('/product/deleteinventoryparent/ajax', 'AdminProductController@deleteParentInventory');
});
// pos admin
Route::group(['prefix' => '/gwc/pos/','middleware' => ['admin']], function () {
    Route::get('cash', 'AdminPOSController@cash');
    Route::get('', 'AdminPOSController@days');
    Route::get('transactions/{date1?}/{date2?}/{pos_id?}', 'AdminPOSController@transactions');
    Route::get('orders/{date1?}/{date2?}/{pos_id?}', 'AdminPOSController@orders');
});

///********************************* Start Iran Developer **********************/////
//most sold
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/mostsold', 'AdminMostSoldController@index');
	Route::post('/gwc/mostsold/reset-date-range', 'AdminMostSoldController@resetDateRange');
	Route::post('/gwc/mostsold/ajax', 'AdminMostSoldController@storeValuesInCookies');
});

//most viewed
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/mostviewed', 'AdminMostViewedController@index');
});

//out of stock
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/outofstock', 'AdminOutofstockController@index');
	Route::post('/gwc/outofstock/updateqty', 'AdminOutofstockController@updateQty');
});

//top buyers
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/topbuyers', 'AdminTopBuyersController@index');
	Route::post('/gwc/topbuyers/reset-date-range', 'AdminTopBuyersController@resetDateRange');
	Route::post('/gwc/topbuyers/ajax', 'AdminTopBuyersController@storeValuesInCookies');
});

//inventory report
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/inventory', 'AdminInventoryController@index');
});

//days time report
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/daystime', 'AdminDaysTimeController@index');
	Route::post('/gwc/daystime/reset-date-range', 'AdminDaysTimeController@resetDateRange');
	Route::post('/gwc/daystime/ajax', 'AdminDaysTimeController@storeValuesInCookies');
});

//shipment report
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/shipment', 'AdminShipmentController@index');
	Route::post('/gwc/shipment/reset-date-range', 'AdminShipmentController@resetDateRange');
	Route::post('/gwc/shipment/ajax', 'AdminShipmentController@storeValuesInCookies');
});

//zones
Route::group(['middleware' => ['admin']], function () {
	Route::resource('/gwc/zones/{zone}/price', 'AdminZonesPriceController');
	Route::resource('/gwc/zones', 'AdminZonesController');
	Route::get('/gwc/zones/ajax/{id}', 'AdminZonesController@updateStatusAjax');
	Route::post('/gwc/zones/{id}', 'AdminZonesController@update');
	Route::get('/gwc/zones/delete/{id}', 'AdminZonesController@destroy');
});

//currencies
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/currencies/ajax/{id}', 'AdminCurrenciesController@updateStatusAjax');
	Route::post('/gwc/currencies/{id}', 'AdminCurrenciesController@update');
	Route::get('/gwc/currencies/delete/{id}', 'AdminCurrenciesController@destroy');
	Route::resource('/gwc/currencies', 'AdminCurrenciesController');
});



///********************************* Start Iran Developer **********************/////

//Roles
Route::group(['middleware' => ['admin']], function () {
	Route::resource('gwc/roles', 'RoleController');
	Route::post('/gwc/roles/{id}', 'RoleController@update');
	Route::get('/gwc/roles/destroy/{id}', 'RoleController@destroy');
});
//POS
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/pos/{oid}', 'AdminProductPosController@showpos');
	Route::get('/PosProductsVueJs', 'AdminProductPosController@PosProductsVueJs');
	Route::get('/PosProductsVueJs_GetAttribute', 'AdminProductPosController@PosProductsVueJs_GetAttribute');
	Route::get('/PosCartVueJs', 'AdminProductPosController@PosCartVueJs');
	Route::get('/PosCartTotalVueJs', 'AdminProductPosController@PosCartTotalVueJs');
});
Route::group(['middleware' => ['admin']], function () {

	//quick add item
	Route::get('/vendor/product/addQuick', 'AdminProductController@addQuick');
	Route::post('/vendor/product/PostaddQuick', 'AdminProductController@PostaddQuick')->name('product.addQuick');
	Route::get('/vendor/product/deleteRolloverImage/{id}', 'AdminProductController@deleteRolloverImage');
	Route::get('/vendor/product/deleteImage/{id}', 'AdminProductController@deleteImage');
	Route::get('/vendor/product/deletePdf/{id}', 'AdminProductController@deletePdf')->name('delete.deletePdf');

	Route::post('/vendor/product/{id}', 'AdminProductController@update');
	Route::get('/vendor/product/{id}/gallery', 'AdminProductController@productGallery');
	//Route::post('/vendor/product/{id}/gallery','AdminProductController@productGalleryUpdalod')->name('uploadImages');
	Route::get('/vendor/product/{id}/deletegallery/{gid}', 'AdminProductController@deleteGalleryImage');
	Route::get('/vendor/productGallery/{id}/{title_en}/{title_ar}/{display_order}', 'AdminProductController@updateProductGalleryAjax');
	//attribute
	Route::get('/vendor/product/{id}/options', 'AdminProductController@productOptions');
	Route::post('/vendor/product/{id}/options', 'AdminProductController@productAttributeUpdate')->name('uploadAttribute');
	Route::get('/vendor/product/deleteattribute/ajax', 'AdminProductController@deleteAttribute');
	Route::get('/vendor/product/deleteattributeparent/ajax', 'AdminProductController@deleteParentOption');
	//other option
	Route::get('/vendor/product/deleteotherchosenoption/ajax', 'AdminProductController@deleteOtherOption');

	//seo & tags
	Route::get('/vendor/product/{id}/seo-tags', 'AdminProductController@productseotags');
	Route::post('/vendor/product/{id}/seo-tags', 'AdminProductController@productseotagsSave')->name('seotags');
	//categories
	Route::get('/vendor/product/{id}/categories', 'AdminProductController@productCategory');
	Route::post('/vendor/product/{id}/categories', 'AdminProductController@productCategoryUpdate')->name('uploadCategory');
	Route::get('/vendor/productCategory/{id}/{category}', 'AdminProductController@updateProductCategoryAjax');
	Route::get('/vendor/product/{id}/deleteprodcategory/{cid}', 'AdminProductController@deleteProdcategory');
	//finish
	Route::get('/vendor/product/{id}/finish', 'AdminProductController@finishView');
	Route::post('/vendor/product/{id}/finish', 'AdminProductController@finishSave')->name('finishSave');

	Route::get('/vendor/product/delete/{id}', 'AdminProductController@destroy');
	Route::get('/vendor/product/{id}/view', 'AdminProductController@view');
	Route::get('/vendor/product/ajax/{id}', 'AdminProductController@updateStatusAjax');
	Route::get('/vendor/productexport/ajax/{id}', 'AdminProductController@updateExportStatusAjax');

	Route::get('/vendor/product/editsinglequantity/ajax', 'AdminProductController@editsinglequantityAjax');
	//reviews
	Route::get('/vendor/product/reviews', 'AdminProductController@productReviews');
	Route::get('/vendor/product/reviews/delete/{id}', 'AdminProductController@destroyReviews');
	Route::get('/vendor/reviews/ajax/{id}', 'AdminProductController@updateStatusReviewsAjax');
	//product inquiry
	Route::get('/vendor/product/product-inquiry', 'AdminProductController@productInquiry');
	Route::get('/vendor/product/product-inquiry/delete/{id}', 'AdminProductController@destroyInquiry');
	//reset filteration
	Route::get('/vendor/product/reset/ajax', 'AdminProductController@resetProductFilteration');
	//duplicate item
	Route::get('/vendor/product/duplicate/{id}', 'AdminProductController@createDuplicateItem');

	Route::post('/vendor/product/{id}/gallery', 'AdminProductController@upload')->name('uploadgalleryimages');

	Route::get('/vendor/tags', 'AdminProductController@tagslists')->name('tagsName');
	Route::post('/vendor/tagsPost', 'AdminProductController@tagsPost')->name('tagsPost');
	Route::get('/vendor/product-delete-tags/{tag}', 'AdminProductController@deleteTags')->name('deleteTags');

	Route::get('/vendor/product/{id}/quickedit', 'AdminProductController@quickEdit');
	Route::post('/vendor/product/{id}/quickupdate', 'AdminProductController@quickUpdate')->name('product.quickEdit');

	//section
	Route::get('/vendor/sections', 'AdminProductController@showSections');
	Route::post('/vendor/sections/saveSection', 'AdminProductController@saveSection')->name('saveSection');
	Route::post('/vendor/sections/saveEditSection/{id}', 'AdminProductController@saveEditSection')->name('saveEditSection');
	Route::get('/vendor/sections/delete/{id}', 'AdminProductController@destroySections');
	Route::get('/vendor/sections/ajax/{id}', 'AdminProductController@updateStatusSectionAjax');
	Route::get('/vendor/product/createqrcode', 'AdminProductController@QrCodeAll');
	Route::get('/vendor/sections/ajaxAsorting/{id}', 'AdminProductController@ajaxAsorting');
	//option
	Route::get('/vendor/product/options', 'AdminProductController@viewOptions');
	Route::get('/vendor/options/addchosenoption/ajax', 'AdminProductController@addchosenoption');
	//update upper category manually
	Route::get('/vendor/updateUpperCategoryManually', 'AdminProductController@updateUpperCategoryManually');

	Route::resource('vendor/product', 'AdminProductController');
});

//product
Route::group(['middleware' => ['admin']], function () {

	//quick add item
	Route::get('/gwc/product/addQuick', 'AdminProductController@addQuick');
	Route::post('/gwc/product/PostaddQuick', 'AdminProductController@PostaddQuick')->name('product.addQuick');
	Route::get('/gwc/product/deleteRolloverImage/{id}', 'AdminProductController@deleteRolloverImage');
	Route::get('/gwc/product/deleteImage/{id}', 'AdminProductController@deleteImage');
	Route::get('/gwc/product/deletePdf/{id}', 'AdminProductController@deletePdf')->name('delete.deletePdf');

	Route::post('/gwc/product/{id}', 'AdminProductController@update');
	Route::get('/gwc/product/{id}/gallery', 'AdminProductController@productGallery');
	//Route::post('/gwc/product/{id}/gallery','AdminProductController@productGalleryUpdalod')->name('uploadImages');
	Route::get('/gwc/product/{id}/deletegallery/{gid}', 'AdminProductController@deleteGalleryImage');
	Route::get('/gwc/productGallery/{id}/{title_en}/{title_ar}/{display_order}', 'AdminProductController@updateProductGalleryAjax');
	//attribute
	Route::get('/gwc/product/{id}/options', 'AdminProductController@productOptions');
	Route::post('/gwc/product/{id}/options', 'AdminProductController@productAttributeUpdate')->name('uploadAttribute');
	Route::get('/gwc/product/deleteattribute/ajax', 'AdminProductController@deleteAttribute');
	Route::get('/gwc/product/deleteattributeparent/ajax', 'AdminProductController@deleteParentOption');
	//other option
	Route::get('/gwc/product/deleteotherchosenoption/ajax', 'AdminProductController@deleteOtherOption');

	//seo & tags
	Route::get('/gwc/product/{id}/seo-tags', 'AdminProductController@productseotags');
	Route::post('/gwc/product/{id}/seo-tags', 'AdminProductController@productseotagsSave')->name('seotags');
	//categories
	Route::get('/gwc/product/{id}/categories', 'AdminProductController@productCategory');
	Route::post('/gwc/product/{id}/categories', 'AdminProductController@productCategoryUpdate')->name('uploadCategory');
	Route::get('/gwc/productCategory/{id}/{category}', 'AdminProductController@updateProductCategoryAjax');
	Route::get('/gwc/product/{id}/deleteprodcategory/{cid}', 'AdminProductController@deleteProdcategory');
	//finish
	Route::get('/gwc/product/{id}/finish', 'AdminProductController@finishView');
	Route::post('/gwc/product/{id}/finish', 'AdminProductController@finishSave')->name('finishSave');

	Route::get('/gwc/product/delete/{id}', 'AdminProductController@destroy');
	Route::get('/gwc/product/{id}/view', 'AdminProductController@view');
	Route::get('/gwc/product/ajax/{id}', 'AdminProductController@updateStatusAjax');
	Route::get('/gwc/productexport/ajax/{id}', 'AdminProductController@updateExportStatusAjax');

	Route::get('/gwc/product/editsinglequantity/ajax', 'AdminProductController@editsinglequantityAjax');
	//reviews
	Route::get('/gwc/product/reviews', 'AdminProductController@productReviews');
	Route::get('/gwc/product/reviews/delete/{id}', 'AdminProductController@destroyReviews');
	Route::get('/gwc/reviews/ajax/{id}', 'AdminProductController@updateStatusReviewsAjax');
	//product inquiry
	Route::get('/gwc/product/product-inquiry', 'AdminProductController@productInquiry');
	Route::get('/gwc/product/product-inquiry/delete/{id}', 'AdminProductController@destroyInquiry');
	//reset filteration
	Route::get('/gwc/product/reset/ajax', 'AdminProductController@resetProductFilteration');
	//duplicate item
	Route::get('/gwc/product/duplicate/{id}', 'AdminProductController@createDuplicateItem');

	Route::post('/gwc/product/{id}/gallery', 'AdminProductController@upload')->name('uploadgalleryimages');

	Route::get('/gwc/tags', 'AdminProductController@tagslists')->name('tagsName');
	Route::post('/gwc/tagsPost', 'AdminProductController@tagsPost')->name('tagsPost');
	Route::get('/gwc/product-delete-tags/{tag}', 'AdminProductController@deleteTags')->name('deleteTags');

	Route::get('/gwc/product/{id}/quickedit', 'AdminProductController@quickEdit');
	Route::post('/gwc/product/{id}/quickupdate', 'AdminProductController@quickUpdate')->name('product.quickEdit');

	//section
	Route::get('/gwc/sections', 'AdminProductController@showSections');
	Route::post('/gwc/sections/saveSection', 'AdminProductController@saveSection')->name('saveSection');
	Route::post('/gwc/sections/saveEditSection/{id}', 'AdminProductController@saveEditSection')->name('saveEditSection');
	Route::get('/gwc/sections/delete/{id}', 'AdminProductController@destroySections');
	Route::get('/gwc/sections/ajax/{id}', 'AdminProductController@updateStatusSectionAjax');
	Route::get('/gwc/product/createqrcode', 'AdminProductController@QrCodeAll');
	Route::get('/gwc/sections/ajaxAsorting/{id}', 'AdminProductController@ajaxAsorting');
	//option
	Route::get('/gwc/product/options', 'AdminProductController@viewOptions');
	Route::get('/gwc/options/addchosenoption/ajax', 'AdminProductController@addchosenoption');
	//update upper category manually
	Route::get('/gwc/updateUpperCategoryManually', 'AdminProductController@updateUpperCategoryManually');

	Route::resource('gwc/product', 'AdminProductController');
});

//warranty
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/warranty/{id}', 'AdminWarrantyController@update');
	Route::get('/gwc/warranty/delete/{id}', 'AdminWarrantyController@destroy');
	Route::get('/gwc/warranty/ajax/{id}', 'AdminWarrantyController@updateStatusAjax');
	Route::resource('gwc/warranty', 'AdminWarrantyController');
});

//delivery times
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/deliverytimes/{id}', 'AdminDeliveryTimesController@update');
	Route::get('/gwc/deliverytimes/delete/{id}', 'AdminDeliveryTimesController@destroy');
	Route::get('/gwc/deliverytimes/ajax/{id}', 'AdminDeliveryTimesController@updateStatusAjax');
	Route::resource('gwc/deliverytimes', 'AdminDeliveryTimesController');
});

//options

Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/options/{id}', 'AdminOptionsController@update');
	Route::get('/gwc/options/delete/{id}', 'AdminOptionsController@destroy');
	Route::get('/gwc/options/ajax/{id}', 'AdminOptionsController@updateStatusAjax');
	Route::get('/gwc/options/deletechildoption/{id}', 'AdminOptionsController@deletechildoption');
	Route::resource('gwc/options', 'AdminOptionsController');
});


//webpush
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/webpush/save', 'webPushController@saveWebPush')->name('savePush');
	Route::post('/gwc/webpush/saveEdit/{id}', 'webPushController@saveEditWebPush')->name('saveEdit');
	Route::get('/gwc/webpush/delete/{id}', 'webPushController@destroyWebPushs');
	Route::get('/gwc/webpush/devicetokens', 'webPushController@devicetokens');
	Route::get('/gwc/webpush/devicetokens/delete/{id}', 'webPushController@deletedevicetokens');
	Route::resource('gwc/webpush', 'webPushController');
});

//coupon
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/coupon/export_Coupons', 'AdminCouponController@exportCoupons')->name('exportCoupons');
	Route::post('/gwc/coupon/multiple_coupon', 'AdminCouponController@storeMultipleCoupon')->name('storeMultipleCoupon');
	Route::post('/gwc/coupon/{id}', 'AdminCouponController@update');
	Route::get('/gwc/coupon/delete/{id}', 'AdminCouponController@destroy');
	Route::get('/gwc/coupon/{id}/view', 'AdminCouponController@view');
	Route::get('/gwc/coupon/ajax/{id}', 'AdminCouponController@updateStatusAjax');
	Route::get('/gwc/coupon/create_multiple', 'AdminCouponController@createMultiple');
	Route::resource('gwc/coupon', 'AdminCouponController');
});
//faq
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/faq/{id}', 'AdminFaqController@update');
	Route::get('/gwc/faq/delete/{id}', 'AdminFaqController@destroy');
	Route::get('/gwc/faq/{id}/view', 'AdminFaqController@view');
	Route::get('/gwc/faq/ajax/{id}', 'AdminFaqController@updateStatusAjax');
	Route::resource('gwc/faq', 'AdminFaqController');
});


Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/apis/{id}', 'AdminApisController@update');
	Route::get('/gwc/apis/delete/{id}', 'AdminApisController@destroy');
	Route::get('/gwc/apis/{id}/view', 'AdminApisController@view');
	Route::get('/gwc/apis/ajax/{id}', 'AdminApisController@updateStatusAjax');
	Route::resource('gwc/apis', 'AdminApisController');
});

//single pages
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/singlepages/{id}', 'AdminSinglePagesController@update');
	Route::get('/gwc/singlepages/deletesinglepagesImage/{id}', 'AdminSinglePagesController@deleteImage');
	Route::get('/gwc/singlepages/delete/{id}', 'AdminSinglePagesController@destroy');
	Route::get('/gwc/singlepages/{id}/view', 'AdminSinglePagesController@view');
	Route::get('/gwc/singlepages/ajax/{id}', 'AdminSinglePagesController@updateStatusAjax');
	Route::resource('gwc/singlepages', 'AdminSinglePagesController');
});
//slideshow
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/slideshow/{id}', 'AdminSlideshowController@update');
	Route::get('/gwc/slideshow/deleteImage/{id}', 'AdminSlideshowController@deleteImage');
	Route::get('/gwc/slideshow/delete/{id}', 'AdminSlideshowController@destroy');
	Route::get('/gwc/slideshow/ajax/{id}', 'AdminSlideshowController@updateStatusAjax');
	Route::resource('gwc/slideshow', 'AdminSlideshowController');
});

//banner
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/banner/{id}', 'AdminBannerController@update');
	Route::get('/gwc/banner/deleteImage/{id}', 'AdminBannerController@deleteImage');
	Route::get('/gwc/banner/delete/{id}', 'AdminBannerController@destroy');
	Route::get('/gwc/banner/ajax/{id}', 'AdminBannerController@updateStatusAjax');
	Route::resource('gwc/banner', 'AdminBannerController');
});

//pop
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/popup/{id}', 'AdminPopupController@update');
	Route::get('/gwc/popup/deleteImage/{id}', 'AdminPopupController@deleteImage');
	Route::get('/gwc/popup/delete/{id}', 'AdminPopupController@destroy');
	Route::get('/gwc/popup/ajax/{id}', 'AdminPopupController@updateStatusAjax');
	Route::resource('gwc/popup', 'AdminPopupController');
});

//brands
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/brand/{id}', 'AdminBrandController@update');
	Route::get('/gwc/brand/deleteImage/{id}', 'AdminBrandController@deleteImage');
	Route::get('/gwc/brand/deleteBgImage/{id}', 'AdminBrandController@deleteBgImage');
	Route::get('/gwc/brand/delete/{id}', 'AdminBrandController@destroy');
	Route::get('/gwc/brand/{id}/view', 'AdminBrandController@view');
	Route::get('/gwc/brand/ajax/{id}', 'AdminBrandController@updateStatusAjax');
	Route::get('/gwc/brandlogo/ajax/{id}', 'AdminBrandController@updateLogoStatusAjax');
	Route::get('/gwc/brandhome/ajax/{id}', 'AdminBrandController@updateHomeStatusAjax');
	Route::resource('gwc/brand', 'AdminBrandController');
});
//manufacturers
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/manufacturer/{id}', 'AdminManufacturerController@update');
	Route::get('/gwc/manufacturer/deleteImage/{id}', 'AdminManufacturerController@deleteImage');
	Route::get('/gwc/manufacturer/deleteHeaderImage/{id}', 'AdminManufacturerController@deleteHeaderImage');
	Route::get('/gwc/manufacturer/delete/{id}', 'AdminManufacturerController@destroy');
	Route::get('/gwc/manufacturer/{id}/view', 'AdminManufacturerController@view');
	Route::get('/gwc/manufacturer/ajax/{id}', 'AdminManufacturerController@updateStatusAjax');
	Route::get('/gwc/manufacturerhome/ajax/{id}', 'AdminManufacturerController@updateStatusHomeAjax');
	Route::get('/gwc/manufactureorders/{mid}', 'AdminCustomersController@listmanufactureorders');
	Route::get('/gwc/manufactureordersdetails/{mid}/{oid}', 'AdminCustomersController@manufactureordersdetails');

	Route::resource('gwc/manufacturer', 'AdminManufacturerController');
});
//color
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/color/updateDisplayOrder', 'AdminColorController@updateDisplayOrder');
	Route::post('/gwc/color/{id}', 'AdminColorController@update');
	Route::get('/gwc/color/deleteImage/{id}', 'AdminColorController@deleteImage');
	Route::get('/gwc/color/delete/{id}', 'AdminColorController@destroy');
	Route::get('/gwc/color/{id}/view', 'AdminColorController@view');
	Route::get('/gwc/color/ajax/{id}', 'AdminColorController@updateStatusAjax');
	Route::resource('gwc/color', 'AdminColorController');
});
//size
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/size/{id}', 'AdminSizeController@update');
	Route::get('/gwc/size/delete/{id}', 'AdminSizeController@destroy');
	Route::get('/gwc/size/{id}/view', 'AdminSizeController@view');
	Route::get('/gwc/size/ajax/{id}', 'AdminSizeController@updateStatusAjax');
	Route::resource('gwc/size', 'AdminSizeController');
});
//Categories
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/category/{id}', 'AdminCategoriesController@update');
	Route::get('/gwc/category/deleteImage/{id}', 'AdminCategoriesController@deleteImage');
	Route::get('/gwc/category/deleteHImage/{id}', 'AdminCategoriesController@deleteHeaderImage');
	Route::get('/gwc/category/delete/{id}', 'AdminCategoriesController@destroy');
	Route::get('/gwc/category/csv', 'AdminCategoriesController@downloadCSV');
	Route::get('/gwc/category/{id}/view', 'AdminCategoriesController@view');
	Route::post('/gwc/category/{id}/view', 'AdminCategoriesController@updateOffer')->name('categoryoffer');
	Route::get('/gwc/highlighted/ajax/{id}', 'AdminCategoriesController@updateHighLightedStatusAjax');
	Route::get('/gwc/category/ajax/{id}', 'AdminCategoriesController@updateStatusAjax');
	Route::resource('gwc/category', 'AdminCategoriesController');
});

//contact us
Route::group(['middleware' => ['admin']], function () {
	Route::get('/gwc/contactus/subjects', 'AdminInboxController@showSubjects');
	Route::post('/gwc/contactus/saveSubject', 'AdminInboxController@saveSubject')->name('saveSubject');
	Route::get('/gwc/contactus/subjects/delete/{id}', 'AdminInboxController@destroySubjects');
	Route::get('/gwc/contactus/{id}/view', 'AdminInboxController@view');
	Route::get('/gwc/contactus/inbox/delete/{id}', 'AdminInboxController@destroy');
	Route::get('/gwc/subjects/ajax/{id}', 'AdminInboxController@updateStatusAjax');
	Route::resource('gwc/contactus/inbox', 'AdminInboxController');
});

//customers
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/customers/{id}', 'AdminCustomersController@update');
	Route::get('/gwc/customers/deletecustomersImage/{id}', 'AdminCustomersController@deleteImage');
	Route::get('/gwc/customers/delete/{id}', 'AdminCustomersController@destroy');
	Route::get('/gwc/customers/ajax/{id}', 'AdminCustomersController@updateStatusAjax');
	Route::get('/gwc/customers-seller/ajax/{id}', 'AdminCustomersController@updateSellerStatusAjax');
	Route::get('/gwc/customers/{id}/view', 'AdminCustomersController@view');
	Route::get('/gwc/customers/pdf', 'AdminCustomersController@downloadPDF');
	Route::get('/gwc/customers/changepass/{id}', 'AdminCustomersController@changepass');
	Route::post('/gwc/customers/changepass/{id}', 'AdminCustomersController@editchangepass')->name('customers.changepass');
	Route::post('/gwc/customers/address/{id}', 'AdminCustomersController@addAddress')->name('customersaddress');
	Route::get('/gwc/customers/addressDefault/ajax/{id}', 'AdminCustomersController@chooseDefaultAddress');
	Route::get('/gwc/customers/deleteAddress/{cid}/{id}', 'AdminCustomersController@deleteAddress');
	Route::get('/gwc/customers/wishitems', 'AdminCustomersController@viewCustomerWishItems');
	Route::get('/gwc/customers/wishitems/delete/{id}', 'AdminCustomersController@deleteWishItem');
	//vendor orders
	Route::get('/vendor/orders', 'AdminCustomersController@listVendorOrders');
	Route::get('/vendor/orders/{oid}/view', 'AdminCustomersController@ViewVendorOrder');

	//orders
	Route::get('/gwc/orders', 'AdminCustomersController@listCustomersOrders');
	Route::get('/gwc/orders/{oid}/view', 'AdminCustomersController@ViewCustomerOrder');
	Route::get('/gwc/orders/{order_id}/payment-status', 'AdminCustomersController@orderPaymentInfo');
	Route::get('/gwc/orders/ajax', 'AdminCustomersController@storeValuesInCookies');
	Route::get('/gwc/orders/status/ajax', 'AdminCustomersController@orderStatus');
	Route::get('/gwc/orders/resetSearch/ajax', 'AdminCustomersController@orderResetFilter');
	Route::get('/gwc/orders/delete/{id}', 'AdminCustomersController@deleteOrder');
	Route::get('/gwc/orders/print', 'AdminCustomersController@printOrdersInRole')->name('printOrdersInRole');
	/////Iran office////
	Route::get('/gwc/orders/deleteproduct/{orderid}/{productid}', 'AdminCustomersController@deleteProductFromOrder');
	Route::post('/gwc/orders/updateprodqty', 'AdminCustomersController@updateProductQtyOfOrder');
	Route::post('/gwc/orders/searchitemcode', 'AdminCustomersController@searchItemCode');
	Route::post('/gwc/orders/add-product-to-order', 'webCartController@ajaxAddProductToOrder');
	/////end iran//////
	Route::get('/gwc/payments', 'AdminCustomersController@listPayments');
	Route::get('/gwc/payments/ajax', 'AdminCustomersController@storeValuesInCookies');
	Route::get('/gwc/payments/delete/{id}', 'AdminCustomersController@deletePayment');

	Route::get('/vendor/payments', 'AdminCustomersController@listVendorPayment');


	//orders track history
	Route::get('/gwc/orders-track/{oid}/create', 'AdminCustomersController@createTrackHistory');
	Route::post('/gwc/orders-track/{oid}/create', 'AdminCustomersController@postTrackHistory')->name('track-orders.postnewtrack');

	Route::get('/gwc/orders-track/{id}/edittrack', 'AdminCustomersController@edittrack');
	Route::post('/gwc/orders-track/{id}/edittrack', 'AdminCustomersController@updatetrack')->name('orders-track.updatetrack');
	Route::get('/gwc/orders-track/delete/{id}', 'AdminCustomersController@destroyTrack');
	Route::get('/gwc/orders-track/ajax/{id}', 'AdminCustomersController@updateOrderStatusAjax');
	Route::get('gwc/orders-track/{oid}', 'AdminCustomersController@listorderhistory');
	Route::get('gwc/storetocookie/ajax', 'AdminCustomersController@storetocookie');
	Route::get('gwc/order/discountapply/ajax', 'AdminCustomersController@applydiscountAmount');
	Route::get('gwc/orders/notification/ajax', 'AdminCustomersController@loadmodalforordernotification');

	Route::resource('gwc/customers', 'AdminCustomersController');
});

//customers
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/country/{id}', 'AdminCountryController@update');
	Route::get('/gwc/country/deletecountryImage/{id}', 'AdminCountryController@deleteImage');
	Route::get('/gwc/country/delete/{id}', 'AdminCountryController@destroy');
	Route::get('/gwc/country/ajax/{id}', 'AdminCountryController@updateStatusAjax');
	Route::get('/gwc/country/ajax-state/{id}', 'AdminCountryController@getStateAjax');
	Route::resource('gwc/country', 'AdminCountryController');
});

Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/{parent_id}/state/{id}', 'AdminStateController@update')->name('state.update');
	Route::get('/gwc/{parent_id}/state/deletecountryImage/{id}', 'AdminStateController@deleteImage');
	Route::get('/gwc/{parent_id}/state/delete/{id}', 'AdminStateController@destroy');
	Route::get('/gwc/state/ajax/{id}', 'AdminStateController@updateStatusAjax');
	Route::get('/gwc/state/ajax-area/{id}', 'AdminStateController@getAreaAjax');
	Route::resource('gwc/{parent_id}/state', 'AdminStateController');
});

Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/{parent_id}/area/{id}', 'AdminAreaController@update')->name('area.update');
	Route::get('/gwc/{parent_id}/area/delete/{id}', 'AdminAreaController@destroy');
	Route::get('/gwc/area/ajax/{id}', 'AdminAreaController@updateStatusAjax');
	Route::resource('gwc/{parent_id}/area', 'AdminAreaController');
});



//setting
Route::group(['middleware' => ['admin']], function () {
	Route::post('/gwc/send-sms', 'AdminSettingsController@sendSMS');
	Route::post('/gwc/general-settings/{keyname}', 'AdminSettingsController@update');
	Route::get('/gwc/settings/deletefavicon/', 'AdminSettingsController@deleteFavicon');
	Route::get('/gwc/settings/deleteEmailLogo/', 'AdminSettingsController@deleteEmailLogo');
	Route::get('/gwc/settings/deleteLogo/', 'AdminSettingsController@deleteLogo');
	Route::get('/gwc/settings/deleteFooterLogo/', 'AdminSettingsController@deleteFooterLogo');
	Route::get('/gwc/settings/deletewatermark/', 'AdminSettingsController@deletewatermark');
	Route::get('/gwc/settings/deleteheaderimg/', 'AdminSettingsController@deleteheaderimg');
	Route::get('/gwc/aboutus', 'AdminSettingsController@aboutus');
	Route::post('/gwc/aboutuspost', 'AdminSettingsController@aboutuspost')->name('aboutuspost');
	Route::get('/gwc/aboutus/deleteimage/', 'AdminSettingsController@deleteimage');
	Route::resource('gwc/general-settings', 'AdminSettingsController');
	Route::get('/gwc/mission', 'AdminSettingsController@mission');
	Route::post('/gwc/missionpost', 'AdminSettingsController@missionpost')->name('missionpost');
	Route::get('/gwc/vision', 'AdminSettingsController@vision');
	Route::post('/gwc/visionpost', 'AdminSettingsController@visionpost')->name('visionpost');
	Route::get('/gwc/teamcontent', 'AdminSettingsController@teamcontent');
	Route::post('/gwc/teamcontentpost', 'AdminSettingsController@teamcontentpost')->name('teamcontentpost');
	Route::get('/gwc/facebook-setting', 'AdminSettingsController@facebooksetting');
	Route::post('/gwc/facebooksettingpost', 'AdminSettingsController@facebooksettingpost')->name('facebooksettingpost');
	Route::get('/gwc/smssetting', 'AdminSettingsController@smssetting');
	Route::post('/gwc/smssettingpost', 'AdminSettingsController@smssettingpost')->name('smssettingpost');
	//export/import
	Route::get('/gwc/export_import', 'AdminExportController@ViewExportImportForm');
	Route::get('/gwc/export_product', 'AdminExportController@export_product');
	Route::post('/gwc/import_product', 'AdminExportController@import_product')->name('import_product');
	Route::get('/gwc/export_product_facebook/{lang}/{country?}', 'AdminExportController@export_product_facebook');
	Route::get('/gwc/export_product_google/{lang}/{country?}', 'AdminExportController@export_product_google');
	Route::get('/gwc/export_product_huawei/{lang}/{country?}', 'AdminExportController@export_product_huawei');
});

//Admin sections
Route::get('/gwc/forgot', 'AdminIndexController@forgotview');
Route::post('gwc/email', 'AdminIndexController@sendResetLinkEmail')->name('gwc.email');
Route::get('gwc/forgot/{token}', 'AdminIndexController@showResetForm')->name('gwc.reset');
Route::post('gwc/forgot/{token}', 'AdminIndexController@resets')->name('gwc.token');
//vendor
Route::get('/vendor/', 'AdminVendorController@index');
Route::post('/vendor/login', 'AdminVendorController@login')->name('vendorlogin');
Route::get('/vendor/home', 'VendorDashboardController@index')->middleware('admin');
Route::post('/vendor/logout', 'AdminDashboardController@logout'); //logout from admin panel
Route::get('/vendor/editprofile', 'AdminUserController@editprofile')->middleware('admin');
Route::post('/vendor/editprofile/save', 'AdminUserController@vendorSaveEditProfile')->name('vendor.update');
Route::get('/vendor/changepassword', 'AdminUserController@changepassword')->middleware('admin');
Route::post('/vendor/users/change/pass', 'AdminUserController@vendorChangePass')->name('vendorChangePass');
Route::get('/vendor/deleteImage/{id}', 'AdminUserController@deleteImage');
Route::get('/vendor/deleteHeaderImage/{id}', 'AdminUserController@deleteHeaderImage');
//Admin sections
Route::get('/vendor/forgot', 'AdminVendorController@forgotview');
Route::post('vendor/email', 'AdminVendorController@sendResetLinkEmail')->name('vendor.email');
Route::get('vendor/forgot/{token}', 'AdminVendorController@showResetForm')->name('vendor.reset');
Route::post('vendor/forgot/{token}', 'AdminVendorController@resets')->name('vendor.token');

//gwc
Route::get('/gwc/', 'AdminIndexController@index');
Route::post('/gwc/login', 'AdminIndexController@login')->name('adminlogin');
Route::get('/gwc/home', 'AdminDashboardController@index')->middleware('admin');
Route::post('/gwc/logout', 'AdminDashboardController@logout'); //logout from admin panel
Route::get('/gwc/logs', 'AdminUserController@logs')->middleware('admin');
Route::get('/gwc/logs/delete/{id}', 'AdminUserController@deleteLogs')->middleware('admin');
Route::get('/gwc/subscribers', 'AdminUserController@subscribers')->middleware('admin');
Route::get('/gwc/subscribers/delete/{id}', 'AdminUserController@deleteSubscriber')->middleware('admin');
Route::get('/gwc/subscribers/csv', 'AdminUserController@exportSubscriber')->middleware('admin');
Route::post('/gwc/subscribers', 'AdminUserController@subscribers')->name('searchSubscribers');
//admin menus
Route::get('/gwc/menus', 'AdminMenuController@index')->middleware('admin');;
Route::post('/gwc/menus', 'AdminMenuController@index')->name('menusearch');
Route::get('/gwc/menus/new', 'AdminMenuController@adminMenusForm')->middleware('admin');
Route::post('/gwc/menus/new', 'AdminMenuController@AddRecord')->name('newmenu');
Route::get('/gwc/menus/edit/{id}', 'AdminMenuController@adminMenusForm')->middleware('admin');
Route::get('/gwc/menus/delete/{id}', 'AdminMenuController@deleteMenus')->middleware('admin');
Route::get('/gwc/menus/ajax/{id}', 'AdminMenuController@updateStatusAjax')->middleware('admin');
//users
Route::get('/gwc/users', 'AdminUserController@index')->middleware('admin');;
Route::post('/gwc/users', 'AdminUserController@index')->name('usersearch');
Route::get('/gwc/users/new', 'AdminUserController@adminUserForm')->middleware('admin');
Route::post('/gwc/users/new', 'AdminUserController@AddRecord')->name('newuser');
Route::get('/gwc/users/edit/{id}', 'AdminUserController@adminUserForm')->middleware('admin');
Route::get('/gwc/users/changepass/{id}', 'AdminUserController@adminUserForm')->middleware('admin');
Route::get('/gwc/users/settings/{id}', 'AdminUserController@adminUserForm')->middleware('admin');
Route::post('/gwc/users/save', 'AdminUserController@adminSaveProfile')->name('adminSaveProfile');
Route::post('/gwc/users/change/pass', 'AdminUserController@adminChangePass')->name('adminChangePass');
Route::get('/gwc/users/delete/{id}', 'AdminUserController@deleteUser')->middleware('admin');
Route::get('/gwc/users/ajax/{id}', 'AdminUserController@updateStatusAjax')->middleware('admin');
Route::get('/gwc/editprofile', 'AdminUserController@editprofile')->middleware('admin');
Route::post('/gwc/editprofile/save', 'AdminUserController@adminSaveEditProfile')->name('adminSaveEditProfile');
Route::get('/gwc/changepassword', 'AdminUserController@changepassword')->middleware('admin');
Route::get('/gwc/notifyemails', 'AdminSettingsController@notifyemails')->middleware('admin');
Route::post('/gwc/saveEmail', 'AdminSettingsController@saveEmail')->name('saveEmail');
Route::get('/gwc/notifyemails/delete/{id}', 'AdminSettingsController@destroyEmails');
Route::get('/gwc/notifyemails/ajax/{id}', 'AdminSettingsController@updateStatusAjax');


//////////////////////////////////////////////////WEBSITE//////////////////////////////////////////////////

// Route::get('locale/{locale}', function ($locale) {
// 	Session::put('locale', $locale);
// 	return redirect()->back();
// });

/*Common Route Paths used by Website */

//////////////////////////////////////////////STARTS//////////////////////////////////////////////////////////
Route::group(
    [
        'domain' => '{countrySubDomainCode}.'.config('app.url')
    ],
    function () {
//product quick view
        Route::get('/ajax_quickview', 'webCartController@ajax_quickview');
        Route::get('/ajax_quickview_addtocart', 'webCartController@ajax_quickview_addtocart');
        Route::get('/ajax_quickview_getPrice_BySize', 'webCartController@ajax_quickview_getprice_by_size');
        Route::get('/ajax_quickview_getPrice_ByColor', 'webCartController@ajax_quickview_getprice_by_color');

        Route::get('/ajax_quickview_getColor_BySize', 'webCartController@ajax_quickview_getColor_BySize');
        Route::get('/ajax_addtocart_single', 'webCartController@ajax_addtocart_single');
        Route::get('/ajax_reload_temp_order_box', 'webCartController@ajax_reload_temp_order_box');
        Route::get('/countTempOrdersAjax', 'webCartController@countTempOrdersAjax');
        Route::get('/deleteTempOrdersAjax', 'webCartController@deleteTempOrdersAjax');
        Route::get('/ajax_details_getColor_BySize', 'webCartController@ajax_details_getColor_BySize');
        Route::get('/ajax_get_option_price', 'webCartController@ajax_get_option_price');
        Route::get('/ajax_get_option_check_price', 'webCartController@ajax_get_option_check_price');
        Route::get('/ajax_get_option_select_price', 'webCartController@ajax_get_option_select_price');
        Route::get('/ajax_details_getPrice_BySize', 'webCartController@ajax_details_getPrice_BySize');
        Route::get('/ajax_is_express_delivery', 'webCartController@ajax_is_express_delivery');


//add to wish list
        Route::get('/ajax_add_to_wish_list', 'webCartController@ajax_add_to_wish_list');
        Route::get('/ajax_get_color_image', 'webCartController@ajax_get_color_image');
//show quick search
        Route::get('/ajax_product_quick_search', 'webCartController@ajax_product_quick_search');
//newsletter
        Route::get('/ajax_newsletter_subscribe', 'webController@ajax_newsletter_subscribe');

//product sorting
        Route::get('/ajax_product_sort_by', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_product_per_page', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_product_price_range', 'webController@ajax_store_value_in_cookies');
//brand orting
        Route::get('/ajax_brand_sort_by', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_brand_per_page', 'webController@ajax_store_value_in_cookies');
//offer orting
        Route::get('/ajax_offer_sort_by', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_offer_per_page', 'webController@ajax_store_value_in_cookies');
//clear all filters
        Route::get('/ajax_product_filter', 'webController@ajax_product_filter');
//filter by tahs
        Route::get('/ajax_product_filter_by_tags', 'webController@ajax_store_value_in_cookies');
//filter by size
        Route::get('/ajax_product_filter_by_size', 'webController@ajax_store_value_in_cookies');
//filter by color
        Route::get('/ajax_product_filter_by_color', 'webController@ajax_store_value_in_cookies');
//save longitude & latitude in cookie
        Route::get('/ajax_post_latlong', 'webController@ajax_post_latlong');
//product sorting
        Route::get('/ajax_search_sort_by', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_search_per_page', 'webController@ajax_store_value_in_cookies');
        Route::get('/ajax_search_price_range', 'webController@ajax_store_value_in_cookies');
//clear all filters
        Route::get('/ajax_product_search', 'webController@ajax_product_search');
//filter by tahs
        Route::get('/ajax_product_search_by_tags', 'webController@ajax_store_value_in_cookies');
//filter by size
        Route::get('/ajax_product_search_by_size', 'webController@ajax_store_value_in_cookies');
//filter by color
        Route::get('/ajax_product_search_by_color', 'webController@ajax_store_value_in_cookies');
//update cart quantity
        Route::get('/ajax_change_cart_quantity', 'webCartController@ajax_change_cart_quantity');
//remove cart's items
        Route::get('/ajax_remove_my_cart', 'webCartController@ajax_remove_my_cart');
        Route::get('/ajax_remove_my_cart_item', 'webCartController@ajax_remove_my_cart_item');
//apply coupon
        Route::get('/ajax_apply_coupon_to_cart', 'webCartController@ajax_apply_coupon_to_cart');
        Route::get('/ajax_apply_seller_discount_to_cart', 'webCartController@ajax_apply_seller_discount_to_cart');
//post inquiry 
        Route::get('/ajax_post_inquiry', 'webController@ajax_post_inquiry');

//******add to cart details*****///
        Route::post('/ajax_details_addtocart', 'webCartController@ajax_details_addtocart')->name('addtocartDetails');
//******end *******************////

//updadte slider click
        Route::get('/ajax_post_slidecount', 'webController@ajax_post_slidecount');
        Route::get('/ajax_post_bannercount', 'webController@ajax_post_bannercount');

//get country /state / area
        Route::get('/ajax_get_country_state_area_request', 'webCartController@ajax_get_country_state_area_request');
        Route::get('/ajax_get_area_delivery', 'webCartController@ajax_get_area_delivery');
//view order track
        Route::get('/ajax_get_track_orderid', 'webCartController@ajax_get_track_orderid');

        Route::get('/ajax_remove_wish_list', 'accountController@ajax_remove_wish_list');
        Route::get('/ajax_remove_my_order', 'webCartController@ajax_remove_my_order');
//reload address
        Route::get('/ajax_get_customer_address', 'webCartController@ajax_get_customer_address');
    });
    
Route::any('/masterCard_response/{transactionId}', 'webCartController@getMasterCardResponse');

//payment response for Knet
Route::get('/knet_response', 'webCartController@getKnetResponse');

//knet accept payment
Route::post('/knet_response_accept', 'webCartController@knet_response_accept');
Route::get('/knet_failed', 'webCartController@knet_failed');
Route::get('rollbackknetfailedorder', 'AdminCustomersController@rollbackknetfailedorder');

// CBK Pay
Route::any('/cbk_response_accept', 'webCartController@cbk_response_accept');

//q8link knet return response
Route::get('/knet_response_q8link_return', 'webCartController@knet_response_q8link_return');


//tahseel accept
Route::get('/tahseel_response_accept', 'webCartController@tahseel_response_accept');
//myfatoorah accept
Route::get('/myfatoorah_response_accept', 'webCartController@myfatoorah_response_accept');
//paypal accept
Route::get('/paypal_return', 'webCartController@paypal_response_accept');
//cyber return
Route::get('/csreturnurl', 'webCartController@csreturnurl');
Route::post('/csreturn', 'webCartController@cs_response_accept');


///////////////////////////////////////////////ENDS//////////////////////////////////////////////////////////////


Route::get('/', 'RedirectController@home');

Route::get('/bundle/{id}/products', 'webBundleController@ajax_products')->name('webBundleProduct');
Route::group(
	[
		'prefix' => '{locale}',
		'where' => ['locale' => '[a-zA-Z]{2}'],
		'middleware' => 'setlocale',
        'domain' => '{countrySubDomainCode}.'.config('app.url')
	],
	function () {
		Route::get('/', 'webController@index')->name('home');

        Route::get('/bundle', 'webBundleController@index')->name('webBundle');

		Route::post('/subscribe_newsletter', 'webController@subscribe_newsletter');
		Route::get('/contactus', 'webController@viewcontact');
		Route::post('/contactform', 'webController@contactform')->name('contactform');
		Route::get('/supplier-registration', 'userController@supplierRegistration');
		Route::get('/supplier-registration-done', 'userController@supplierRegistrationdone');
		Route::post('/supplier-registration', 'userController@createSupplier')->name('supplierregister');
		//product details
		Route::get('/details/{id}/{slug}', 'webCartController@viewProductDetails')->name('detailsIdSlug');
		Route::get('/directdetails/{id}/{slug}', 'webCartController@directdetails')->name('directdetailsIdSlug');
		Route::get('/details/{id}', 'webCartController@viewProductDetails')->name('detailsId');
		//get color image by color
		//show quick search
		Route::get('/search', 'webController@searchResults')->name('web.search');
		//post product review
		Route::post('/details/{id}/{slug}', 'webController@reviewForm');
		//products listings
		Route::get('/products/{catid}/{slug}', 'webController@listProducts')->name('productsCatidSlug');
		Route::get('/product-tag/{tag}', 'webController@listProductsByTags')->name('web.product-tag');
		Route::get('/categories', 'webController@showCategories')->name('categories');
		Route::get('/listCategoriesVueJs', 'webController@listCategoriesVueJs');
		//get all items from section
		Route::get('/allsections/{secid}/{slug}', 'webController@listSectionsProducts')->name('web.allsections');

		Route::get('/vendors/{vendorkey}', 'webController@listItemsByVendor')->name('web.vendorsListing');
		//offer
		Route::get('/offers', 'webController@offers')->name('offer');
		//save token
		Route::get('/web_push_token_save', 'webPushController@saveToken');

		Route::get('/product/{id}/customize', 'webCartController@customizeProduct');
		//search
		//show shopping cart
		Route::get('/cart', 'webCartController@cartview');
		//checkout
		Route::get('/checkout', 'webCartController@checkout')->name('checkout');
		//post checkout
		Route::post('/checkout', 'webCartController@saveconfirm')->name('checkoutconfirmform');

		//brands listing 
		Route::get('/brands/{brandkey}', 'webController@listItemsByBrand')->name('web.brandsListing');

		//user
		Route::get('/login', 'userController@loginForm');
		Route::post('/login', 'userController@loginAuthenticate')->name('loginform');
		Route::get('/register', 'userController@registerform');
		Route::post('/register', 'userController@createAccount')->name('registerform');
		//single pages
		Route::get('/page/{slug}', 'webController@singlePage');
		Route::get('/faq', 'webController@faq');
		// Password Reset Routes...
		Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
		Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
		Route::get('password/reset/{token}', 'ForgotPasswordController@showResetForm')->name('password.reset');
		Route::post('password/reset/{token}', 'ForgotPasswordController@resets')->name('password.token');

		//loggedin user routes
		Route::group(['middleware' => ['webs']], function () {
			Route::get('/account', 'accountController@index')->name('account');
			Route::get('/editprofile', 'accountController@editprofileForm');
			Route::post('/editprofile', 'accountController@editprofileSave')->name('editprofileSave');
			Route::post('/changepass', 'accountController@changepass')->name('changepass');
			Route::get('/changepass', 'accountController@changepassForm');
			Route::get('/wishlist', 'accountController@viewwishlist')->name('wishlist');
			Route::get('/newaddress', 'accountController@newaddress');
			Route::post('/addressSave', 'accountController@addressSave')->name('addressSave');
			Route::get('/editaddress/{id}', 'accountController@editaddress');
			Route::post('/editaddress/{id}', 'accountController@editaddressSave')->name('editaddressSave');
			Route::get('/addressdelete/{id}', 'accountController@addressdelete');
			//view my order
			Route::get('/myorders', 'webCartController@viewmyorders');
			Route::get('/orderdetails/{orderid}', 'webCartController@myorderdetails');
			Route::post('/logout', 'accountController@logout')->name('logout');
		});
		//view completed order details
		Route::get('/order-details/{orderid}', 'webCartController@ordercompleted');
		Route::post('/order-details/{orderid}', 'webCartController@orderCompletedPay')->name('order.details.pay');
		Route::get('/order-details/{orderid}/{gateway}', 'webCartController@orderCompletedStartToPay')->name('order.details.start.pay');
		Route::get('/order-print/{orderid}', 'webCartController@orderprint');
		Route::get('/vendor-print-order/{orderid}/{vendorid}', 'webCartController@vendororderprint');
        Route::prefix('blog')
            ->namespace('Blog')
            ->as('blog.')
            ->Group(function () {
                Route::get('/' , [\App\Http\Controllers\Blog\PostController::class , 'index'] )->name('home');
                Route::get('/category/{id}/{slug?}' , [\App\Http\Controllers\Blog\PostController::class , 'category'] )->name('category');
                Route::get('/post/{id}/{slug?}' , [\App\Http\Controllers\Blog\PostController::class , 'show'] )->name('show');
                Route::get('/archive/{year}/{month?}' , [\App\Http\Controllers\Blog\PostController::class , 'archive'] )->name('archive');
                Route::get('/tag/{tag}' , [\App\Http\Controllers\Blog\PostController::class , 'tag'] )->name('tag');
                Route::post('/post/{id}/{slug?}' , [\App\Http\Controllers\Blog\PostController::class , 'storeComment'] )->name('storeComment');
        });
        //feed google
        Route::get('google.xml', 'SitemapController@googleFeed');
	}
);

//push notification
Route::get('/testpushy', 'webPushController@testpushy');
Route::get('/cronForOrderPushNotification', 'webPushController@cronForOrderPushNotification');

//feed google
Route::get('/{locale}/google.xml', 'SitemapController@googleFeed');
//sitemap
Route::get('sitemap.xml', 'SitemapController@index');
//******Get Areas From Dezorder *****////
Route::get('/getDezOrderAreas', 'DezOrderStuffController@getDezOrderAreas');

//import hakum items to kash5astore(Do not enable until not going to use it)
//Route::get('getProductsOther/{catid}', 'ImportController@listProducts');;
//Route::get('getItemsFromApi/{catid}/{mcatid}', 'ImportController@getItemsFromApi');
//Route::get('listProductsImages/{catid}', 'ImportController@listProductsImages');
//Route::get('getProductsGalleryImages/{productid}', 'ImportController@getProductsGalleryImages');

//to import item from mrk to other website
//Route::get('getItemsFromApiMrk', 'ImportController@getItemsFromApiMrk');

// //view File and Images
//Route::get('/uploads/{main}/{thumb}/{file}', 'FileController@showthumb')->middleware('cors');
//Route::get('/uploads/{main}/{file}', 'FileController@show')->middleware('cors');
//Route::get('/uploads/{file}', 'FileController@showFile')->middleware('cors');
//Route::get('/videos/{file}', 'FileController@showvideo')->middleware('cors');



