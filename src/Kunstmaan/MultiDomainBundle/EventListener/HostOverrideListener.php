<?php

namespace Kunstmaan\MultiDomainBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HostOverrideListener
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var DomainConfigurationInterface
     */
    protected $domainConfiguration;

    /**
     * @param Session                      $session
     * @param TranslatorInterface          $translator
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(
        Session $session,
        TranslatorInterface $translator,
        DomainConfigurationInterface $domainConfiguration
    ) {
        $this->session             = $session;
        $this->translator          = $translator;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        if ($response instanceof RedirectResponse) {
            return;
        }

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (!$this->isAdminRoute($request->getRequestUri())) {
            return;
        }

        if ($request->getHost() !== $this->domainConfiguration->getHost()) {
            // Add flash message for admin pages
            $this->session->getFlashBag()->add(
                FlashTypes::WARNING,
                $this->translator->trans('multi_domain.host_override_active')
            );
        }
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    protected function isAdminRoute($url)
    {
        preg_match(
            '/^\/(app_(.*)\.php\/)?([a-zA-Z_-]{2,5}\/)?admin\/(.*)/',
            $url,
            $matches
        );

        // Check if path is part of admin area
        if (count($matches) === 0) {
            return false;
        }

        if (strpos($url, '/admin/preview') !== false) {
            return false;
        }

        return true;
    }
}
