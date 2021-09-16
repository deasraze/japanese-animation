<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
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
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            $new = new Email('new-email@app.test'),
            $token,
            $now
        );

        $user->confirmEmailChanging($token->getValue(), $now);

        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
        self::assertEquals($new, $user->getEmail());
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Email changing was not requested.');
        $user->confirmEmailChanging($token->getValue(), $now);
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            new Email('new-email@app.test'),
            $token,
            $now
        );

        $this->expectExceptionMessage('Token is invalid.');
        $user->confirmEmailChanging(Uuid::uuid4()->toString(), $now);
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            new Email('new-email@app.test'),
            $token,
            $now
        );

        $this->expectExceptionMessage('Token is expired.');
        $user->confirmEmailChanging($token->getValue(), $now->modify('+1 day +10 sec'));
    }

    private static function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
