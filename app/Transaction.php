<?php

namespace App;

use App\Classes\Payment\CBKPay;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $table = "gwc_transaction";

    protected function getMfTrackIdAttribute($value)
    {
        // dd($value);
        $val = $value;
        // if (!@$val && !empty(parent::getAttribute('pay_mode')) && parent::getAttribute('pay_mode') == 'KNET' && !empty(parent::getAttribute('trackid'))) {
        //     $cbkPay = new CBKPay();
        //     $paymentDetails = $cbkPay->getPaymentStatusDetailsAlt(parent::getAttribute('trackid'));
        //     dd($paymentDetails);
        //     // $transaction = Transaction::find(parent::getAttribute('trackid'));
        //     // $transaction->MfTrackId = $paymentDetails->trackid;
        //     // $transaction->save();
        //     parent::setAttribute('MfTrackId',$paymentDetails->trackid );
        //     $val = $paymentDetails->MfTrackId;
        // }
        return $val;
    }

    public function orderDetails(){
        return $this->hasOne(OrdersDetails::class, 'order_id', 'trackid');
    }
}
