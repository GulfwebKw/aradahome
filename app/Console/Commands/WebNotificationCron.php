<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Product;
use App\ProductAttribute;
use App\ProductOptions;
use App\Settings;
use Common;
//email
use App\Mail\SendGrid;
use Mail;

class WebNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'QuantityAlert:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $settingInfo = Settings::where("keyname","setting")->first();
		$toemail     = $settingInfo->email;
		if(!empty($toemail)){
		\Log::info("Cron: Min Quantity Email Notification");
		$isEmail=0;
		$emailBody='<table width="100%" border="0">';
		$products = Product::where('is_attribute',0)->where('quantity','<=',0)->where('is_alert_min_qty',0)->get();
		if(!empty($products) && count($products)>0){
			foreach($products as $key=>$product){
			  $emailBody.='<tr>'.$key.'<td>'.$product->item_code.'</td><td>'.$product->title_en.'</td></tr>';
			  $strRec=Product::find($product->id);
			  $strRec->is_alert_min_qty=1;
			  $strRec->save();
			  \Log::info($product->item_code);
			}
		   $isEmail+=1;	
		   
		}
		$emailBody.='</table>';
		//
		if(!empty($isEmail)){
		 $data = [
		 'dear'            => "Dear Admin",
		 'footer'          => trans('webMessage.email_footer'),
		 'message'         => "Below is the item logs which has no quantity <br> ".$emailBody,
		 'subject'         => "Quantity Alert Notification",
		 'email_from'      => $settingInfo->from_email,
		 'email_from_name' => $settingInfo->from_name
		 ];
		 Mail::to($toemail)->send(new SendGrid($data));
		 }
		\Log::info("Cron: Temp order logs removed");
		}
    }
	
}
