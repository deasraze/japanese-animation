<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Confirm\Command;
use App\Auth\Command\JoinByEmail\Confirm\Handler;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/join/confirm', name: self::class, methods: ['POST'])]
class ConfirmAction extends AbstractController
{
    public function __invoke(Validator $validator, Handler $handler, Command $command): Response
    {
        $validator->validate($command);

        $handler->handle($command);

        return $this->json([]);
    }
}
