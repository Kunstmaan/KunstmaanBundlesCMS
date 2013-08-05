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
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (!$this->enabled
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'text/css'))
            || 'css' !== $request->getRequestFormat()
        ) {
            return;
        }

        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('ETag', null);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setPrivate();
        $response->setMaxAge(0);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }

}
