<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Remove;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RemoveFixture extends Fixture
{
    public const ACTIVE = '00000000-0000-0000-0000-000000000001';
    public const WAIT = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $active = (new UserBuilder())
            ->withId(new Id(self::ACTIVE))
            ->withEmail(new Email('active@app.test'))
            ->withName(new Name('ActiveUser'))
            ->active()
            ->build();
        $manager->persist($active);

        $wait = (new UserBuilder())
            ->withId(new Id(self::WAIT))
            ->withEmail(new Email('wait@app.test'))
            ->withName(new Name('WaitUser'))
            ->build();
        $manager->persist($wait);

        $manager->flush();
    }
}
