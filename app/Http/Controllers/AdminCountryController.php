<?php

namespace App\Http\Controllers;

use App\Area;
use App\CountryGatway;
use App\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use App\Country;
use App\Settings;
use App\State;
use Image;
use File;
use Response;
use PDF;
use Hash;
use Auth;

class AdminCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request) //Request $request
    {

        $settingInfo = Settings::where("keyname", "setting")->first();

        //check search queries
        if (!empty($request->get('q'))) {
            $q = $request->get('q');
        } else {
            $q = $request->q;
        }


        //menus records
        if (!empty($q)) {
            $countryLists = Country::with('zones')->where('parent_id', 0)->where('name_en', 'LIKE', '%' . $q . '%')
                ->orwhere('name_ar', 'LIKE', '%' . $q . '%')
                ->orderBy('display_order', $settingInfo->default_sort)
                ->paginate($settingInfo->item_per_page_back);
            $countryLists->appends(['q' => $q]);
        } else {
            $countryLists = Country::with('zones')->where('parent_id', 0)->orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        }
        return view('gwc.country.index', ['countryLists' => $countryLists]);
    }


    /**
     * Display the Services listings
     **/
    public function create()
    {
        $lastOrderInfo = Country::OrderBy('display_order', 'desc')->first();
        if (!empty($lastOrderInfo->display_order)) {
            $lastOrder = ($lastOrderInfo->display_order + 1);
        } else {
            $lastOrder = 1;
        }

        $zones = Zone::where('is_active', 1)->get();

        return view('gwc.country.create', compact('lastOrder', 'zones'));
    }


    /**
     * Store New Services Details
     **/
    public function store(Request $request)
    {

        $settingInfo = Settings::where("keyname", "setting")->first();

        $image_thumb_w = 60;
        $image_thumb_h = 30;

        $image_big_w = 180;
        $image_big_h = 90;

        //field validation
        $this->validate($request, [
            'name_en' => 'required|string|unique:gwc_country,name_en',
            'name_ar' => 'required|string|unique:gwc_country,name_ar',
            'code' => 'required|string|unique:gwc_country,code|max:2',
            'currency' => 'required|string|exists:gwc_currencies,code|max:3',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);


        try {


            //upload image
            $imageName = "";
            if ($request->hasfile('image')) {
                $imageName = 'c-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('uploads/country'), $imageName);
                // open file a image resource
                $imgbig = Image::make(public_path('uploads/country/' . $imageName));
                //resize image
                $imgbig->resize($image_big_w, $image_big_h); //Fixed w,h
                //if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
                // insert watermark at bottom-right corner with 10px offset
                //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
                //}
                // save to imgbig thumb
                $imgbig->save(public_path('uploads/country/' . $imageName));

                //create thumb
                // open file a image resource
                $img = Image::make(public_path('uploads/country/' . $imageName));
                //resize image
                $img->resize($image_thumb_w, $image_thumb_h); //Fixed w,h
                // save to thumb
                $img->save(public_path('uploads/country/thumb/' . $imageName));
            }

            $country = new Country;
            $country->name_en = $request->input('name_en');
            $country->name_ar = $request->input('name_ar');
            $country->zone_id = $request->input('zone');
            $country->code = strtolower($request->input('code'));
            $country->currency = strtoupper($request->input('currency'));
            $country->shipment_method = $request->input('shipment');
            $country->display_order = $request->input('display_order');
            $country->is_state = !empty($request->input('is_state')) ? $request->input('is_state') : '0';
            $country->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
            $country->image = $imageName;
            $country->save();

            if( is_array($request->gateways))
                foreach ($request->gateways as $gateway)
                    $country->gateway()->save(new CountryGatway(['gateway' => $gateway]));

            //save logs
            $key_name = "country";
            $key_id = $country->id;
            $message = "New country record is added as (" . $request->input('name_en') . ")";
            $created_by = Auth::guard('admin')->user()->id;
            Common::saveLogs($key_name, $key_id, $message, $created_by);
            //end save logs

            return redirect('/gwc/country')->with('message-success', 'Country is added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('message-error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editcountry = Country::find($id);
        $zones = Zone::where('is_active', 1)->get();

        return view('gwc.country.edit', compact('editcountry', 'zones'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $settingInfo = Settings::where("keyname", "setting")->first();

        $image_thumb_w = 60;
        $image_thumb_h = 30;
        $image_big_w = 180;
        $image_big_h = 90;

        //field validation
        $this->validate($request, [
            'name_en' => 'required|string|unique:gwc_country,name_en,' . $id,
            'name_ar' => 'required|string|unique:gwc_country,name_ar,' . $id,
            'code' => 'required|string|max:2|unique:gwc_country,code,' . $id,
            'currency' => 'required|string|exists:gwc_currencies,code|max:3',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);


        try {


            $country = Country::find($id);
            $imageName = '';
            //upload image
            if ($request->hasfile('image')) {
                //delete image from folder
                if (!empty($country->image)) {
                    $web_image_path = "/uploads/country/" . $country->image;
                    $web_image_paththumb = "/uploads/country/thumb/" . $country->image;
                    if (File::exists(public_path($web_image_path))) {
                        File::delete(public_path($web_image_path));
                        File::delete(public_path($web_image_paththumb));
                    }
                }
                //
                $imageName = 'c-' . md5(time()) . '.' . $request->image->getClientOriginalExtension();

                $request->image->move(public_path('uploads/country'), $imageName);
                //create thumb
                // open file a image resource
                $imgbig = Image::make(public_path('uploads/country/' . $imageName));
                //resize image
                $imgbig->resize($image_big_w, $image_big_h); //Fixed w,h

                //if($settingInfo->is_watermark==1 && !empty($settingInfo->watermark_img)){
                // insert watermark at bottom-right corner with 10px offset
                //$imgbig->insert(public_path('uploads/logo/'.$settingInfo->watermark_img), 'bottom-right', 10, 10);
                //}
                // save to imgbig thumb
                $imgbig->save(public_path('uploads/country/' . $imageName));

                //create thumb
                // open file a image resource
                $img = Image::make(public_path('uploads/country/' . $imageName));
                //resize image
                $img->resize($image_thumb_w, $image_thumb_h); //Fixed w,h
                // save to thumb
                $img->save(public_path('uploads/country/thumb/' . $imageName));
            } else {
                $imageName = $country->image;
            }
            $country->name_en = $request->input('name_en');
            $country->name_ar = $request->input('name_ar');
            $country->zone_id = $request->input('zone');
            $country->code = strtolower($request->input('code'));
            $country->currency = strtoupper($request->input('currency'));
            $country->delivery_fee = $request->input('delivery_fee');
            $country->shipment_method = $request->input('shipment');
            $country->display_order = $request->input('display_order');
            $country->is_state = !empty($request->input('is_state')) ? $request->input('is_state') : '0';
            $country->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
            $country->image = $imageName;
            $country->save();

            $country->gateway()->delete();
            if( is_array($request->gateways))
                foreach ($request->gateways as $gateway)
                    $country->gateway()->save(new CountryGatway(['gateway' => $gateway]));

            $check = $request->apply_charge == 1 ? true : false;
            if ($check) {
                $this->applyDefaultCharge($country->id, $country->delivery_fee);
            }

            //save logs
            $key_name = "country";
            $key_id = $country->id;
            $message = "Country details are updated for " . $request->input('name_en');
            $created_by = Auth::guard('admin')->user()->id;
            Common::saveLogs($key_name, $key_id, $message, $created_by);
            //end save logs


            return redirect('/gwc/country')->with('message-success', 'Information is updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('message-error', $e->getMessage());
        }
    }

    public function applyDefaultCharge($countryId, $defaultFee)
    {
        $states = State::where('parent_id', $countryId)->get();
        foreach ($states as $state) {
            $state->delivery_fee = $defaultFee;
            $state->save();
            $areas = Area::where('parent_id', $state->id)->get();
            foreach ($areas as $area) {
                $area->delivery_fee = $defaultFee;
                $area->save();
            }
        }
    }



    /**
     * Delete the Image.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function deleteImage($id)
    {
        $country = Country::find($id);
        //delete image from folder
        if (!empty($country->image)) {
            $web_image_path = "/uploads/country/" . $country->image;
            $web_image_paththumb = "/uploads/country/thumb/" . $country->image;
            if (File::exists(public_path($web_image_path))) {
                File::delete(public_path($web_image_path));
                File::delete(public_path($web_image_paththumb));
            }
        }

        //save logs
        $key_name = "country";
        $key_id = $country->id;
        $message = "Country image is removed for " . $country->name_en;
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        $country->image = '';
        $country->save();
        return redirect()->back()->with('message-success', 'Image is deleted successfully');
    }

    /**
     * Delete country along with childs via ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check param ID
        if (empty($id)) {
            return redirect('/gwc/country')->with('message-error', 'Param ID is missing');
        }
        //get cat info
        $country = Country::find($id);
        //check cat id exist or not
        if (empty($country->id)) {
            return redirect('/gwc/country')->with('message-error', 'No record found');
        }

        //delete parent cat mage
        if (!empty($country->image)) {
            $web_image_path = "/uploads/country/" . $country->image;
            $web_image_paththumb = "/uploads/country/thumb/" . $country->image;
            if (File::exists(public_path($web_image_path))) {
                File::delete(public_path($web_image_path));
                File::delete(public_path($web_image_paththumb));
            }
        }
        //save logs
        $key_name = "country";
        $key_id = $country->id;
        $message = "A country record is removed for " . $country->name_en;
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs
        //end deleting parent cat image
        //delete child
        $this->deletesChild($country->id);
        $country->delete();
        return redirect()->back()->with('message-success', 'country is deleted successfully');
    }

    //delete states & areas
    public function deletesChild($parent_id)
    {
        $getChilds = Country::where("parent_id", $parent_id)->get();
        if (!empty($getChilds) && count($getChilds) > 0) {
            foreach ($getChilds as $getChild) {
                $this->deletesChild($getChild->id);
                Country::find($getChild->id)->delete();
            }
        }
    }


    //update status
    public function updateStatusAjax(Request $request)
    {
        $recDetails = Country::where('id', $request->id)->first();
        if ($recDetails['is_active'] == 1) {
            $active = 0;
        } else {
            $active = 1;
        }
        //save logs
        $key_name = "country";
        $key_id = $recDetails->id;
        $message = "Country status is changed to " . $active . " for " . $recDetails->name_en;
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        $recDetails->is_active = $active;
        $recDetails->save();
        return ['status' => 200, 'message' => 'Status is modified successfully'];
    }

    //get ajax states
    public function getStateAjax($id)
    {
        $states = Country::where('parent_id', $id)->where('is_active', 1)->get();
        return Response::json($states);
    }

    public static function countCountryStateArea($parentid)
    {
        return Country::where('parent_id', $parentid)->get()->count();
    }
}
