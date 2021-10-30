<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Ramsey\Uuid\Uuid;

class UserNetwork
{
    private string $id;
    private User $user;
    private Network $network;

    public function __construct(User $user, Network $network)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->network = $network;
    }

    public function getNetwork(): Network
    {
        return $this->network;
    }
}
