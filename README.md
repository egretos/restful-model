# Laravel Restful Model

This package created to integrate API`s as Laravel Model.

This package is 

## Installation

Install package:
````shell script
composer require egretos/restful-model
````

Publish `rest_connections.php` config:
````shell script
php artisan vendor:publish --provider="Egretos\RestModel\RestModelServiceProvider"
````

Configure your connection in `config/rest_connections`

````php
<?php
return [
    'default_connection' => env('REST_CONNECTION', 'base_connection'),

    'connections' => [
        'base_connection' => [
            'domain' => env('REST_DOMAIN', 'example.com'),
            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: x-www-form-urlencoded, json
        ],
    ],
];
````

