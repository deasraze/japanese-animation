<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\NewEmailTokenSender;
use App\Auth\Service\Tokenizer;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    public function __construct(
        private UserRepository $users,
        private Tokenizer $tokenizer,
        private NewEmailTokenSender $sender,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('Email is already used.');
        }

        $date = new DateTimeImmutable();

        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate($date),
            $date
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
