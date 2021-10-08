<?php

declare(strict_types=1);

namespace App\Http\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CommandResolver implements ArgumentValueResolverInterface
{
    public function __construct(private DenormalizerInterface $denormalizer)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return 'command' === $argument->getName() && null !== $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var string $command */
        $command = $argument->getType();
        $data = [];

        if ($this->shouldHasRequestBody($request->getMethod())) {
            $data = $request->toArray();
        }

        yield $this->denormalizer->denormalize($data, $command, 'array');
    }

    private function shouldHasRequestBody(string $method): bool
    {
        return \in_array($method, ['POST', 'PUT', 'PATCH'], true);
    }
}
