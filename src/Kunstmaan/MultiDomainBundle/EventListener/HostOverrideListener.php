<?php

namespace Kunstmaan\MultiDomainBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
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
     * @var AdminRouteHelper
     */
    protected $adminRouteHelper;

    /**
     * @param Session                      $session
     * @param TranslatorInterface          $translator
     * @param DomainConfigurationInterface $domainConfiguration
     * @param AdminRouteHelper $adminRouteHelper
     */
    public function __construct(
        Session $session,
        TranslatorInterface $translator,
        DomainConfigurationInterface $domainConfiguration,
        AdminRouteHelper $adminRouteHelper
    ) {
        $this->session             = $session;
        $this->translator          = $translator;
        $this->domainConfiguration = $domainConfiguration;
        $this->adminRouteHelper    = $adminRouteHelper;
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

        if (!$this->adminRouteHelper->isAdminRoute($request->getRequestUri())) {
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
}
