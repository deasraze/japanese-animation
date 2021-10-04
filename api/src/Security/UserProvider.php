<?php

declare(strict_types=1);

namespace App\Security;

use App\Auth\Query\FindIdentityByEmail\Fetcher;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private Fetcher $fetcher)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (null === $user = $this->fetcher->fetch($identifier)) {
            throw new UserNotFoundException();
        }

        return new UserIdentity(
            $user->id,
            $user->email,
            $user->passwordHash,
            $user->role
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return UserIdentity::class === $class || is_subclass_of($class, UserIdentity::class);
    }

    public function loadUserByUsername(string $username): void
    {
    }
}
