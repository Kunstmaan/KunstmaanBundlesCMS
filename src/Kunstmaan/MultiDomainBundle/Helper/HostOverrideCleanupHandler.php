<?php

namespace Kunstmaan\MultiDomainBundle\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class HostOverrideCleanupHandler implements LogoutHandlerInterface
{
    /**
     * This method is called by the LogoutListener when a user has requested
     * to be logged out. Usually, you would unset session variables, or remove
     * cookies, etc.
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        // Remove host override cookie
        if ($request->cookies->has(DomainConfiguration::OVERRIDE_HOST)) {
            $response->headers->clearCookie(DomainConfiguration::OVERRIDE_HOST);
        }
    }
}
