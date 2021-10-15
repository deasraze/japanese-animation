<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Auth\Command\ChangePassword\Command;
use App\Auth\Command\ChangePassword\Handler;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/auth/user/password/change', methods: ['PUT'])]
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
        /** @var UserInterface $user */
        $user = $this->getUser();

        $command->id = $user->getUserIdentifier();

        $this->validator->validate($command);
        $this->handler->handle($command);

        return $this->json([]);
    }
}
