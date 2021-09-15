<?php

declare(strict_types=1);

namespace App\Auth\Service;

use Webmozart\Assert\Assert;

class PasswordHasher
{
    private int $cost;

    public function __construct(int $cost = 13)
    {
        $this->cost = $cost;
    }

    public function hash(string $plainPassword): string
    {
        Assert::notEmpty($plainPassword);

        return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword) {
            return false;
        }

        return password_verify($plainPassword, $hashedPassword);
    }
}
