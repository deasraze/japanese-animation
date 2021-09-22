<?php

declare(strict_types=1);

namespace App\Tests\Functional;

final class Json
{
    public static function decode(string $json): array
    {
        /** @var array */
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    public static function encode(mixed $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
