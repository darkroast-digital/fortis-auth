<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function index($request, $response, $args)
    {
        //
    }

    public function create($request, $response, $args)
    {
        $comment = Comment::create([
            'user_id' => $this->auth->user()->id,
            'post_id' => $args['post_id'],
            'body' => $request->getParams()['message'],
        ]);

        $post = Post::find($args['post_id']);

        return $response->withRedirect($this->router->pathFor('dashboard.posts.show', [
            'slug' => $post->slug,
        ]));
    }

    public function store($request, $response, $args)
    {
        //
    }

    public function show($request, $response, $args)
    {
        //
    }

    public function edit($request, $response, $args)
    {
        //
    }

    public function update($request, $response, $args)
    {
        //
    }

    public function destroy($request, $response, $args)
    {
        //
    }
}

