<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Security\Jwt\JWTUserIdentity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/user', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
class UserAction extends AbstractController
{
    public function __invoke(): Response
    {
        /** @var JWTUserIdentity $user */
        $user = $this->getUser();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'role' => $user->getRoles()[0],
        ]);
    }
}
