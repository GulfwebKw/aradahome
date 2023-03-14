<?php

namespace App\Http\Controllers;

use App\BundleCategories;
use App\bundleSetting;
use App\Settings;
use Illuminate\Http\Request;

class webBundleController extends Controller
{

    public function index(){
        if ( ! (new bundleSetting())->is_active )
            abort(404);
        $categories = BundleCategories::CategoriesTree();
        return view('website.bundle', compact('categories'));
    }
    public function ajax_products($id){
        if ( ! (new bundleSetting())->is_active )
            abort(404);
        $settingInfo   = Settings::where("keyname", "setting")->first();
        $isProductOutOfStock = $settingInfo->show_out_of_stock;
        $productQuantity = $isProductOutOfStock == 0 ? 0 : -1;
        $category = BundleCategories::findOrFail($id);
        $html = "";
        $products = $category->allproducts()->with(['products' => function($query) use($productQuantity) {
            $query->whereIn('is_active',[1,2]);
            $query->where('Quantity', '>' , $productQuantity );
        }])->get();
        $TempOrder = BundleCategories::allTempOrderProductQuantity();
        foreach ( $products as $product){
            if ( $product->products == null ) continue;
            $html .= '
                <div class="col-12 col-md-5 col-lg-4">
							<div class="tt-product thumbprod-center" style="    padding: 25px;">
								<div class="tt-image-box">
								    <span class="tt-img">
								        <img src="'.url('uploads/product/thumb/'.$product->products->image).'"alt="'.$product->products['title_'.app()->getLocale()].'">
                                    </span>
                                    '.( $product->products['quantity'] <= 0 ? '<span class="tt-label-location"><span class="tt-label-sale">'.trans('webMessage.outofstock').'</span>                                                                                                                            <span class="tt-label" style="background-color:;color:#fff;border-radius:5px;font-size:12px;padding:3px;">
                                                                </span>
                                                                                                                    </span>' : '' ).'
                                </div>
								<div class="tt-description">
									<h2 class="tt-title">'.$product->products['title_'.app()->getLocale()].'</h2>
									<div class="tt-row">
										<ul class="tt-add-info">
											<li><a href="/details/'.$product->products->id.'/'.$product->products->slug.'" target="_blank">'.__('webMessage.viewdetails').'</a></li>
										</ul>
									</div>
									<div class="tt-price">'.\App\Currency::default().' '.$product->products->retail_price.'</div>
									'.( $product->products['quantity'] > 0 ? '
									<form onsubmit="return false:" name="addtocartDetailsForm" id="addtocartDetailsForm_'.$product->products->id.'" method="POST" action="/ajax_details_addtocart?'.app()->getLocale().'" enctype="multipart/form-data">

										<input type="hidden" name="_token" value="'.csrf_token().'">
										<input type="hidden" name="product_id" id="product_id_'.$product->products->id.'" value="'.$product->products->id.'">
										<input type="hidden" name="price" id="unit_price_'.$product->products->id.'" value="'.$product->products->retail_price.'">
										<div class="tt-input-counter style-01" style="margin: 10px auto;">
										    <div style="display:none;">'.$product->products->quantity.'</div>
											<span class="minus-btn"></span>
											<input type="text" value="'.( $TempOrder[$product->products->id] ?? 1 ).'" size="5" name="quantity_attr" id="quantity_attr_'.$product->products->id.'">
											<span class="plus-btn"></span>
										</div>
										<div onclick="addToCartDetails('.$product->products->id.')" id="details_cartbtn_'.$product->products->id.'" class="btn tt-btn-addtocart thumbprod-button-bg">'.__('webMessage.addtocart').'</div>
									</form>
									<div class="alert alert-success mt-3" style="display: none;" id="item_is_added_bundle_'.$product->products->id.'">'.__('webMessage.item_is_added').'</div>': '').'
								</div>
							</div>
						</div>
            ';
        }
        return ['status' => 200 , 'html' => $html , 'resource' =>$products ];
    }
}
