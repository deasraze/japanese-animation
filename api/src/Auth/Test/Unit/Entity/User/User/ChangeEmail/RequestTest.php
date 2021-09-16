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
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-email@app.test'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            $new = new Email('new-email@app.test'),
            $token,
            $now
        );

        self::assertNotNull($user->getNewEmailToken());
        self::assertEquals($token, $user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('User is not active.');
        $user->requestEmailChanging(
            new Email('new-email@app.test'),
            $token,
            $now
        );
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-email@app.test'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Email is already same.');
        $user->requestEmailChanging($old, $token, $now);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('old-email@app.test'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            $new = new Email('new-email@app.test'),
            $token,
            $now
        );

        $this->expectExceptionMessage('Email changing is already requested.');
        $user->requestEmailChanging($new, $token, $now->modify('+1 hour'));
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('old-email@app.test'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging(
            $new = new Email('new-email@app.test'),
            $token,
            $now
        );

        $newDate = $now->modify('+1 day +5 hours');
        $newToken = self::createToken($now->modify('+1 day'));

        $user->requestEmailChanging($new, $newToken, $newDate);

        self::assertEquals($newToken, $user->getNewEmailToken());
    }

    private static function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
