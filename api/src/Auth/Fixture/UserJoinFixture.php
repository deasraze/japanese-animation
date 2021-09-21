<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserJoinFixture extends Fixture
{
    // password
    private const HASH = '$2y$13$ccyFAR0s0C96hIFC2oi6N.BFOB82GA73HGG8Ij27Aro6Dn6s9NZ.O';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            Id::generate(),
            new DateTimeImmutable('-1 hours'),
            new Email('join-existing@app.test'),
            new Name('JoinExisting'),
            self::HASH,
            new Token('00000000-0000-0000-0000-100000000001', new DateTimeImmutable('+1 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
