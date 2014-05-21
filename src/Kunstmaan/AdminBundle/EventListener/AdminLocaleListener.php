<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * AdminLocaleListener to override default locale if user-specific locale is set in database
 */
class AdminLocaleListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext $context
     */
    private $context;

    /**
     * @var TranslatorInterface $translator
     */
    private $translator;

    /**
     * @var string $defaultadminlocale
     */
    private $defaultAdminlocale;

    /**
     * @param SecurityContext     $context
     * @param TranslatorInterface $translator
     * @param string              $defaultAdminLocale
     */
    public function __construct(SecurityContext $context, TranslatorInterface $translator, $defaultAdminLocale)
    {
        $this->translator         = $translator;
        $this->context            = $context;
        $this->defaultAdminlocale = $defaultAdminLocale;
    }

    /**
     * onKernelRequest
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            // return immediately
            return;
        }

        $url = $event->getRequest()->getRequestUri();

        if ($this->context->getToken()) {
            preg_match('/^\/(app_(.*)\.php\/)?([a-zA-Z_-]{2,5}\/)?admin\/(.*)/', $url, $match);

            if (count($match)) {
                $locale = $this->context->getToken()->getUser()->getAdminLocale();

                if (!$locale) {
                    $locale = $this->defaultAdminlocale;
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
            // Must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
