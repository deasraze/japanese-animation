<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::email($value);

        $this->value = mb_strtolower($value);
    }

    public function isEqualTo(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
