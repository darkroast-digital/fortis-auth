<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Mailgun\Mailgun;

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

        if ($request->getParam('remember_me')) {
            $token = bin2hex(random_bytes(100));
            setcookie('remember_token', $token, time() + (10 * 365 * 24 * 60 * 60));

            $user->remember_token = password_hash($token, PASSWORD_DEFAULT);
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

        $password = $request->getParam('password');

        $user = User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

        $mg = Mailgun::create('key-430a3c205c21f327abdf2db317f386f2');

        $mg->messages()->send('fortisgroup.ca', [
          'from' => 'communication@fortisgroup.ca',
          'to' => $user->email,
          'subject' => 'Introducing the NEW Fortis Group Employee Portal',
          'html' => $this->view->fetch('mail/welcome.twig', compact('user', 'password'))
        ]);

        $this->flash->addMessage('info', 'You are now signed up!');

        $this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('dashboard.index', [
            'user' => $user->email,
        ]));
    }
}
