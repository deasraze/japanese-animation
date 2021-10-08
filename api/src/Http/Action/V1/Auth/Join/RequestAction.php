<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Request\Command;
use App\Auth\Command\JoinByEmail\Request\Handler;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/join', name: self::class, methods: ['POST'])]
class RequestAction extends AbstractController
{
    public function __construct(
        private Validator $validator,
        private Handler $handler,
    ) {
    }

    public function __invoke(Command $command): Response
    {
        $this->validator->validate($command);

        $this->handler->handle($command);

        return $this->json([], 201);
    }
}
