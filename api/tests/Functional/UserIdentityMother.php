<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Security\Test\Builder\UserIdentityBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentityMother
{
    public static function admin(): UserInterface
    {
        return (new UserIdentityBuilder())
            ->withRole('ROLE_ADMIN')
            ->active()
            ->build();
    }

    public static function user(): UserInterface
    {
        return (new UserIdentityBuilder())
            ->active()
            ->build();
    }
}
