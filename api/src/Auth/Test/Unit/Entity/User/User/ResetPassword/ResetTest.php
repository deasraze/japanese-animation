<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);
        $user->resetPassword($token->getValue(), $hash = 'new-hash', $now);

        self::assertEquals($hash, $user->getPasswordHash());
        self::assertNull($user->getResetPasswordToken());
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $this->expectExceptionMessage('Reset password was not requested.');
        $user->resetPassword(Uuid::uuid4()->toString(), 'new-hash', new DateTimeImmutable());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);

        $this->expectExceptionMessage('Token is invalid.');
        $user->resetPassword('invalid-token', 'new-hash', $now);
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);

        $this->expectExceptionMessage('Token is expired.');
        $user->resetPassword($token->getValue(), 'new-hash', $now->modify('+2 days'));
    }

    private static function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
