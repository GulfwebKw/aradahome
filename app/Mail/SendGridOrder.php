<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Settings;//model

class SendGridOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
         $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $settingInfo = Settings::where("keyname","setting")->first();
		
		$email_from      = $this->data['email_from'];
		$email_from_name = $this->data['email_from_name'];
        $subject         = $this->data['subject'];
        $customerDetails = $this->data['customerDetails'];
		$invoiceDetails  = $this->data['invoiceDetails'];
		$orderDetails    = $this->data['orderDetails'];
		$paymentDetails  = $this->data['paymentDetails'];
		$trackYourOrder  = $this->data['trackYourOrder'];
		$deartxt         = $this->data['deartxt'];
		$bodytxt         = $this->data['bodytxt'];
		
		
		if(!empty($settingInfo->invoice_template) && $settingInfo->invoice_template==1){
		$emailTemplate = "emails.template_order";
		}else if(!empty($settingInfo->invoice_template) && $settingInfo->invoice_template==2){
		$emailTemplate = "emails.template_order_2";
		}else{
		$emailTemplate = "emails.template_order";
		}
		
        return $this->view($emailTemplate)
                    ->from($email_from,$email_from_name)
                    ->subject($subject)
                    ->with(['customerDetails'=>$customerDetails,'invoiceDetails'=>$invoiceDetails,'orderDetails'=>$orderDetails,'paymentDetails'=>$paymentDetails,'trackYourOrder'=>$trackYourOrder,'deartxt'=>$deartxt,'bodytxt'=>$bodytxt]);
    }
}
