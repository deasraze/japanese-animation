<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Network;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\User
 */
final class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        self::assertCount(0, $user->getNetworks());

        $network = new Network('google', 'google-1');

        $user->attachNetwork($network);

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0]);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->active()->build();

        $network = new Network('google', 'google-1');

        $user->attachNetwork($network);

        $this->expectExceptionMessage('Network is already attached.');
        $user->attachNetwork($network);
    }
}
