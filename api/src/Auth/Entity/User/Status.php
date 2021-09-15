<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Status
{
    public const WAIT = 'wait';
    public const ACTIVE = 'active';
    public const BLOCKED = 'blocked';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::WAIT,
            self::ACTIVE,
            self::BLOCKED,
        ]);

        $this->name = $name;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this->name;
    }

    public function isWait(): bool
    {
        return self::WAIT === $this->name;
    }

    public function isBlocked(): bool
    {
        return self::BLOCKED === $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
