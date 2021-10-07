<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Block;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BlockFixture extends Fixture
{
    public const ACTIVE = '00000000-0000-0000-0000-000000000001';
    public const BLOCKED = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $active = (new UserBuilder())
            ->withId(new Id('00000000-0000-0000-0000-000000000001'))
            ->withEmail(new Email('active-user@app.test'))
            ->withName(new Name('ActiveUser'))
            ->active()
            ->build();
        $manager->persist($active);

        $blocked = (new UserBuilder())
            ->withId(new Id('00000000-0000-0000-0000-000000000002'))
            ->withEmail(new Email('blocked-user@app.test'))
            ->withName(new Name('BlockedUser'))
            ->build();

        $blocked->block();
        $manager->persist($blocked);

        $manager->flush();
    }
}
