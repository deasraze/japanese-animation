<?php

declare(strict_types=1);

namespace App\Validator\Test;

use App\Validator\Validator;
use App\Validator\ValidatorException;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 * @covers \App\Validator\Validator
 */
final class ValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $command = new stdClass();

        $origin = $this->createMock(ValidatorInterface::class);
        $origin->expects(self::once())->method('validate')
            ->with(self::equalTo($command))
            ->willReturn(new ConstraintViolationList());

        $validator = new Validator($origin);
        $validator->validate($command);
    }

    public function testInvalid(): void
    {
        $command = new stdClass();

        $origin = $this->createMock(ValidatorInterface::class);
        $origin->expects(self::once())->method('validate')
            ->with(self::equalTo($command))
            ->willReturn($violations = new ConstraintViolationList([
                $this->createStub(ConstraintViolationInterface::class),
            ]));

        $validator = new Validator($origin);
        try {
            $validator->validate($command);

            self::fail('Expected exception is not thrown.');
        } catch (Exception $e) {
            self::assertInstanceOf(ValidatorException::class, $e);
            self::assertEquals($violations, $e->getViolations());
        }
    }
}
