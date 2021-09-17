<?php

declare(strict_types=1);

namespace App\Frontend;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontendUrlTwigExtension extends AbstractExtension
{
    public function __construct(private FrontendUrlGenerator $generator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('frontend_url', [$this, 'url']),
        ];
    }

    public function url(string $uri, array $params = []): string
    {
        return $this->generator->generate($uri, $params);
    }
}
