<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class JoinConfirmationSender
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(Email $to, Token $token): void
    {
        $email = (new TemplatedEmail())
            ->subject('Join Confirmation')
            ->to($to->getValue())
            ->htmlTemplate('auth/join/confirm.html.twig')
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
