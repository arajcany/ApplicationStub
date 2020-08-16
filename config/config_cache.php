<?php

/**
 * Configure the cache adapters.
 */
return [
    'Cache' => [
        'default' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'path' => CACHE,
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        'quick_burn' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'path' => CACHE,
            'duration' => '+1 min',
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        'table_list' => [
            'className' => 'File',
            'prefix' => 'table_list_',
            'path' => CACHE . 'connection',
            'duration' => '+1 hour',
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        'query_results_app' => [
            //more for caching application properties such as Settings
            'className' => 'File',
            'prefix' => 'app_',
            'path' => CACHE . 'queries/app',
            'duration' => '+300 seconds',
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        'query_results_general' => [
            //for caching general queries
            'className' => 'File',
            'prefix' => 'general_',
            'path' => CACHE . 'queries/general',
            'duration' => '+1 minute',
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        /**
         * Configure the cache used for general framework caching.
         * Translation cache files are stored with this configuration.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         * If you set 'className' => 'Null' core cache will be disabled.
         */
        '_cake_core_' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'prefix' => 'myapp_cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKECORE_URL', null),
        ],

        /**
         * Configure the cache for model and datasource caches. This cache
         * configuration is used to store schema descriptions, and table listings
         * in connections.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_cake_model_' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'prefix' => 'myapp_cake_model_',
            'path' => CACHE . 'models/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEMODEL_URL', null),
        ],

        /**
         * Configure the cache for routes. The cached routes collection is built the
         * first time the routes are processed via `config/routes.php`.
         * Duration will be set to '+2 seconds' in bootstrap.php when debug = true
         */
        '_cake_routes_' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'prefix' => 'myapp_cake_routes_',
            'path' => CACHE,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEROUTES_URL', null),
        ],
    ],
];
