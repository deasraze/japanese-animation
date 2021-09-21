<?php

declare(strict_types=1);

namespace App\Validator\Test;

use App\Validator\ValidatorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 * @covers \App\Validator\ValidatorException
 */
final class ValidatorExceptionTest extends TestCase
{
    public function testSuccess(): void
    {
        $exception = new ValidatorException($violations = new ConstraintViolationList([]));

        self::assertEquals('Invalid input.', $exception->getMessage());
        self::assertEquals($violations, $exception->getViolations());
    }
}
