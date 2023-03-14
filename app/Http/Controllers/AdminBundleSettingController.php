<?php

namespace App\Http\Controllers;

use App\bundleSetting;
use App\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminBundleSettingController extends Controller
{


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit()
    {
        $settingInfo = Settings::where("keyname", "setting")->first();
        $setting = new bundleSetting();
        return view('gwc.bundle.setting.edit', ['settingInfo' => $settingInfo , 'BSetting' => $setting]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'percent' => 'nullable|numeric|min:0|max:100',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'is_active' => 'nullable',
        ]);
        $setting = new bundleSetting();
        $setting->percent = $request->percent ?? 0;
        $setting->price = $request->price ?? 0;
        $setting->quantity = $request->quantity;
        $setting->is_active = $request->is_active ? true : false;
        return redirect()->route('bundle.edit')->with('message-success',__('adminMessage.bundles.updated'));
    }

}
