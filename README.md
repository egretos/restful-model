# Laravel Restful Model

## Introduction

This package is providing an object mapper for REST HTTP services using
Eloquent-like "Models". When using the Restful Model, third-party APIs and REST resources
have a "Model" reflection that is used for integration. Restful Model allows you
to read, update, delete resources via HTTP request and modify them as easy
as Eloquent do it with databases.

## Installation

Install package via composer:
````shell script
composer require egretos/restful-model
````

Publish `rest_connections.php` config:
````shell script
php artisan vendor:publish --provider="Egretos\RestModel\RestModelServiceProvider"
````

## Configuration

Before you start, you need to define your first connection.
All HTTP connections stored in `config/rest_connections.php` file.
Every connection required `domain` configuration for working.

This is a most simple connection config:
````php
<?php
return [
    // Connection which will be used for models by default
    'default_connection' => env('REST_CONNECTION', 'base_connection'),

    'connections' => [
        'base_connection' => [
            'domain' => env('REST_DOMAIN', 'http://jsonplaceholder.typicode.com'),
            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: x-www-form-urlencoded, json
        ],
    ],
];
````

## Model

### Model definition

Define your model. Restful Model has a very close structure with Eloquent Model.
Here we crete a Post model to handle `post` HTTP restful resource:
[`http://jsonplaceholder.typicode.com/posts`](http://jsonplaceholder.typicode.com/posts)

````php
use Egretos\RestModel\Model;

class Post extends Model
{

}
````

### Resource name

The model will use the resource `posts`,
which is the name of the model in plural nouns.
You can use your own resource name when you need it by using `$model->resource`:

[`http://jsonplaceholder.typicode.com/comments`](http://jsonplaceholder.typicode.com/posts)

````php
use Egretos\RestModel\Model;

class Reviews extends Model
{
    protected $resource = 'comments';
}
````

### Restful connections

When your model using not default connection,
just add your second connection to configuration and tell it to model:

`rest_connections.php`:
````php
<?php
return [
    // Connection which will be used for models by default
    'default_connection' => env('REST_CONNECTION', 'base_connection'),

    'connections' => [
        'base_connection' => [
            'domain' => env('REST_DOMAIN', 'http://jsonplaceholder.typicode.com'),
            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: www-form, x-www-form-urlencoded, json
        ],
        
        'payment_api' => [
            'domain' => env('PAYMENT_DOMAIN', 'http://paypaypay.pay.pay'),
        ],
    ],
];
````

Model class for `http://paypaypay.pay.pay/payment`

````php
use Egretos\RestModel\Model;

class Payment extends Model
{
    public $connection = 'payment_api';
}
````

## HTTP queries

Models used to make http requests and to map data into Model objects.
Models are using a build-in HTTP query builder,
which allows you to build any query you need.

Here we get all posts from API and print the title of the first post in response.
Link is `GET http://jsonplaceholder.typicode.com/posts`.

````php
/** GET http://jsonplaceholder.typicode.com/posts */
foreach (Post::all() as $post) {
    echo $post->title; // sunt aut facere repellat...
}
````

### Retrieving single models

In case when you need to get a one model, next methods should help you:

````php
/** GET http://jsonplaceholder.typicode.com/posts/1 */
Post::find(1);

/** GET http://jsonplaceholder.typicode.com/posts?title=Lorem */
Post::where('title', 'Lorem')->first();
````

### HTTP query builder

The `all` method will do a `GET` request to `/posts` resource and then map all results into `Post` objects.
In case when we need to modify a request use query builder such like Laravel Eloquent query builder.

````php
use Egretos\RestModel\Request;

    $posts = Post::query()
        ->addHeader('Content-Language', 'en') // Puts new header to request
        ->setMethod(Request::METHOD_OPTIONS) // Set OPTIONS request method
        ->where('title', 'Lorem') // Sets query `title` param to `Lorem`
        ->send(); // Send a request. Here we get a response
````

## Save model

You can use `$model->save()` method just like in Eloquent.
This action will do `update()` if model is exists and `update` when model is not.

Create case:
````php
$post = new Post;
$post->title = 'This title will be updated';

/** POST http://jsonplaceholder.typicode.com/posts */
$post->save();
````

Update case:
````php
/** GET http://jsonplaceholder.typicode.com/posts/1 */
$post = Post::find(1);
$post->title = 'This title will be updated';

/** PUT http://jsonplaceholder.typicode.com/posts/1 */
$post->save();
````

## Create and update actions

`save()` method can be unpredictable or unclear in some cases, 
so there are `create` and `update()` methods.

Create method will send a POST request with model attributes as data
````php
$post = new Post;
$post->title = 'This title will be updated';

/** POST http://jsonplaceholder.typicode.com/posts */
$post->create();
````

Update method will do PUT request to route with `id` at the end
````php
/** GET http://jsonplaceholder.typicode.com/posts/1 */
$post = Post::find(1);
$post->title = 'This title will be updated';

/** PUT http://jsonplaceholder.typicode.com/posts/1 */
$post->update();
````

## first or find or

## Work with model attributes (Mass assignment + JSON)

## Last request and last response

## deleting models (+ query)

## Replicating models

## Configuration

## Handle HTTP Exceptions
