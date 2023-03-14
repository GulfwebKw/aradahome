<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductsQuantity extends Model
{
    protected $table = "gwc_products_quantity";
    protected $fillable = ['product_id',	'attribute_id',	'option_id',	'inventory_id',	'quantity' , 'is_qty_deduct'];

    public function inventory(){
        return $this->belongsTo(Inventory::class , 'inventory_id')->orderBy('priority');
    }

    public function product(){
        return $this->belongsTo(Product::class , 'product_id');
    }

    public function attribute(){
        return $this->belongsTo(ProductAttribute::class , 'attribute_id');
    }

    public function options(){
        return $this->belongsTo(ProductOptions::class , 'option_id');
    }


    public static function quantityUpdateAfterInventoryActivityChange($inventory , $type = null)
    {
        $isActive = 0;
        if ($type == 'activity')
            $isActive = $inventory->is_active == 1 ? ($inventory->deleted_at == null ? 1 : 0) : ($inventory->deleted_at == null ? -1 : 0);
        if ($type == 'store')
            $isActive = ($inventory->deleted_at == null) ? ($inventory->is_active == 1 ? 1 : 0) : ($inventory->is_active == 1 ? -1 : 0)  ;

        if ($isActive != 0) {
            $quantities = ProductsQuantity::where('inventory_id', $inventory->id)->where('is_qty_deduct' , 1)->selectRaw('product_id , sum(quantity) as quantity')->groupBy('product_id')->get()->toArray();
            foreach ($quantities as $quantity) {
                $product = Product::find($quantity['product_id']);
                if ($product != null) {
                    $newQuantity = $product->quantity + ($quantity['quantity'] * $isActive);
                    $product->update(['quantity' => max($newQuantity, 0)]);
                }
            }
        }
    }

}
