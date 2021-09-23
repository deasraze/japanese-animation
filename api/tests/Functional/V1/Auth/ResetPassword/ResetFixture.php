<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ResetPassword;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ResetFixture extends Fixture
{
    public const VALID = '00000000-0000-0000-0000-000000000001';
    public const EXPIRED = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $date = new DateTimeImmutable();

        $user = (new UserBuilder())
            ->withEmail(new Email('valid@app.test'))
            ->withName(new Name('valid'))
            ->active()
            ->build();

        $user->requestResetPassword(
            new Token(self::VALID, $date->modify('+1 hour')),
            $date
        );

        $manager->persist($user);

        $user = (new UserBuilder())
            ->withEmail(new Email('expired@app.test'))
            ->withName(new Name('expired'))
            ->active()
            ->build();

        $user->requestResetPassword(
            new Token(self::EXPIRED, $date->modify('-1 hour')),
            $date
        );

        $manager->persist($user);

        $manager->flush();
    }
}
