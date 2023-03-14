<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Settings;//model

class SendErrorApiUpdateProductQuantity extends Mailable
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
		$email_from      = $this->data['email_from'];
		$email_from_name = $this->data['email_from_name'];
        $subject         = $this->data['subject'];
		$bodytxt         = $this->data['bodytxt'];

		$emailTemplate = "emails.automaticOrderQuantityUpdate";

        return $this->view($emailTemplate)
                    ->from($email_from,$email_from_name)
                    ->subject($subject)
                    ->with(['bodytxt'=>$bodytxt]);
    }
}
