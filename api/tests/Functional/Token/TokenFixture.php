<?php

declare(strict_types=1);

namespace App\Tests\Functional\Token;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TokenFixture extends Fixture
{
    public static function activeUserEmail(): string
    {
        return 'active-user@app.test';
    }

    public static function waitUserEmail(): string
    {
        return 'wait-user@app.test';
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('active-user@app.test'))
            ->withName(new Name('ActiveUser'))
            ->withPasswordHash('$2y$13$p4xY4/WymQSlBxWMOzthR.YoQbNuEXYKVWRC4WRvEbbPrh3yFDZLO') // password
            ->active()
            ->build();
        $manager->persist($user);

        $user = (new UserBuilder())
            ->withEmail(new Email('wait-user@app.test'))
            ->withName(new Name('WaitUser'))
            ->withPasswordHash('$2y$13$p4xY4/WymQSlBxWMOzthR.YoQbNuEXYKVWRC4WRvEbbPrh3yFDZLO') // password
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
