<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Auth\Command\Block;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/{id}/block', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
class BlockAction extends AbstractController
{
    public function __invoke(string $id, Block\Handler $handler): Response
    {
        $command = new Block\Command();
        $command->id = $id;

        $handler->handle($command);

        return $this->json([]);
    }
}
