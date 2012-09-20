<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Http\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Maps anonymous user to guest user (copies roles from guest user on DB)
 */
class GuestUserListener implements ListenerInterface
{
    /* @var SecurityContextInterface $context */
    private $context;

    /* @var UserProviderInterface $provider */
    private $provider;

    /* @var string $providerKey */
    private $providerKey;

    /**
     * @param SecurityContextInterface $context     The security context
     * @param UserProviderInterface    $provider    The user provider
     * @param string                   $providerKey The provider key
     */
    public function __construct(SecurityContextInterface $context, UserProviderInterface $provider, $providerKey)
    {
        $this->context     = $context;
        $this->provider    = $provider;
        $this->providerKey = $providerKey;
    }

    /**
     * Handle response event (anonymous login).
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->context->getToken()) {
            return;
        }

        // Map anonymous login to guest user roles
        $user  = $this->provider->loadUserByUsername('guest');
        $roles = $user->getRoles();

        $token = new AnonymousToken($this->providerKey, 'guest', $roles);
        $this->context->setToken($token);
    }

}
