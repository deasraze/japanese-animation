<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\joinByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
            $hash = 'hash',
            $token = new Token(
                Uuid::uuid4()->toString(),
                new DateTimeImmutable('+1 hour')
            )
        );

        self::assertFalse($user->isActive());
        self::assertFalse($user->isBlocked());

        self::assertTrue($user->isWait());
        self::assertTrue($user->getRole()->isUser());

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($name, $user->getName());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getJoinConfirmToken());
    }
}
