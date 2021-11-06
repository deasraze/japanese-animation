<?php

declare(strict_types=1);

namespace App\Security\Test;

use App\Security\UserIdentity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @covers \App\Security\UserIdentity
 */
final class UserIdentityTest extends TestCase
{
    public function testSuccess(): void
    {
        $identity = new UserIdentity(
            $id = Uuid::uuid4()->toString(),
            $email = 'user-identity@app.test',
            $password = 'hash',
            $role = 'ROLE_USER',
            true,
        );

        self::assertEquals($id, $identity->getId());
        self::assertEquals($email, $identity->getUserIdentifier());
        self::assertEquals($email, $identity->getUsername());
        self::assertEquals($password, $identity->getPassword());
        self::assertEquals([$role], $identity->getRoles());
        self::assertTrue($identity->isActive());

        self::assertNull($identity->getSalt());
    }
}
