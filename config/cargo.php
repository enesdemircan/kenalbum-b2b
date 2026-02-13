<?php

return [
    'everest' => [
        'authorization' => env('EVEREST_CARGO_AUTH', 'gjBTnR13tEwZ5XyLS2FmHJpWhOk6xUGdQP0YMqbN'),
        'from_email' => env('EVEREST_CARGO_EMAIL', 'mihra@everestkargo.com'),
        'api_url' => env('EVEREST_CARGO_API_URL', 'https://webpostman.everestkargo.com/restapi/client/consignment/add'),
    ],
    
    'yurticiAliciOdemeli' => [
        'username' => env('YURTICI_CARGO_USERNAME', '9171N955821472A'),
        'password' => env('YURTICI_CARGO_PASSWORD', 'w71iRVw9V2f844Pc'),
        'test_mode' => env('YURTICI_CARGO_TEST_MODE', false), // Test modu varsayılan olarak açık
    ],

    'yurticiGondericiOdemeli' => [
        'username' => env('YURTICI_CARGO_USERNAME', '9171N955821472G'),
        'password' => env('YURTICI_CARGO_PASSWORD', 'k8m3mxuUm0SBaS1z'),
        'test_mode' => env('YURTICI_CARGO_TEST_MODE', false), // Test modu varsayılan olarak açık
    ],
    
    'kolay_gelsin' => [
        'musteri' => env('KOLAY_GELSIN_MUSTERI','Kencolor.api'),
        'sifre' => env('KOLAY_GELSIN_SIFRE','Color.5108'),
        'base_url' => 'https://bff.kolaygelsin.com/gateway',
        'api_url' => 'https://bff.kolaygelsin.com/gateway'
    ],
]; 