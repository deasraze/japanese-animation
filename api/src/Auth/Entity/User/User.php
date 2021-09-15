<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;

class User
{
    private Id $id;
    private DateTimeImmutable $date;
    private Email $email;
    private Name $name;
    private Status $status;
    private ?string $passwordHash = null;

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Name $name, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->name = $name;
        $this->status = $status;
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        string $passwordHash
    ): self {
        $user = new self($id, $date, $email, $name, Status::wait());

        $user->passwordHash = $passwordHash;

        return $user;
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }
}
