<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Network;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Auth\Entity\User\Network
 */
final class NetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $network = new Network($name = 'vk', $identity = '000001');

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network('', '000001');
    }

    public function testEmptyIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network('vk', '');
    }

    public function testCase(): void
    {
        $name = 'google';
        $identity = 'google-1';

        $network = new Network(mb_strtoupper($name), mb_strtoupper($identity));

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEqual(): void
    {
        $network = new Network('vk', '000001');

        self::assertTrue($network->isEqualTo(new Network('vk', '000001')));
        self::assertFalse($network->isEqualTo(new Network('vk', '000002')));
        self::assertFalse($network->isEqualTo(new Network('google', 'google-1')));
    }
}
