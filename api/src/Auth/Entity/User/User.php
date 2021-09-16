<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use DomainException;

class User
{
    private Id $id;
    private DateTimeImmutable $date;
    private Email $email;
    private Name $name;
    private ?string $passwordHash = null;
    private Status $status;
    private ?Token $joinConfirmToken = null;
    private Role $role;

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Name $name, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->name = $name;
        $this->status = $status;
        $this->role = Role::user();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        string $passwordHash,
        Token $token,
    ): self {
        $user = new self($id, $date, $email, $name, Status::wait());

        $user->passwordHash = $passwordHash;
        $user->joinConfirmToken = $token;

        return $user;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->joinConfirmToken) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmToken->validate($token, $date);

        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (null === $this->passwordHash) {
            throw new DomainException('User does not have an old password.');
        }

        if (!$hasher->verify($this->passwordHash, $current)) {
            throw new DomainException('Current password is incorrect.');
        }

        $this->passwordHash = $hasher->hash($new);
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new DomainException('Role is already same.');
        }

        $this->role = $role;
    }

    public function remove(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Unable to remove active user.');
        }
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isBlocked(): bool
    {
        return $this->status->isBlocked();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
