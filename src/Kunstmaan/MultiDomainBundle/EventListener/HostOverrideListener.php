<?php

namespace Kunstmaan\MultiDomainBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HostOverrideListener
{
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

    public function __construct(TranslatorInterface $translator, DomainConfigurationInterface $domainConfiguration, AdminRouteHelper $adminRouteHelper)
    {
        $this->translator = $translator;
        $this->domainConfiguration = $domainConfiguration;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
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

        if ($request->getHost() === $this->domainConfiguration->getHost()) {
            return;
        }

        // Add flash message for admin pages
        $request->getSession()->getFlashBag()->add(FlashTypes::WARNING, $this->translator->trans('multi_domain.host_override_active'));
    }
}
