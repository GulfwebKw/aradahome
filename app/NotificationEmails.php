<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class NotificationEmails extends Model
{
    use Notifiable;
	
	
	public $table = "gwc_notification_emails";
	
	
}
