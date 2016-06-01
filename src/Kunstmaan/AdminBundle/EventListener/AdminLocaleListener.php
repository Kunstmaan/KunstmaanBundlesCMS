<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface   $translator
     * @param string                $defaultAdminLocale
     * @param string                $providerKey          Firewall name to check against
     */
    public function __construct(TokenStorageInterface $tokenStorage, TranslatorInterface $translator, $defaultAdminLocale, $providerKey = 'main')
    {
        $this->translator         = $translator;
        $this->tokenStorage       = $tokenStorage;
        $this->defaultAdminLocale = $defaultAdminLocale;
        $this->providerKey        = $providerKey;
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

        if ($token && $this->isAdminToken($this->providerKey, $token) && $this->isAdminRoute($url)) {
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
     * @param string $url
     *
     * @return bool
     */
    private function isAdminRoute($url)
    {
        //If the url contains an admin part and a preview part then it is not an admin route
        preg_match('/^(\/app_[a-zA-Z]+\.php)?\/([a-zA-Z_-]{2,5}\/)?admin(\/.*)?\/preview/', $url, $matches);

        if (count($matches) > 0) {
            return false;
        }

        preg_match('/^\/(app_[a-zA-Z]+\.php\/)?([a-zA-Z_-]{2,5}\/)?admin\/(.*)/', $url, $matches);

        // Check if path is part of admin area
        if (count($matches) === 0) {
            return false;
        }

        return true;
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
