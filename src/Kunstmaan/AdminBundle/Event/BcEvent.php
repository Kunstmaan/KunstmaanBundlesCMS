<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event;

if (!class_exists(Event::class)) {
    /**
     * Symfony 3.4
     *
     * @internal
     */
    abstract class BcEvent extends LegacyEvent
    {
    }
} else {
    /**
     * Symfony >= 4.3
     *
     * @internal
     */
    abstract class BcEvent extends Event
    {
    }
}
