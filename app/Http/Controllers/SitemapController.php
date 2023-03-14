<?php

namespace App\Http\Controllers;

use App\Country;
use App\Exports\ProductExportGoogleMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use App\Product;
use App\Categories;
use App\Brand;

//email
use App\Mail\SendGrid;
use Mail;
use DB;

class SitemapController extends Controller
{


    public function index()
    {
        $staticsx[] = ['loc' => url("/")];
        $staticsx[] = ['loc' => url("en/offers")];
        $staticsx[] = ['loc' => url("ar/offers")];
        $staticsx[] = ['loc' => url("en/about-us")];
        $staticsx[] = ['loc' => url("ar/about-us")];
        $staticsx[] = ['loc' => url("en/contactus")];
        $staticsx[] = ['loc' => url("ar/contactus")];
        $staticsx[] = ['loc' => url("en/login")];
        $staticsx[] = ['loc' => url("ar/login")];
        $staticsx[] = ['loc' => url("en/register")];
        $staticsx[] = ['loc' => url("ar/register")];
        $staticsx[] = ['loc' => url("en/faq")];
        $staticsx[] = ['loc' => url("ar/faq")];
        $brands = Brand::where('is_active', 1)->orderBy('id', 'DESC')->get();
        $products = Product::where('is_active', 1)->orderBy('id', 'DESC')->get();
        $categories = Categories::where('is_active', 1)->where('parent_id', 0)->orderBy('id', 'DESC')->get();
        return response()->view('website.sitemap.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'staticsx' => $staticsx
        ])->header('Content-Type', 'text/xml');
    }

    public function googleFeed(Request $request){
        $countryCode = $request->route('countrySubDomainCode');
        $locale = $request->route('locale');
        $locale = trim(strtolower($locale));
        $locale = ($locale == 'en' or $locale == 'ar') ? $locale : 'en' ;
        $country = Country::where('code', $countryCode)
            ->where('is_active', 1)
            ->where('parent_id', 0)->first();
        if ( $country == null )
            $country = new Country();
        config(['excel.exports.csv.delimiter' => "~~~~" , 'excel.exports.csv.enclosure' => "" , 'excel.exports.csv.line_ending' => '||||'  ]);
        $csv =  Excel::raw(new ProductExportGoogleMerchant($country,$locale,$request->inventory) , \Maatwebsite\Excel\Excel::CSV);

        $lines = explode('||||', $csv);
        array_pop($lines);
        $keys = explode(config('excel.exports.csv.delimiter') , $lines[0]);
        array_shift($lines);
        return response()->view('website.sitemap.google', compact('lines', 'keys', 'country'))->header('Content-Type', 'text/xml');

        // $array = array();
        // foreach ($lines as $i => $line) {
        //     $values = explode(config('excel.exports.csv.delimiter') , $line);
        //     foreach ($values as $index => $value)
        //         $array[$i][$ids[$index]] = $value;
        // }
        // print $this->to_xml($array);

        //return Excel::download(new ProductExportGoogleMerchant($country,$locale,$request->inventory), 'product_google_'.$locale.($country->exists ? '_'. $country->code : '') .'.xlsx');
    }
}
