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

class UserJoinConfirmFixture extends Fixture
{
    // password
    private const HASH = '$2y$13$ccyFAR0s0C96hIFC2oi6N.BFOB82GA73HGG8Ij27Aro6Dn6s9NZ.O';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            Id::generate(),
            new DateTimeImmutable('-1 hours'),
            new Email('join-wait-active@app.test'),
            new Name('JoinWaitActive'),
            self::HASH,
            new Token('00000000-0000-0000-0000-200000000001', new DateTimeImmutable('+1 hours'))
        );

        $manager->persist($user);

        $user = User::requestJoinByEmail(
            Id::generate(),
            new DateTimeImmutable('-2 hours'),
            new Email('join-wait-expired@app.test'),
            new Name('JoinWaitExpired'),
            self::HASH,
            new Token('00000000-0000-0000-0000-200000000002', new DateTimeImmutable('-2 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
