<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangePassword;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Network;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChangePasswordFixture extends Fixture
{
    public const VIA_EMAIL = '00000000-0000-0000-0000-000000000001';
    public const VIA_NETWORK = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new Id(self::VIA_EMAIL))
            ->withEmail(new Email('via-email@app.test'))
            ->withName(new Name('ViaEmail'))
            ->withPasswordHash('$2y$13$p4xY4/WymQSlBxWMOzthR.YoQbNuEXYKVWRC4WRvEbbPrh3yFDZLO') // password
            ->active()
            ->build();
        $manager->persist($user);

        $user = (new UserBuilder())
            ->viaNetwork(new Network('change-password', '00001'))
            ->withId(new Id(self::VIA_NETWORK))
            ->withEmail(new Email('via-network@app.test'))
            ->withName(new Name('ViaNetwork'))
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
