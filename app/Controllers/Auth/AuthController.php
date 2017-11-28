<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function getLogOut($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('auth.login'));
    }

    public function getLogIn($request, $response)
    {

        return $this->view->render($response, 'auth/login.twig');
    }

    public function postLogIn($request, $response)
    {

        $user = User::where('email', $request->getParam('email'))->first();

        if ($user != null) {

            $validation = $this->validator->validate($request, [
                'email' => v::email()->notEmpty(),
                'password' => v::noWhitespace()->notEmpty()->matchesPassword($user->password),
            ]);

            if ($validation->failed()) {
                $this->flash->addMessage('error', 'Oh no, something went wrong!');

                return $response->withRedirect($this->router->pathFor('auth.login'));
            }

        }

        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if (!$auth) {
            $this->flash->addMessage('error', 'Sorry, that wasn\'t quite right...');
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        $user->active = date('Y-m-d H:i:s');
        $user->save();

        return $response->withRedirect($this->router->pathFor('dashboard.index', [
            'user' => $user->email,
        ]));
    }

    public function getRegister($request, $response)
    {
        return $this->view->render($response, 'auth/register.twig');
    }

    public function postRegister($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => v::notEmpty(),
            'email' => v::email()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Oh no, something went wrong!');

            return $response->withRedirect($this->router->pathFor('auth.register'));
        }

        $user = User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

        $this->flash->addMessage('info', 'You are now signed up!');

        $this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('dashboard.index', [
            'user' => $user->email,
        ]));
    }
}
