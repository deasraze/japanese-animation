<?php

declare(strict_types=1);

namespace App\Http\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PreferredLocaleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 16]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($preferredLanguage = $event->getRequest()->getPreferredLanguage()) {
            $event->getRequest()->setLocale($preferredLanguage);
        }
    }
}
