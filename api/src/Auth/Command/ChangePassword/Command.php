<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangePassword;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $id = '';
    /**
     * @Assert\AtLeastOneOf({
     *     @Assert\NotBlank,
     *     @Assert\Length(min=8)
     * })
     */
    public string $current = '';
    /**
     * @Assert\AtLeastOneOf({
     *     @Assert\NotBlank,
     *     @Assert\Length(min=8)
     * })
     */
    public string $new = '';
}
