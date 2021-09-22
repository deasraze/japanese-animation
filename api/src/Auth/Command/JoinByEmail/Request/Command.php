<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\GroupSequence({"Command", "Strict"})
 */
class Command
{
    /**
     * @Assert\NotBlank
     * @Assert\Email(groups={"Strict"})
     */
    public string $email = '';
    /**
     * @Assert\NotBlank
     * @Assert\Type("alnum", groups={"Strict"})
     * @Assert\Length(min=4, max=20, groups={"Strict"})
     */
    public string $nickname = '';
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=8, groups={"Strict"})
     */
    public string $password = '';
}
