<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Trait EventDispatcherTrait
 */
trait EventDispatcherTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @required
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if (null !== $this->container && null === $this->eventDispatcher) {
            $this->eventDispatcher = $this->container->get('event_dispatcher');
        }

        return $this->eventDispatcher;
    }
}
