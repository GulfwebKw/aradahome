<?php
namespace App\Console\Commands;

use App\Country;
use App\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addCountry';

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
        try {
            DB::beginTransaction();
            $path = storage_path() . "/csc.json";
            $json = json_decode(file_get_contents($path));
            $listCountry = Country::where('parent_id', 0)->get()->pluck('code')->toArray();
            $listCurrency = Currency::all()->pluck('code')->toArray();
            $orderCountry = 26;
            foreach ($json as $i1 => $country) {
                if (!in_array(strtolower($country->iso2), $listCountry)) {
                    $currency = "USD";
                    if (in_array(strtoupper($country->currency), $listCurrency))
                        $currency = $country->currency;
                    $countryAdded = new Country();
                    $countryAdded->name_en = $country->name;
                    $countryAdded->name_ar = $country->name;
                    $countryAdded->currency = $currency;
                    $countryAdded->code = strtolower($country->iso2);
                    $countryAdded->zone_id = 1;
                    $countryAdded->shipment_method = "zoneprice";
                    $countryAdded->image = strtolower($country->iso2) . ".png";
                    $countryAdded->is_active = 0;
                    $countryAdded->display_order = $orderCountry;
                    $countryAdded->is_state = 1;
                    $countryAdded->parent_id = 0;
                    $countryAdded->latitude = $country->latitude;
                    $countryAdded->longitude = $country->longitude;
                    $countryAdded->saveOrFail();
                    $orderCountry++;
                    $this->line("country: {$i1}.{$country->name}({$orderCountry})");
                    $orderState = 1;
                    foreach ($country->states as $i2 => $state) {
                        $stateAdded = new Country();
                        $stateAdded->name_en = $state->name;
                        $stateAdded->name_ar = $state->name;
                        $stateAdded->zone_id = 0;
                        $stateAdded->is_active = 1;
                        $stateAdded->display_order = $orderState;
                        $stateAdded->is_state = 0;
                        $stateAdded->parent_id = $countryAdded->id;
                        $stateAdded->latitude = $state->latitude;
                        $stateAdded->longitude = $state->longitude;
                        $stateAdded->saveOrFail();
                        $orderState++;
                        $this->line("country: {$i1}.{$country->name}({$orderCountry}) | State: {$i2}.{$state->name}({$orderState})");
                        $orderCity = 1;
                        foreach ($state->cities as $i3 => $city) {
                            $cityAdded = new Country();
                            $cityAdded->name_en = $city->name;
                            $cityAdded->name_ar = $city->name;
                            $cityAdded->zone_id = 0;
                            $cityAdded->is_active = 1;
                            $cityAdded->display_order = $orderCity;
                            $cityAdded->is_state = 0;
                            $cityAdded->parent_id = $stateAdded->id;
                            $cityAdded->latitude = $city->latitude;
                            $cityAdded->longitude = $city->longitude;
                            $cityAdded->saveOrFail();
                            $orderCity++;
                            $this->line("country: {$i1}.{$country->name}({$orderCountry}) | State: {$i2}.{$state->name}({$orderState}) |  City: {$i3}.{$city->name}({$orderCity})");
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $exception){
            $this->error($exception->getMessage());
            DB::rollBack();
        }
    }
}
