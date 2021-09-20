<?php

declare(strict_types=1);

namespace App\Http\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name=HomeAction::class, methods={"GET"})
 */
class HomeAction
{
    public function __invoke(): Response
    {
        return new JsonResponse();
    }
}
