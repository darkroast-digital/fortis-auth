<?php

/*
|--------------------------------------------------------------------------
| #API
|--------------------------------------------------------------------------
*/


use App\Controllers\Api\TokensController;
use App\Controllers\Api\UsersController;
use App\Controllers\PostsController;

$app->group('/api', function () {

// #POSTS
// =========================================================================

$this->get('/post/{slug}', PostsController::class . ':get');

});
