<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;


/**
 * AdminLocaleListener to override default locale if user-specific locale is set in database
 */
class AdminLocaleListener implements EventSubscriberInterface
{
    /** @var SecurityContext $context */
    private $context;
    /** @var TranslatorInterface $translator */
    private $translator;

    private $container;

    /**
     * constructor
     *
     * @param SecurityContext $context
     * @param TranslatorInterface $translator
     */
    public function __construct(SecurityContext $context, TranslatorInterface $translator, Container $container)
    {
        $this->translator = $translator;
        $this->context = $context;
        $this->container = $container;
    }

    /**
     * onKernelView event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelView(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();

        if ($this->context->getToken()) {
            $locale = $this->context->getToken()->getUser()->getAdminLocale();

            preg_match("/^\/(app_(.*)\.php\/)?([a-z][a-z]\/)?admin\/(.*)/", $url, $match);

            if (count($match)) {
                if (!$locale) { // if adminlocale is not specified for this user
                    if ($this->container->hasParameter('defaultadminlocale')) { // if default adminlocale exists
                        // use default adminlocale
                        $locale = $this->container->getParameter('defaultadminlocale');
                    } else {
                        // use defaultlocale
                        $locale = $this->container->getParameter('defaultlocale');
                    }
                }
                $this->translator->setLocale($locale);
            }
        }

    }

    /**
     * getSubscribedEvents
     */
    static public function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
