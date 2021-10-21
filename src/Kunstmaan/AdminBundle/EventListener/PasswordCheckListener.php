<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
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
     * @var Session|RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface|LegacyTranslatorInterface
     */
    private $translator;

    /**
     * @var AdminRouteHelper
     */
    private $adminRouteHelper;

    /**
     * @param TranslatorInterface|LegacyTranslatorInterface $translator
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Router $router, /* RequestStack */ $requestStack, /* TranslatorInterface|LegacyTranslatorInterface */ $translator, AdminRouteHelper $adminRouteHelper)
    {
        // NEXT_MAJOR Add "Symfony\Contracts\Translation\TranslatorInterface" typehint when sf <4.4 support is removed.
        if (!$translator instanceof \Symfony\Contracts\Translation\TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The "$translator" parameter should be instance of "%s" or "%s"', \Symfony\Contracts\Translation\TranslatorInterface::class, LegacyTranslatorInterface::class));
        }

        if (!$requestStack instanceof SessionInterface && !$requestStack instanceof RequestStack) {
            throw new \InvalidArgumentException(sprintf('The fourth argument of "%s" should be instance of "%s" or "%s"', __METHOD__, SessionInterface::class, RequestStack::class));
        }

        if ($requestStack instanceof SessionInterface) {
            @trigger_error(sprintf('Passing a service instance of "%s" for the first argument in "%s" is deprecated since KunstmaanAdminBundle 5.10 and an instance of "%s" will be required in KunstmaanAdminBundle 6.0.', SessionInterface::class, __METHOD__, RequestStack::class), E_USER_DEPRECATED);
        }

        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @param GetResponseEvent|ResponseEvent $event
     **/
    public function onKernelRequest($event)
    {
        if (!$event instanceof GetResponseEvent && !$event instanceof RequestEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(ResponseEvent::class) ? ResponseEvent::class : GetResponseEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

        $url = $event->getRequest()->getRequestUri();
        if (!$this->adminRouteHelper->isAdminRoute($url)) {
            return;
        }

        $route = $event->getRequest()->get('_route');
        if (null === $route || in_array($route, ['kunstmaan_admin_forced_change_password', 'fos_user_change_password'], true)) {
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
        if ($this->requestStack instanceof SessionInterface) {
            return $this->requestStack;
        }

        return $this->requestStack->getCurrentRequest()->getSession();
    }
}
