<?php

namespace Kunstmaan\LiveReloadBundle\EventListener;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class DisableCacheListener implements EventSubscriberInterface {

    protected $enabled;

    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->enabled) {
            return;
        }

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $response = $event->getResponse();
        $types = array('css' => 'text/css', 'js' => 'application/javascript');
        foreach ($types as $short => $long) {
            if (($response->headers->has('Content-Type') && strpos($response->headers->get('Content-Type'), $long) !== false)
                || $short == $request->getRequestFormat()
            ) {
                $response->headers->set('Cache-Control', 'no-cache');
                $response->headers->set('ETag', null);
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->setPrivate();
                $response->setMaxAge(0);
                $event->setResponse($response);
            }
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }

}
