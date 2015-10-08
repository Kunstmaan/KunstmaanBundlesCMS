<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PasswordCheckListener to check if the user has to change his password
 */
class PasswordCheckListener
{
    /**
     * @var $authorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var $tokenStorage
     */
    private $tokenStorage;

    /**
     * @var $router
     */
    private $router;

    /**
     * @var $session
     */
    private $session;

    /**
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage         $tokenStorage
     * @param Router               $router
     * @param Session              $session
     */
    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, Router $router, Session $session)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @param GetResponseEvent $event
     **/
    public function onKernelRequest(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();
        if ($this->tokenStorage->getToken() && $this->isAdminRoute($url)) {
            $route = $event->getRequest()->get('_route');
            if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') && $route != 'fos_user_change_password') {
                $user = $this->tokenStorage->getToken()->getUser();
                if ($user->isPasswordChanged() === false) {
                    $response = new RedirectResponse($this->router->generate('fos_user_change_password'));
                    $this->session->getFlashBag()->add('error', 'Your password has not yet been changed');
                    $event->setResponse($response);
                }
            }
        }
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
}
