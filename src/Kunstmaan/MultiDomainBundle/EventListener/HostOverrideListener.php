<?php

namespace Kunstmaan\MultiDomainBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HostOverrideListener
{
    /**
     * @deprecated since KunstmaanMultiDomainBundle 5.10 and will be removed in KunstmaanMultiDomainBundle 6.0.
     *
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

    public function __construct(
        /* Session $session,*/
        /* TranslatorInterface */ $translator,
        /* DomainConfigurationInterface */ $domainConfiguration,
        /* AdminRouteHelper */ $adminRouteHelper
    ) {
        if (func_num_args() > 3) {
            @trigger_error(sprintf('Passing 4 arguments and a service instance of "%s" as the first argument in "%s" is deprecated since KunstmaanMultiDomainBundle 5.10 and the constructor signature will change in KunstmaanMultiDomainBundle 6.0. Remove the first argument and inject the required services instead.', SessionInterface::class, __METHOD__), E_USER_DEPRECATED);

            //Legacy constructor
            if (!$translator instanceof SessionInterface) {
                throw new \InvalidArgumentException(sprintf('First argument passed to "%s" must be of the type "%s", "%s" given', __METHOD__, SessionInterface::class, get_class($translator)));
            }

            if (!$domainConfiguration instanceof LegacyTranslatorInterface && !$domainConfiguration instanceof TranslatorInterface) {
                throw new \InvalidArgumentException(sprintf('Second argument passed to "%s" must be of the type "%s" or "%s", "%s" given', __METHOD__, LegacyTranslatorInterface::class, TranslatorInterface::class, get_class($domainConfiguration)));
            }

            if (!$adminRouteHelper instanceof DomainConfigurationInterface) {
                throw new \InvalidArgumentException(sprintf('Thrird argument passed to "%s" must be of the type "%s", "%s" given', __METHOD__, DomainConfigurationInterface::class, get_class($adminRouteHelper)));
            }

            $extraParam = func_get_arg(3);
            if (!$extraParam instanceof AdminRouteHelper) {
                throw new \InvalidArgumentException(sprintf('Fourth argument passed to "%s" must be of the type "%s", "%s" given', __METHOD__, AdminRouteHelper::class, get_class($extraParam)));
            }

            $this->session = $translator;
            $this->translator = $domainConfiguration;
            $this->domainConfiguration = $adminRouteHelper;
            $this->adminRouteHelper = $extraParam;

            return;
        }

        if (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('Argument "$translator" passed to "%s" must be of the type "%s" or "%s", "%s" given', __METHOD__, LegacyTranslatorInterface::class, TranslatorInterface::class, get_class($translator)));
        }

        if (!$domainConfiguration instanceof DomainConfigurationInterface) {
            throw new \InvalidArgumentException(sprintf('First argument passed to "%s" must be of the type "%s", "%s" given', __METHOD__, DomainConfigurationInterface::class, get_class($domainConfiguration)));
        }

        if (!$adminRouteHelper instanceof AdminRouteHelper) {
            throw new \InvalidArgumentException(sprintf('First argument passed to "%s" must be of the type "%s", "%s" given', __METHOD__, AdminRouteHelper::class, get_class($adminRouteHelper)));
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
        $session = $this->session ?? $request->getSession();
        $session->getFlashBag()->add(FlashTypes::WARNING, $this->translator->trans('multi_domain.host_override_active'));
    }
}
