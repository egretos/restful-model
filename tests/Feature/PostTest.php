<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_request()
    {
        $response = $this->getJson('/posts');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_post_request()
    {
        $response = $this->postJson('/post-request',['title'=>"sunt aut facere repellat provident occaecati excepturi optio reprehenderit"]);

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure([
                    'post' => [
                        'id',
                        'title',
                    ]
                ]);
    }

    public function test_put_request()
    {

        $response = $this->putJson('/put-request');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_patch_request()
    {

        $response = $this->patchJson('/patch-request');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_delete_request()
    {

        $response = $this->deleteJson('/delete-request');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_modify_request()
    {

        $response = $this->postJson('/modify-headers');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_request_with_query_params()
    {

        $response = $this->postJson('/request-with-query-params');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_store()
    {
        $data = [
            'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'body' => 'quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto',
            'userId' => 13
        ];
        $response = $this->postJson('/store',$data);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_find()
    {
        $response = $this->getJson('/find/13');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_update()
    {
        $data = [
            'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'body' => 'quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto',
            'userId' => 15
        ];
        $response = $this->putJson('/update/102',$data);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_save()
    {
        $data = [
            'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'body' => 'quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto',
            'userId' => 19
        ];
        $response = $this->postJson('/save/23',$data);

        $response->assertStatus(Response::HTTP_OK);
    }
}
