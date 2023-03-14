<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\OrdersTemp;
use App\OrdersTempOption;
use Common;
class RemoveLogsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cron';

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
        \Log::info("Cron: Temp order logs listed");
		$strTempOrders = OrdersTemp::where('created_at','<',date('Y-m-d'))->get();
		if(!empty($strTempOrders) && count($strTempOrders)>0){
			foreach($strTempOrders as $strTempOrder){
			  $strRec=OrdersTemp::find($strTempOrder->id);
			  $strRec->delete();	
			}
		}
		$strTempOrdersOptions = OrdersTempOption::where('created_at','<',date('Y-m-d'))->get();
		if(!empty($strTempOrdersOptions) && count($strTempOrdersOptions)>0){
			foreach($strTempOrdersOptions as $strTempOrdersOption){
			  $strRec=OrdersTempOption::find($strTempOrdersOption->id);
			  $strRec->delete();	
			}
		}
		\Log::info("Cron: Temp order logs removed");
    }
}
