<?php

declare(strict_types=1);

namespace App\Container\Auth\Service;

use App\Auth\Service\Tokenizer;
use DateInterval;

class TokenizerFactory
{
    public static function create(string $interval): Tokenizer
    {
        return new Tokenizer(new DateInterval($interval));
    }
}
