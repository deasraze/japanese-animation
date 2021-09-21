<?php

declare(strict_types=1);

namespace App\Http\Test\Response;

use App\Http\Response\DomainExceptionHandler;
use DomainException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 * @covers \App\Http\Response\DomainExceptionHandler
 */
final class DomainExceptionHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new DomainException($message = 'Some error')
        );

        $handler = new DomainExceptionHandler($logger);
        $handler->onKernelException($event);

        self::assertInstanceOf(JsonResponse::class, $response = $event->getResponse());
        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getContent());

        /** @var array $data */
        $data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        self::assertEquals(['message' => $message], $data);
    }

    public function testOtherException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new RuntimeException('Some error')
        );

        $handler = new DomainExceptionHandler($logger);
        $handler->onKernelException($event);

        self::assertNull($event->getResponse());
    }
}
