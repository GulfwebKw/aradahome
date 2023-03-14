<?php
  
namespace App\Exports;
  
use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
  
class ProductExport implements FromCollection
{
    
	public function __construct($brand)
    {
        $this->brand = $brand;

    }
	
	/**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if(!empty($this->brand)){
		$prods =  Product::where('brand_id',$this->brand)->get();
		}else{
		$prods =  Product::get();
		}
		 return collect($prods);
    }
}