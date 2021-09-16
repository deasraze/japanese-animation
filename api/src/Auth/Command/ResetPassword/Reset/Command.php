<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $token = '';
    /**
     * @Assert\AtLeastOneOf({
     *     @Assert\NotBlank,
     *     @Assert\Length(min=8)
     * })
     */
    public string $password = '';
}
