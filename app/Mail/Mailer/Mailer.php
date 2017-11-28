<?php

namespace App\Mail\Mailer;

use Slim\Views\Twig;

class Mailer
{

    protected $twig;

    public function __construct($mail, Twig $twig)
    {
        $this->mail = $mail;
        $this->twig = $twig;
    }

    public function sdf ()
    {
        dump($this->to['receipiants']);
        dump($this->from['name']);
        die;
    }

    public function to($receipiants = [])
    {
        $this->to = compact('receipiants');

        return $this;
    }

    public function alwaysFrom($address, $name = null)
    {
        $this->from = compact('address', 'name');

        return $this;
    }

    public function from($address, $name = null)
    {
        $this->from = compact('address', 'name');

        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function send($view, $viewData = null)
    {
        $this->mail->setFrom($this->from['address'], $this->from['name']);

        foreach ($this->to['receipiants'] as $receipiant) {
            $this->mail->addAddress($receipiant['email'], $receipiant['name']);
        }

        $this->mail->isHTML(true);

        $this->mail->Subject = $this->subject;
        $this->mail->Body = $this->parseView($view, $viewData);

        if(!$this->mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $this->mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

    protected function parseView($view, $viewData)
    {
        return $this->twig->fetch($view, $viewData);
    }
}
