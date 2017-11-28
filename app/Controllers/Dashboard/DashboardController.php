<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index($request, $response, $args)
    {
        $postsLength = count(Post::all());
        $posts = Post::orderBy('created_at', 'desc')->limit(5)->get();

        return $this->view->render($response, 'dashboard/index.twig', compact('posts', 'comments'));
    }
}

