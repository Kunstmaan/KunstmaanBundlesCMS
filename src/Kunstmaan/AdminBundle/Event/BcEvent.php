<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

// Clean up when sf 3.4 support is removed
if (is_subclass_of(EventDispatcherInterface::class, ContractsEventDispatcherInterface::class)) {
    abstract class BcEvent extends ContractsEvent
    {
    }
} else {
    abstract class BcEvent extends Event
    {
    }
}
