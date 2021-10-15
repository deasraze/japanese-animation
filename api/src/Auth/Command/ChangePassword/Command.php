<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangePassword;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['Command', 'Strict'])]
class Command
{
    #[Assert\NotBlank]
    public string $id = '';
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, groups: ['Strict'])]
    public string $current = '';
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, groups: ['Strict'])]
    public string $new = '';
}
