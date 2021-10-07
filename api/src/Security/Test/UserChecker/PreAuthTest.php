<?php

declare(strict_types=1);

namespace App\Security\Test\UserChecker;

use App\Security\Test\Builder\UserIdentityBuilder;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 * @covers \App\Security\UserChecker
 */
final class PreAuthTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $identity = (new UserIdentityBuilder())
            ->active()
            ->build();

        $checker = new UserChecker();
        $checker->checkPreAuth($identity);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOtherObject(): void
    {
        $object = $this->createStub(UserInterface::class);

        $checker = new UserChecker();
        $checker->checkPreAuth($object);
    }

    public function testNotActive(): void
    {
        $identity = (new UserIdentityBuilder())
            ->build();

        $checker = new UserChecker();

        $this->expectExceptionMessage('Your account is not active.');
        $checker->checkPreAuth($identity);
    }
}
