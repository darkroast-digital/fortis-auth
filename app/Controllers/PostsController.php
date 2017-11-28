<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class PostsController extends Controller
{
    public function index($request, $response, $args)
    {
        $posts = Post::orderBy('updated_at', 'desc')->paginate(10);

        return $this->view->render($response, 'dashboard/posts/index.twig', compact('posts'));
    }

    public function show($request, $response, $args)
    {
        $post = Post::where('slug', $args['slug'])->first();
        $comments = Comment::where('post_id', $post->id)->paginate(5);

        $featured = null;
        $slug = $args['slug'];

        if (file_exists(__DIR__ . '/../../assets/uploads/posts/' . $slug . '/featured.jpg')) {
            $featured = '/assets/uploads/posts/' . $slug . '/featured.jpg';
        }

        $files = [];

        if (count(glob(__DIR__ . '/../../assets/uploads/posts/' . $slug . '/files/*'))) {
            $scan = scandir(__DIR__ . '/../../assets/uploads/posts/' . $slug . '/files');
            unset($scan[0]);
            unset($scan[1]);

            foreach ($scan as $file) {
                array_push($files, $file);
            }
        }

        $recentPosts = Post::orderBy('created_at', 'desc')->limit(3)->get();

        $post->body = $this->markdown->text($post->body);

        return $this->view->render($response, 'post.twig', compact('post', 'comments', 'recentPosts', 'featured', 'files'));
    }

    public function get($request, $response, $args)
    {
        $post = Post::where('slug', $args['slug'])->first();

        return $response->withJson($post);
    }

    public function create($request, $response, $args)
    {
        return $this->view->render($response, 'dashboard/posts/create.twig');
    }

    public function store($request, $response, $args)
    {
        $user = $this->auth->user();
        $params = $request->getParams();

        if (isset($_FILES['files'])) {

            $files = $_FILES['files'];
            $total = count($files['name']);
            $image = $_FILES['featured'];

        }
        $slug = $this->slug->slugify($params['name']);
        $basePath = __DIR__ . '/../../assets/uploads/posts/' . $slug;
        $users = User::all();

        $active = false;

        if ($request->getParam('active') == true) {
            $active = true;
        }

        if (isset($_FILES['files'])) {

            if (!file_exists(__DIR__ . '/../../assets/uploads/posts/' . $slug)) {
                mkdir(__DIR__ . '/../../assets/uploads/posts/' . $slug);
            };

            move_uploaded_file($image['tmp_name'], __DIR__ . '/../../assets/uploads/posts/' . $slug . '/featured.jpg');

            if (!file_exists($basePath . '/files/')) {
                mkdir($basePath . '/files/');
            }

            for ($i=0; $i < $total; $i++) {
                $filePath = $basePath . '/files/' . $files['name'][$i];
                move_uploaded_file($files['tmp_name'][$i], $filePath);
            }

        }

        $post = Post::create([
            'user_id' => $user->id,
            'name' => $params['name'],
            'slug' => $this->slug->slugify($params['name']),
            'body' => $params['body'],
            'active' => $active
        ]);

        foreach ($users as $user) {
            $this->mail->from('josh@darkroast.co', 'Fortis Group')
                      ->to([
                            [
                                'name' => $user->name,
                                'email' => $user->email
                            ],
                      ])
                      ->subject('There is a new post from Fortis Group!')
                      ->send('mail/mail.twig', compact('user', 'post'));
        }

        $this->flash->addMessage('info', 'Post Created!');

        return $response->withRedirect($this->router->pathFor('dashboard.posts'));
    }

    public function edit($request, $response, $args)
    {
        $post = Post::where('slug', $args['slug'])->first();
        $comments = Comment::where('post_id', $post->id)->paginate(10);
        $featured = null;
        $slug = $args['slug'];

        $active = false;

        if ($post->active == true) {
            $active = true;
        }

        if (file_exists(__DIR__ . '/../../assets/uploads/posts/' . $slug . '/featured.jpg')) {
            $featured = '/assets/uploads/posts/' . $slug . '/featured.jpg';
        }

        $users = User::all();

        foreach ($users as $user) {
            $this->mail->from('josh@darkroast.co', 'Fortis Group')
                      ->to([
                            [
                                'name' => $user->name,
                                'email' => $user->email
                            ],
                      ])
                      ->subject('A Post Has Been Editied.')
                      ->send('mail/mail.twig', compact('user', 'post'));
        }

        return $this->view->render($response, 'dashboard/posts/edit.twig', compact('post', 'comments', 'featured', 'visible'));
    }

    public function update($request, $response, $args)
    {
        $params = $request->getParams();

        $active = false;

        if (isset($params['active'])) {
            $active = true;
        }

        $files = $_FILES;
        $image = $files['featured'];
        $slug = $this->slug->slugify($params['name']);

        if (!file_exists(__DIR__ . '/../../assets/uploads/posts/' . $slug)) {
            mkdir(__DIR__ . '/../../assets/uploads/posts/' . $slug);
        };

        move_uploaded_file($image['tmp_name'], __DIR__ . '/../../assets/uploads/posts/' . $slug . '/featured.jpg');

        $post = Post::where('slug', $args['slug'])->first();

        $post->name = $params['name'];
        $post->slug = $this->slug->slugify($params['name']);
        $post->body = $params['body'];
        $post->active = $active;

        $post->save();

        $this->flash->addMessage('info', 'Post Edited!');

        return $response->withRedirect($this->router->pathFor('dashboard.posts.edit', ['slug' => $post->slug]));
    }

    public function trash($request, $response, $args)
    {
        $post = Post::find($args['id']);

        $post->delete();
    }

    public function delete($request, $response, $args)
    {
        $slug = $args['slug'];
        $post = Post::where('slug', $slug);
        $path = __DIR__ . '/../../assets/uploads/posts/' . $slug;

         function rrmdir($dir) { 
           if (is_dir($dir)) { 
             $objects = scandir($dir); 
             foreach ($objects as $object) { 
               if ($object != "." && $object != "..") { 
                 if (is_dir($dir."/".$object))
                   rrmdir($dir."/".$object);
                 else
                   unlink($dir."/".$object); 
               } 
             }
             rmdir($dir); 
           } 
         }

         rrmdir($path);

        $post->delete();

        $this->flash->addMessage('error', 'Post Deleted!');
        return $response->withRedirect($this->router->pathFor('dashboard.posts'));
    }
}

