<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Name;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\Name
 */
final class NameTest extends TestCase
{
    public function testSuccess(): void
    {
        $name = new Name($nickname = 'nickname12');

        self::assertEquals($nickname, $name->getNickname());
    }

    public function testCase(): void
    {
        $nickname = 'NickName';
        $name = new Name($upper = mb_strtoupper($nickname));

        self::assertNotEquals($nickname, $name->getNickname());
        self::assertEquals($upper, $name->getNickname());
    }

    /**
     * @dataProvider getCases
     */
    public function testInvalid(string $nickname): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name($nickname);
    }

    /**
     * @return iterable<array-key, array<array-key, string>>
     */
    public function getCases(): iterable
    {
        return [
            'short' => ['nic'],
            'large' => ['There are many variations of passages of Lorem Ipsum available'],
            'symbols' => ['nick@na\/me**'],
            'spaces' => ['nick na  me'],
        ];
    }
}
