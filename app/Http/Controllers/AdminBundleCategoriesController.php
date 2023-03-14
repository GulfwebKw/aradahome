<?php

namespace App\Http\Controllers;

use App\BundleCategories;
use App\Product;
use App\ProductBundleCategory;
use Illuminate\Http\Request;
use Image;
use File;
use Response;
use Auth;
use DB;

class AdminBundleCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $categories    = BundleCategories::with('allproducts')->where('parent_id', '=', 0)->get();
      $allCategories = BundleCategories::all();
	  
	  $totalCategories = count($allCategories);

      return view('gwc.bundle.category.index',compact('categories','allCategories','totalCategories'));
    }
	
	/**
	Display the Categories listings
	**/
	public function create()
    {
	
	$categories = BundleCategories::where('parent_id', '=', 0)->get();
	$lastOrderInfo = BundleCategories::where('parent_id', '=', 0)->OrderBy('display_order','desc')->first();
	//dd($lastOrderInfo->display_order);
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	//$categoriesTree = $categories->tree();
	return view('gwc.bundle.category.create')->with([
        'categories'  =>  $categories,'lastOrder'=>$lastOrder
      ]);
	}
	

	
	/**
	Store New Category Details
	**/
	public function store(Request $request)
    {
		$category_thumb_w = 450;
		$category_thumb_h = 450;
		
		$category_big_w = 990;
		$category_big_h = 990;
		
		//field validation
	    $this->validate($request, [
            'name_en' => 'required|min:3|max:100|string',
			'name_ar' => 'required|min:3|max:100|string',
			'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
		
	    try{
		
		
		//upload image
		$imageName="";
		if($request->hasfile('image')){
		$imageName = 'c-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/bundle_category'), $imageName);
		// open file a image resource
		$imgbig = Image::make(public_path('uploads/bundle_category/'.$imageName));
		//resize image
		$imgbig->resize($category_big_w,$category_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
		//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
		// insert watermark at bottom-right corner with 10px offset
		//$imgbig->insert(public_path('uploads/bundle/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
		//}
		// save to imgbig thumb
		$imgbig->save(public_path('uploads/bundle_category/'.$imageName));
		
		//create thumb
		// open file a image resource
		$img = Image::make(public_path('uploads/bundle_category/'.$imageName));
		//resize image
		$img->resize($category_thumb_w,$category_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
		// save to thumb
		$img->save(public_path('uploads/bundle_category/thumb/'.$imageName));
		}

		$category = new BundleCategories;
		
		$category->parent_id=$request->input('parent_id');
		$category->name_en=$request->input('name_en');
		$category->name_ar=$request->input('name_ar');
		$category->details_en=$request->input('details_en');
		$category->details_ar=$request->input('details_ar');
		$category->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$category->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$category->image=$imageName;
		$category->save();
		
		//save logs
		$key_name   = "BundleCategory";
		$key_id     = $category->id;
		$message    = "New category is added (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs

        return redirect('/gwc/bundles/category')->with('message-success','Category is added successfully');
		
		}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	 /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	    $categories   = BundleCategories::where('parent_id', '=', 0)->get();
	    $editcategory = BundleCategories::find($id);
        return view('gwc.bundle.category.edit',compact('editcategory','categories'));
    }
	
	
	 /**
     * Show the details of the categories.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$categoryDetails = BundleCategories::find($id);
		//$countCats = $categoryDetails->childs()->count();
		$countCats = $this->countChildPages($categoryDetails);
		$countProducts = Product::query()
							->select('gwc_products.id','gwc_products_bundle_category.product_id','gwc_products_bundle_category.category_id')
							->join('gwc_products_bundle_category','gwc_products_bundle_category.product_id','=','gwc_products.id')
							->where(['gwc_products_bundle_category.category_id' => $id])
							->get();
        return view('gwc.bundle.category.view',compact('categoryDetails','countCats','countProducts'));
    }
	
	
	
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category_thumb_w = 450;
		$category_thumb_h = 450;
		
		$category_big_w = 990;
		$category_big_h = 990;
		
		
	 //field validation  
	   $this->validate($request, [
            'name_en' => 'required|min:3|max:100|string',
			'name_ar' => 'required|min:3|max:100|string',
			'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
	   try{
	   
	 
		
	$category = BundleCategories::find($id);
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($category->image)){
	$web_image_path = "/uploads/bundle_category/".$category->image;
	$web_image_paththumb = "/uploads/bundle_category/thumb/".$category->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/bundle_category'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/bundle_category/'.$imageName));
	//resize image
	$imgbig->resize($category_big_w,$category_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	
	//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    //$imgbig->insert(public_path('uploads/bundle/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	//}
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/bundle_category/'.$imageName));
	
	//create thumb
	// open file a image resource
    $img = Image::make(public_path('uploads/bundle_category/'.$imageName));
	//resize image
	$img->resize($category_thumb_w,$category_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/bundle_category/thumb/'.$imageName));
	
    $category->image=$imageName;
	
	}
	
	$category->parent_id=$request->input('parent_id');
	$category->name_en=$request->input('name_en');
	$category->name_ar=$request->input('name_ar');
	$category->details_en=$request->input('details_en');
	$category->details_ar=$request->input('details_ar');
	$category->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
	$category->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
	$category->save();
	//save logs
		$key_name   = "BundleCategory";
		$key_id     = $category->id;
		$message    = "Bundle Category is edited (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	return redirect('/gwc/bundles/category')->with('message-success','Category is updated successfully');
	  }catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
	}
	
	/**
     * Delete the Image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	public function deleteImage($id){
	$category = Categories::find($id);
	//delete image from folder
	if(!empty($category->image)){
	$web_image_path = "/uploads/bundle_category/".$category->image;
	$web_image_paththumb = "/uploads/bundle_category/thumb/".$category->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$category->image='';
	$category->save();
	    //save logs
		$key_name   = "BundleCategory";
		$key_id     = $category->id;
		$message    = "Bundle Category image is deleted (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	
	public function deleteHeaderImage($id){
	$category = BundleCategories::find($id);
	//delete image from folder
	if(!empty($category->header_image)){
	$web_image_path = "/uploads/bundle_category/".$category->header_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	 }
	}
	
	$category->header_image='';
	$category->save();
	    //save logs
		$key_name   = "BundleCategory";
		$key_id     = $category->id;
		$message    = "Bundle Category header_image is deleted (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	
	/**
     * Delete category along with childs via ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function destroy($id){
	 //check param ID
	 if(empty($id)){
	 return redirect('/gwc/bundles/category')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $category = BundleCategories::find($id);
	 //check cat id exist or not
	 if(empty($category->id)){
	    return redirect('/gwc/bundles/category')->with('message-error','No record found'); 
	 }
	 self::DeleteProductCategory($id); // delete items & pictures & attributes
	 //delete child categories
	 $this->destroy_childs($id);
	 //delete parent cat mage
	 if(!empty($category->image)){
	 $web_image_path = "/uploads/bundle_category/".$category->image;
	 $web_image_paththumb = "/uploads/bundle_category/thumb/".$category->image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	  }
	 }
	 
	 if(!empty($category->header_image)){
	 $web_image_path = "/uploads/bundle_category/".$category->header_image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	  }
	 }
	    //save logs
		$key_name   = "BundleCategory";
		$key_id     = $category->id;
		$message    = "Bundle Category is removed (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	 //end deleting parent cat image
	 $category->delete();
	 return redirect()->back()->with('message-success','Category is deleted successfully');	
	 }
	 
	 //child category
	 public function destroy_childs($id){
	 //check param ID
	 if(!empty($id)){
	 //get cat info
	 $categorys = BundleCategories::where("parent_id",$id)->get();
	 //check cat id exist or not
	 if(count($categorys)){
	 foreach($categorys as $category){
			 $categorychild = $category;
			 //delete parent cat mage
			 if(!empty($categorychild->image)){
			 $web_image_path = "/uploads/bundle_category/".$categorychild->image;
			 $web_image_paththumb = "/uploads/bundle_category/thumb/".$categorychild->image;
			 if(File::exists(public_path($web_image_path))){
			   File::delete(public_path($web_image_path));
			   File::delete(public_path($web_image_paththumb));
			  }
			 }
			 $this->destroy_childs($category->id);
			 
			 //end deleting parent cat image
			 $categorychild->delete();
	 
	 	                             }
	                      }
	                }	
	 }
	 
	 
	public static function DeleteProductCategory($catid){
	$categoryLists = ProductBundleCategory::where('category_id',$catid)->get();
	if(!empty($categoryLists) && count($categoryLists)>0){
	foreach($categoryLists as $categoryList){
        $categoryList->delete();
	  }	
	 }
	}


	/**
	Download Table as CSV
	**/ 
	public function downloadCSV(){

    $table = Categories::all();
    $filename = "categories.csv";
    $handle = fopen($filename, 'w+');
    fputcsv($handle, array(
	    'id', 
		'name_en', 
		'name_ar', 
		'details_en',
		'details_ar',
		'image',
		'display_order',
		'is_active',
		'parent_id',
		'created_at',
		'updated_at'
		));

    foreach($table as $row) {
        fputcsv($handle, array(
		$row['id'], 
		$row['name_en'], 
		$row['name_ar'], 
		$row['details_en'], 
		$row['details_ar'],
		$row['image'],
		$row['display_order'],
		$row['is_active'],
		$row['parent_id'],
		$row['created_at'],
		$row['updated_at']
		));
    }

    fclose($handle);
    $headers = array(
        'Content-Type' => 'text/csv',
    );

    return Response::download($filename, 'categories.csv', $headers);
    }
	
	
	///count categories & its sub categories
	public function countChildPages($category) {
    $category->pagesCount = 0;
    $category->pagesCount += $category->childs->count();
    foreach( $category->childs as $child ) {
        $category->pagesCount += $this->countChildPages( $child );
    }
    return $category->pagesCount;
    } 
   //update category offer
   
   public function updateOffer(Request $request,$id){
    try{
	
    $category = BundleCategories::find($id);
	
	//upload image
	if($request->hasfile('offer_image')){
	//delete image from folder
	if(!empty($category->offer_image)){
	$web_image_path = "/uploads/bundle_category/".$category->offer_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	 }
	}
	//
	$imageName = 'promo-'.md5(time()).'.'.$request->offer_image->getClientOriginalExtension();
	
	$request->offer_image->move(public_path('uploads/bundle_category'), $imageName);
	
    $category->offer_image=$imageName;
	
	}
	
	
	$category->title_1_en=$request->input('title_1_en');
	$category->title_1_ar=$request->input('title_1_ar');
	$category->title_2_en=$request->input('title_2_en');
	$category->title_2_ar=$request->input('title_2_ar');
	$category->title_3_en=$request->input('title_3_en');
	$category->title_3_ar=$request->input('title_3_ar');
	$category->title_4_en=$request->input('title_4_en');
	$category->title_4_ar=$request->input('title_4_ar');
	$category->offer_link=$request->input('offer_link');
	$category->is_offer=!empty($request->input('is_offer'))?$request->input('is_offer'):'0';
	$category->save();
	return redirect()->back()->with('message-success','Offer details are updated successfully');	
	}catch (\Exception $e) {
	    return redirect()->back()->with('message-error',$e->getMessage());	
	    }
   }	
	
	
	//update status
	public function updateHighLightedStatusAjax(Request $request)
    {
		$recDetails = BundleCategories::where('id',$request->id)->first();
		if($recDetails['is_highlighted']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "BundleCategory";
		$key_id     = $recDetails->id;
		$message    = "Bundle Category highligted status is changed to ".$active." (".$recDetails->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_highlighted=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
	
	//update status
	public function updateStatusAjax(Request $request)
    {
		$recDetails = BundleCategories::where('id',$request->id)->first();
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "BundleCategory";
		$key_id     = $recDetails->id;
		$message    = "Bundle Category status is changed to ".$active." (".$recDetails->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	}



    //view product categories
    public function productCategory($id)
    {
        $Categories     = BundleCategories::where('parent_id', 0)->orderBy('display_order', 'desc')->get();
        $listCategories = ProductBundleCategory::where('product_id', $id)->orderBy('category_id', 'asc')->get();
        $editproduct    = Product::find($id);

        if (Auth::guard('admin')->user()->userType == "vendor") {
            $dirPath = "gwc.vendor.product";
        } else {
            $dirPath = "gwc.bundle.product";
        }

        return view($dirPath . '.edit', compact('editproduct', 'listCategories', 'Categories'));
    }

    ///////////////////////////////////////////////save product categories/////////////////////////////////////////////////
    public function productCategoryUpdate(Request $request, $id)
    {
        try {
            //edit existing categories
            $listCategories = ProductBundleCategory::where('product_id', $id)->get();
            if (!empty($listCategories) && count($listCategories) > 0) {
                foreach ($listCategories as $listCategory) {
                    $catgoryedit = ProductBundleCategory::where('id', $listCategory->id)->first();
                    if (!empty($request->input('category-' . $listCategory->id))) {
                        $catgoryedit->category_id = $request->input('category-' . $listCategory->id);
                        $catgoryedit->save();
                    }
                }
            }
            //end
            if (!empty($id) && !empty($request->attach) && count($request->attach) > 0) {

                $i = 0;
                $j = 0;
                $txt = '';
                foreach ($request->attach as $key => $file) {
                    if (!empty($file['category'])) {
                        $category = $request->attach[$key]['category'];
                        if (!empty($category)) {
                            if ($this->isCategoryExist($id, $category)) {
                                $j++;
                            } else {
                                $filerec = new ProductBundleCategory;
                                $filerec->product_id    = $id;
                                $filerec->category_id   = $category;
                                $filerec->save();
                                //update upper tree cat
                                self::updateUpperCateory($id, $category);
                                $i++;
                            }
                        }
                    }
                }
                if (!empty($j)) {
                    $txt = ' ' . $j . ' is already exist.';
                }
            }


            if (Auth::guard('admin')->user()->userType == "vendor") {
                $baseredirectPath = "/vendor/product";
            } else {
                $baseredirectPath = "/gwc/product";
            }


            if (!empty($request->input('redirect_to_listing'))) {
                return redirect($baseredirectPath)->with('message-success', 'Bundle Categories are updated successfully');
            } else {
                return redirect($baseredirectPath)->with('message-success', 'Bundle Categories are updated successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('message-error', $e->getMessage());
        }
    }
    //check category exist or not for an item
    public function isCategoryExist($product_id, $category_id)
    {
        $filerec = ProductBundleCategory::where('product_id', $product_id)->where('category_id', $category_id)->first();
        if (!empty($filerec->id)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function isCategoryExists($product_id, $category_id)
    {
        $filerec = ProductBundleCategory::where('product_id', $product_id)->where('category_id', $category_id)->first();
        if (!empty($filerec->id)) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function updateUpperCateory($id, $category)
    {

        $upperCategory = BundleCategories::where("id", $category)->first();
        if (!empty($upperCategory->parent_id)) {
            $upperParentCategory = BundleCategories::where("id", $upperCategory->parent_id)->first();
            if (empty(self::isCategoryExists($id, $upperParentCategory->id))) {
                $filerec = new ProductBundleCategory();
                $filerec->product_id    = $id;
                $filerec->category_id   = $upperParentCategory->id;
                $filerec->save();
                //
                if (!empty($upperParentCategory->id)) {
                    self::updateUpperCateory($id, $upperParentCategory->id);
                }
            }
        }
    }

    //delete product chosen category
    public function deleteProdcategory($product_id, $id)
    {

        $attachimg = ProductBundleCategory::where('id', $id)->where('product_id', $product_id)->first();
        $productInfo = Product::where('id', $product_id)->first();

        //save logs
        $key_name   = "product-category";
        $key_id     = $attachimg->id;
        $message    = "A category is removed for (" . $productInfo->title_en . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        $attachimg->delete();

        return redirect()->back()->with('message-success', 'Category is deleted successfully');
    }

}
