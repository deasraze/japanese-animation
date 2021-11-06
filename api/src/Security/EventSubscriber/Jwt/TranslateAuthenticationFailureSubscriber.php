<?php

declare(strict_types=1);

namespace App\Security\EventSubscriber\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslateAuthenticationFailureSubscriber implements EventSubscriberInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $response = $event->getResponse();

        $exception = $event->getException();
        $errorMessage = $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security');

        $event->setResponse(
            new JsonResponse(
                ['message' => $errorMessage],
                $response->getStatusCode(),
                $response->headers->all()
            )
        );
    }
}
