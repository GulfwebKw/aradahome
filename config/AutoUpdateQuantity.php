<?php

return [
    'oskarphone' => [
        'Active' => true,
        'Inventory_Active' => true,
        'Active_email_report' => true,
        'product_status_if_qty_more_than_zero' => 1, //	0 = Not publish , 1= Published , 2=Preorder	 , null=Not Change
        'product_export_if_qty_more_than_zero' => null, //	0 = Can Not Export , 1= Can Export , null=Not Change

        'Inventory' => [
            'can_delete' => '0',
        ],

        'Mapping' => [
            'product' => null,
            'quantity' => 'stock',
            'sku' => 'sku',
        ],

        'API' =>[
            'Link' => 'https://oskarphone.com/kuwait/rest/products',
            'Header' => [
                'Authorization' => 'YU%^Oskar#2020$KMS*^1537^G#GHFDJ$^*FHG^^KWT',

            ],
            'Body' => [

            ],
            'method' => 'POST',
        ],
    ],
];