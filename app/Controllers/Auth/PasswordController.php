<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{

    public function getForgot($request, $response, $args)
    {
        return $this->view->render($response, 'auth/forgot.twig');
    }

    public function postForgot($request, $response, $args)
    {
        $user = User::where('email', $request->getParam('email'))->first();

        $hash = hash('sha256', $user->email . time());

        $user->reset_token = hash('sha256', $user->email . time());
        $user->save();

        $this->mail->from('joshstobbs@gmail.com', 'Josh Stobbs')
              ->to([
                    [
                        'name' => $user->name,
                        'email' => $user->email
                    ]
              ])
              ->subject('Hey! ' . $user->name . ' A password reset was requested on your account.')
              ->send('mail/mail.twig', compact('user'));

        $this->flash->addMessage('success', 'Check your email for your reset link.');
        
        return $response->withRedirect($this->router->pathFor('auth.forgot'));
    }

    public function getReset($request, $response, $args)
    {
        $token = $args['token'];

        return $this->view->render($response, 'auth/reset.twig', compact('token'));
    }

    public function postReset($request, $response, $args)
    {
        $user = User::where('reset_token', $args['token'])->first();
        $password = $request->getParam('password');

        $validation = $this->validator->validate($request, [
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.forgot'));
        }

        $user->password = password_hash($password, PASSWORD_DEFAULT);

        $user->save();

        $this->flash->addMessage('info', 'Your password was changed.');
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }
}
