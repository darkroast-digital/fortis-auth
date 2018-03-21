<?php

namespace App\Middleware;

use App\Models\User;

class RememberMeMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];

            $users = User::all();

            foreach ($users as $user) {
                if (password_verify($token, $user->remember_token)) {
                    $_SESSION['user'] = $user->id;
                    // return $response->withRedirect($this->router->pathFor('dashboard.index'));
                }
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
