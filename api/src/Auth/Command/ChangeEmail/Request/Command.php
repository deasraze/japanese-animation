<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $id = '';
    #[Assert\NotBlank, Assert\Email]
    public string $email = '';
}
