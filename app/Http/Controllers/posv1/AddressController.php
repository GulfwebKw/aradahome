<?php

namespace App\Http\Controllers\posv1;

use App\Area;
use App\Country;
use App\CustomersAddress;
use App\Http\Controllers\Controller;
use App\State;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(User $user ){
        $addressDetails = CustomersAddress::where('customer_id',$user->id)->get();
        if(!empty($addressDetails) && count($addressDetails)>0){
            $add=[];$subadd=[];$countryName='';$stateName='';$areaName='';
            foreach($addressDetails as $addressDetail){
                $add['id']=$addressDetail->id;
                $add['country_id']=$addressDetail->country_id;
                $add['state_id']=$addressDetail->state_id;
                $add['area_id']=$addressDetail->area_id;
                //country
                $countryDetails = Country::where('is_active',1)->where('id',$addressDetail->country_id)->first();
                if($countryDetails->id){
                    $countryName = $countryDetails->name_en;
                }
                //state
                $stateDetails = State::where('id',$addressDetail->state_id)->first();
                if($stateDetails->id){
                    $stateName = $stateDetails->name_en;
                }
                //area
                $areaDetails = Area::where('id',$addressDetail->area_id)->first();
                if($areaDetails->id){
                    $areaName = $areaDetails->name_en;
                }

                $add['country_name'] = $countryName;
                $add['state_name']   = $stateName;
                $add['area_name']    = $areaName;
                $add['block']        = !empty($addressDetail->block)?$addressDetail->block:'';
                $add['street']       = !empty($addressDetail->street)?$addressDetail->street:'';
                $add['avenue']       = !empty($addressDetail->avenue)?$addressDetail->avenue:'';
                $add['house']        = !empty($addressDetail->house)?$addressDetail->house:'';
                $add['floor']        = !empty($addressDetail->floor)?$addressDetail->floor:'';
                $add['title']        = !empty($addressDetail->title)?$addressDetail->title:'My Address';
                $add['is_default']   =  !empty($addressDetail->is_default)?$addressDetail->is_default:'0';
                $add['landmark']     =  !empty($addressDetail->landmark)?$addressDetail->landmark:'';
                $add['latitude']     =  !empty($addressDetail->latitude)?$addressDetail->latitude:'';
                $add['longitude']    =  !empty($addressDetail->longitude)?$addressDetail->longitude:'';

                $subadd[]=$add;
            }
            return response()->json(['data' => $subadd]);
        }else{
            return response()->json(['data' => null]);
        }
    }

    public function store(Request $request , User $user ){
        $validator = Validator::make($request->all(), [
            'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
            'state'        => 'required|numeric|gt:0',
            'area'         => 'required|numeric|gt:0',
        ],[
            'title.required'=>trans('webMessage.title_required'),
            'country.required'=>trans('webMessage.country_required'),
            'state.required'=>trans('webMessage.state_required'),
            'area.required'=>trans('webMessage.area_required'),
            'country.gt'=>trans('webMessage.country_required'),
            'state.gt'=>trans('webMessage.state_required'),
            'area.gt'=>trans('webMessage.area_required'),
        ]);

        if($validator->fails()){
            return response()->json(['data' => $validator->errors()->all()], 400);
        }

        $address = new CustomersAddress;
        $address->customer_id=$user->id;
        $address->title=$request->input('title');
        $address->country_id=$request->input('country');
        $address->state_id=$request->input('state');
        $address->area_id=$request->input('area');
        $address->block=$request->input('block');
        $address->street=$request->input('street');
        $address->avenue=$request->input('avenue');
        $address->house=$request->input('house');
        $address->floor=$request->input('floor');
        $address->landmark=$request->input('landmark');
        $address->latitude=$request->input('latitude');
        $address->longitude=$request->input('longitude');
        $address->is_default=!empty($request->input('is_default'))?$request->input('is_default'):'0';
        $address->save();
        //save other 0
        self::changeDefaultOther($user->id,$address->id);

        return response()->json(['data' => $address]);
    }

    public function update(Request $request , User $user , CustomersAddress $CustomersAddress ){
        $validator = Validator::make($request->all(), [
            'title'        => 'required|min:3|max:150|string',
            'country'      => 'required|numeric|gt:0',
            'state'        => 'required|numeric|gt:0',
            'area'         => 'required|numeric|gt:0',
        ],[
            'title.required'=>trans('webMessage.title_required'),
            'country.required'=>trans('webMessage.country_required'),
            'state.required'=>trans('webMessage.state_required'),
            'area.required'=>trans('webMessage.area_required'),
            'country.gt'=>trans('webMessage.country_required'),
            'state.gt'=>trans('webMessage.state_required'),
            'area.gt'=>trans('webMessage.area_required'),
        ]);

        if($validator->fails()){
            return response()->json(['data' => $validator->errors()->all()], 400);
        }

        $address = $CustomersAddress;
        if( $address->customer_id != $user->id ){
            return response()->json(['data' => trans('webMessage.invalid_infornation')], 400);
        }
        $address->title=$request->input('title');
        $address->country_id=$request->input('country');
        $address->state_id=$request->input('state');
        $address->area_id=$request->input('area');
        $address->block=$request->input('block');
        $address->street=$request->input('street');
        $address->avenue=$request->input('avenue');
        $address->house=$request->input('house');
        $address->floor=$request->input('floor');
        $address->landmark=$request->input('landmark');
        $address->latitude=$request->input('latitude');
        $address->longitude=$request->input('longitude');
        $address->is_default=!empty($request->input('is_default'))?$request->input('is_default'):'0';
        $address->save();
        //save other 0
        self::changeDefaultOther($user->id,$address->id);

        return response()->json(['data' => $address]);
    }

    public function delete(Request $request , User $user , CustomersAddress $CustomersAddress ){
        if(empty($CustomersAddress->id)){
            return response()->json(['data' => trans('webMessage.invalid_infornation')], 400);
        }if($CustomersAddress->customer_id != $user->id ){
            return response()->json(['data' => trans('webMessage.invalid_infornation')], 400);
        }
        $CustomersAddress->delete();
        return response()->json(['data' =>trans('webMessage.address_removed_success')]);
    }

    public static function changeDefaultOther($customerid,$defaultid){
        $address = CustomersAddress::where('customer_id',$customerid)->where('id','!=',$defaultid)->get();
        if(!empty($address) && count($address)){
            foreach($address as $addres){
                $newAddres = CustomersAddress::find($addres->id);
                $newAddres->is_default=0;
                $newAddres->save();
            }
        }
    }
}
