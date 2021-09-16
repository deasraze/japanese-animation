<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class BlockTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        self::assertTrue($user->isActive());

        $user->block();

        self::assertFalse($user->isActive());
        self::assertTrue($user->isBlocked());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $user->block();

        $this->expectExceptionMessage('User is already blocked.');
        $user->block();
    }
}
