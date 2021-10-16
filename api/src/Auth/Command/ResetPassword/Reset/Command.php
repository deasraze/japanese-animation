<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['Command', 'Strict'])]
class Command
{
    #[Assert\NotBlank]
    public string $token = '';
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, groups: ['Strict'])]
    public string $password = '';
}
