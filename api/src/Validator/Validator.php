<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $object): void
    {
        $violations = $this->validator->validate($object);

        if (0 !== $violations->count()) {
            throw new ValidatorException($violations);
        }
    }
}
