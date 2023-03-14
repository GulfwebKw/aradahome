<?php 
//SANBOX
return [ 
    'client_id' => 'AfrC4srXeUtepHHMUIwlQPifUqsCzj5fSlLPdHZKQhYpLn_-f5_GN1gVBACgnxypdeuLWz1GV4_H9xGd',
	'secret'    => 'EHIoFu0pefTChCa3cOST-_ubH68EbRxVoXjNLAFLrPHV0niq7zA76P2cSsK_CNlv_J8ZHL7RZZ5f8lJv',
    'settings' => array(
        'mode' => 'sandbox',
        'http.ConnectionTimeOut' => 1000,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'FINE'
    ),
];

//LIVE
/*
return [ 
    'client_id' => 'AfvhvnfFy2fQ4Ve5YpRqwgshC4CE4xqciDp61FFhc8IVWPEZWwtHpYXPoh9TY364rHwAA1ijfnRWa4L0',
	'secret'    => 'EHB3ALV6M3nqlYK3kwmncoFohcoMtcHj2tqvI_SPFQTjuRcEnM57eXejLCWm4m80TOumrlhKXtIfPsgE',
    'settings' => array(
        'mode' => 'live',
        'http.ConnectionTimeOut' => 1000,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'FINE'
    ),
];*/