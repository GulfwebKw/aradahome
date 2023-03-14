<?php

namespace App\Http\Controllers;

use App\Zone;
use Illuminate\Http\Request;
use App\Settings;
use Image;
use File;
use Response;
use Auth;

class AdminZonesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        $zones = Zone::orderBy('display_order', $settingInfo->default_sort)->paginate($settingInfo->item_per_page_back);
        return view('gwc.zones.index', ['zones' => $zones]);
    }


    /**
     * Display the clients listings
     **/
    public function create()
    {
        $lastOrderInfo = Zone::OrderBy('display_order', 'desc')->first();
        if (!empty($lastOrderInfo->display_order)) {
            $lastOrder = ($lastOrderInfo->display_order + 1);
        } else {
            $lastOrder = 1;
        }
        return view('gwc.zones.create')->with(['lastOrder' => $lastOrder]);
    }


    /**
     * Store New clients Details
     **/
    public function store(Request $request)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();

        //field validation
        $this->validate($request, [
            'title_en' => 'required|min:3|max:190|string',
            'title_ar' => 'required|min:3|max:190|string',
            'display_order' => 'required|numeric|unique:gwc_zones,display_order',
        ]);

        $zone = new Zone();
        $zone->title_en = $request->input('title_en');
        $zone->title_ar = $request->input('title_ar');
        $zone->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
        $zone->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
        $zone->save();

        //save logs
        $key_name = "zones";
        $key_id = $zone->id;
        $message = "A new record for zones is added. (" . $zone->title_en . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/zones')->with('message-success', 'A record is added successfully');
    }

    public function show()
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $zone = Zone::find($id);
        return view('gwc.zones.edit', compact('zone'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $settingInfo = Settings::where("keyname", "setting")->first();

        //field validation
        $this->validate($request, [
            'title_en' => 'required|min:3|max:190|string',
            'title_ar' => 'required|min:3|max:190|string',
            'display_order' => 'required|numeric|unique:gwc_zones,display_order,' . $id,
        ]);

        $zone = Zone::find($id);
        $zone->title_en = $request->input('title_en');
        $zone->title_ar = $request->input('title_ar');
        $zone->is_active = !empty($request->input('is_active')) ? $request->input('is_active') : '0';
        $zone->display_order = !empty($request->input('display_order')) ? $request->input('display_order') : '0';
        $zone->save();


        //save logs
        $key_name = "zones";
        $key_id = $zone->id;
        $message = "Record for zones is edited. (" . $zone->title_en . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/zones')->with('message-success', 'Information is updated successfully');
    }


    /**
     * Delete clients along with childs via ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        //check param ID
        if (empty($id)) {
            return redirect('/gwc/zones')->with('message-error', 'Param ID is missing');
        }

        $zone = Zone::find($id);
        if (empty($zone->id)) {
            return redirect('/gwc/zones')->with('message-error', 'No record found');
        }

        //save logs
        $key_name = "zones";
        $key_id = $zone->id;
        $message = "A record is removed. (" . $zone->title_en . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        $zone->delete();
        return redirect()->back()->with('message-success', 'clients is deleted successfully');
    }


    //update status
    public function updateStatusAjax(Request $request)
    {
        $recDetails = Zone::where('id', $request->id)->first();
        if ($recDetails['is_active'] == 1) {
            $active = 0;
        } else {
            $active = 1;
        }

        //save logs
        $key_name = "zones";
        $key_id = $recDetails->id;
        $message = "zone status is changed to " . $active . " (" . $recDetails->title_en . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        $recDetails->is_active = $active;
        $recDetails->save();
        return ['status' => 200, 'message' => 'Status is modified successfully'];
    }

}
