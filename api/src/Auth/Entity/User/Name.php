<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Name
{
    private string $nickname;

    public function __construct(string $nickname)
    {
        $nickname = str_replace(' ', '', $nickname);

        Assert::lengthBetween($nickname, 4, 20);
        Assert::alnum($nickname);

        $this->nickname = $nickname;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }
}
