<?php

declare(strict_types=1);

namespace App\Auth\Query\FindIdentityByEmail;

class Identity
{
    public function __construct(
        public string $id,
        public string $email,
        public string $passwordHash,
        public string $role
    ) {
    }
}
