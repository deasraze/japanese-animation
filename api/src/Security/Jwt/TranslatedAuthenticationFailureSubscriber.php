<?php

declare(strict_types=1);

namespace App\Security\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatedAuthenticationFailureSubscriber implements EventSubscriberInterface
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

        if (!$response instanceof JWTAuthenticationFailureResponse) {
            return;
        }

        $exception = $event->getException();
        $errorMessage = $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security');

        $response->setMessage($errorMessage);
    }
}
