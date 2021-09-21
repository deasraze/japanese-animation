<?php

declare(strict_types=1);

namespace App\Http\Test\Response;

use App\Http\Response\ValidationExceptionHandler;
use App\Validator\ValidatorException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 * @covers \App\Http\Response\ValidationExceptionHandler
 */
final class ValidationExceptionHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation('Invalid Nickname', null, [], null, 'nickname', 'nick'),
            new ConstraintViolation('Incorrect Email', null, [], null, 'email', 'not-email'),
        ]);

        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/test'),
            HttpKernelInterface::MAIN_REQUEST,
            new ValidatorException($violations),
        );

        $handler = new ValidationExceptionHandler();
        $handler->onKernelException($event);

        self::assertInstanceOf(JsonResponse::class, $response = $event->getResponse());
        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string) $response->getContent());

        /** @var array $data */
        $data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        self::assertEquals([
            'errors' => [
                'nickname' => 'Invalid Nickname',
                'email' => 'Incorrect Email',
            ],
        ], $data);
    }

    public function testOtherException(): void
    {
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/test'),
            HttpKernelInterface::MAIN_REQUEST,
            new DomainException(),
        );

        $handler = new ValidationExceptionHandler();
        $handler->onKernelException($event);

        self::assertNull($event->getResponse());
    }
}
