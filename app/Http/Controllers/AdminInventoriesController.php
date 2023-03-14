<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\model\comment;
use App\ProductsQuantity;
use App\Settings;
use Illuminate\Http\Request;

class AdminInventoriesController extends Controller
{
    protected $settingInfo ;

    protected function setting(){
        $this->settingInfo = Settings::where("keyname", "setting")->first();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        self::setting();
        $inventories = Inventory::when(!empty($request->get('q')) , function ($query) use ($request){
            $query->where("title" , "like" , "%".$request->get('q')."%");
        })->when(!empty($request->q) , function ($query) use ($request){
            $query->where("title" , "like" , "%".$request->get('q')."%");
        })->when(!empty($request->deleted) , function ($query) use ($request){
            $query->withTrashed();
        })->orderBy('priority')->paginate($this->settingInfo->item_per_page_back);
        return view('gwc.inventories.index' , compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $maxPriority = Inventory::max('priority') + 1;
        return view('gwc.inventories.create' , compact('maxPriority') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'title' => 'required|string',
            'priority' => 'required|numeric|unique:gwc_inventories,priority',
        ]);
        Inventory::create([
            'title'=>$request->title,
            'priority'=>$request->priority,
            'description'=>$request->description,
        ]);
        return redirect('/gwc/inventories')->with('success', __('adminMessage.inventory.addSuccess'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('gwc.inventories.edit' , compact('inventory') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request , [
            'title' => 'required|string',
            'priority' => 'required|numeric|unique:gwc_inventories,priority,'.$id,
        ]);
        $inventory = Inventory::findOrFail($id);
        $inventory->update([
            'title'=>$request->title,
            'priority'=>$request->priority,
            'description'=>$request->description,
        ]);
        return redirect('/gwc/inventories')->with('success', __('adminMessage.inventory.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $inventory = Inventory::where('can_delete' , 1)->findOrFail($id);
        $inventory->delete();
        return redirect('/gwc/inventories')->with('success', __('adminMessage.inventory.deleteSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function restore($id)
    {
        $inventory = Inventory::where('can_delete' , 1)->onlyTrashed()->findOrFail($id);
        $inventory->restore();
        return redirect('/gwc/inventories')->with('success', __('adminMessage.inventory.restoreSuccess'));
    }


    /**
     * change status the resource
     */
    public function updateStatusAjax($id)
    {
        $resource = Inventory::findOrFail($id);
        $resource->toggle('is_active');
        ProductsQuantity::quantityUpdateAfterInventoryActivityChange($resource,'activity');
        return response()->json(['message' => 'Status change successfully.'] );
    }
}
