<?php

namespace App\Exports;

use App\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponCodeExport implements FromCollection, WithHeadings
{


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $coup = [];
        $coupons =   Coupon::where('is_active', 1)->get();
        if (!empty($coupons) && count($coupons) > 0) {
            foreach ($coupons  as $coupon) {
                $coup[] = [
                    'id'             => $coupon->id,
                    'title_en'          => $coupon->title_en,
                    'title_ar'          => $coupon->title_ar,
                    'coupon_code'    => $coupon->coupon_code,
                    'coupon_type'   => $coupon->coupon_type,
                    'coupon_value'      => $coupon->coupon_value,
                    'start_date'          => $coupon->start_date,
                    'end_date'           => $coupon->end_date,
                    'price_start'     => $coupon->price_start,
                    'price_end'          => $coupon->price_end,
                    'usage_limit' => $coupon->usage_limit,
                    'is_free'      => $coupon->is_free,
                    'is_zone_free'      => $coupon->is_zone_free,
                    'is_for'           => $coupon->is_for,

                ];
            }
        }
        return collect($coup);
    }


    public function headings(): array
    {
        return ['id', 'title_en', 'title_ar', 'coupon_code', ' coupon_type', 'coupon_value', 'start_date', 'end_date', 'price_start', 'price_end', 'usage_limit', 'is_free', 'is_zone_free', 'is_for'];
    }
}
