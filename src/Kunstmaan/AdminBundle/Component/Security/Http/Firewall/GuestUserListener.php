<?php

namespace Kunstmaan\AdminBundle\Component\Security\Http\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Description of GuestUserListener
 *
 */
class GuestUserListener implements ListenerInterface
{
    private $context;
    private $provider;
    private $providerKey;
    private $logger;

    public function __construct(SecurityContextInterface $context, UserProviderInterface $provider, $providerKey, $logger = null)
    {
        $this->context = $context;
        $this->provider = $provider;
        $this->providerKey = $providerKey;
        $this->logger = $logger;
    }

    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->context->getToken()) {
            return;
        }
        
        // Map anonymous login to guest user roles
        $user = $this->provider->loadUserByUsername('guest');
        $roles = $user->getRoles();
        
        $token = new AnonymousToken($this->providerKey, 'guest', $roles);
        $this->context->setToken($token);
    }
    
}
