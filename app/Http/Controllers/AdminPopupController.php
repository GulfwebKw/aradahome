<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Popup;
use App\Settings;
use Image;
use File;
use Response;
use Auth;

class AdminPopupController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function index(Request $request)
	{

		$settingInfo = Settings::where("keyname", "setting")->first();
		$popupLists = Popup::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
		return view('gwc.popup.index', ['popupLists' => $popupLists]);
	}


	/**
	Display the banner listings
	 **/
	public function create()
	{

		$lastOrderInfo = Popup::OrderBy('display_order', 'desc')->first();
		if (!empty($lastOrderInfo->display_order)) {
			$lastOrder = ($lastOrderInfo->display_order + 1);
		} else {
			$lastOrder = 1;
		}
		return view('gwc.popup.create')->with(['lastOrder' => $lastOrder]);
	}



	/**
	Store New banner Details
	 **/
	public function store(Request $request)
	{

		$image_thumb_w = 600;
		$image_thumb_h = 600;
		$image_big_w   = 600;
		$image_big_h   = 600;
		$settingInfo = Settings::where("keyname", "setting")->first();

		//field validation
		$this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_en',
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_ar',
			'image'        => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);

		try {
			//upload Popup Image
			$popupImagePath = null;
			if ($request->image) {
				$popupImagePath  = $this->savePopupImage($request->image);
			}

			$popup = new Popup;
			$popup->title_en = $request->input('title_en');
			$popup->title_ar = $request->input('title_ar');
			$popup->link_type = $request->input('link_type');
			$popup->link = $request->input('link');
			$popup->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$popup->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$popup->image = $popupImagePath;
			$popup->save();

			//save logs
			$key_name   = "popup";
			$key_id     = $popup->id;
			$message    = "A new record for banner is added. (" . $popup->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/popup')->with('message-success', 'A record is added successfully');
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
		$editPopup = Popup::find($id);
		return view('gwc.popup.edit', compact('editPopup'));
	}


	/**
	 * Show the details of the banner.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	// public function view($id)
	// {
	// 	$bannerDetails = Banner::find($id);
	// 	return view('gwc.banner.view', compact('bannerDetails'));
	// }



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
		//field validation  
		$this->validate($request, [
			'title_en'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_en,' . $id,
			'title_ar'     => 'nullable|min:3|max:190|string|unique:gwc_banners,title_ar,' . $id,
			'image'        => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);


		try {
			$popup = Popup::find($id);
			$popupImage = '';

			if (!empty($request->image)) {
				//delete Pop Image
				if (!empty($popup->image)) {
					$popUpImage = "/uploads/popup/" . $popup->image;
					$thumbImage = "/uploads/popup/thumb/" . $popup->image;
					$this->deleteImages($popUpImage, $thumbImage);
				}
				//Otherwise save image
				$popupImage  = $this->savePopUpImage($request->image);
				$popup->image = $popupImage;
			}
			$popup->title_en = $request->input('title_en');
			$popup->title_ar = $request->input('title_ar');
			$popup->link_type = $request->input('link_type');
			$popup->link = $request->input('link');
			$popup->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$popup->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
			$popup->save();


			//save logs
			$key_name   = "popup";
			$key_id     = $popup->id;
			$message    = "Record for popup is edited. (" . $popup->title_en . ")";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/popup')->with('message-success', 'Information is updated successfully');
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
		$popup = Popup::find($id);
		//delete image from folder
		if (!empty($popup->image)) {
			$web_image_path = "/uploads/popup/" . $popup->image;
			$web_image_paththumb = "/uploads/popup/thumb/" . $popup->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		$popup->image = '';
		$popup->save();

		//save logs
		$key_name   = "popup";
		$key_id     = $popup->id;
		$message    = "Image is removed. (" . $popup->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		return redirect()->back()->with('message-success', 'Popup is deleted successfully');
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
			return redirect('/gwc/popup')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$popup = Popup::find($id);
		//check cat id exist or not
		if (empty($popup->id)) {
			return redirect('/gwc/popup')->with('message-error', 'No record found');
		}

		//delete parent cat mage
		if (!empty($popup->image)) {
			$web_image_path = "/uploads/popup/" . $popup->image;
			$web_image_paththumb = "/uploads/popup/thumb/" . $popup->image;
			if (File::exists(public_path($web_image_path))) {
				File::delete(public_path($web_image_path));
				File::delete(public_path($web_image_paththumb));
			}
		}

		//save logs
		$key_name   = "popup";
		$key_id     = $popup->id;
		$message    = "A record is removed. (" . $popup->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$popup->delete();
		return redirect()->back()->with('message-success', 'popup is deleted successfully');
	}




	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = Popup::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "popup";
		$key_id     = $recDetails->id;
		$message    = "popup status is changed to " . $active . " (" . $recDetails->title_en . ")";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	public  function savePopUpImage($image)
	{
		$image_w = 600;
		$img_thumb_w = 300;
		$imgExt = $image->getClientOriginalExtension();

		$imageName = "";
		$popupImagePath = 'uploads/popup/';
		$thumbImagePath = 'uploads/popup/thumb/';

		$imageName = 'b-' . md5(time()) . '.' .  $imgExt;
		if ($imgExt == 'svg') {
			$image->move(public_path($popupImagePath), $imageName);
			copy(public_path($popupImagePath  . $imageName), public_path($thumbImagePath . $imageName));
		} else {
			$image->move(public_path($popupImagePath), $imageName);
			$contentImage = Image::make(public_path($popupImagePath  . $imageName));
			$contentImage->resize($image_w, null, function ($constraint) {
				$constraint->aspectRatio();
			})->save(public_path($popupImagePath  . $imageName));
			$this->saveToThumb($img_thumb_w, $img_thumb_w, $popupImagePath, $imageName, $thumbImagePath);
		}
		return $imageName;
	}
	public function saveToThumb($thumb_H, $thumb_W, $parentPath, $parentImageName, $destination)
	{
		$img = Image::make(public_path($parentPath . $parentImageName));
		//resize image
		$img->resize($thumb_H, $thumb_W, function ($constraint) {
			$constraint->aspectRatio();
		}); //Fixed w,h
		// save to thumb
		$img->save(public_path($destination . $parentImageName));
	}
	public function deleteImages($img, $imgThumb)
	{
		if (File::exists(public_path($img))) {
			File::delete(public_path($img));
			File::delete(public_path($imgThumb));
		}
	}
}
