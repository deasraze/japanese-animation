<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfirmFixture extends Fixture
{
    public const VALID = '00000000-0000-0000-0000-000000000001';
    public const EXPIRED = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $now = new DateTimeImmutable();

        $user = (new UserBuilder())
            ->withEmail(new Email('valid@app.test'))
            ->withName(new Name('Valid'))
            ->active()
            ->build();
        $user->requestEmailChanging(
            new Email('new-valid@app.test'),
            new Token(self::VALID, $now->modify('+1 hour')),
            $now
        );

        $manager->persist($user);

        $user = (new UserBuilder())
            ->withEmail(new Email('expired@app.test'))
            ->withName(new Name('Expired'))
            ->active()
            ->build();
        $user->requestEmailChanging(
            new Email('new-expired@app.test'),
            new Token(self::EXPIRED, $now->modify('-1 hour')),
            $now
        );

        $manager->persist($user);

        $manager->flush();
    }
}
