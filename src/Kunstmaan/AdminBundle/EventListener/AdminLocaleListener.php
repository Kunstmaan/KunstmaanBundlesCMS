<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AdminLocaleListener to override default locale if user-specific locale is set in database
 */
class AdminLocaleListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var TranslatorInterface|LegacyTranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $defaultAdminLocale;

    /**
     * @var string
     */
    private $providerKey;

    /**
     * @var AdminRouteHelper
     */
    private $adminRouteHelper;

    /**
     * @param TranslatorInterface|LegacyTranslatorInterface $translator
     * @param string                                        $defaultAdminLocale
     * @param string                                        $providerKey        Firewall name to check against
     */
    public function __construct(TokenStorageInterface $tokenStorage, /* TranslatorInterface|LegacyTranslatorInterface */ $translator, AdminRouteHelper $adminRouteHelper, $defaultAdminLocale, $providerKey = 'main')
    {
        // NEXT_MAJOR Add "Symfony\Contracts\Translation\TranslatorInterface" typehint when sf <4.4 support is removed.
        if (!$translator instanceof \Symfony\Contracts\Translation\TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The "$translator" parameter should be instance of "%s" or "%s"', TranslatorInterface::class, LegacyTranslatorInterface::class));
        }

        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->defaultAdminLocale = $defaultAdminLocale;
        $this->providerKey = $providerKey;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @param GetResponseEvent|RequestEvent $event
     */
    public function onKernelRequest($event)
    {
        if (!$event instanceof GetResponseEvent && !$event instanceof RequestEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(RequestEvent::class) ? RequestEvent::class : GetResponseEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

        $url = $event->getRequest()->getRequestUri();
        if (!$this->adminRouteHelper->isAdminRoute($url)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token && $this->isAdminToken($this->providerKey, $token)) {
            $locale = $token->getUser()->getAdminLocale();

            if (!$locale) {
                $locale = $this->defaultAdminLocale;
            }

            $this->translator->setLocale($locale);
        }
    }

    /**
     * @param TokenInterface $token
     */
    private function isAdminToken($providerKey, TokenInterface $token = null): bool
    {
        return \is_callable([$token, 'getProviderKey']) && $token->getProviderKey() === $providerKey;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // The event subscriber must be registered after the Symfony FirewallListener so the user token is populated.
            KernelEvents::REQUEST => [['onKernelRequest', 5]],
        ];
    }
}
