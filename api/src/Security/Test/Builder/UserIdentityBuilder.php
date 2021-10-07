<?php

declare(strict_types=1);

namespace App\Security\Test\Builder;

use App\Security\UserIdentity;
use Ramsey\Uuid\Uuid;

class UserIdentityBuilder
{
    private string $id;
    private string $email;
    private string $password;
    private string $role;
    private bool $isActive = false;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->email = 'user-identity@example.com';
        $this->password = 'hash';
        $this->role = 'ROLE_USER';
    }

    public function active(): self
    {
        $clone = clone $this;

        $clone->isActive = true;

        return $clone;
    }

    public function withRole(string $role): self
    {
        $clone = clone $this;

        $clone->role = $role;

        return $clone;
    }

    public function build(): UserIdentity
    {
        return new UserIdentity(
            $this->id,
            $this->email,
            $this->password,
            $this->role,
            $this->isActive
        );
    }
}
