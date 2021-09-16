<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Role;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\Role
 */
final class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $role = new Role($name = Role::ADMIN);

        self::assertEquals($name, $role->getName());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Role('incorrect');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Role('');
    }

    public function testUser(): void
    {
        $role = Role::user();

        self::assertTrue($role->isUser());
    }

    public function testEqual(): void
    {
        $roleUser = Role::user();
        $roleAdmin = new Role(Role::ADMIN);

        self::assertFalse($roleUser->isEqualTo($roleAdmin));
        self::assertTrue($roleUser->isEqualTo(Role::user()));
    }
}
