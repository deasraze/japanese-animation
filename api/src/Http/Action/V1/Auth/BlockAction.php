<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Annotation\Guid;
use App\Auth\Command\Block\Command;
use App\Auth\Command\Block\Handler;
use App\Security\Jwt\JWTUserIdentity;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/auth/users/{id}/block', requirements: ['id' => Guid::PATTERN], methods: ['PUT'])]
#[IsGranted('ROLE_MANAGE_USERS')]
class BlockAction extends AbstractController
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(string $id): Response
    {
        /** @var JWTUserIdentity $user */
        $user = $this->getUser();

        if ($id === $user->getId()) {
            return $this->json([
                'message' => $this->translator->trans('error.block_yourself', [], 'auth'),
            ], 400);
        }

        $command = new Command();
        $command->id = $id;

        $this->validator->validate($command);
        $this->handler->handle($command);

        return $this->json([]);
    }
}
