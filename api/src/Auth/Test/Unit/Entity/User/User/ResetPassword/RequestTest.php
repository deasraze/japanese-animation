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
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);

        self::assertNotNull($user->getResetPasswordToken());
        self::assertEquals($token, $user->getResetPasswordToken());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);

        $this->expectExceptionMessage('Password reset is already requested.');
        $user->requestResetPassword($token, $now);
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())
            ->build();

        self::assertTrue($user->isWait());

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('User is not active.');
        $user->requestResetPassword($token, $now);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestResetPassword($token, $now);

        $newDate = $now->modify('+1 day +1 hour');
        $newToken = self::createToken($newDate->modify('+1 day'));

        $user->requestResetPassword($newToken, $newDate);

        self::assertEquals($newToken, $user->getResetPasswordToken());
    }

    private static function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
