<?php

/*
|--------------------------------------------------------------------------
| #WEB
|--------------------------------------------------------------------------
*/




use App\Controllers\CommentController;
use App\Controllers\Dashboard\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ResourcesController;
use App\Controllers\PostsController;
use App\Controllers\SearchController;
use App\Controllers\UsersController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;




// #HOME
// =========================================================================

$app->get('/', HomeController::class . ':index')->setName('home');




// #POSTS
// =========================================================================

$app->get('/post/{slug}', PostsController::class . ':show')->setName('post');




// #DASHBOARD
// =========================================================================

// USER

$app->group('/dashboard', function () {

    $this->get('', DashboardController::class . ':index')->setName('dashboard.index');


    $this->get('/posts', PostsController::class . ':index')->setName('dashboard.posts');
    $this->get('/posts/show/{slug}', PostsController::class . ':show')->setName('dashboard.posts.show');

    $this->post('/comment/create/{post_id}', CommentController::class . ':create')->setName('dashboard.comment.create');

    $this->get('/users/profile/{id}', UsersController::class . ':edit')->setName('dashboard.users.profile');

    $this->get('/search/{category}', SearchController::class . ':search')->setName('dashboard.search');

    $this->get('/resources', ResourcesController::class . ':index')->setName('dashboard.resources');
    $this->get('/resources/show/{directory}', ResourcesController::class . ':show')->setName('dashboard.resources.show');

})->add(new AuthMiddleware($container));




// ADMIN

$app->group('/dashboard', function () {

    $this->get('/posts/create', PostsController::class . ':create')->setName('dashboard.posts.create');
    $this->get('/posts/edit/{slug}', PostsController::class . ':edit')->setName('dashboard.posts.edit');

    $this->post('/posts/{slug}/update', PostsController::class . ':update')->setName('dashboard.posts.update');
    $this->post('/posts/store', PostsController::class . ':store')->setName('dashboard.posts.store');

    $this->get('/posts/delete/{slug}', PostsController::class . ':delete')->setName('dashboard.posts.delete');

    $this->get('/users', UsersController::class . ':index')->setName('dashboard.users');
    $this->get('/users/create', UsersController::class . ':create')->setName('dashboard.users.create');
    $this->get('/users/edit/{id}', UsersController::class . ':edit')->setName('dashboard.users.edit');

    $this->post('/users/store', UsersController::class . ':store')->setName('dashboard.users.store');

    $this->get('/users/delete/{id}', UsersController::class . ':destroy')->setName('dashboard.users.delete');

    $this->get('/resources/create', ResourcesController::class . ':create')->setName('dashboard.resources.create');
    $this->post('/resources/store', ResourcesController::class . ':store')->setName('dashboard.resources.store');
    $this->get('/resources/delete', ResourcesController::class . ':destroy')->setName('dashboard.resources.delete');

})->add(new AuthMiddleware($container))->add(new AdminMiddleware($container));
