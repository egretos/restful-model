<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::All();

        return $posts;
    }

    public function postRequest(Request $request)
    {
        $title = $request->get('title');

        $post = new Post;
        $post->title = $title;

        $post->create();

        return response()->json(['post' => json_decode($post)]);
    }

    public function putRequest()
    {
        Post::query()
            ->setMethod("put")
            ->send();
    }
    public function patchRequest()
    {

        Post::query()
            ->setMethod("patch")
            ->send();
    }

    public function deleteRequest()
    {
        Post::query()
            ->setMethod("delete")
            ->send();



    }
    public function modifyHeaders()
    {
        Post::query()
            ->addHeader('Content-Language', 'en')
            ->send();



    }

    public function requestWithQueryParams()
    {
        Post::query()
            ->where('title', 'Lorem')
            ->send();



    }


    public function store(Request $request)
    {
        $post = new Post;
        $data = $request->all();

        $post->create($data);
    }

    public function find($id)
    {
        return Post::find($id);
    }

    public function update($id,Request $request)
    {
        $data = $request->all();

        $post = Post::find($id);
        $post->fill($data);

        $post->save();
    }

    public function save($id,Request $request)
    {
        $data = $request->all();

        if ($post = Post::find($id))
        {
            $post->fill($data);
            $post->save();
        }
        else
        {
            $post = new Post;


            $post->create($data);
        }
    }

}
