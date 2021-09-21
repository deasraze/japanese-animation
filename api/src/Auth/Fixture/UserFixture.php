<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class UserFixture extends Fixture
{
    // password
    private const HASH = '$2y$13$ccyFAR0s0C96hIFC2oi6N.BFOB82GA73HGG8Ij27Aro6Dn6s9NZ.O';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new DateTimeImmutable('-30 days'),
            new Email('admin@app.test'),
            new Name('Admin'),
            self::HASH,
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date);
        $user->changeRole(new Role(Role::ADMIN));

        $manager->persist($user);

        $user = User::requestJoinByEmail(
            new Id('00000000-0000-0000-0000-000000000002'),
            $date = new DateTimeImmutable('-28 days'),
            new Email('user@app.test'),
            new Name('user001'),
            self::HASH,
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date);

        $manager->persist($user);
    }
}
