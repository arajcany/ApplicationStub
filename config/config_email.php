<?php
return [
    'EmailTransport' => [
        //will be populated/overwritten from DB
        'default' => [
            'className' => 'Smtp',
            'timeout' => 10,
            'host' => 'localhost',
            'port' => 25,
        ]
    ],
    'Email' => [
        //will be populated/overwritten from DB
        'default' => [
            'transport' => 'default',
            'from' => 'app@localhost',
        ]
    ]
];
