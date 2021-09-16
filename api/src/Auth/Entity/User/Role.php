<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::ADMIN,
        ]);

        $this->name = $name;
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public function isEqualTo(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function isUser(): bool
    {
        return $this->name === self::USER;
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
