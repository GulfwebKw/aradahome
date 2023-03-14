<?php

namespace App\Http\Controllers\posv1;

use App\CashLog;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Settings;
use App\AdminPos; //model
use App\Mail\SendGrid;
use Mail;
use Common;

//rules
use App\Rules\Name;
use App\Rules\Mobile;


class PosCashController extends Controller
{

    public function startShift(Request $request){
        $validator = Validator::make($request->all(), [
            'startCash' => 'nullable|numeric|between:0,99999999.99',
        ]);
        if ($validator->fails()) {
            $response['data'] = $validator->errors()->all();
            return response($response, 401);
        }
        if ( ! $request->has('startCash') or $request->startCash == null )
            $request->merge(['startCash' => 0 ]);
        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = $request->user();
            /** @var WorkTime $workTime */
            $workTime = $user->workTimes()->getModel();
            $workTime->pos_id = $user->id;
            if ($workTime->hasOpenShift()) {
                $response['data'] = 'First close last shift!';
                return response($response, 400);
            }
            $workTime->start = Carbon::now();
            $workTime->startCash = $request->has('startCash') ? $request->startCash : 0;
            $workTime->saveOrFail();
            if ($request->has('startCash') and $request->startCash > 0) {
                $workTime->cashs()->create([
                    'pos_id' => $workTime->pos_id,
                    'amount' => $request->startCash,
                    'type' => "in",
                    'description' => "Start Cash for ".Carbon::now()->format('Y-m-d H:i:s'),
                    'refrence_id' => $workTime->id,
                    'refrence_type' => WorkTime::class,
                    'beforeCash' => 0,
                    'afterCash' => $request->startCash,
                ]);
            }
            DB::commit();
            $response['data'] = 'Shift started.';
            return response($response, 200);
        } catch (\Exception $exception){
            DB::rollBack();
            $response['data'] = $exception->getMessage();
            return response($response, 500);
        }
    }

    public function endShift(Request $request){
        $validator = Validator::make($request->all(), [
            'countCash' => 'required|numeric|between:0,99999999.99',
            'countCard' => 'required|numeric|between:0,99999999.99',
        ]);
        if ($validator->fails()) {
            $response['data'] = $validator->errors()->all();
            return response($response, 401);
        }
        $user = $request->user();
        /** @var WorkTime $workTime */
        $workTime = $user->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, 400);
        }
        $lastCash = $workTime->lastCash();
        if ( $lastCash == null ){
            $lastCash = new CashLog();
            $lastCash->afterCash = 0 ;
            $lastCash->countCash = 0 ;
            $lastCash->countCard = 0 ;
        }
        $workTime->ended = Carbon::now();
        $workTime->endCash = $lastCash->afterCash;
        $workTime->countCash = $request->countCash;
        $workTime->countCard = $request->countCard;
        $workTime->contradictionCountCash = $request->countCash - $lastCash->afterCash  ;
        $workTime->contradictionCountCard = $request->countCard - ( $workTime->cardPay - $workTime->cardRefund )  ;
        $workTime->contradictionCashOfSystem = $lastCash->afterCash - ($workTime->cashPay + $workTime->startCash );
        $workTime->save();
        return response(['data' => $workTime], 200);
    }

    public function shiftDetails(Request $request){
        $user = $request->user();
        /** @var WorkTime $workTime */
        $workTime = $user->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, 400);
        }
        $lastCash = $workTime->lastCash();
        if ( $lastCash == null ){
            $lastCash = new CashLog();
            $lastCash->afterCash = 0 ;
        }
//        $response['data']  = [
//            'existCash' => $lastCash->afterCash,
//            'shouldExistCash' => $workTime->cashPay + $workTime->startCash - $workTime->cashRefund ,
//            'startCash' => $workTime->startCash,
//            'sellCash' => $workTime->cashPay,
//            'sellCard' => $workTime->cardPay,
//            'totalSell' => $workTime->totalSell,
//            'refundCash' => $workTime->cashRefund,
//            'refundCard' => $workTime->cardRefund,
//            'totalRefund' => $workTime->totalRefund,
//            'total' => $workTime->totalSell - $workTime->totalRefund,
//        ];
        $response['data']  = [
            'startCash' => $workTime->startCash,
            'sellCash' => $workTime->cashPay,
            'sellCard' => $workTime->cardPay,
            'cashIn' => $lastCash->afterCash - $workTime->startCash - $workTime->cashPay + $workTime->cashRefund,
            'totalSell' => $workTime->totalSell,
            'totalFunds' => $lastCash->afterCash + $workTime->cardPay - $workTime->cardRefund,
            'totalRefund' => $workTime->totalRefund,
            'refundCash' => $workTime->cashRefund,
            'refundCard' => $workTime->cardRefund,
            'totalCashFunds' => $lastCash->afterCash,
            'startShift' => $workTime->getOriginal('start'),
            'now' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        $response['data']['history'] =  $workTime->cashs()
            ->when($request->has('type') , function ($query) use ($request){
                $query->where('type' , $request->type );
            })
            ->when($request->has('description') , function ($query) use ($request){
                $query->where('description' , 'like' , '%'.$request->description.'%' );
            })
            ->orderByDesc('updated_at')
            ->where(function ($query){
                $query->where('description' , 'not like' , 'Receive cash for order%' )
                    ->where('description' , 'not like' , 'Refund cash for order%' );
            })
            ->with('pos')
            ->get();
        return response($response, 200);
    }

    public function cashHistory(Request $request){

        $user = $request->user();
        /** @var WorkTime $workTime */
        $workTime = $user->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, 400);
        }
        $response['data'] =  $workTime->cashs()
            ->when($request->has('type') , function ($query) use ($request){
                $query->where('type' , $request->type );
            })
            ->when($request->has('description') , function ($query) use ($request){
                $query->where('description' , 'like' , '%'.$request->description.'%' );
            })
            ->with('pos')
            ->orderByDesc('updated_at')
            ->get();
        return response($response, 200);
    }

    public function cashChange(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|between:0,99999999.99',
            'type' => 'required|in:in,out',
        ]);
        if ($validator->fails()) {
            $response['data'] = $validator->errors()->all();
            return response($response, 401);
        }
        $user = $request->user();
        /** @var WorkTime $workTime */
        $workTime = $user->openWorkTime();
        if ( $workTime == null ){
            $response['data'] = 'Please start shift!';
            return response($response, 400);
        }
        $lastCash = $workTime->lastCash();
        $changeCount =  $request->type == "in" ? $request->amount : $request->amount * -1 ;
        $response['data']  = $workTime->cashs()->create([
            'pos_id' => $workTime->pos_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description ?? "Manually exchange.",
            'beforeCash' => $lastCash ? $lastCash->afterCash : 0,
            'afterCash' => $lastCash ? $lastCash->afterCash + $changeCount : $changeCount,
        ]);
        return response($response, 200);
    }
}
