<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Status($name = Status::WAIT);

        self::assertEquals($name, $status->getName());
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('invalid');
    }

    public function testActive(): void
    {
        $status = Status::active();

        self::assertFalse($status->isWait());
        self::assertFalse($status->isBlocked());
        self::assertTrue($status->isActive());
    }

    public function testWait(): void
    {
        $status = Status::wait();

        self::assertFalse($status->isActive());
        self::assertFalse($status->isBlocked());
        self::assertTrue($status->isWait());
    }
}
