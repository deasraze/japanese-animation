<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\ResetPassword;

use App\Auth\Command\ResetPassword\Reset\Command;
use App\Auth\Command\ResetPassword\Reset\Handler;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth/password/reset/confirm", name=ResetAction::class, methods={"POST"})
 */
class ResetAction extends AbstractController
{
    public function __invoke(Validator $validator, Handler $handler, Command $command): Response
    {
        $validator->validate($command);

        $handler->handle($command);

        return $this->json([]);
    }
}
