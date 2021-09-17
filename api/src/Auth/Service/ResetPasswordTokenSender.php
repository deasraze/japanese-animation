<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ResetPasswordTokenSender
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(Email $to, Token $token): void
    {
        $email = (new TemplatedEmail())
            ->subject('Resetting Password')
            ->to($to->getValue())
            ->htmlTemplate('auth/password/confirm.html.twig')
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
