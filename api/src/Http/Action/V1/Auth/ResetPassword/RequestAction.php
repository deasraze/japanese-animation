<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\ResetPassword;

use App\Auth\Command\ResetPassword\Request\Command;
use App\Auth\Command\ResetPassword\Request\Handler;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/password/reset/request', name: self::class, methods: ['POST'])]
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
