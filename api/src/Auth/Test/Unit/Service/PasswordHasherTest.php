<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Service\PasswordHasher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Service\PasswordHasher
 */
final class PasswordHasherTest extends TestCase
{
    public function testHash(): void
    {
        $hasher = new PasswordHasher(8);

        $hash = $hasher->hash($password = 'password');

        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    public function testVerify(): void
    {
        $hasher = new PasswordHasher(8);

        $hash = $hasher->hash($password = 'password');

        self::assertTrue($hasher->verify($hash, $password));
        self::assertFalse($hasher->verify($hash, 'other-password'));
    }

    public function testVerifyEmpty(): void
    {
        $hasher = new PasswordHasher(8);

        $hash = $hasher->hash('password');

        self::assertFalse($hasher->verify($hash, ''));
    }

    public function testEmpty(): void
    {
        $hasher = new PasswordHasher(8);

        $this->expectException(InvalidArgumentException::class);
        $hasher->hash('');
    }
}
