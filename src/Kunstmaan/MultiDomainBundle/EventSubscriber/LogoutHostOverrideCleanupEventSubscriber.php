<?php

namespace Kunstmaan\MultiDomainBundle\EventSubscriber;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutHostOverrideCleanupEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'cleanupHostOverrideSession',
        ];
    }

    public function cleanupHostOverrideSession(LogoutEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $session = $request->getSession();
        if (!$session->has(DomainConfiguration::OVERRIDE_HOST)) {
            return;
        }

        // Remove host override
        $session->remove(DomainConfiguration::OVERRIDE_HOST);
    }
}
