<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public string $email = '';
    /**
     * @Assert\Type("alnum")
     * @Assert\Length(min=4, max=20)
     */
    public string $nickname = '';
    /**
     * @Assert\AtLeastOneOf({
     *     @Assert\NotBlank,
     *     @Assert\Length(min=8)
     * })
     */
    public string $password = '';
}
