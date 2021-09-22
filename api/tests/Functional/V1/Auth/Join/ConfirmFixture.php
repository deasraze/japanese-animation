<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Join;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfirmFixture extends Fixture
{
    public const VALID = '00000000-0000-0000-0000-000000000001';
    public const EXPIRED = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('confirm-valid@app.test'),
            new Name('ConfirmValid'),
            'password-hash',
            new Token(self::VALID, $date->modify('+1 hour'))
        );

        $manager->persist($user);

        $user = User::requestJoinByEmail(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('confirm-expired@app.test'),
            new Name('ConfirmExpired'),
            'password-hash',
            new Token(self::EXPIRED, $date->modify('-1 hour'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
