<?php

namespace App\Console\Commands;

use App\Http\Controllers\Common;
use App\Mail\SendErrorApiUpdateProductQuantity;
use App\NotificationEmails;
use App\Product;
use App\ProductAttribute;
use App\ProductOptions;
use App\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use function foo\func;

class updateQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update-quantity {driver*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update quantity of products from api';

    private $inventory = null ;
    private $driver = "default" ;


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allQuantityChange = 0 ;
        $timer_start = time();
        $drivers = self::argument('driver') ??  "default";
        foreach ( $drivers as $driver) {
            $this->driver = $driver;
            if (config('AutoUpdateQuantity.' . $this->driver . '.Active')) {
                if (config('AutoUpdateQuantity.' . $this->driver . '.Inventory_Active')) {
                    $inventory = \App\Inventory::when(is_array(config('AutoUpdateQuantity.' . $this->driver . '.Inventory')) and count(config('AutoUpdateQuantity.' . $this->driver . '.Inventory')) > 0, function ($query) {
                        foreach (config('AutoUpdateQuantity.' . $this->driver . '.Inventory') as $key => $value)
                            $query->where($key, $value);
                    })->first();
                    if ($inventory != null)
                        $this->inventory = $inventory->id;
                }
                try {
                    $this->line('Start fetch data from API of : '.$this->driver );
                    $data = $this->callAPI();
                    if ( $data == null )
                        continue;
                    $this->line('Data get.');
                    $productsFind = Arr::get($data, config('AutoUpdateQuantity.' . $this->driver . '.Mapping.product'));
                    unset($data);
                    $skuQuantities = Arr::pluck($productsFind, config('AutoUpdateQuantity.' . $this->driver . '.Mapping.quantity'), config('AutoUpdateQuantity.' . $this->driver . '.Mapping.sku'));
                    unset($productsFind);
                    $this->line('Quantity find inside data.');
                    $this->line('Start updating database:');
                    $lastQuantity = [];
                    $bar = $this->output->createProgressBar(count($skuQuantities));
                    $bar->start();
                    foreach ($skuQuantities as $sku => $quantity) {
                        $bar->advance();
                        if ($sku != null or $sku != "") {
                            $sku = trim($sku);
                            $product = Product::where('sku_no', $sku)->where('is_attribute', false)->first();
                            if ($product == null) {
                                $ProductAttribute = ProductAttribute::where('sku_no', $sku)->first();
                                if ($ProductAttribute == null) {
                                    $ProductOptions = ProductOptions::where('sku_no', $sku)->first();
                                    if ($ProductOptions == null)
                                        continue;

                                    $lastQuantity[] = $this->updateQuantity(count($lastQuantity), $sku, $ProductOptions, $quantity, 'option_id');
                                } else
                                    $lastQuantity[] = $this->updateQuantity(count($lastQuantity), $sku, $ProductAttribute, $quantity, 'attribute_id');
                            } else
                                $lastQuantity[] = $this->updateQuantity(count($lastQuantity), $sku, $product, $quantity, 'product_id', true);
                        }
                    }
                    $bar->finish();
                    $this->line('');
                    $this->line('All Quantity updated');
                    $this->table(
                        ['Num.', 'Product ID', 'SKU', 'Last Quantity', 'New Quantity'], $lastQuantity
                    );
                    $allQuantityChange +=  count($lastQuantity);
                    $this->line('');
                    $this->line('');
                } catch (\Exception  $e) {
                    $this->notificationError($e->getMessage());
                    $this->error($e->getMessage());
                    die();
                }
            }
        }
        if ( $allQuantityChange > 0 )
            Common::saveLogs("logs", 0, 'Automatically quantity update for ' . $allQuantityChange . 'X Products. Execute time: ' . (time() - $timer_start) . ' seconds');
        $this->line('update database successfully.');
    }

    private function callAPI(){
        try {
            $client = new \GuzzleHttp\Client();
            if (strtolower(config('AutoUpdateQuantity.'.$this->driver.'.API.Link')) == "post") {
                $request = $client->post(config('AutoUpdateQuantity.'.$this->driver.'.API.Link'), ['body' => config('AutoUpdateQuantity.'.$this->driver.'.API.Body'), 'headers' => config('AutoUpdateQuantity.'.$this->driver.'.API.Header')]);
                $response = $request->getBody()->getContents();
            } else {
                $request = $client->get(config('AutoUpdateQuantity.'.$this->driver.'.API.Link'), ['headers' => config('AutoUpdateQuantity.'.$this->driver.'.API.Header')]);
                $response = $request->getBody()->getContents();
            }
            return json_decode($response, true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $message = json_decode($e->getResponse()->getBody()->getContents(), true);
            $this->notificationError($message['Message'] ?? $e->getMessage());
            $this->error( $message['Message'] ?? $e->getMessage()  ) ;
            die();
        }
    }


    private function updateQuantity($count , $sku , $object , $quantity , $key  , $otherOptionNull = false){
        $count++;
        $returnData = [ $count , 'Not Found!',$sku,0,0];
        if ( config('AutoUpdateQuantity.'.$this->driver.'.Inventory_Active') ) {
            if ( $this->inventory != null ) {
                $returnData[1] = 'Not Found Inventory Quantity!';
                $ProductsQuantity = \App\ProductsQuantity::where($key, $object->id)
                    ->when($otherOptionNull, function ($query) {
                        $query->where('option_id', null);
                        $query->where('attribute_id', null);
                    })->where('inventory_id', $this->inventory)->first();
                if ( $ProductsQuantity != null){
                    $returnData[1] = $ProductsQuantity->product_id;
                    $returnData[3] = $ProductsQuantity->quantity;
                    $ProductsQuantity->quantity = $quantity;
                    $returnData[4] = $ProductsQuantity->quantity;
                    $ProductsQuantity->save();


                    if ( config('AutoUpdateQuantity.'.$this->driver.'.product_status_if_qty_more_than_zero') != null or config('AutoUpdateQuantity.'.$this->driver.'.product_export_if_qty_more_than_zero') != null ) {
                        $productDetails = Product::where('id', $ProductsQuantity->product_id)->first();
                        if (config('AutoUpdateQuantity.' . $this->driver . '.product_status_if_qty_more_than_zero') != null and $productDetails->quantity > 0) {
                            $productDetails->is_active = config('AutoUpdateQuantity.' . $this->driver . '.product_status_if_qty_more_than_zero');
                        }
                        if (config('AutoUpdateQuantity.' . $this->driver . '.product_export_if_qty_more_than_zero') != null and $productDetails->quantity > 0) {
                            $productDetails->is_export_active = config('AutoUpdateQuantity.' . $this->driver . '.product_export_if_qty_more_than_zero');
                        }
                        $productDetails->save();
                    }
                }
            }
        } else {
            $returnData[3] = $object->quantity;
            $object->quantity = $quantity;
            $returnData[4] = $object->quantity;
            $object->save();
            if ( get_class($object) != Product::class  ){
                $returnData[1] = $object->product_id;
                $productDetails   = Product::where('id', $object->product_id)->first();
                if (!empty($productDetails->id) ) {
                    $qty     = ProductAttribute::where('product_id', $productDetails->id)->get()->sum('quantity');
                    $optyQty = ProductOptions::where('product_id', $productDetails->id)->get()->sum('quantity'); //option
                    $qty     = $qty + $optyQty;
                    //save qty
                    $productDetails->quantity = $qty;
                    if ( config('AutoUpdateQuantity.'.$this->driver.'.product_status_if_qty_more_than_zero') != null and $qty > 0  ){
                        $productDetails->is_active = config('AutoUpdateQuantity.'.$this->driver.'.product_status_if_qty_more_than_zero');
                    }
                    if ( config('AutoUpdateQuantity.'.$this->driver.'.product_export_if_qty_more_than_zero') != null and $qty > 0  ){
                        $productDetails->is_export_active = config('AutoUpdateQuantity.'.$this->driver.'.product_export_if_qty_more_than_zero');
                    }
                    $productDetails->save();
                }
            } else
                $returnData[1] = $object->id;
        }
        return $returnData;
    }

    private function notificationError($message){
        if ( config('AutoUpdateQuantity.'.$this->driver.'.Active_email_report') ) {
            $settingInfo = Settings::where("keyname", "setting")->first();
            //send email to admins
            $adminNotifications = NotificationEmails::where('is_active', 1)->get();
            if (!empty($adminNotifications) && count($adminNotifications) > 0) {
                foreach ($adminNotifications as $adminNotification) {
                    $data = [
                        'bodytxt' => $message,
                        'subject' => "Error in updating automatically quantity",
                        'email_from' => $settingInfo->from_email,
                        'email_from_name' => $settingInfo->from_name
                    ];
                    Mail::to($adminNotification->email)->send(new SendErrorApiUpdateProductQuantity($data));
                }
            }
        }
    }
}
