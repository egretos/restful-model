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
            'domain' => env('REST_DOMAIN', 'http://jsonplaceholder.typicode.com'),
            'content-type' => 'www-form', // Data format for PUT and POST requests. Available: x-www-form-urlencoded, json
        ],
    ],
];
````

Define a model which will be your hook to all

````php
use Egretos\RestModel\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];
}
````

Do what you want with model!
````php
/**
 * GET http://jsonplaceholder.typicode.com/posts
 * @var $posts Collection|Post[]
 */
$posts = Post::query()->index();

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

