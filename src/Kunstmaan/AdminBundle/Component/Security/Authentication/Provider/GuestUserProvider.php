<?php

namespace Kunstmaan\AdminBundle\Component\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Description of GuestUserProvider
 *
 * @author wim
 */
class GuestUserProvider implements AuthenticationProviderInterface
{

    private $userProvider;
    
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    
    public function authenticate(TokenInterface $token)
    {
        // Do authentication here!
        die('GuestUserProvider.authenticate!');
    }
    
    public function supports(TokenInterface $token)
    {
        return $token instanceof AnonymousToken;
    }
    
}
