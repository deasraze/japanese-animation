<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Name;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class JoinByNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::joinByNetwork(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('example@app.test'),
            $name = new Name('Example'),
            $network = new Network('vk', '00005')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($name, $user->getName());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0]);
    }
}
