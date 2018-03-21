<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Mailgun\Mailgun;

class ContactController extends Controller
{
    public function index($request, $response, $args)
    {
        return $this->view->render($response, 'support.twig');
    }

    public function send($request, $response, $args)
    {
        $params = $request->getParams();
        $body = "Name: {$params['name']} <br> Email: {$params['email']} <br> Issue: {$params['subject']} <br> Message: {$params['message']}";

        $mg = Mailgun::create('key-430a3c205c21f327abdf2db317f386f2');
        $domain = 'https://fortisgroup.ca';

        $mg->messages()->send('fortisgroup.ca', [
            'from'    => 'communication@fortisgroup.ca',
            'to'      => 'josh@darkroast.co',
            'subject' => 'A support request from a Fortis user.',
            'html'    => $body
        ]);

        $this->flash->addMessage('info', 'Request Sent!');

        return $response->withRedirect($this->router->pathFor('support'));
    }
}

