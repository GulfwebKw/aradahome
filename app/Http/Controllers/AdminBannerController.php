<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banner;
use App\Settings;
use Image;
use File;
use Response;
use Auth;

class AdminBannerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		$bannerLists = Banner::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
		return view('gwc.banner.index', ['bannerLists' => $bannerLists]);
	}


	/**
	 * Display the banner listings
	 **/
	public function create()
	{

		$lastOrderInfo = Banner::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}
		return view('gwc.banner.create')->with(['lastOrder' => $lastOrder]);
	}



	/**
	 *Store New banner Details
	 **/
	public function store(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();

		if (!empty($request->input('image_size')) && $request->input('image_size') == 1) {
			$image_thumb_w = 296;
			$image_thumb_h = 520;
			$image_big_w   = 592;
			$image_big_h   = 508;
		} else if (!empty($request->input('image_size')) && $request->input('image_size') == 2) {
			$image_thumb_w = 296;
			$image_thumb_h = 520;
			$image_big_w   = 592;
			$image_big_h   = 1040;
		} else if (!empty($request->input('image_size')) && $request->input('image_size') == 3) {
			$image_thumb_w = 612;
			$image_thumb_h = 256;
			$image_big_w   = 1224;
			$image_big_h   = 512;
		} else {
			$image_thumb_w = 290;
			$image_thumb_h = 250;
			$image_big_w   = 890;
			$image_big_h   = 850;
		}

		//field validation
		$this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_en',
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_ar',
			'image'        => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);



		try {


			//upload image
			$imageName = "";
			if ($request->hasfile('image')) {
				$imageName = 'b-' . md5(time());
				$origExt =  $request->image->getClientOriginalExtension();
				$request->image->move(public_path('uploads/banner/'), $imageName . '.' . $origExt);


				// open file a image resource
				//$imgbig = Image::make(public_path('uploads/banner/'.$imageName));
				//resize image
				//$imgbig->resize($image_big_w,$image_big_h,function($constraint){
				//$constraint->aspectRatio();
				//});//Fixed w,h
				//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
				// insert watermark at bottom-right corner with 10px offset
				//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
				//}
				// save to imgbig thumb
				//$imgbig->save(public_path('uploads/banner/'.$imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/banner/'. $imageName . '.' . $origExt));
				$img = $img->encode('webp', 98);
				//resize image
				$big = $img->resize(1920, 1080, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				}); //Fixed w,h
				$big->save(public_path('uploads/banner/'. $imageName . '.webp'));

				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/banner/thumb/'. $imageName . '.webp'));

				if (file_exists(public_path('uploads/banner/'. $imageName . '.' . $origExt))) {
					unlink(public_path('uploads/banner/'. $imageName . '.' . $origExt));
				}
			}

			$banner = new Banner;

			$banner->btype = $request->input('btype');
			$banner->title_en = $request->input('title_en');
			$banner->title_ar = $request->input('title_ar');
			$banner->link_id = $request->input('link_id');
			$banner->link_type = $request->input('link_type');
			$banner->link = $request->input('link');
			$banner->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$banner->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			if ($settingInfo->theme == 8 || $settingInfo->theme == 10 || $settingInfo->theme == 11 || $settingInfo->theme == 15) {
				$banner->image_size = !empty($request->input('image_size')) ? $request->input('image_size') : '0';
				$banner->box = !empty($request->input('box')) ? $request->input('box') : '0';
			}
			$banner->image = $imageName . '.webp';
			$banner->save();

			//save logs
			$key_name   = "banner";
			$key_id     = $banner->id;
			$message    = "A new record for banner is added. (" . $banner->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/banner')->with('message-success', 'A record is added successfully');
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
		$editbanner = Banner::find($id);
		return view('gwc.banner.edit', compact('editbanner'));
	}


	/**
	 * Show the details of the banner.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function view($id)
	{
		$bannerDetails = Banner::find($id);
		return view('gwc.banner.view', compact('bannerDetails'));
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

		if (!empty($request->input('image_size')) && $request->input('image_size') == 1) {
			$image_thumb_w = 296;
			$image_thumb_h = 520;
			$image_big_w   = 592;
			$image_big_h   = 508;
		} else if (!empty($request->input('image_size')) && $request->input('image_size') == 2) {
			$image_thumb_w = 296;
			$image_thumb_h = 520;
			$image_big_w   = 592;
			$image_big_h   = 1040;
		} else if (!empty($request->input('image_size')) && $request->input('image_size') == 3) {
			$image_thumb_w = 612;
			$image_thumb_h = 256;
			$image_big_w   = 1224;
			$image_big_h   = 512;
		} else {
			$image_thumb_w = 290;
			$image_thumb_h = 250;
			$image_big_w   = 890;
			$image_big_h   = 850;
		}

		//field validation  
		$this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_en,' . $id,
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_ar,' . $id,
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);


		try {




			$banner = Banner::find($id);
			$imageName = '';
			//upload image
			if ($request->hasfile('image')) {
				//delete image from folder
				if (!empty($banner->image)) {
					$web_image_path = "/uploads/banner/" . $banner->image;
					$web_image_paththumb = "/uploads/banner/thumb/" . $banner->image;
					if (File::exists(public_path($web_image_path))) {
						File::delete(public_path($web_image_path));
						File::delete(public_path($web_image_paththumb));
					}
				}
				//
				$imageName = 'b-' . md5(time()) . '.webp';

				$request->image->move(public_path('uploads/banner'), $imageName);
				//create thumb
				// open file a image resource
				//$imgbig = Image::make(public_path('uploads/banner/'.$imageName));
				//resize image
				//$imgbig->resize($image_big_w,$image_big_h,function($constraint){
				//$constraint->aspectRatio();
				//});//Fixed w,h

				//if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
				// insert watermark at bottom-right corner with 10px offset
				//$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
				//}
				// save to imgbig thumb
				//$imgbig->save(public_path('uploads/banner/'.$imageName));

				//create thumb
				// open file a image resource
				$img = Image::make(public_path('uploads/banner/' . $imageName));
				$img = $img->encode('webp', 98);
				//resize image
				$img->resize($image_thumb_w, $image_thumb_h, function ($constraint) {
					$constraint->aspectRatio();
				}); //Fixed w,h
				// save to thumb
				$img->save(public_path('uploads/banner/thumb/' . $imageName));
			} else {
				$imageName = $banner->image;
			}

			$banner->btype = $request->input('btype');
			$banner->title_en = $request->input('title_en');
			$banner->title_ar = $request->input('title_ar');
			$banner->link_id = $request->input('link_id');
			$banner->link_type = $request->input('link_type');
			$banner->link = $request->input('link');
			$banner->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			if ($settingInfo->theme == 8 || $settingInfo->theme == 10 || $settingInfo->theme == 11 || $settingInfo->theme == 15) {
				$banner->image_size = !empty($request->input('image_size')) ? $request->input('image_size') : '0';
				$banner->box = !empty($request->input('box')) ? $request->input('box') : '0';
			}
			$banner->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$banner->image = $imageName;
			$banner->save();


			//save logs
			$key_name   = "banner";
			$key_id     = $banner->id;
			$message    = "Record for banner is edited. (" . $banner->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/banner')->with('message-success', 'Information is updated successfully');
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
		$banner = Banner::find($id);
		//delete image from folder
		if (!empty($banner->image)) {
			$web_image_path = "/uploads/banner/" . $banner->image;
			$web_image_paththumb = "/uploads/banner/thumb/" . $banner->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$banner->image = '';
		$banner->save();

		//save logs
		$key_name   = "banner";
		$key_id     = $banner->id;
		$message    = "Image is removed. (" . $banner->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		return redirect()->back()->with('message-success', 'Image is deleted successfully');
	}

	/**
	 * Delete banner along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/banner')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$banner = Banner::find($id);
		//check cat id exist or not
		if (empty($banner->id)) {
			return redirect('/gwc/banner')->with('message-error', 'No record found');
		}

		//delete parent cat mage
		if (!empty($banner->image)) {
			$web_image_path = "/uploads/banner/" . $banner->image;
			$web_image_paththumb = "/uploads/banner/thumb/" . $banner->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "banner";
		$key_id     = $banner->id;
		$message    = "A record is removed. (" . $banner->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$banner->delete();
		return redirect()->back()->with('message-success', 'banner is deleted successfully');
	}




	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Banner::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "banner";
		$key_id     = $recDetails->id;
		$message    = "banner status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}
}
