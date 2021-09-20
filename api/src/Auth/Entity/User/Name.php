<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private string $nickname;

    public function __construct(string $nickname)
    {
        Assert::lengthBetween($nickname, 4, 20);
        Assert::alnum($nickname);

        $this->nickname = $nickname;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }
}
