<?php

namespace App\Http\Controllers;

use App\Country;
use App\Exports\ProductExportGoogleMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Admin;//model
use App\Menus;//model
use App\Customers; //model
use App\Contactus; //model
use App\Settings; //model
use App\Categories; //model
use App\Product; //model
use App\Brand; //model
use App\AdminLogs; //model
use App\OrdersDetails; //model
use App\Orders;
use App\Transaction;
use App\Exports\ProductExport;
use App\Exports\ProductExportFacebook;
use App\Exports\ProductExportHuawei;
use App\Imports\ProductImport;
use DB;
use Common;
use Carbon;
//gapi
use App\Gapi\Gapi;


class AdminExportController  extends Controller
{
    
   //view export/import page		
	public function ViewExportImportForm(){
	 return view('gwc.setting.adminExportImportForm');
	}
	
  
    /** Export Product
    * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export_product(Request $request) 
    {
        
		return Excel::download(new ProductExport($request->brand), 'product.xlsx');
    }
	
	/** Export Product
    * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export_product_facebook(Request $request,$lang,Country $country)
    {
        $lang = $lang??'en';
        return Excel::download(new ProductExportFacebook($country,$lang,$request->inventory), 'product_facebook_'.$lang.($country->exists ? '_'. $country->code : '') .'.xlsx');
    }
	
	public function export_product_google(Request $request,$lang,Country $country)
    {
	    $lang = $lang??'en';
        return Excel::download(new ProductExportGoogleMerchant($country,$lang,$request->inventory), 'product_google_'.$lang.($country->exists ? '_'. $country->code : '') .'.xlsx');
    }

    public function export_product_huawei(Request $request,$lang,Country $country)
    {
        $lang = $lang??'en';
        return Excel::download(new ProductExportHuawei($country,$lang,$request->inventory), 'product_huawei_'.$lang.($country->exists ? '_'. $country->code : '') .'.xlsx');
    }
	
	/** Import Product
    * @return \Illuminate\Http\RedirectResponse
     */
    public function import_product() 
    {
        Excel::import(new ProductImport,request()->file('file_product'));
        return back()->with('message-success','Product data is imported successfully');
    }	
}
