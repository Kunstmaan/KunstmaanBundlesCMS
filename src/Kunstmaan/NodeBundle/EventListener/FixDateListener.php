<?php

namespace Kunstmaan\ViewBundle\EventListener;

use Gedmo\Exception\RuntimeException;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

/**
* this fixes the bug with date vs Date headers
*
*/
class FixDateListener
{
   /**
* @param GetResponseEvent $event
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