<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\ChangeEmail;

use App\Auth\Command\ChangeEmail\Request\Command;
use App\Auth\Command\ChangeEmail\Request\Handler;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/auth/user/change/email', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
class RequestAction extends AbstractController
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

        return $this->json([], 201);
    }
}
