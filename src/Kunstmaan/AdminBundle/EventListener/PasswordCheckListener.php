<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AdminRouteHelper
     */
    private $adminRouteHelper;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Router $router, RequestStack $requestStack, TranslatorInterface $translator, AdminRouteHelper $adminRouteHelper)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $url = $event->getRequest()->getRequestUri();
        if (!$this->adminRouteHelper->isAdminRoute($url)) {
            return;
        }

        $route = $event->getRequest()->get('_route');
        if (null === $route || $route === 'kunstmaan_admin_forced_change_password') {
            return;
        }

        if (null === $this->tokenStorage->getToken()) {
            return;
        }

        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user->isPasswordChanged()) {
            return;
        }

        $response = new RedirectResponse($this->router->generate('kunstmaan_admin_forced_change_password'));
        $session = $this->getSession();
        $session->getFlashBag()->add(
            FlashTypes::DANGER,
            $this->translator->trans('kuma_admin.password_check.flash.error')
        );
        $event->setResponse($response);
    }

    private function getSession(): SessionInterface
    {
        return method_exists($this->requestStack, 'getSession') ? $this->requestStack->getSession() : $this->requestStack->getCurrentRequest()->getSession();
    }
}
