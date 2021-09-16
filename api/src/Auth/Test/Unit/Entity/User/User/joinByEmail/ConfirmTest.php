<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\joinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = self::createToken())
            ->build();

        self::assertFalse($user->isActive());
        self::assertTrue($user->isWait());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getJoinConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = self::createToken())
            ->build();

        $this->expectExceptionMessage('Token is invalid.');
        $user->confirmJoin(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = self::createToken())
            ->build();

        $this->expectExceptionMessage('Token is expired.');
        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = self::createToken())
            ->active()
            ->build();

        $this->expectExceptionMessage('Confirmation is not required.');
        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }

    private static function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
    }
}
