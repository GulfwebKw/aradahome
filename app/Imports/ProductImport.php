<?php
  
namespace App\Imports;
  
use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
  
class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'slug'           => $row[0], 
			'item_code'      => $row[1],
			'sku_no'         => $row[2],
			'title_en'       => $row[3],
			'title_ar'       => $row[4],
			'sdetails_en'    => $row[5],
			'sdetails_ar'    => $row[6],
			'details_en'     => $row[7],
			'details_ar'     => $row[8],
			'retail_price'   => $row[9],
			'old_price'      => $row[10],
			'quantity'       => $row[11],
			'is_attribute'   => $row[12],
			'image'          => $row[13],
			'rollover_image' => $row[14],
			'seokeywords_en' => $row[15],
			'seokeywords_ar' => $row[16],
			'seodescription_en' => $row[17],
			'seodescription_ar' => $row[18],
			'is_active'      => $row[19]
        ]);
    }
}