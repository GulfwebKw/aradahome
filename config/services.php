<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
	
	'knet_test' => [
        'TRANSPORTAL_ID'      => env('TEST_TRANSPORTAL_ID'),
		'TRANSPORTAL_PASS'    => env('TEST_TRANSPORTAL_PASS'),
		'CURRENCY_CODE'       => env('TEST_CURRENCY_CODE'),
		'LANGID'              => env('TEST_LANGID'),
		'ACTION'              => env('TEST_ACTION'),
		'TERM_RESOURCE_KEY'   => env('TEST_TERM_RESOURCE_KEY'),
		'PAYMENT_REQUEST_URL' => env('TEST_PAYMENT_REQUEST_URL'),
    ],
	'knet_live' => [
        'TRANSPORTAL_ID'      => env('LIVE_TRANSPORTAL_ID'),
		'TRANSPORTAL_PASS'    => env('LIVE_TRANSPORTAL_PASS'),
		'CURRENCY_CODE'       => env('LIVE_CURRENCY_CODE'),
		'LANGID'              => env('LIVE_LANGID'),
		'ACTION'              => env('LIVE_ACTION'),
		'TERM_RESOURCE_KEY'   => env('LIVE_TERM_RESOURCE_KEY'),
		'PAYMENT_REQUEST_URL' => env('LIVE_PAYMENT_REQUEST_URL'),
    ],
	'cbk' => [
        'CLIENT_ID'           => env('CBK_CLIENT_ID'),
        'CLIENT_SECRET'       => env('CBK_CLIENT_SECRET'),
        'ENCRP_KEY'           => env('CBK_ENCRP_KEY'),
        'URL'                 => env('CBK_URL'),
    ],
	'tahseel_test' => [
        'TAH_UID'             => env('TEST_TAH_UID'),
		'TAH_PWD'             => env('TEST_TAH_PWD'),
		'TAH_SECRET'          => env('TEST_TAH_SECRET'),
		'TAH_ORDER_URL'       => env('TEST_TAH_ORDER_URL'),
		'TAH_INFO_URL'        => env('TEST_TAH_INFO_URL'),
		'TAH_RETURN_URL'      => env('TEST_TAH_RETURN_URL'),
    ],
	'tahseel_live' => [
        'TAH_UID'             => env('LIVE_TAH_UID'),
		'TAH_PWD'             => env('LIVE_TAH_PWD'),
		'TAH_SECRET'          => env('LIVE_TAH_SECRET'),
		'TAH_ORDER_URL'       => env('LIVE_TAH_ORDER_URL'),
		'TAH_INFO_URL'        => env('LIVE_TAH_INFO_URL'),
		'TAH_RETURN_URL'      => env('LIVE_TAH_RETURN_URL'),
    ],
	'myfatoorah_test' => [
        'MF_CURRENCY'         => env('TEST_MF_CURRENCY'),
		'MF_CURRENCY_ID'      => env('TEST_MF_CURRENCY_ID'),
		'MF_TOKEN_API_URL'    => env('TEST_MF_TOKEN_API_URL'),
		'MF_CALLBACK'         => env('TEST_MF_CALLBACK'),
		'MF_INVOICE_URL'      => env('TEST_MF_INVOICE_URL'),
		'MF_USERNAME'         => env('TEST_MF_USERNAME'),
		'MF_PASSWORD'         => env('TEST_MF_PASSWORD'),
    ],
	'myfatoorah_live' => [
        'MF_CURRENCY'         => env('LIVE_MF_CURRENCY'),
		'MF_CURRENCY_ID'      => env('LIVE_MF_CURRENCY_ID'),
		'MF_TOKEN_API_URL'    => env('LIVE_MF_TOKEN_API_URL'),
		'MF_CALLBACK'         => env('LIVE_MF_CALLBACK'),
		'MF_INVOICE_URL'      => env('LIVE_MF_INVOICE_URL'),
		'MF_USERNAME'         => env('LIVE_MF_USERNAME'),
		'MF_PASSWORD'         => env('LIVE_MF_PASSWORD'),
    ]

];
