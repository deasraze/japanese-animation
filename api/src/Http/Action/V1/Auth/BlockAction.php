<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Annotation\Guid;
use App\Auth\Command\Block\Command;
use App\Auth\Command\Block\Handler;
use App\Validator\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/{id}/block', requirements: ['id' => Guid::PATTERN], methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
class BlockAction extends AbstractController
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
    ) {
    }

    public function __invoke(string $id): Response
    {
        $command = new Command();
        $command->id = $id;

        $this->validator->validate($command);
        $this->handler->handle($command);

        return $this->json([]);
    }
}
