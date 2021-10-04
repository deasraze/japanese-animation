<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private string $id,
        private string $username,
        private string $password,
        private string $role
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): void
    {
    }
}
