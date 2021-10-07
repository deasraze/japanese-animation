<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private DateTimeImmutable $date;
    private Email $email;
    private Name $name;
    private string $passwordHash;
    private Token $joinConfirmToken;
    private bool $active = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new DateTimeImmutable();
        $this->email = new Email('mail@example.com');
        $this->name = new Name('nickname');
        $this->passwordHash = 'hash';
        $this->joinConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;

        $clone->id = $id;

        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;

        $clone->email = $email;

        return $clone;
    }

    public function withName(Name $name): self
    {
        $clone = clone $this;

        $clone->name = $name;

        return $clone;
    }

    public function withPasswordHash(string $hash): self
    {
        $clone = clone $this;

        $clone->passwordHash = $hash;

        return $clone;
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;

        $clone->joinConfirmToken = $token;

        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;

        $clone->active = true;

        return $clone;
    }

    public function build(): User
    {
        $user = User::requestJoinByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->name,
            $this->passwordHash,
            $this->joinConfirmToken
        );

        if (true === $this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->getValue(),
                $this->joinConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}
