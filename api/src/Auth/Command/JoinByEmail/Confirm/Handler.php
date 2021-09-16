<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    public function __construct(private UserRepository $users, private Flusher $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $token = $command->token;

        if (!$user = $this->users->findByJoinConfirmToken($token)) {
            throw new DomainException('Token is invalid.');
        }

        $user->confirmJoin($token, new DateTimeImmutable());

        $this->flusher->flush();
    }
}
