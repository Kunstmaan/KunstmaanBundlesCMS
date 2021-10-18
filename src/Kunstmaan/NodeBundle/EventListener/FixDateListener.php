<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Fixes bug with date vs Date headers
 */
class FixDateListener
{
    /**
     * Make sure response has a timestamp
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response) {
            $date = $response->getDate();
            if (null === $date) {
                $response->setDate(new \DateTime());
            }
        }
    }
}
