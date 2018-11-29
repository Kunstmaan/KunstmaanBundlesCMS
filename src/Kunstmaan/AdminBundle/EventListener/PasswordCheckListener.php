<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;

/**
 * PasswordCheckListener to check if the user has to change his password
 */
class PasswordCheckListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AdminRouteHelper
     */
    private $adminRouteHelper;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param Router                        $router
     * @param Session                       $session
     * @param TranslatorInterface           $translator
     * @param AdminRouteHelper              $adminRouteHelper
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Router $router, Session $session, TranslatorInterface $translator, AdminRouteHelper $adminRouteHelper)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @param GetResponseEvent $event
     **/
    public function onKernelRequest(GetResponseEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();
        if ($this->tokenStorage->getToken() && $this->adminRouteHelper->isAdminRoute($url)) {
            $route = $event->getRequest()->get('_route');
            if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') && $route != 'fos_user_change_password') {
                $user = $this->tokenStorage->getToken()->getUser();
                if ($user->isPasswordChanged() === false) {
                    $response = new RedirectResponse($this->router->generate('fos_user_change_password'));
                    $this->session->getFlashBag()->add(
                        FlashTypes::ERROR,
                        $this->translator->trans('kuma_admin.password_check.flash.error')
                    );
                    $event->setResponse($response);
                }
            }
        }
    }
}
