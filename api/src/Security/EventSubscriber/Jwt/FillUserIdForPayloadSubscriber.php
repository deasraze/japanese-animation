<?php

declare(strict_types=1);

namespace App\Security\EventSubscriber\Jwt;

use App\Security\UserIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FillUserIdForPayloadSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var UserIdentity $user */
        $user = $event->getUser();
        $payload = $event->getData();

        $payload['id'] = $user->getId();

        $event->setData($payload);
    }
}
