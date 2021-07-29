<?php
/**
 * Configuration of REST`ful resources
 */
return [
    // Connection which will be used for models by default
    'default_connection' => env('REST_CONNECTION', 'base_connection'),

    'connections' => [

        /**
         * Example connection
         */
        'base_connection' => [
            'domain' => env('REST_DOMAIN', 'http://jsonplaceholder.typicode.com'),

            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: x-www-form-urlencoded, json
        ],
    ],
];
