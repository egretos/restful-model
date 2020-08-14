<?php
/**
 * Configuration if REST`ful resources
 */
return [
    'default_connection' => env('REST_CONNECTION', 'base_connection'),

    'connections' => [

        /**
         * Example connection
         */
        'base_connection' => [
            'domain' => env('REST_DOMAIN', 'example.com'),
            'prefix' => env('REST_PREFIX', 'api/1.0'),
            'auth' => [ // delete this if you are will not use authorization at all
                'type' => 'basic_auth',
                'login' => env('REST_AUTH_LOGIN', 'example.com'),
                'password' => env('REST_AUTH_LOGIN', 'put_password_here'),
            ],
            'response_index' => 'data', // Array index which used for in main index response. Delete if not used
            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: x-www-form-urlencoded, json
            'normalizer' => 'json', // json, body
            'paginator' => [
                'page_key' => 'page',
                'per_page_key' => 'per_page',
                'page_response_key' => 'meta.current_page',
                'per_page_response_key' => 'meta.per_page',
                'total_response_key' => 'meta.total',
            ],
        ],
    ],
];
