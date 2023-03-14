<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    function detailsSlug($countrySubDomainCode,$id, $slug) {
        return redirect()->route('detailsIdSlug', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function search(Request $request,$countrySubDomainCode) {
        return redirect()->route('web.search', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'sq' => $request->sq ?? ""]);
    }
    function directDetailsSlug($countrySubDomainCode,$id, $slug) {
        return redirect()->route('directdetailsIdSlug', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function directDetails($countrySubDomainCode,$id, $slug) {
        return redirect()->route('directdetailsIdSlug', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function details($countrySubDomainCode,$id) {
        return redirect()->route('detailsId', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'id' => $id]);
    }
    function detailsNoLocale($id, $slug) {
        return redirect()->route('detailsIdSlug', ['locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function searchNoLocale(\Illuminate\Http\Request $request) {
        return redirect()->route('web.search', ['locale' => app()->getLocale() , 'sq' => $request->sq ?? ""]);
    }
    function directDetailsNoLocale($id, $slug) {
        return redirect()->route('directdetailsIdSlug', ['locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function directDetailsNoLocaleAlt($id, $slug) {
        return redirect()->route('directdetailsIdSlug', ['locale' => app()->getLocale(), 'id' => $id, 'slug' => $slug]);
    }
    function detailsNoLocaleNoSlug($id) {
        return redirect()->route('detailsId', ['locale' => app()->getLocale(), 'id' => $id]);
    }
    function productsNoLocale($catid, $slug) {
        return redirect()->route('productsCatidSlug', ['locale' => app()->getLocale(), 'catid' => $catid, 'slug' => $slug]);
    }
    function products($countrySubDomainCode,$catid, $slug) {
        return redirect()->route('productsCatidSlug', ['countrySubDomainCode' => $countrySubDomainCode,'locale' => app()->getLocale(), 'catid' => $catid, 'slug' => $slug]);
    }
    function home() {
        return redirect(app()->getLocale());
    }
}
