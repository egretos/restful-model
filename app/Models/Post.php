<?php

namespace App\Models;


use Egretos\RestModel\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'userId'
    ];
}
