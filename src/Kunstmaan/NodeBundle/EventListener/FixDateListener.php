<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Fixes bug with date vs Date headers
 */
class FixDateListener
{
    /**
     * Make sure response has a timestamp
     *
     * @param FilterResponseEvent|GetResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response) {
            $date = $response->getDate();
            if (empty($date)) {
                $response->setDate(new \DateTime());
            }
        }
    }
}
