<?php

/*
|--------------------------------------------------------------------------
| #AUTH
|--------------------------------------------------------------------------
*/




use App\Controllers\Auth\AuthController;
use App\Controllers\Auth\PasswordController;




// #AUTHENTICATION
// =========================================================================

$app->get('/register', AuthController::class . ':getRegister')->setName('auth.register');
$app->post('/register', AuthController::class . ':postRegister');
$app->get('/login', AuthController::class . ':getLogIn')->setName('auth.login');
$app->post('/login', AuthController::class . ':postLogIn');
$app->get('/logout', AuthController::class . ':getLogOut')->setName('auth.logout');




// #PASSWORD RESET
// =========================================================================

$app->get('/forgot', PasswordController::class . ':getForgot')->setName('auth.forgot');
$app->post('/forgot', PasswordController::class . ':postForgot')->setName('auth.forgot');
$app->get('/reset/{token}', PasswordController::class . ':getReset')->setName('auth.reset');
$app->post('/reset/{token}', PasswordController::class . ':postReset');
