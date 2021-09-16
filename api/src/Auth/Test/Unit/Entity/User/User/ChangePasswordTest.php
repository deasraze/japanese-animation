<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user->changePassword('old-password', 'new-password', $hasher);

        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testWrongCurrent(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('Current password is incorrect.');
        $user->changePassword('wrong-current-password', 'new-password', $hasher);
    }

    private function createHasher(bool $valid, string $hash): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);

        $hasher->method('hash')->willReturn($hash);
        $hasher->method('verify')->willReturn($valid);

        return $hasher;
    }
}
