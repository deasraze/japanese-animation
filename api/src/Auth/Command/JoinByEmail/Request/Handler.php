<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Auth\Service\Tokenizer;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Tokenizer $tokenizer,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        $name = new Name($command->nickname);

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('Email is already used.');
        }

        if ($this->users->hasByNickname($name)) {
            throw new DomainException('Nickname is already used.');
        }

        $date = new DateTimeImmutable();

        $user = User::requestJoinByEmail(
            Id::generate(),
            $date,
            $email,
            $name,
            $this->hasher->hash($command->password),
            $this->tokenizer->generate($date)
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}
