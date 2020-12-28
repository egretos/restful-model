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

````php
use Egretos\RestModel\Model;

class News extends Model
{
    protected $resource = 'articles';
}
````

### Restful connections

When your model using not default connection,
just add your second connection to configuration and tell it to model:

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
use App\Models\Post;

foreach (Post::all() as $post) {
    echo $post->title; // sunt aut facere repellat...
}
````


````php

/**
 * GET http://jsonplaceholder.typicode.com/posts/10
 * @var $post Post
 */
$post = Post::query()->find(10);

$post->title = 'new post title which needs to be saved';

/**
 * PUT http://jsonplaceholder.typicode.com/posts/10
 * @var $post Post
 */
$post->query()->update();

$newPost = Post::make([
    'title' => 'fake post title',
    'body' => 'fake post body',
]);

/**
 * POST http://jsonplaceholder.typicode.com/posts
 * @var $post Post
 */
$newPost->query()->create();

/**
 * Delete http://jsonplaceholder.typicode.com/posts/{id_of_newPost}
 * @var $post Post
 */
$newPost->query()->delete();
````

