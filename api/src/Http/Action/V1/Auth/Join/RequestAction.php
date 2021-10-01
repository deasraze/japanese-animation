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
    public function __invoke(Validator $validator, Handler $handler, Command $command): Response
    {
        $validator->validate($command);

        $handler->handle($command);

        return $this->json([], 201);
    }
}
