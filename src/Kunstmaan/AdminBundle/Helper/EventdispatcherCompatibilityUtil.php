<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * NEXT_MAJOR remove when sf4.4 support is dropped.
 *
 * @internal
 */
final class EventdispatcherCompatibilityUtil
{
    public static function upgradeEventDispatcher(EventDispatcherInterface $eventDispatcher): EventDispatcherInterface
    {
        // On Symfony 5.0+, the legacy proxy is a no-op and it is deprecated in 5.1+
        // Detecting the parent class of GenericEvent (which changed in 5.0) allows to avoid using the deprecated no-op API.
        if (is_subclass_of(GenericEvent::class, Event::class)) {
            return $eventDispatcher;
        }

        // BC layer for Symfony 4.4 where we need to apply the decorating proxy in case of non-upgraded dispatcher.
        return LegacyEventDispatcherProxy::decorate($eventDispatcher);
    }
}
