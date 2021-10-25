<?php

namespace Kunstmaan\MultiDomainBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HostOverrideListener
{
    /**
     * @var LegacyTranslatorInterface|TranslatorInterface
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

    public function __construct(
        /* TranslatorInterface */ $translator,
        DomainConfigurationInterface $domainConfiguration,
        AdminRouteHelper $adminRouteHelper
    ) {
        if (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('Argument "$translator" passed to "%s" must be of the type "%s" or "%s", "%s" given', __METHOD__, LegacyTranslatorInterface::class, TranslatorInterface::class, get_class($translator)));
        }

        $this->translator = $translator;
        $this->domainConfiguration = $domainConfiguration;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @param FilterResponseEvent|ResponseEvent $event
     */
    public function onKernelResponse($event)
    {
        if (!$event instanceof FilterResponseEvent && !$event instanceof ResponseEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(ResponseEvent::class) ? ResponseEvent::class : FilterResponseEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

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

        if ($request->getHost() === $this->domainConfiguration->getHost()) {
            return;
        }

        // Add flash message for admin pages
        $request->getSession()->getFlashBag()->add(FlashTypes::WARNING, $this->translator->trans('multi_domain.host_override_active'));
    }
}
