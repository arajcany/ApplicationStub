<?php
/*
 * Prevent the CsrfProtectionMiddleware from loading for these particular Controller/Action combinations
 * controller string
 * action string|string[]
 * requestType string|string[] http request methods such as GET POST PUT DELETE AJAX
 */
return [
    'Csrf' => [
        'ignore' => [
            ['controller' => 'load-tests', 'action' => 'splat', 'requestType' => ['ajax', 'post', 'get']],
            ['controller' => 'foo', 'action' => 'bar', 'requestType' => 'ajax'],
            ['controller' => 'foo', 'action' => ['bar', 'bar', 'bar', 'bar'], 'requestType' => 'ajax'],
        ]
    ],
];
