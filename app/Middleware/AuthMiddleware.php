<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (!$this->auth->check()) {

            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        $response = $next($request, $response);
        return $response;
    }
}
