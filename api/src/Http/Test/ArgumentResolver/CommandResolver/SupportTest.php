<?php

declare(strict_types=1);

namespace App\Http\Test\ArgumentResolver\CommandResolver;

use App\Http\ArgumentResolver\CommandResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 * @covers \App\Http\ArgumentResolver\CommandResolver
 */
final class SupportTest extends TestCase
{
    public function testSuccess(): void
    {
        $request = Request::create('/test');
        $metadata = new ArgumentMetadata(
            'command',
            'Command',
            false,
            false,
            null
        );
        $denormalizer = $this->createStub(DenormalizerInterface::class);

        $resolver = new CommandResolver($denormalizer);

        self::assertTrue($resolver->supports($request, $metadata));
    }

    public function testNot(): void
    {
        $request = Request::create('/test');
        $metadata = new ArgumentMetadata(
            'not-command',
            'Command',
            false,
            false,
            null
        );
        $denormalizer = $this->createStub(DenormalizerInterface::class);

        $resolver = new CommandResolver($denormalizer);

        self::assertFalse($resolver->supports($request, $metadata));
    }
}
