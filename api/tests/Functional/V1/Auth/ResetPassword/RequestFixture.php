<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ResetPassword;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('existing@app.test'))
            ->withName(new Name('existing'))
            ->active()
            ->build();

        $manager->persist($user);

        $user = (new UserBuilder())
            ->withEmail(new Email('wait@app.test'))
            ->withName(new Name('waiting'))
            ->build();

        $manager->persist($user);

        $manager->flush();
    }
}
