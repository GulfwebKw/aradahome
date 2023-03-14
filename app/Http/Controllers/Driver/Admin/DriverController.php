<?php

namespace App\Http\Controllers\Driver\Admin;

use App\Driver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $drivers = Driver::orderByDesc('created_at')
            ->when($request->q != "",function ($query) use($request){
                $query->where('id' , (int) filter_var($request->q, FILTER_SANITIZE_NUMBER_INT))
                    ->orWhere('first_name_en' ,'Like' , '%'.$request->q.'%' )
                    ->orWhere('first_name_ar' ,'Like' , '%'.$request->q.'%' )
                    ->orWhere('last_name_en' ,'Like' , '%'.$request->q.'%' )
                    ->orWhere('last_name_ar' ,'Like' , '%'.$request->q.'%' )
                    ->orWhere('username' ,'Like' , '%'.$request->q.'%' )
                    ->orWhere('phone' ,'Like' , '%'.$request->q.'%' );
            })
            ->paginate();
        return view('driver.driver.index' , compact('drivers' ));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $driver = new Driver();
        return view('driver.driver.edit' , compact('driver' ));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request , [
			'first_name_en'   => 'required|string|unique:gwc_brands,title_en',
			'first_name_ar'     => 'required|string|unique:gwc_brands,title_ar',
			'last_name_en'   => 'required|string|unique:gwc_brands,title_en',
			'last_name_ar'     => 'required|string|unique:gwc_brands,title_ar',
			'phone'   => 'nullable|numeric',
			'details_ar'   => 'nullable|string',
			'username'     => 'required|string|min:4|unique:gwc_drivers,username',
			'avatar'      => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'      => 'required|confirmed|min:6',
        ]);

        try {
            $data = $request->toArray() ;
            //upload image
            if ($request->hasfile('profile_avatar')) {
                $imageName = 'driver-' . md5(time()) . '.' . $request->profile_avatar->getClientOriginalExtension();
                $request->profile_avatar->move(public_path('uploads/users'), $imageName);
                $data['avatar'] = $imageName;
            }
            $data['is_active'] = $request->has('is_active');
            $data['password'] = bcrypt($request->password);
            $driver = Driver::create($data);
            if ($driver->exists)
                return redirect()->route('driver.admin.driver.index')->with('message-success', 'Driver add successfully.');
        } catch (\Exception $exception){}
        return redirect()->back()->withInput()->withErrors('Unknown Error occurred! Please try again!');
    }

    /**
     * Display the specified resource.
     *
     * @param Driver $driver
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Driver $driver
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Driver $driver)
    {
        return view('driver.driver.edit' , compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Driver $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Driver $driver)
    {
        $this->validate($request , [
            'first_name_en'   => 'required|string|unique:gwc_brands,title_en',
            'first_name_ar'     => 'required|string|unique:gwc_brands,title_ar',
            'last_name_en'   => 'required|string|unique:gwc_brands,title_en',
            'last_name_ar'     => 'required|string|unique:gwc_brands,title_ar',
            'phone'   => 'nullable|numeric',
            'details_ar'   => 'nullable|string',
            'username'     => 'required|string|min:4|unique:gwc_drivers,username,'.$driver->id,
            'profile_avatar'      => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'      => 'nullable|confirmed|min:6',
        ]);

        try {

            $driver->fill($request->toArray());
            //upload image
            if ($request->hasfile('profile_avatar')) {
                if ( file_exists(public_path('uploads/users/'.$driver->profile_avatar)) and $driver->profile_avatar != "")
                    unlink(public_path('uploads/users/'.$driver->profile_avatar));
                $imageName = 'driver-' . md5(time()) . '.' . $request->profile_avatar->getClientOriginalExtension();
                $request->profile_avatar->move(public_path('uploads/users'), $imageName);
                $driver->avatar = $imageName;
            }
            $driver->is_active = $request->has('is_active');
            if ($request->has('password') and $request->password != null )
                $driver->password =  bcrypt($request->password) ;
            $driver->saveOrFail();
            return redirect()->route('driver.admin.driver.index')->with('message-success', 'Driver Updated successfully.');
        } catch (\Exception $exception){}
        return redirect()->back()->withInput()->withErrors('Unknown Error occurred! Please try again!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Driver $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('driver.admin.driver.index')->with('message-success', 'Driver Deleted successfully.');

    }

    public function print(Request $request, $lang = "en" , $driverId = null){
        $drivers = Driver::when($driverId != null , function ($query) use ($driverId) {
            $query->where('id' , $driverId);
        })->orderByDesc('created_at')->paginate(4);
        $allDriver = $request->has('allDriver');
        return view('driver.driver.print' , compact('drivers' , 'lang' , 'allDriver'));
    }
}
