<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;



class AdminLocaleListener implements EventSubscriberInterface
{
    private $translator;
    private $context;


    public function __construct(SecurityContext $context, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->context = $context;
    }

    public function onKernelView(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();

        if ($this->context->getToken()) {
            $locale = $this->context->getToken()->getUser()->getLocaleAdmin();

            preg_match("/^\/(app_(.*)\.php\/)?[a-z][a-z]\/admin\/(.*)/", $url, $match);

            if (count($match) && $locale) {
                $this->translator->setLocale($locale);
            }
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
