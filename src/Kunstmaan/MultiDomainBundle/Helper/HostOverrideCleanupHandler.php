<?php

namespace Kunstmaan\MultiDomainBundle\Helper;

use Kunstmaan\MultiDomainBundle\EventSubscriber\LogoutHostOverrideCleanupEventSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

/**
 * @deprecated since 6.3. Will be removed in 7.0, the "Kunstmaan\MultiDomainBundle\EventSubscriber\LogoutHostOverrideCleanupEventSubscriber" will be used instead.
 */
class HostOverrideCleanupHandler implements LogoutHandlerInterface
{
    /**
     * This method is called by the LogoutListener when a user has requested
     * to be logged out. Usually, you would unset session variables, or remove
     * cookies, etc.
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        trigger_deprecation('kunstmaan/multidomain-bundle', '6.3', 'The "%s" class is deprecated and is replaced by the "%s" subscriber with the new authentication system.', self::class, LogoutHostOverrideCleanupEventSubscriber::class);
        // Remove host override
        if ($request->hasPreviousSession() && $request->getSession()->has(DomainConfiguration::OVERRIDE_HOST)) {
            $request->getSession()->remove(DomainConfiguration::OVERRIDE_HOST);
        }
    }
}
