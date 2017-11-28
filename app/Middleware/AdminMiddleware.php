<?php

namespace App\Middleware;

class AdminMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['user'])) {
            if (!$this->auth->admin()) {

                $this->flash->addMessage('error', 'Sorry!  You can\'t do that.');

                return $response->withRedirect($this->router->pathFor('dashboard.index'));
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
