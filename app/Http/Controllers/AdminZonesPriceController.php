<?php

namespace App\Http\Controllers;

use App\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminZonesPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Zone $zone)
    {
        return view('gwc.zonePrice.index' , compact('zone'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create(Zone $zone)
    {
        return view('gwc.zonePrice.create' , compact('zone'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request,Zone $zone)
    {
        $this->validate($request , [
            'from' => 'required|numeric|min:0',
            'to' => 'required|numeric|min:0|gt:from',
            'price' => 'required|numeric|min:0',
        ]);

        $zone->prices()->create($request->all());

        //save logs
        $key_name   = "Zone";
        $key_id     = $zone->id;
        $message    = "New Price record is added for ".$zone->title_en.". (" . $request->from .' - ' .  $request->to .' : ' . $request->price . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/zones/'.$zone->id.'/price')->with('message-success', 'Price is added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Zone $zone , $id)
    {
        $price = $zone->prices()->findOrFail($id);
        return view('gwc.zonePrice.edit' , compact('zone','price'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request,Zone $zone , $id)
    {
        $priceTemp = $price = $zone->prices()->findOrFail($id);
        $this->validate($request , [
            'from' => 'required|numeric|min:0',
            'to' => 'required|numeric|min:0|gt:from',
            'price' => 'required|numeric|min:0',
        ]);

        $price->update($request->all());

        //save logs
        $key_name   = "Zone";
        $key_id     = $zone->id;
        $message    = "Price Modified for ".$zone->title_en.". ( new: " . $request->from .' - ' .  $request->to .' : ' . $request->price . " | last"  . $priceTemp->from .' - ' .  $priceTemp->to .' : ' . $priceTemp->price . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/zones/'.$zone->id.'/price')->with('message-success', 'Price is Modified successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Zone $zone , $id)
    {
        $price = $zone->prices()->findOrFail($id);
        $price->delete();
        //save logs
        $key_name   = "Zone";
        $key_id     = $zone->id;
        $message    = "Price of ".$zone->title_en." is deleted. (" . $price->from .' - ' .  $price->to .' : ' . $price->price . ")";
        $created_by = Auth::guard('admin')->user()->id;
        Common::saveLogs($key_name, $key_id, $message, $created_by);
        //end save logs

        return redirect('/gwc/zones/'.$zone->id.'/price')->with('message-success', 'Price is deleted successfully');

    }
}
