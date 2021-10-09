<?php

declare(strict_types=1);

namespace App\Http\Test\EventSubscriber;

use App\Http\EventSubscriber\DomainExceptionHandler;
use DomainException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 * @covers \App\Http\EventSubscriber\DomainExceptionHandler
 */
final class DomainExceptionHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/test'),
            HttpKernelInterface::MAIN_REQUEST,
            new DomainException($message = 'Some error.')
        );

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::once())->method('trans')->with(
            self::equalTo($message),
            self::equalTo([]),
            self::equalTo('exceptions')
        )->willReturn($translateMessage = 'Ошибка.');

        $handler = new DomainExceptionHandler($logger, $translator);
        $handler->onKernelException($event);

        self::assertInstanceOf(JsonResponse::class, $response = $event->getResponse());
        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getContent());

        /** @var array $data */
        $data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        self::assertEquals(['message' => $translateMessage], $data);
    }

    public function testOtherException(): void
    {
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/test'),
            HttpKernelInterface::MAIN_REQUEST,
            new RuntimeException('Some error.')
        );

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::never())->method('trans');

        $handler = new DomainExceptionHandler($logger, $translator);
        $handler->onKernelException($event);

        self::assertNull($event->getResponse());
    }
}
