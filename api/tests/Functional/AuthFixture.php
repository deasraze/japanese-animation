<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    public static function userIdentifier(): string
    {
        return 'auth-user@app.test';
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('auth-user@app.test'))
            ->withName(new Name('AuthUser'))
            ->withPasswordHash('$2y$13$p4xY4/WymQSlBxWMOzthR.YoQbNuEXYKVWRC4WRvEbbPrh3yFDZLO') // password
            ->active()
            ->build();

        $manager->persist($user);

        $manager->flush();
    }
}
