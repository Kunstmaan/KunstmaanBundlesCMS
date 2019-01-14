<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\Toolbar\DataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ToolbarListener implements EventSubscriberInterface
{
    const DISABLED = 1;

    const ENABLED = 2;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AdminRouteHelper
     */
    protected $adminRouteHelper;

    /**
     * @var array
     */
    protected $providerKeys;

    /**
     * @var array
     */
    protected $adminFirewallName;

    /**
     * ToolbarListener constructor.
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param DataCollector         $dataCollector
     * @param AuthorizationChecker  $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param bool                  $enabled
     * @param ContainerInterface    $container
     * @param AdminRouteHelper      $adminRouteHelper
     * @param array                 $providerKeys
     * @param string                $adminFirewallName
     */
    public function __construct(
        \Twig_Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        DataCollector $dataCollector,
        AuthorizationChecker $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        $enabled,
        ContainerInterface $container,
        AdminRouteHelper $adminRouteHelper,
        array $providerKeys,
        $adminFirewallName = 'main'
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->dataCollector = $dataCollector;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->enabled = $enabled;
        $this->container = $container;
        $this->adminRouteHelper = $adminRouteHelper;
        $this->providerKeys = $providerKeys;
        $this->adminFirewallName = $adminFirewallName;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -125],
        ];
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return !$this->container->has('profiler') && $this->enabled;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->isEnabled() || HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();
        $session = $request->getSession();
        $url = $event->getRequest()->getRequestUri();
        $token = $this->tokenStorage->getToken();

        if (null !== $token && method_exists($token, 'getProviderKey')) {
            $key = $token->getProviderKey();
        } else {
            $key = $this->adminFirewallName;
        }

        // Only enable toolbar when the kunstmaan_admin.toolbar_firewall_names config value contains the current firewall name.
        if (!\in_array($key, $this->providerKeys, false)) {
            return false;
        }

        // Only enable toolbar when we can find an authenticated user in the session from the kunstmaan_admin.admin_firewall_name config value.
        $authenticated = false;
        /* @var PostAuthenticationGuardToken $token */
        if ($session->isStarted() && $session->has(sprintf('_security_%s', $this->adminFirewallName))) {
            $token = unserialize($session->get(sprintf('_security_%s', $this->adminFirewallName)));
            $authenticated = $token->isAuthenticated();
        }

        // Do not capture redirects or modify XML HTTP Requests
        if (!$authenticated || !$event->isMasterRequest() || $request->isXmlHttpRequest() || $this->adminRouteHelper->isAdminRoute($url)) {
            return;
        }

        if ($response->isRedirection() || ($response->headers->has('Content-Type') && false === strpos(
                    $response->headers->get('Content-Type'),
                    'html'
                ))
            || 'html' !== $request->getRequestFormat()
            || false !== stripos($response->headers->get('Content-Disposition'), 'attachment;')
        ) {
            return;
        }

        $this->injectToolbar($response, $request);
    }

    /**
     * Injects the admin toolbar into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectToolbar(Response $response, Request $request)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $toolbar = "\n".str_replace(
                    "\n",
                    '',
                    $this->twig->render(
                        '@KunstmaanAdmin/Toolbar/toolbar.html.twig',
                        ['collectors' => $this->dataCollector->getDataCollectors()]
                    )
                )."\n";
            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }
}
