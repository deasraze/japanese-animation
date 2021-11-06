<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Auth\Command\ChangePassword\Command;
use App\Auth\Command\ChangePassword\Handler;
use App\Security\Jwt\JWTUserIdentity;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/user/change/password', methods: ['PUT'])]
#[IsGranted('ROLE_USER')]
class ChangePasswordAction extends AbstractController
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
    ) {
    }

    public function __invoke(Command $command): Response
    {
        /** @var JWTUserIdentity $user */
        $user = $this->getUser();

        $command->id = $user->getId();

        $this->validator->validate($command);
        $this->handler->handle($command);

        return $this->json([]);
    }
}
