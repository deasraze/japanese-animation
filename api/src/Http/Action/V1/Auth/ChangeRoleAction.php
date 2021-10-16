<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Annotation\Guid;
use App\Auth\Command\ChangeRole\Command;
use App\Auth\Command\ChangeRole\Handler;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/auth/users/{id}/role', requirements: ['id' => Guid::PATTERN], methods: ['PUT'])]
#[IsGranted('ROLE_MANAGE_USERS')]
class ChangeRoleAction extends AbstractController
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(string $id, Command $command): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($id === $user->getUserIdentifier()) {
            return $this->json([
                'message' => $this->translator->trans('error.role_yourself', [], 'auth'),
            ], 400);
        }

        $command->id = $id;

        $this->validator->validate($command);
        $this->handler->handle($command);

        return $this->json([]);
    }
}
