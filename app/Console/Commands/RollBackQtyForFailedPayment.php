<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Orders;
use App\ProductOptions;
use App\OrdersOption;
use App\OrdersDetails;
use App\Product;
use App\ProductAttribute;
use App\Settings; //model
use Common;

class RollBackQtyForFailedPayment extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'rollbackqty:cron';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Roll back quantity for failed payment';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	//aut change status after 5 days
	public function changeorderstatusauto()
	{

		$date = \Carbon\Carbon::now()->subDays(5)->format('Y-m-d');

		$setting = Settings::where("keyname", "setting")->first();
		if (!empty($setting->auto_change_order_status)) {
			\Log::info('Auto change order status start - ' . date('Y-m-d H:i:s'));
			$orderLists = OrdersDetails::whereIn('order_status', ['pending', 'outfordelivery', 'received'])
				->where('created_at', '<', $date)
				->get();
			if (!empty($orderLists) && count($orderLists) > 0) {
				foreach ($orderLists as $orderList) {
					$orderDt = OrdersDetails::find($orderList->id);
					$orderDt->order_status = 'completed';
					$orderDt->save();
					\Log::info('Auto order status changes = ' . $orderList->order_id);
				}
			}
			\Log::info('Auto change order status end - ' . date('Y-m-d H:i:s'));
		}
	}
	///
	public function rollbackknetfailedorder()
	{
		\Log::info('Qty rolled back Start');
		$orderLists = OrdersDetails::where('is_paid', 0)
			->where('is_qty_rollbacked', 0)
			->where('order_status', '!=', 'completed')
			->where('pay_mode', '!=', ['COD', 'POSTKNET'])
			->whereRaw('created_at >= now() - interval 15 minute')
			->get();
		if (!empty($orderLists) && count($orderLists) > 0) {
			foreach ($orderLists as $orderList) {
				$this->rollbackknetfailedorderlist($orderList->id);
			}
		}
		\Log::info('Qty rolled back End');
	}

	public function rollbackknetfailedorderlist($oid)
	{

		$orderDetails = OrdersDetails::find($oid);
		$orderDetails->is_qty_rollbacked = 1;
		$orderDetails->save();

		\Log::info('Qty rolled back For ' . $oid);

		$orderLists   = Orders::where("oid", $oid)->get();
		if (!empty($orderLists) && count($orderLists) > 0) {
			foreach ($orderLists as $orderList) {
				//option
				$OrderOptions = OrdersOption::where("oid", $orderList->id)->get();
				if (!empty($OrderOptions) && count($OrderOptions) > 0) {
					foreach ($OrderOptions as $OrderOption) {
						$this->changeOptionQuantity($OrderOption->product_id , 'a', $OrderOption->option_child_ids, $orderList->quantity, $orderList->inventory); //add qty
					}
				}
				//end option
				$this->rollbackedQuantity($orderList->product_id, $orderList->quantity, $orderList->size_id, $orderList->color_id, $orderList->inventory);
			}
		}
	}


	public function rollbackedQuantity($product_id, $quantity, $size_id = 0, $color_id = 0,$inventories)
	{
        $inventories = json_decode($inventories , true);
        $productDetails   = Product::where('id', $product_id)->first();
        if (empty($productDetails['is_attribute'])) {
            foreach ($inventories as $inventory){
                $productQuantity = $productDetails->getQuantity($inventory['IID'], null, null,false,false,true);
                if ($productQuantity->is_qty_deduct == 1) {
                    $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                    $productQuantity->save();
                }
            }
        } else {
            if (!empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->where('color_id', $color_id)->get();
                    foreach ($inventories as $inventory){
                        $productQuantities = $productDetails->getQuantity($inventory['IID'],$attributes->pluck('id')->toArray(),null,true, false, true);
                        foreach($productQuantities as $productQuantity) {
                            if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                                $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                                $productQuantity->save();
                            }
                        }
                    }
                
            } else if (!empty($size_id) && empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('size_id', $size_id)->get();
                    foreach ($inventories as $inventory){
                        $productQuantities = $productDetails->getQuantity($inventory['IID'],$attributes->pluck('id')->toArray(),null,true, false, true);
                        foreach($productQuantities as $productQuantity) {
                            if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                                $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                                $productQuantity->save();
                            }
                        }
                    }
                
            } else if (empty($size_id) && !empty($color_id)) {
                $attributes = ProductAttribute::where('product_id', $product_id)->where('color_id', $color_id)->get();
                    foreach ($inventories as $inventory){
                        $productQuantities = $productDetails->getQuantity($inventory['IID'],$attributes->pluck('id')->toArray(),null,true, false, true);
                        foreach($productQuantities as $productQuantity) {
                            if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                                $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                                $productQuantity->save();
                            }
                        }
                    }
                
            }
        }
	}

	//change qty from option
	public function changeOptionQuantity($product_id,$mode, $ids, $quantity,$inventories)
	{
        $inventories = json_decode($inventories , true);
        $explodechildids = explode(",", $ids);
        $productDetails = Product::find($product_id);
        for ($i = 0; $i < count($explodechildids); $i++) {
            foreach ($inventories as $inventory) {
                $productQuantities = $productDetails->getQuantity($inventory['IID'], null, $explodechildids[$i], true);
                foreach ($productQuantities as $productQuantity) {
                    if ($productQuantity != null and $productQuantity->is_qty_deduct == 1) {
                        $productQuantity->quantity = $productQuantity->quantity + $inventory['q'];
                        $productQuantity->save();
                        break;
                    }
                }
            }
        }
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{

		$this->rollbackknetfailedorder();
		$this->changeorderstatusauto();
	}
}
