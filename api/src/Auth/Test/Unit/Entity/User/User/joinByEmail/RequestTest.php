<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\joinByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::requestJoinByEmail(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@app.test'),
            $name = new Name('nickname'),
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($name, $user->getName());
    }
}
