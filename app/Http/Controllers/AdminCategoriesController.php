<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;
use App\Settings;
use App\Product;
use App\ProductGallery;
use App\ProductCategory;
use App\ProductAttribute;
use Image;
use File;
use Response;
use Auth;
use DB;

class AdminCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $categories    = Categories::with('allproducts')->where('parent_id', '=', 0)->get();
      $allCategories = Categories::all();
	  
	  $totalCategories = count($allCategories);

      return view('gwc.category.index',compact('categories','allCategories','totalCategories'));
    }
	
	/**
	Display the Categories listings
	**/
	public function create()
    {
	
	$categories = Categories::where('parent_id', '=', 0)->get();
	$lastOrderInfo = Categories::where('parent_id', '=', 0)->OrderBy('display_order','desc')->first();
	//dd($lastOrderInfo->display_order);
	if(!empty($lastOrderInfo->display_order)){
	$lastOrder=($lastOrderInfo->display_order+1);
	}else{
	$lastOrder=1;
	}
	//$categoriesTree = $categories->tree();
	return view('gwc.category.create')->with([
        'categories'  =>  $categories,'lastOrder'=>$lastOrder
      ]);
	}
	

	
	/**
	Store New Category Details
	**/
	public function store(Request $request)
    {
	$settingInfo = Settings::where("keyname","setting")->first();
		
		$category_thumb_w = 450;
		$category_thumb_h = 450;
		
		$category_big_w = 990;
		$category_big_h = 990;
		
		//field validation
	    $this->validate($request, [
            'name_en' => 'required|min:3|max:100|string',
			'name_ar' => 'required|min:3|max:100|string',
			'friendly_url' => 'required|min:3|max:200|string|unique:gwc_categories,friendly_url',
			'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
		
	    try{
		
		
		//upload image
		$imageName="";
		if($request->hasfile('image')){
		$imageName = 'c-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
		$request->image->move(public_path('uploads/category/original'), $imageName);
		// open file a image resource
		$imgbig = Image::make(public_path('uploads/category/original/'.$imageName));
		//resize image
		$imgbig->resize($category_big_w,$category_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
		//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
		// insert watermark at bottom-right corner with 10px offset
		//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
		//}
		// save to imgbig thumb
		$imgbig->save(public_path('uploads/category/'.$imageName));
		
		//create thumb
		// open file a image resource
		$img = Image::make(public_path('uploads/category/'.$imageName));
		//resize image
		$img->resize($category_thumb_w,$category_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
		// save to thumb
		$img->save(public_path('uploads/category/thumb/'.$imageName));
		}
		
		//header image
		
		if($request->hasfile('header_image')){
		$headerimageName = 'h-'.md5(time()).'.'.$request->header_image->getClientOriginalExtension();
		$request->header_image->move(public_path('uploads/category'), $headerimageName);
		}else{
		$headerimageName='';
		}

		$category = new Categories;
		
		$category->parent_id=$request->input('parent_id');
		$category->name_en=$request->input('name_en');
		$category->name_ar=$request->input('name_ar');
		$category->details_en=$request->input('details_en');
		$category->details_ar=$request->input('details_ar');
		$category->seo_keywords_en=$request->input('seo_keywords_en');
		$category->seo_keywords_ar=$request->input('seo_keywords_ar');
		$category->seo_description_en=$request->input('seo_description_en');
		$category->seo_description_ar=$request->input('seo_description_ar');
		$category->friendly_url=$request->input('friendly_url');
		$category->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
		$category->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
		$category->is_full_width=!empty($request->input('is_full_width'))?$request->input('is_full_width'):'0';
		$category->image=$imageName;
		$category->header_image=$headerimageName;
		$category->save();
		
		//save logs
		$key_name   = "category";
		$key_id     = $category->id;
		$message    = "New category is added (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs

        return redirect('/gwc/category')->with('message-success','Category is added successfully');
		
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
	    $categories   = Categories::where('parent_id', '=', 0)->get();
	    $editcategory = Categories::find($id);
        return view('gwc.category.edit',compact('editcategory','categories'));
    }
	
	
	 /**
     * Show the details of the categories.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
		$categoryDetails = Categories::find($id);
		//$countCats = $categoryDetails->childs()->count();
		$countCats = $this->countChildPages($categoryDetails);
		$countProducts = Product::query()
							->select('gwc_products.id','gwc_products_category.product_id','gwc_products_category.category_id')
							->join('gwc_products_category','gwc_products_category.product_id','=','gwc_products.id')
							->where(['gwc_products_category.category_id' => $id])
							->get();
        return view('gwc.category.view',compact('categoryDetails','countCats','countProducts'));
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
	
	$settingInfo = Settings::where("keyname","setting")->first();
	    $category_thumb_w = 450;
		$category_thumb_h = 450;
		
		$category_big_w = 990;
		$category_big_h = 990;
		
		
	 //field validation  
	   $this->validate($request, [
            'name_en' => 'required|min:3|max:100|string',
			'name_ar' => 'required|min:3|max:100|string',
			'friendly_url' => 'required|min:3|max:200|string|unique:gwc_categories,friendly_url,'.$id,
			'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
		
		
	   try{
	   
	 
		
	$category = Categories::find($id);
	//upload image
	if($request->hasfile('image')){
	//delete image from folder
	if(!empty($category->image)){
	$web_image_path = "/uploads/category/".$category->image;
	$web_image_paththumb = "/uploads/category/thumb/".$category->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	//
	$imageName = 'b-'.md5(time()).'.'.$request->image->getClientOriginalExtension();
	
	$request->image->move(public_path('uploads/category/original'), $imageName);
	//create thumb
	// open file a image resource
    $imgbig = Image::make(public_path('uploads/category/original/'.$imageName));
	//resize image
	$imgbig->resize($category_big_w,$category_big_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	
	//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
	// insert watermark at bottom-right corner with 10px offset
    //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
	//}
	// save to imgbig thumb
	$imgbig->save(public_path('uploads/category/'.$imageName));
	
	//create thumb
	// open file a image resource
    $img = Image::make(public_path('uploads/category/'.$imageName));
	//resize image
	$img->resize($category_thumb_w,$category_thumb_h,function($constraint){$constraint->aspectRatio();});//Fixed w,h
	// save to thumb
	$img->save(public_path('uploads/category/thumb/'.$imageName));
	
    $category->image=$imageName;
	
	}
	//header image
	if($request->hasfile('header_image')){
	//delete image from folder
	if(!empty($category->header_image)){
	$web_image_path = "/uploads/category/".$category->header_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	 }
	}
	//
	$headerimageName = 'h-'.md5(time()).'.'.$request->header_image->getClientOriginalExtension();
	$request->header_image->move(public_path('uploads/category'), $headerimageName);
    $category->header_image=$headerimageName;
	}
	
	$category->parent_id=$request->input('parent_id');
	$category->name_en=$request->input('name_en');
	$category->name_ar=$request->input('name_ar');
	$category->details_en=$request->input('details_en');
	$category->details_ar=$request->input('details_ar');
	$category->seo_keywords_en=$request->input('seo_keywords_en');
	$category->seo_keywords_ar=$request->input('seo_keywords_ar');
	$category->seo_description_en=$request->input('seo_description_en');
	$category->seo_description_ar=$request->input('seo_description_ar');
	$category->friendly_url=$request->input('friendly_url');
	$category->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
	$category->display_order=!empty($request->input('display_order'))?$request->input('display_order'):'0';
	$category->is_full_width=!empty($request->input('is_full_width'))?$request->input('is_full_width'):'0';
	$category->save();
	//save logs
		$key_name   = "category";
		$key_id     = $category->id;
		$message    = "Category is edited (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	return redirect('/gwc/category')->with('message-success','Category is updated successfully');
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
	$web_image_path = "/uploads/category/".$category->image;
	$web_image_paththumb = "/uploads/category/thumb/".$category->image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	 }
	}
	
	$category->image='';
	$category->save();
	    //save logs
		$key_name   = "category";
		$key_id     = $category->id;
		$message    = "Category image is deleted (".$category->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
	return redirect()->back()->with('message-success','Image is deleted successfully');	
	}
	
	
	public function deleteHeaderImage($id){
	$category = Categories::find($id);
	//delete image from folder
	if(!empty($category->header_image)){
	$web_image_path = "/uploads/category/".$category->header_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	 }
	}
	
	$category->header_image='';
	$category->save();
	    //save logs
		$key_name   = "category";
		$key_id     = $category->id;
		$message    = "Category header_image is deleted (".$category->name_en.")";
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
	 return redirect('/gwc/category')->with('message-error','Param ID is missing'); 
	 }
	 //get cat info
	 $category = Categories::find($id);
	 //check cat id exist or not
	 if(empty($category->id)){
	 return redirect('/gwc/category')->with('message-error','No record found'); 
	 }
	 self::DeleteProductCategory($id); // delete items & pictures & attributes
	 //delete child categories
	 $this->destroy_childs($id);
	 //delete parent cat mage
	 if(!empty($category->image)){
	 $web_image_path = "/uploads/category/".$category->image;
	 $web_image_paththumb = "/uploads/category/thumb/".$category->image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	  }
	 }
	 
	 if(!empty($category->header_image)){
	 $web_image_path = "/uploads/category/".$category->header_image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	  }
	 }
	    //save logs
		$key_name   = "category";
		$key_id     = $category->id;
		$message    = "Category is removed (".$category->name_en.")";
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
	 $categorys = Categories::where("parent_id",$id)->get();
	 //check cat id exist or not
	 if(count($categorys)){
	 foreach($categorys as $category){
			 $categorychild = Categories::find($category->id);
			 //delete parent cat mage
			 if(!empty($categorychild->image)){
			 $web_image_path = "/uploads/category/".$categorychild->image;
			 $web_image_paththumb = "/uploads/category/thumb/".$categorychild->image;
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
	$categoryLists = ProductCategory::where('category_id',$catid)->get();
	if(!empty($categoryLists) && count($categoryLists)>0){
	foreach($categoryLists as $categoryList){
	    self::deleteProduct($categoryList->product_id);
	    $caty = ProductCategory::find($categoryList->id);
	    $caty->delete();
	  }	
	 }
	} 
	
	 public static function deleteProduct($id){
	 //get cat info
	 $product = Product::find($id);
	 if(!empty($product->id)){
	 
	 //delete parent cat mage
	 if(!empty($product->image)){
	 $web_image_path = "/uploads/product/".$product->image;
	 $web_image_paththumb = "/uploads/product/thumb/".$product->image;
	 $web_image_pathorigin = "/uploads/product/original/".$product->image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	   File::delete(public_path($web_image_pathorigin));
	  }
	 }
	 
	 //delete rollover
	 if(!empty($product->rollover_image)){
	 $web_image_path = "/uploads/product/".$product->rollover_image;
	 $web_image_paththumb = "/uploads/product/thumb/".$product->rollover_image;
	 $web_image_pathorigin = "/uploads/product/original/".$product->rollover_image;
	 if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	   File::delete(public_path($web_image_paththumb));
	   File::delete(public_path($web_image_pathorigin));
	  }
	 }
	    //save logs
		$key_name   = "product";
		$key_id     = $product->id;
		$message    = "An item is removed. (".$product->title_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
	 //remove gallery
	 self::removeMultipleImages($product->id);
	 //remove attributes
	 self::removeChosenAttributes($product->id);
	 
	 $product->delete();
	 }
	 }
	 
	 
	 public static function removeMultipleImages($product_id){
	    $attachimgs = ProductGallery::where('product_id',$product_id)->get();
		if(!empty($attachimgs) && count($attachimgs)>0){
		foreach($attachimgs as $attachimg){	
		$attachimgsingle = ProductGallery::find($attachimg->id);
		//delete image from folder
		if(!empty($attachimgsingle->file_name)){
		$web_image_path = "/uploads/product/".$attachimgsingle->file_name;
		$web_image_thumb_path = "/uploads/product/thumb/".$attachimgsingle->file_name;
		$web_image_origin_path = "/uploads/product/original/".$attachimgsingle->file_name;
		   if(File::exists(public_path($web_image_path))){
		   File::delete(public_path($web_image_path));
		   File::delete(public_path($web_image_thumb_path));
		   File::delete(public_path($web_image_origin_path));
		   }
		  }	
		  $attachimgsingle->delete();
		 }
		}
	}
	
	//remove chosen attributes
	public static function removeChosenAttributes($product_id){
	    $attachimgs = ProductAttribute::where('product_id',$product_id)->get();
		if(!empty($attachimgs) && count($attachimgs)>0){
		foreach($attachimgs as $attachimg){	
		  $attachimgsingle = ProductAttribute::find($attachimg->id);
		  $attachimgsingle->delete();
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
		'seo_keywords_en',
		'seo_keywords_ar',
		'seo_description_en',
		'seo_description_ar',
		'image',
		'friendly_url',
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
		$row['seo_keywords_en'], 
		$row['seo_keywords_ar'],
		$row['seo_description_en'],
		$row['seo_description_ar'],
		$row['image'],
		$row['friendly_url'],
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
	
    $category = Categories::find($id);
	
	//upload image
	if($request->hasfile('offer_image')){
	//delete image from folder
	if(!empty($category->offer_image)){
	$web_image_path = "/uploads/category/".$category->offer_image;
	if(File::exists(public_path($web_image_path))){
	   File::delete(public_path($web_image_path));
	 }
	}
	//
	$imageName = 'promo-'.md5(time()).'.'.$request->offer_image->getClientOriginalExtension();
	
	$request->offer_image->move(public_path('uploads/category'), $imageName);
	
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
		$recDetails = Categories::where('id',$request->id)->first(); 
		if($recDetails['is_highlighted']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "category";
		$key_id     = $recDetails->id;
		$message    = "Category highligted status is changed to ".$active." (".$recDetails->name_en.")";
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
		$recDetails = Categories::where('id',$request->id)->first(); 
		if($recDetails['is_active']==1){
			$active=0;
		}else{
			$active=1;
		}
		
		//save logs
		$key_name   = "category";
		$key_id     = $recDetails->id;
		$message    = "Category status is changed to ".$active." (".$recDetails->name_en.")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name,$key_id,$message,$created_by);
		//end save logs
		
		
		$recDetails->is_active=$active;
		$recDetails->save();
		return ['status'=>200,'message'=>'Status is modified successfully'];
	} 
	
	 
}
