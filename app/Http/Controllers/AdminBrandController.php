<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Settings;
use Image;
use File;
use Response;
use App\Services\BrandSlug;
use PDF;
use Auth;

class AdminBrandController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		$brandLists  = Brand::with('products')->orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
		return view('gwc.brand.index', ['brandLists' => $brandLists]);
	}


	/**
	Display the brand listings
	 **/
	public function create()
	{

		$lastOrderInfo = Brand::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}
		return view('gwc.brand.create')->with(['lastOrder' => $lastOrder]);
	}



	/**
	Store New brand Details
	 **/
	public function store(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		$image_thumb_w = 450;
		$image_thumb_h = 450;

		$image_big_w = 900;
		$image_big_h = 900;

		//field validation'mimes:jpeg,png,jpg,gif,svg|max:2048'
		$this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_brands,title_en',
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_brands,title_ar',
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'bgimage'      => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'discount' 	   => 'required|integer|min:0|max:99'
		]);



		try {

			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
				$imageName = 'b-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/brand'), $imageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/brand/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/brand/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/brand/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/brand/thumb/' . $imageName));
			}

			//background image
			$bgimageName = '';
			//upload image
			if ($request->hasfile('bgimage')) {
				$bgimageName = 'bg-' . md5(time()) . '.' . $request->bgimage->getClientOriginalExtension();
				$request->bgimage->move(public_path('uploads/brand'), $bgimageName);
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/brand/' . $bgimageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h	
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/brand/' . $bgimageName));
				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/brand/' . $bgimageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/brand/thumb/' . $bgimageName));
			}

			$brand = new Brand;
			//slug
			$slug = new BrandSlug;

			$brand->slug = $slug->createSlug($request->title_en);
			$brand->title_en = $request->input('title_en');
			$brand->title_ar = $request->input('title_ar');
			$brand->details_en = $request->input('details_en');
			$brand->details_ar = $request->input('details_ar');
			$brand->is_discount =  !empty($request->input('is_discount')) ? $request->input('is_discount') : '0';
			$brand->discount = $request->input('discount');
			$brand->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$brand->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$brand->image = $imageName;
			$brand->bgimage = $bgimageName;
			$brand->save();

			//save logs
			$key_name   = "brand";
			$key_id     = $brand->id;
			$message    = "A new record for brand is added. (" . $brand->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/brand')->with('message-success', 'A record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
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
		$editbrand = Brand::find($id);
		return view('gwc.brand.edit', compact('editbrand'));
	}


	/**
	 * Show the details of the brand.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$brandDetails = Brand::find($id);
		return view('gwc.brand.view', compact('brandDetails'));
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
	

		$settingInfo = Settings::where("keyname", "setting")->first();

		$image_thumb_w = 450;
		$image_thumb_h = 450;
		$image_big_w   = 900;
		$image_big_h   = 900;

		//field validation  
		$this->validate($request, [
			'title_en'     => 'required|min:3|max:190|string|unique:gwc_brands,title_en,' . $id,
			'title_ar'     => 'required|min:3|max:190|string|unique:gwc_brands,title_ar,' . $id,
			'details_en'   => 'nullable|string',
			'details_ar'   => 'nullable|string',
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'bgimage'      => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'discount' 	   => 'required|integer|min:0|max:99'
		]);


		try {



			$brand = Brand::find($id);
			$imageName = '';
			//upload image
			if ($request->hasfile('image')) {
				//delete image from folder
				if (!empty($brand->image)) {
					$web_image_path = "/uploads/brand/" . $brand->image;
					$web_image_paththumb = "/uploads/brand/thumb/" . $brand->image;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$imageName = 'b-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();

				$request->image->move(public_path('uploads/brand'), $imageName);
				//create thumb
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/brand/' . $imageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h	
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/brand/' . $imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/brand/' . $imageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/brand/thumb/' . $imageName));
			} else {
				$imageName = $brand->image;
			}


			//background image
			$bgimageName = '';
			//upload image
			if ($request->hasfile('bgimage')) {
				//delete image from folder
				if (!empty($brand->bgimage)) {
					$web_image_path = "/uploads/brand/" . $brand->bgimage;
					$web_image_paththumb = "/uploads/brand/thumb/" . $brand->bgimage;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$bgimageName = 'bg-' . md5(time()) . '.' . $request->bgimage->getClientOriginalExtension();

				$request->bgimage->move(public_path('uploads/brand'), $bgimageName);
				//create thumb
				// open file a image resource
				$imgbig = Image::make(public_path('uploads/brand/' . $bgimageName));
				//resize image
				$imgbig->resize($image_big_w, $image_big_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h	
				// save to imgbig thumb
				$imgbig->save(public_path('uploads/brand/' . $bgimageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/brand/' . $bgimageName));
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/brand/thumb/' . $bgimageName));
			} else {
				$bgimageName = $brand->bgimage;
			}

			//slug
			$slug = new BrandSlug;

			$brand->slug = $slug->createSlug($request->title_en, $id);
			$brand->title_en = $request->input('title_en');
			$brand->title_ar = $request->input('title_ar');
			$brand->details_en = $request->input('details_en');
			$brand->details_ar = $request->input('details_ar');
			$brand->is_discount = !empty($request->input('is_discount')) ? $request->input('is_discount') : '0';
			$brand->discount = $request->discount;
			$brand->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$brand->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$brand->image = $imageName;
			$brand->bgimage = $bgimageName;
			$brand->save();


			//save logs
			$key_name   = "brand";
			$key_id     = $brand->id;
			$message    = "Record for brand is edited. (" . $brand->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs


			return redirect('/gwc/brand')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}

	/**
	 * Delete the Image.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	public function deleteImage($id)
	{
		$brand = Brand::find($id);
		//delete image from folder
		if (!empty($brand->image)) {
			$web_image_path = "/uploads/brand/" . $brand->image;
			$web_image_paththumb = "/uploads/brand/thumb/" . $brand->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$brand->image = '';
		$brand->save();

		//save logs
		$key_name   = "brand";
		$key_id     = $brand->id;
		$message    = "Brand Image is removed. (" . $brand->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		return redirect()->back()->with('message-success', 'Image is deleted successfully');
	}


	public function deleteBgImage($id)
	{
		$brand = Brand::find($id);
		//delete image from folder
		if (!empty($brand->bgimage)) {
			$web_image_path = "/uploads/brand/" . $brand->bgimage;
			$web_image_paththumb = "/uploads/brand/thumb/" . $brand->bgimage;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$brand->bgimage = '';
		$brand->save();

		//save logs
		$key_name   = "brand";
		$key_id     = $brand->id;
		$message    = "Brand Bg Image is removed. (" . $brand->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect()->back()->with('message-success', 'Image is deleted successfully');
	}

	/**
	 * Delete brand along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/brand')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$brand = Brand::find($id);
		//check cat id exist or not
		if (empty($brand->id)) {
			return redirect('/gwc/brand')->with('message-error', 'No record found');
		}

		//delete parent cat mage
		if (!empty($brand->image)) {
			$web_image_path = "/uploads/brand/" . $brand->image;
			$web_image_paththumb = "/uploads/brand/thumb/" . $brand->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		if (!empty($brand->bgimage)) {
			$web_image_path = "/uploads/brand/" . $brand->bgimage;
			$web_image_paththumb = "/uploads/brand/thumb/" . $brand->bgimage;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "brand";
		$key_id     = $brand->id;
		$message    = "A record is removed. (" . $brand->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$brand->delete();
		return redirect()->back()->with('message-success', 'Brand is deleted successfully');
	}




	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Brand::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "brand";
		$key_id     = $recDetails->id;
		$message    = "Brand status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}


	public function updateHomeStatusAjax(Request $request)
	{
		$recDetails = Brand::where('id', $request->id)->first();
		if ($recDetails['is_home'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "brand";
		$key_id     = $recDetails->id;
		$message    = "Brand status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_home = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}


	public function updateLogoStatusAjax(Request $request)
	{
		$recDetails = Brand::where('id', $request->id)->first();
		if ($recDetails['is_logo_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "brand";
		$key_id     = $recDetails->id;
		$message    = "Brand Logo status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_logo_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}
}
