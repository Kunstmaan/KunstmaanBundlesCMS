<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;

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
     * @var TranslatorInterface
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
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface   $translator
     * @param string                $defaultAdminLocale
     * @param AdminRouteHelper      $adminRouteHelper
     * @param string                $providerKey        Firewall name to check against
     */
    public function __construct(TokenStorageInterface $tokenStorage, TranslatorInterface $translator, AdminRouteHelper $adminRouteHelper, $defaultAdminLocale, $providerKey = 'main')
    {
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->defaultAdminLocale = $defaultAdminLocale;
        $this->providerKey = $providerKey;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * onKernelRequest
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();
        $token = $this->tokenStorage->getToken();

        if ($token && $this->isAdminToken($this->providerKey, $token) && $this->adminRouteHelper->isAdminRoute($url)) {
            $locale = $token->getUser()->getAdminLocale();

            if (!$locale) {
                $locale = $this->defaultAdminLocale;
            }

            $this->translator->setLocale($locale);
        }
    }

    /**
     * @param TokenInterface $token
     * @param                $providerKey
     *
     * @return bool
     */
    private function isAdminToken($providerKey, TokenInterface $token = null)
    {
        return is_callable([$token, 'getProviderKey']) && $token->getProviderKey() === $providerKey;
    }

    /**
     * getSubscribedEvents
     */
    public static function getSubscribedEvents()
    {
        return array(
            // Must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
