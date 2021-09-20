<?php

declare(strict_types=1);

namespace App\Http\Test\Request\CommandResolver;

use App\Http\Request\CommandResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 * @covers \App\Http\Request\CommandResolver
 */
final class ResolveTest extends TestCase
{
    public function testPost(): void
    {
        $data = ['name' => 'John'];
        $request = Request::create('/test', 'POST', content: json_encode($data));
        $metadata = self::createArgumentMetadata();

        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects(self::once())->method('denormalize')
            ->willReturnCallback(static function (mixed $body, string $type, string $format = null) use ($data, $metadata): int {
                self::assertEquals($data, $body);
                self::assertEquals($metadata->getType(), $type);
                self::assertEquals('array', $format);

                return 1;
            });

        $resolver = new CommandResolver($denormalizer);

        $generator = $resolver->resolve($request, $metadata);
        $generator->current();
        $generator->next();
    }

    public function testGet(): void
    {
        $request = Request::create('/test', content: json_encode(['name' => 'John']));
        $metadata = self::createArgumentMetadata();

        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects(self::once())->method('denormalize')
            ->willReturnCallback(static function (mixed $body, string $type, string $format = null) use ($metadata): int {
                self::assertEquals([], $body);
                self::assertEquals($metadata->getType(), $type);
                self::assertEquals('array', $format);

                return 1;
            });

        $resolver = new CommandResolver($denormalizer);
        $resolver->resolve($request, $metadata)->current();
    }

    private static function createArgumentMetadata(): ArgumentMetadata
    {
        return new ArgumentMetadata(
            'command',
            'Command',
            false,
            false,
            null
        );
    }
}
