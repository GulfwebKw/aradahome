<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\SmsNotify;
use Common;
class SmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:cron';

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
        \Log::info("Cron: SMS is started!");
		$strSms = SmsNotify::where('id','!=',0)->first();
		if(!empty($strSms->id)){
		Common::SendSms($strSms->send_to,$strSms->send_msg,1);	
		$strSms->delete();	
		}
		\Log::info("Cron: SMS is finished!");
    }
}
