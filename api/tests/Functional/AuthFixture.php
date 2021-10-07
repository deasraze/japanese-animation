<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    /**
     * @return array{identifier: string, password: string}
     */
    public static function userCredentials(): array
    {
        return [
            'identifier' => 'auth-user@app.test',
            'password' => 'password',
        ];
    }

    /**
     * @return array{identifier: string, password: string}
     */
    public static function adminCredentials(): array
    {
        return [
            'identifier' => 'auth-admin@app.test',
            'password' => 'password',
        ];
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

        $admin = (new UserBuilder())
            ->withEmail(new Email('auth-admin@app.test'))
            ->withName(new Name('AuthAdmin'))
            ->withPasswordHash('$2y$13$p4xY4/WymQSlBxWMOzthR.YoQbNuEXYKVWRC4WRvEbbPrh3yFDZLO') // password
            ->active()
            ->build();
        $admin->changeRole(new Role(Role::ADMIN));

        $manager->persist($admin);
        $manager->flush();
    }
}
