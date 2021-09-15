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

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->name = $name;
    }

    public static function requestJoinByEmail(Id $id, DateTimeImmutable $date, Email $email, Name $name): self
    {
        return new self($id, $date, $email, $name);
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
}
