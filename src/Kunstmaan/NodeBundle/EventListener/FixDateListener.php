<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Fixes bug with date vs Date headers
 */
class FixDateListener
{
    /**
     * Make sure response has a timestamp
     *
     * @param FilterResponseEvent|ResponseEvent $event
     */
    public function onKernelResponse($event)
    {
        if (!$event instanceof FilterResponseEvent && !$event instanceof ResponseEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(ResponseEvent::class) ? ResponseEvent::class : FilterResponseEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

        $response = $event->getResponse();
        if ($response) {
            $date = $response->getDate();
            if (null === $date) {
                $response->setDate(new \DateTime());
            }
        }
    }
}
