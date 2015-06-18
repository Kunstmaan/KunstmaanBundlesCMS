<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * AdminLocaleListener to override default locale if user-specific locale is set in database
 */
class AdminLocaleListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
     */
    private $context;

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
     * @param SecurityContext     $context
     * @param TranslatorInterface $translator
     * @param string              $defaultAdminLocale
     * @param string              $providerKey          Firewall name to check against
     */
    public function __construct(SecurityContext $context, TranslatorInterface $translator, $defaultAdminLocale, $providerKey = 'main')
    {
        $this->translator         = $translator;
        $this->context            = $context;
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
        if ($this->isAdminToken($this->context->getToken(), $this->providerKey) && $this->isAdminRoute($url)) {
            $token = $this->context->getToken();
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
    private function isAdminToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function isAdminRoute($url)
    {
        preg_match('/^\/(app_(.*)\.php\/)?([a-zA-Z_-]{2,5}\/)?admin\/(.*)/', $url, $matches);

        // Check if path is part of admin area
        if (count($matches) === 0) {
            return false;
        }

        if (strpos($url, '/admin/preview') !== false) {
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
