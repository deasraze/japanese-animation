<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Security\Test\Builder\UserIdentityBuilder;

class UserIdentityMother
{
    public static function admin(): UserIdentityBuilder
    {
        return (new UserIdentityBuilder())
            ->withRole('ROLE_ADMIN')
            ->active();
    }

    public static function user(): UserIdentityBuilder
    {
        return (new UserIdentityBuilder())
            ->active();
    }
}
