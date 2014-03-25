<?php

namespace Kunstmaan\AdminBundle\EventListener

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;


class AdminLocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    private $translator;


    public function __construct(TranslatorInterface $translator, $defaultLocale = 'en')
    {
        $this->defaultLocale    = $defaultLocale;
        $this->translator       = $translator;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();

        preg_match("/^\/(app_(.*)\.php\/)?[a-z][a-z]\/admin\/(.*)/", $url, $match);

        if (count($match)) {
            $this->translator->setLocale($this->defaultLocale);
        }
    }


    static public function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
