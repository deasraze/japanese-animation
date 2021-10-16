<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangeRole;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChangeRoleFixture extends Fixture
{
    public const ADMIN = '00000000-0000-0000-0000-000000000001';
    public const USER = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new Id(self::USER))
            ->withEmail(new Email('user@app.test'))
            ->withName(new Name('User'))
            ->active()
            ->build();

        $manager->persist($user);

        $admin = (new UserBuilder())
            ->withId(new Id(self::ADMIN))
            ->withEmail(new Email('admin@app.test'))
            ->withName(new Name('Admin'))
            ->active()
            ->build();

        $admin->changeRole(new Role(Role::ADMIN));

        $manager->persist($admin);

        $manager->flush();
    }
}
