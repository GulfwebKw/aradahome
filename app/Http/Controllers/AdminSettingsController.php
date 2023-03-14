<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;
use App\Settings; //model
use App\NotificationEmails; //model
use Image;
use File;
use Response;
use Auth;

class AdminSettingsController extends Controller
{
	//view the page
	public function index()
	{


		//currency
		$currencies = Currency::getCode();
		//sorting
		$sortings = ["ASC", "DESC"];
		//social link
		$sociallinks = ["facebook", "twitter", "instagram", "linkedin", "youtube", "whatsapp"];
		//available payments
		$paymentslink = ["COD", "POSTKNET", "KNET", "TPAY", "GKNET", "GTPAY", "TAH", "MF", "PAYPAL", 'CS','Q8LINK','MasterCard']; //"COD:Cash On Delivery","KNET:Knet Gateway","TPAY","CC","MF","GKNET","GTPAY","PAYPAL"

		$settingDetails = Settings::where('keyname', 'setting')->first();

		return view('gwc.setting.adminSettingsForm', compact('settingDetails', 'currencies', 'sortings', 'sociallinks', 'paymentslink'));
	}

	/**
	Store setting details
	 **/
	public function update(Request $request, $keyname)
	{

		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/general-settings')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}
		//field validation
		$this->validate($request, [
			'name_en' => 'required|min:3|max:150|string',
			'name_ar' => 'required|min:3|max:150|string',
			'seo_description_en' => 'required|min:3|max:600|string',
			'seo_description_ar' => 'required|min:3|max:600|string',
			'seo_keywords_en' => 'required|min:3|max:300|string',
			'seo_keywords_ar' => 'required|min:3|max:300|string',
			'owner_name' => 'required|min:3|max:150|string',
			'address_en' => 'required|min:3|max:500|string',
			'address_ar' => 'required|min:3|max:500|string',
			'email' => 'required|email|min:3|max:150|string',
			'mobile' => 'nullable|string|max:192',
			'phone' => 'nullable|string|max:192',
			'logo' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'emaillogo' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
			'favicon' => 'mimes:jpg,jpeg,gif,png,ico,x-icon|max:2048',
			'header_image' => 'mimes:jpg,jpeg,gif,png|max:2048',
			'item_per_page_front' => 'numeric',
			'item_per_page_back' => 'numeric',
			'bestseller_items' => 'numeric',
			'featured_items' => 'numeric',
			'latest_items' => 'numeric',
			'special_items' => 'numeric',
			'items_of_the_day' => 'numeric',
			'base_currency' => 'string',
			'default_sort' => 'string',
			'image_thumb_w' => 'required',
			'image_thumb_h' => 'required',
			'image_big_w' => 'required',
			'image_big_h' => 'required',
			'office_hours_en' => 'nullable|max:192|string',
			'office_hours_ar' => 'nullable|max:192|string',
		]);

		//check if water status is on , image required
		$is_watermark = !empty($request->input('is_watermark')) ? $request->input('is_watermark') : '0';
		if ($request->hasfile('watermark_img') && $is_watermark == 0) {
			return redirect()->back()->with("message-error", "Please choose watermark status");
		}

		try {


			$setting = Settings::where("keyname", "setting")->first();
			//upload logo

			if ($request->hasfile('logo')) {
				//delete image from folder
				if (!empty($setting->logo)) {
					$web_logo_path = "/uploads/logo/" . $setting->logo;
					if (File::exists(public_path($web_logo_path))) {
						File::delete(public_path($web_logo_path));
					}
				}
				$logoName = 'logo-' . md5(time()) . '.' . $request->logo->getClientOriginalExtension();
				$request->logo->move(public_path('uploads/logo'), $logoName);

				$img = Image::make(public_path('uploads/logo/' . $logoName));
				$img->resize(48, 48, function ($constraint) {
					$constraint->aspectRatio();
				});
				$img->save(public_path('uploads/logo/qr.png'));
			} else {
				$logoName = $setting->logo;
			}

			if ($request->hasfile('footerlogo')) {
				//delete image from folder
				if (!empty($setting->footerlogo)) {
					$web_logo_path = "/uploads/logo/" . $setting->footerlogo;
					if (File::exists(public_path($web_logo_path))) {
						File::delete(public_path($web_logo_path));
					}
				}
				$footerlogoName = 'footr-' . md5(time()) . '.' . $request->footerlogo->getClientOriginalExtension();
				$request->footerlogo->move(public_path('uploads/logo'), $footerlogoName);
			} else {
				$footerlogoName = $setting->footerlogo;
			}


			//upload email logo
			$emaillogoName = "";
			if ($request->hasfile('emaillogo')) {
				//delete image from folder
				if (!empty($setting->emaillogo)) {
					$web_elogo_path = "/uploads/logo/" . $setting->emaillogo;
					if (File::exists(public_path($web_elogo_path))) {
						File::delete(public_path($web_elogo_path));
					}
				}
				$emaillogoName = 'elogo-' . md5(time()) . '.' . $request->emaillogo->getClientOriginalExtension();
				$request->emaillogo->move(public_path('uploads/logo'), $emaillogoName);
			} else {
				$emaillogoName = $setting->emaillogo;
			}
			//upload favicon
			$faviconName = "";
			if ($request->hasfile('favicon')) {
				//delete image from folder
				if (!empty($setting->favicon)) {
					$web_fav_path = "/uploads/logo/" . $setting->favicon;
					if (File::exists(public_path($web_fav_path))) {
						File::delete(public_path($web_fav_path));
					}
				}
				$faviconName = 'favicon-' . md5(time()) . '.' . $request->favicon->getClientOriginalExtension();
				$request->favicon->move(public_path('uploads/logo'), $faviconName);
			} else {
				$faviconName = $setting->favicon;
			}

			//upload header image
			$headerName = "";
			if ($request->hasfile('header_image')) {
				//delete image from folder
				if (!empty($setting->header_image)) {
					$web_hed_path = "/uploads/logo/" . $setting->header_image;
					if (File::exists(public_path($web_hed_path))) {
						File::delete(public_path($web_hed_path));
					}
				}
				$headerName = 'header_image-' . md5(time()) . '.' . $request->header_image->getClientOriginalExtension();
				$request->header_image->move(public_path('uploads/logo'), $headerName);
			} else {
				$headerName = $setting->header_image;
			}

			//upload watermark
			$watermarkName = "";
			if ($request->hasfile('watermark_img')) {
				//delete image from folder
				if (!empty($setting->watermark_img)) {
					$web_wm_path = "/uploads/logo/" . $setting->watermark_img;
					if (File::exists(public_path($web_wm_path))) {
						File::delete(public_path($web_wm_path));
					}
				}
				$watermarkName = 'watermark-' . md5(time()) . '.' . $request->watermark_img->getClientOriginalExtension();
				$request->watermark_img->move(public_path('uploads/logo'), $watermarkName);
			} else {
				$watermarkName = $setting->watermark_img;
			}

			//upload key 12 for GA
			$gaName = "";
			if ($request->hasfile('gakeys')) {
				//delete image from folder
				if (!empty($setting->gakeys)) {
					$web_wm_path = "/uploads/logo/" . $setting->gakeys;
					if (File::exists(public_path($web_wm_path))) {
						File::delete(public_path($web_wm_path));
					}
				}
				$gaName = 'ga-' . md5(time()) . '.' . $request->gakeys->getClientOriginalExtension();
				$request->gakeys->move(public_path('uploads/logo'), $gaName);
			} else {
				$gaName = $setting->gakeys;
			}

			$setting->gakeys                = $gaName;
			$setting->google_profileid      = $request->input('google_profileid');
			$setting->google_analyticsemail = $request->input('google_analyticsemail');
			$setting->copyrights_en         = $request->input('copyrights_en');
			$setting->copyrights_ar         = $request->input('copyrights_ar');

			//check app option

			$setting->android_url         = $request->input('android_url');
			$setting->ios_url             = $request->input('ios_url');
			$setting->huawei_url          = $request->input('huawei_url');
			$setting->android_old_version = $request->input('android_old_version');
			$setting->android_new_version = $request->input('android_new_version');
			$setting->ios_old_version     = $request->input('ios_old_version');
			$setting->ios_new_version     = $request->input('ios_new_version');
			///
			//check social links
			$setting->social_facebook  = $request->input('social_facebook');
			$setting->social_twitter   = $request->input('social_twitter');
			$setting->social_instagram = $request->input('social_instagram');
			$setting->social_linkedin  = $request->input('social_linkedin');
			$setting->social_youtube   = $request->input('social_youtube');
			$setting->social_whatsapp  = $request->input('social_whatsapp');

			if (!empty($request->input('payments'))) {
				$setting->payments = implode(",", $request->input('payments'));
			}
			$setting->office_hours_en = $request->input('office_hours_en');
			$setting->office_hours_ar = $request->input('office_hours_ar');

			$setting->name_en = $request->input('name_en');
			$setting->name_ar = $request->input('name_ar');
			$setting->seo_description_en = $request->input('seo_description_en');
			$setting->seo_description_ar = $request->input('seo_description_ar');
			$setting->seo_keywords_en = $request->input('seo_keywords_en');
			$setting->seo_keywords_ar = $request->input('seo_keywords_ar');
			$setting->owner_name = $request->input('owner_name');
			$setting->map_embed_url = $request->input('map_embed_url');
			$setting->address_en = $request->input('address_en');
			$setting->address_ar = $request->input('address_ar');
			$setting->email = $request->input('email');
			$setting->mobile = $request->input('mobile');
			$setting->phone = $request->input('phone');
			//$setting->is_active=!empty($request->input('is_active'))?$request->input('is_active'):'0';
			$setting->footerlogo = $footerlogoName;
			$setting->logo = $logoName;
			$setting->emaillogo = $emaillogoName;
			$setting->favicon = $faviconName;
			$setting->watermark_img = $watermarkName;
			$setting->header_image = $headerName;

			$setting->is_admin_menu_minimize  = !empty($request->input('is_admin_menu_minimize')) ? $request->input('is_admin_menu_minimize') : '0';

			$setting->auto_change_order_status  = !empty($request->input('auto_change_order_status')) ? $request->input('auto_change_order_status') : '0';


			$setting->is_show_tags  = !empty($request->input('is_show_tags')) ? $request->input('is_show_tags') : '0';
			$setting->is_discount  = !empty($request->input('is_discount')) ? $request->input('is_discount') : '0';
			$setting->is_express  = !empty($request->input('is_express')) ? $request->input('is_express') : '0';

			$setting->order_note_en  = !empty($request->input('order_note_en')) ? $request->input('order_note_en') : '';
			$setting->order_note_ar  = !empty($request->input('order_note_ar')) ? $request->input('order_note_ar') : '';

			$setting->invoice_template  = !empty($request->input('invoice_template')) ? $request->input('invoice_template') : '1';
			$setting->invoice_qrcode  = $request->input('invoice_qrcode') == null ? '0' : '1';


			$setting->is_review_active  = !empty($request->input('is_review_active')) ? $request->input('is_review_active') : '0';

			$setting->is_more_menu  = !empty($request->input('is_more_menu')) ? $request->input('is_more_menu') : '0';
			$setting->is_offer_menu = !empty($request->input('is_offer_menu')) ? $request->input('is_offer_menu') : '0';

			$setting->is_float_whatsapp = !empty($request->input('is_float_whatsapp')) ? $request->input('is_float_whatsapp') : '0';

			$setting->is_float_store_location = !empty($request->input('is_float_store_location')) ? $request->input('is_float_store_location') : '0';

			$setting->is_new_item_active = !empty($request->input('is_new_item_active')) ? $request->input('is_new_item_active') : '0';
			$setting->new_item_badge = !empty($request->input('new_item_badge')) ? $request->input('new_item_badge') : '0';

			$setting->is_watermark = !empty($request->input('is_watermark')) ? $request->input('is_watermark') : '0';
			$setting->is_brand_active = !empty($request->input('is_brand_active')) ? $request->input('is_brand_active') : '0';


			$setting->item_per_page_front = !empty($request->input('item_per_page_front')) ? $request->input('item_per_page_front') : '50';
			$setting->item_per_page_back = !empty($request->input('item_per_page_back')) ? $request->input('item_per_page_back') : '50';

			$setting->default_sort = !empty($request->input('default_sort')) ? $request->input('default_sort') : '0';
			//category image
			$setting->image_thumb_w = !empty($request->input('image_thumb_w')) ? $request->input('image_thumb_w') : '150';
			$setting->image_thumb_h = !empty($request->input('image_thumb_h')) ? $request->input('image_thumb_h') : '150';
			$setting->image_big_w = !empty($request->input('image_big_w')) ? $request->input('image_big_w') : '500';
			$setting->image_big_h = !empty($request->input('image_big_h')) ? $request->input('image_big_h') : '500';


			$setting->google_analytics = $request->input('google_analytics');

			$setting->is_lang = !empty($request->input('is_lang')) ? $request->input('is_lang') : '0';

			$setting->prefix = $request->input('prefix');
			$setting->item_code_digits = $request->input('item_code_digits');
			$setting->base_currency = $request->input('base_currency');
			$setting->extra_meta_tags = $request->input('extra_meta_tags');
			$setting->note_for_new_customer_en = $request->input('note_for_new_customer_en');
			$setting->note_for_new_customer_ar = $request->input('note_for_new_customer_ar');
			$setting->newsletter_note_en = $request->input('newsletter_note_en');
			$setting->newsletter_note_ar = $request->input('newsletter_note_ar');
			$setting->flat_rate = $request->input('flat_rate');
			$setting->home_note1_title_en = $request->input('home_note1_title_en');
			$setting->home_note2_title_en = $request->input('home_note2_title_en');
			$setting->home_note3_title_en = $request->input('home_note3_title_en');
			$setting->home_note4_title_en = $request->input('home_note4_title_en');
			$setting->home_note1_details_en = $request->input('home_note1_details_en');
			$setting->home_note2_details_en = $request->input('home_note2_details_en');
			$setting->home_note3_details_en = $request->input('home_note3_details_en');
			$setting->home_note4_details_en = $request->input('home_note4_details_en');
			$setting->home_note1_title_ar = $request->input('home_note1_title_ar');
			$setting->home_note2_title_ar = $request->input('home_note2_title_ar');
			$setting->home_note3_title_ar = $request->input('home_note3_title_ar');
			$setting->home_note4_title_ar = $request->input('home_note4_title_ar');
			$setting->home_note1_details_ar = $request->input('home_note1_details_ar');
			$setting->home_note2_details_ar = $request->input('home_note2_details_ar');
			$setting->home_note3_details_ar = $request->input('home_note3_details_ar');
			$setting->home_note4_details_ar = $request->input('home_note4_details_ar');

			$setting->top_header_note_en = $request->input('top_header_note_en');
			$setting->top_header_note_ar = $request->input('top_header_note_ar');

			$setting->quantit_update_notification_en = $request->input('quantit_update_notification_en');
			$setting->quantit_update_notification_ar = $request->input('quantit_update_notification_ar');

			$setting->from_email = $request->input('from_email');
			$setting->from_name = $request->input('from_name');

			$setting->web_server_key  = $request->input('web_server_key');
			$setting->pushy_api_token = $request->input('pushy_api_token');

			$setting->supplier_registration  = !empty($request->input('supplier_registration')) ? $request->input('supplier_registration') : '0';
			//free delivery
			$setting->is_free_delivery     = !empty($request->input('is_free_delivery')) ? $request->input('is_free_delivery') : '0';
			$setting->free_delivery_amount = !empty($request->input('free_delivery_amount')) ? $request->input('free_delivery_amount') : '0';

			//minimum order amount
			$setting->min_order_amount    = !empty($request->input('min_order_amount')) ? $request->input('min_order_amount') : '0.500';

			$setting->is_brand_image_name = !empty($request->input('is_brand_image_name')) ? $request->input('is_brand_image_name') : 'title';

			$setting->instagram_token     = !empty($request->input('instagram_token')) ? $request->input('instagram_token') : '';
			$setting->instagram_clientId  = !empty($request->input('instagram_clientId')) ? $request->input('instagram_clientId') : '';
			$setting->instagram_userId    = !empty($request->input('instagram_userId')) ? $request->input('instagram_userId') : '';
			$setting->column_list         = !empty($request->input('column_list')) ? $request->input('column_list') : '2';
			$setting->is_home_popup       = !empty($request->input('is_home_popup')) ? $request->input('is_home_popup') : '0';
			$setting->is_cart_popup       = !empty($request->input('is_cart_popup')) ? $request->input('is_cart_popup') : '0';

			$setting->postknet_note_en    = !empty($request->input('postknet_note_en')) ? $request->input('postknet_note_en') : '';
			$setting->postknet_note_ar    = !empty($request->input('postknet_note_ar')) ? $request->input('postknet_note_ar') : '';
			$setting->show_out_of_stock     = !empty($request->input('show_out_of_stock')) ? $request->input('show_out_of_stock') : '0';
			$setting->show_all_currencies     = !empty($request->input('show_all_currencies')) ? $request->input('show_all_currencies') : '0';

			$setting->validate_cust_name    = !empty($request->input('validate_cust_name')) ? $request->input('validate_cust_name') : '0';
			$setting->validate_cust_mob     = !empty($request->input('validate_cust_mob')) ? $request->input('validate_cust_mob') : '0';
			$setting->validate_cust_area = !empty($request->input('validate_cust_area')) ? $request->input('validate_cust_area') : '0';
			$setting->validate_cust_block   = !empty($request->input('validate_cust_block')) ? $request->input('validate_cust_block') : '0';
			$setting->validate_cust_st    = !empty($request->input('validate_cust_st')) ? $request->input('validate_cust_st') : '0';
			$setting->validate_cust_hno  = !empty($request->input('validate_cust_hno')) ? $request->input('validate_cust_hno') : '0';


            $setting->pos_note_en = $request->input('pos_note_en');
            $setting->pos_note_ar = $request->input('pos_note_ar');
            $setting->check_out_note_en = $request->input('check_out_note_en');
            $setting->check_out_note_ar = $request->input('check_out_note_ar');
            if( ! empty($request->input('pos_supervisor_password')) )
                $setting->pos_supervisor_password = bcrypt($request->input('pos_supervisor_password'));

			$setting->save();
			cache()->forget('settingInfoScope');
			//save logs
			$key_name   = "setting";
			$key_id     = $setting->id;
			$message    = "Website settings are updated";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/general-settings')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}


	//delete favicon
	public function deleteFavicon()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->favicon)) {
			$web_fav_path = "/uploads/logo/" . $setting->favicon;
			if (File::exists(public_path($web_fav_path))) {
				File::delete(public_path($web_fav_path));
			}
		}
		$setting->favicon = '';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Favicon is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect('/gwc/general-settings')->with('message-success', 'Favicon is removed successfully');
	}

	//delete logo
	public function deleteLogo()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->logo)) {
			$web_fav_path = "/uploads/logo/" . $setting->logo;
			if (File::exists(public_path($web_fav_path))) {
				File::delete(public_path($web_fav_path));
			}
		}
		$setting->logo = '';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Logo is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/general-settings')->with('message-success', 'Logo is removed successfully');
	}

	public function deleteFooterLogo()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->footerlogo)) {
			$web_fav_path = "/uploads/logo/" . $setting->footerlogo;
			if (File::exists(public_path($web_fav_path))) {
				File::delete(public_path($web_fav_path));
			}
		}
		$setting->footerlogo = '';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Footer Logo is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/general-settings')->with('message-success', 'Logo is removed successfully');
	}

	//delete emai llogo
	public function deleteEmailLogo()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->emaillogo)) {
			$web_fav_path = "/uploads/logo/" . $setting->emaillogo;
			if (File::exists(public_path($web_fav_path))) {
				File::delete(public_path($web_fav_path));
			}
		}
		$setting->emaillogo = '';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Email logo is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/general-settings')->with('message-success', 'E-mail Logo is removed successfully');
	}


	//delete watermark
	public function deletewatermark()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->watermark_img)) {
			$web_wtm_path = "/uploads/logo/" . $setting->watermark_img;
			if (File::exists(public_path($web_wtm_path))) {
				File::delete(public_path($web_wtm_path));
			}
		}
		$setting->watermark_img = '';
		$setting->is_watermark = '0';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Watermark image is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect('/gwc/general-settings')->with('message-success', 'Watermark image is removed successfully');
	}



	public function deleteheaderimg()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->header_image)) {
			$web_wtm_path = "/uploads/logo/" . $setting->header_image;
			if (File::exists(public_path($web_wtm_path))) {
				File::delete(public_path($web_wtm_path));
			}
		}
		$setting->header_image = '';
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "header_image image is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect('/gwc/general-settings')->with('message-success', 'Header image is removed successfully');
	}


	///////////////////////////////////////////////////////////FACEBOOK SETTING
	public function facebooksetting()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		return view('gwc.setting.adminFacebookForm', compact('settingDetails'));
	}

	public function facebooksettingpost(Request $request)
	{

		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/facebook-setting')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		$this->validate($request, [
			'facebook_pixel' => 'nullable|min:3|string'
		]);

		try {

			$setting = Settings::where("keyname", "setting")->first();



			$setting->facebook_pixel           = $request->input('facebook_pixel');
			$setting->og_title                 = !empty($request->input('og_title')) ? $request->input('og_title') : '1';
			$setting->og_description           = !empty($request->input('og_description')) ? $request->input('og_description') : '1';
			$setting->og_url                   = !empty($request->input('og_url')) ? $request->input('og_url') : '1';
			$setting->og_image                 = !empty($request->input('og_image')) ? $request->input('og_image') : '1';
			$setting->og_brand                 = !empty($request->input('og_brand')) ? $request->input('og_brand') : '0';;
			$setting->og_availability          = !empty($request->input('og_availability')) ? $request->input('og_availability') : '1';;
			$setting->og_category              = !empty($request->input('og_category')) ? $request->input('og_category') : '0';;
			$setting->og_condition             = !empty($request->input('og_condition')) ? $request->input('og_condition') : '1';;
			$setting->og_gender                = !empty($request->input('og_gender')) ? $request->input('og_gender') : '0';;
			$setting->og_locale                = !empty($request->input('og_locale')) ? $request->input('og_locale') : '1';;
			$setting->og_retailer_item_id      = !empty($request->input('og_retailer_item_id')) ? $request->input('og_retailer_item_id') : '1';;
			$setting->og_currency              = !empty($request->input('og_currency')) ? $request->input('og_currency') : '1';;
			$setting->og_amount                = !empty($request->input('og_amount')) ? $request->input('og_amount') : '1';;
			$setting->og_sale_price_dates_start = !empty($request->input('og_sale_price_dates_start')) ? $request->input('og_sale_price_dates_start') : '0';;
			$setting->og_sale_price_dates_end  = !empty($request->input('og_sale_price_dates_end')) ? $request->input('og_sale_price_dates_end') : '0';;

			$setting->save();

			//save logs
			$key_name   = "setting";
			$key_id     = $setting->id;
			$message    = "Facebook setting information is updated";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/facebook-setting')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}
	///////////////////////////////////////////////////////////SMS SETTING
	public function smssetting()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		$smsDetails = Common::DezsmsPoints();
		return view('gwc.setting.adminSmsForm', compact('settingDetails', 'smsDetails'));
	}

	public function smssettingpost(Request $request)
	{

		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/smssetting')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		if (!empty($request->is_sms_active) && empty($request->sms_userid)) {
			return redirect()->back()->withErrors(["sms_userid" => "Please enter Dezsms User ID"])->withInput();
		}
		if (!empty($request->is_sms_active) && empty($request->sms_sender_name)) {
			return redirect()->back()->withErrors(["sms_sender_name" => "Please enter Dezsms Sender Name"])->withInput();
		}
		if (!empty($request->is_sms_active) && empty($request->sms_api_key)) {
			return redirect()->back()->withErrors(["sms_api_key" => "Please enter Dezsms API Key"])->withInput();
		}


		if (!empty($request->sms_text_cod_active) && empty($request->sms_text_cod_en) && empty($request->sms_text_cod_ar)) {
			return redirect()->back()->withErrors(["sms_text_cod_en" => "Please enter sms text", "sms_text_cod_ar" => "Please enter sms text"])->withInput();
		}

		if (!empty($request->sms_text_knet_active) && empty($request->sms_text_knet_en) && empty($request->sms_text_knet_ar)) {
			return redirect()->back()->withErrors(["sms_text_knet_en" => "Please enter sms text", "sms_text_knet_ar" => "Please enter sms text"])->withInput();
		}

		if (!empty($request->sms_text_track_order_active) && empty($request->sms_text_track_order_en) && empty($request->sms_text_track_order_ar)) {
			return redirect()->back()->withErrors(["sms_text_track_order_en" => "Please enter sms text", "sms_text_track_order_ar" => "Please enter sms text"])->withInput();
		}

		if (!empty($request->sms_text_outofstock_active) && empty($request->sms_text_outofstock_en) && empty($request->sms_text_outofstock_ar)) {
			return redirect()->back()->withErrors(["sms_text_outofstock_en" => "Please enter sms text", "sms_text_outofstock_ar" => "Please enter sms text"])->withInput();
		}
		if (!empty($request->sms_text_paymentlink_sent_active) && empty($request->sms_text_paymentlink_sent_en) && empty($request->sms_text_paymentlink_sent_ar)) {
			return redirect()->back()->withErrors(["sms_text_paymentlink_sent_en" => "Please enter sms text", "sms_text_paymentlink_sent_ar" => "Please enter sms text"])->withInput();
		}


		try {



			$setting = Settings::where("keyname", "setting")->first();
			$setting->is_sms_active       = !empty($request->input('is_sms_active')) ? $request->input('is_sms_active') : '0';
			$setting->sms_userid          = !empty($request->input('sms_userid')) ? $request->input('sms_userid') : '';
			$setting->sms_sender_name     = !empty($request->input('sms_sender_name')) ? $request->input('sms_sender_name') : '';
			$setting->sms_api_key         = !empty($request->input('sms_api_key')) ? $request->input('sms_api_key') : '';

			if (empty($request->is_sms_active)) {
				$setting->sms_text_cod_active        = !empty($request->input('sms_text_cod_active')) ? $request->input('sms_text_cod_active') : '0';
				$setting->sms_text_knet_active       = !empty($request->input('sms_text_knet_active')) ? $request->input('sms_text_knet_active') : '0';
				$setting->sms_text_cod_active        = !empty($request->input('sms_text_cod_active')) ? $request->input('sms_text_cod_active') : '0';
				$setting->sms_text_outofstock_active = !empty($request->input('sms_text_outofstock_active')) ? $request->input('sms_text_outofstock_active') : '0';
				$setting->sms_text_paymentlink_sent_active = !empty($request->input('sms_text_paymentlink_sent_active')) ? $request->input('sms_text_paymentlink_sent_active') : '0';
			}
			//
			$setting->sms_text_cod_active = !empty($request->input('sms_text_cod_active')) ? $request->input('sms_text_cod_active') : '0';
			$setting->sms_text_cod_en     = !empty($request->input('sms_text_cod_en')) ? $request->input('sms_text_cod_en') : '';
			$setting->sms_text_cod_ar     = !empty($request->input('sms_text_cod_ar')) ? $request->input('sms_text_cod_ar') : '';
			//
			$setting->sms_text_knet_active = !empty($request->input('sms_text_knet_active')) ? $request->input('sms_text_knet_active') : '0';
			$setting->sms_text_knet_en     = !empty($request->input('sms_text_knet_en')) ? $request->input('sms_text_knet_en') : '';
			$setting->sms_text_knet_ar     = !empty($request->input('sms_text_knet_ar')) ? $request->input('sms_text_knet_ar') : '';
			//
			$setting->sms_text_track_order_active     = !empty($request->input('sms_text_track_order_active')) ? $request->input('sms_text_track_order_active') : '0';
			$setting->sms_text_track_order_en = !empty($request->input('sms_text_track_order_en')) ? $request->input('sms_text_track_order_en') : '';
			$setting->sms_text_track_order_ar = !empty($request->input('sms_text_track_order_ar')) ? $request->input('sms_text_track_order_ar') : '';
			//
			$setting->sms_text_outofstock_active = !empty($request->input('sms_text_outofstock_active')) ? $request->input('sms_text_outofstock_active') : '0';
			$setting->sms_text_outofstock_en     = !empty($request->input('sms_text_outofstock_en')) ? $request->input('sms_text_outofstock_en') : '';
			$setting->sms_text_outofstock_ar     = !empty($request->input('sms_text_outofstock_ar')) ? $request->input('sms_text_outofstock_ar') : '';
			
			$setting->sms_text_paymentlink_sent_active = !empty($request->input('sms_text_paymentlink_sent_active')) ? $request->input('sms_text_paymentlink_sent_active') : '0';
			$setting->sms_text_paymentlink_sent_en     = !empty($request->input('sms_text_paymentlink_sent_en')) ? $request->input('sms_text_paymentlink_sent_en') : '';
			$setting->sms_text_paymentlink_sent_ar     = !empty($request->input('sms_text_paymentlink_sent_ar')) ? $request->input('sms_text_paymentlink_sent_ar') : '';

			if (empty($request->is_sms_active)) {
				$setting->sms_text_cod_active         = !empty($request->input('sms_text_cod_active')) ? $request->input('sms_text_cod_active') : '0';
				$setting->sms_text_knet_active        = !empty($request->input('sms_text_knet_active')) ? $request->input('sms_text_knet_active') : '0';
				$setting->sms_text_track_order_active = !empty($request->input('sms_text_track_order_active')) ? $request->input('sms_text_track_order_active') : '0';
				$setting->sms_text_outofstock_active  = !empty($request->input('sms_text_outofstock_active')) ? $request->input('sms_text_outofstock_active') : '0';
				$setting->sms_text_paymentlink_sent_active  = !empty($request->input('sms_text_paymentlink_sent_active')) ? $request->input('sms_text_paymentlink_sent_active') : '0';
			}



			$setting->save();

			//save logs
			$key_name   = "setting";
			$key_id     = $setting->id;
			$message    = "SMS setting information is updated";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/smssetting')->with('message-success', 'Information is updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}
	///about us


	public function aboutus()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		return view('gwc.setting.adminaboutusForm', compact('settingDetails'));
	}

	public function aboutuspost(Request $request)
	{
		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/aboutus')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		$this->validate($request, [
			'about_title_1_en' => 'required|min:3|max:150|string',
			'about_title_1_ar' => 'required|min:3|max:150|string',
			'about_title_2_en' => 'required|min:3|max:600|string',
			'about_title_2_en' => 'required|min:3|max:600|string',
			'about_details_en' => 'required|min:3|string',
			'about_details_ar' => 'required|min:3|string',
			'image'            => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
		]);

		$setting = Settings::where("keyname", "setting")->first();

		if (!empty($setting->image_thumb_w) && !empty($setting->image_thumb_h)) {
			$image_thumb_w = $setting->image_thumb_w;
			$image_thumb_h = $setting->image_thumb_h;
		} else {
			$image_thumb_w = 100;
			$image_thumb_h = 100;
		}

		if (!empty($setting->image_big_w) && !empty($setting->image_big_h)) {
			$image_big_w = $setting->image_big_w;
			$image_big_h = $setting->image_big_h;
		} else {
			$image_big_w = 600;
			$image_big_h = 600;
		}

		if ($request->hasfile('image')) {
			//delete image from folder
			if (!empty($setting->image)) {
				$web_image_path = "/uploads/aboutus/" . $setting->image;
				if (File::exists(public_path($web_image_path))) {
					File::delete(public_path($web_image_path));
				}
			}
			$imageName = 'image-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();

			$request->image->move(public_path('uploads/aboutus'), $imageName);
			// open file a image resource
			$imgbig = Image::make(public_path('uploads/aboutus/' . $imageName));
			//resize image
			$imgbig->resize($image_big_w, $image_big_h); //Fixed w,h

			if ($setting->is_watermark == 1 && !empty($setting->watermark_img)) {
				// insert watermark at bottom-right corner with 10px offset
				$imgbig->insert(public_path('uploads/logo/' . $setting->watermark_img), 'bottom-right', 10, 10);
			}
			// save to imgbig thumb
			$imgbig->save(public_path('uploads/aboutus/' . $imageName));
		} else {
			$imageName = $setting->image;
		}


		$setting->about_title_1_en = $request->input('about_title_1_en');
		$setting->about_title_1_ar = $request->input('about_title_1_ar');
		$setting->about_title_2_en = $request->input('about_title_2_en');
		$setting->about_title_2_ar = $request->input('about_title_2_ar');
		$setting->about_details_en = $request->input('about_details_en');
		$setting->about_details_ar = $request->input('about_details_ar');
		$setting->image = $imageName;
		$setting->save();

		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "About us information is updated";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect('/gwc/aboutus')->with('message-success', 'Information is updated successfully');
	}

	//mission
	public function mission()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		return view('gwc.setting.adminmissionForm', compact('settingDetails'));
	}

	public function missionpost(Request $request)
	{
		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/mission')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		$this->validate($request, [
			'mission_title_en'   => 'required|min:3|max:150|string',
			'mission_title_ar'   => 'required|min:3|max:150|string',
			'mission_details_en' => 'required|min:3|string',
			'mission_details_ar' => 'required|min:3|string'
		]);

		$setting = Settings::where("keyname", "setting")->first();

		$setting->mission_title_en = $request->input('mission_title_en');
		$setting->mission_title_ar = $request->input('mission_title_ar');
		$setting->mission_details_en = $request->input('mission_details_en');
		$setting->mission_details_ar = $request->input('mission_details_ar');
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Mission information is updated";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/mission')->with('message-success', 'Information is updated successfully');
	}

	//vision
	public function vision()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		return view('gwc.setting.adminvisionForm', compact('settingDetails'));
	}

	public function visionpost(Request $request)
	{
		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/vision')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		$this->validate($request, [
			'vision_title_en'   => 'required|min:3|max:150|string',
			'vision_title_ar'   => 'required|min:3|max:150|string',
			'vision_details_en' => 'required|min:3|string',
			'vision_details_ar' => 'required|min:3|string'
		]);

		$setting = Settings::where("keyname", "setting")->first();

		$setting->vision_title_en = $request->input('vision_title_en');
		$setting->vision_title_ar = $request->input('vision_title_ar');
		$setting->vision_details_en = $request->input('vision_details_en');
		$setting->vision_details_ar = $request->input('vision_details_ar');
		$setting->save();

		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Vision information is updated";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs

		return redirect('/gwc/vision')->with('message-success', 'Information is updated successfully');
	}

	//team content
	public function teamcontent()
	{
		$settingDetails = Settings::where('keyname', 'setting')->first();
		return view('gwc.setting.adminTeamTextForm', compact('settingDetails'));
	}

	public function teamcontentpost(Request $request)
	{
		$keyname = "setting";
		if (empty($keyname) || $keyname <> "setting") {
			return redirect('/gwc/mission')->with('message-error', 'Internal error found. Please reload the page and try again.');
		}

		//field validation
		$this->validate($request, [
			'team_content_en' => 'required|min:3|string',
			'team_content_ar' => 'required|min:3|string'
		]);

		$setting = Settings::where("keyname", "setting")->first();

		$setting->team_content_en = $request->input('team_content_en');
		$setting->team_content_ar = $request->input('team_content_ar');
		$setting->save();
		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "Team context information is updated";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/teamcontent')->with('message-success', 'Information is updated successfully');
	}

	//delete watermark
	public function deleteimage()
	{
		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->image)) {
			$web_wtm_path = "/uploads/aboutus/" . $setting->image;
			$web_wtm_path_thumb = "/uploads/aboutus/thumb/" . $setting->image;
			if (File::exists(public_path($web_wtm_path))) {
				File::delete(public_path($web_wtm_path));
			}
			if (File::exists(public_path($web_wtm_path_thumb))) {
				File::delete(public_path($web_wtm_path_thumb));
			}
		}
		$setting->image = '';
		$setting->save();

		//save logs
		$key_name   = "setting";
		$key_id     = $setting->id;
		$message    = "About us image is removed";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs
		return redirect('/gwc/aboutus')->with('message-success', 'Image is removed successfully');
	}

	//show subject
	public function notifyemails()
	{
		$settingInfo    = Settings::where("keyname", "setting")->first();
		$emailsNotifys  = NotificationEmails::orderBy('name', 'ASC')->paginate($settingInfo->item_per_page_back);;
		return view('gwc.emails.notifyemails', compact('emailsNotifys'));
	}
	//save subjects
	public function saveEmail(Request $request)
	{
		//field validation
		$this->validate($request, [
			'name'     => 'required|min:3|max:190|string|unique:gwc_subjects,title_en',
			'email'    => 'required|min:3|max:190|string|unique:gwc_subjects,title_ar',
		]);
		try {

			$subjects = new NotificationEmails;
			$subjects->name = $request->input('name');
			$subjects->email = $request->input('email');
			$subjects->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
			$subjects->save();

			//save logs
			$key_name   = "email";
			$key_id     = $subjects->id;
			$message    = "A new email is added for notification";
			$created_by = Auth::guard('admin')->user()->id;
			Common::saveLogs($key_name, $key_id, $message, $created_by);
			//end save logs

			return redirect('/gwc/notifyemails')->with('message-success', 'Record is added successfully');
		} catch (\Exception $e) {
			return redirect()->back()->with('message-error', $e->getMessage());
		}
	}


	/**
	 * Delete services along with childs via ID.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyEmails($id)
	{
		//check param ID
		if (empty($id)) {
			return redirect('/gwc/notifyemails')->with('message-error', 'Param ID is missing');
		}
		//get cat info
		$emails = NotificationEmails::find($id);
		//check cat id exist or not
		if (empty($emails->id)) {
			return redirect('/gwc/notifyemails')->with('message-error', 'No record found');
		}

		//save logs
		$key_name   = "emails";
		$key_id     = $emails->id;
		$message    = "A email is removed from notification";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$emails->delete();
		return redirect()->back()->with('message-success', 'Record is deleted successfully');
	}

	//update status
	public function updateStatusAjax(Request $request)
	{
		$recDetails = NotificationEmails::where('id', $request->id)->first();
		if ($recDetails['is_active'] == 1) {
			$active = 0;
		} else {
			$active = 1;
		}

		//save logs
		$key_name   = "email";
		$key_id     = $recDetails->id;
		$message    = "Email status is changed to " . $active . " in notification";
		$created_by = Auth::guard('admin')->user()->id;
		Common::saveLogs($key_name, $key_id, $message, $created_by);
		//end save logs


		$recDetails->is_active = $active;
		$recDetails->save();
		return ['status' => 200, 'message' => 'Status is modified successfully'];
	}

	public static function getSetting()
	{
		return Settings::where("keyname", "setting")->first();
	}
	
	public function sendSMS(Request $request){
		return Common::SendSms($request->to, $request->sms_msg);
	}
}
