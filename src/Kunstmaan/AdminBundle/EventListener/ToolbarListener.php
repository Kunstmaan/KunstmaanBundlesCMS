<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\Toolbar\DataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var AuthorizationCheckerInterface
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
     * ToolbarListener constructor.
     *
     * @param \Twig_Environment             $twig
     * @param UrlGeneratorInterface         $urlGenerator
     * @param DataCollector                 $dataCollector
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param bool                          $enabled
     * @param ContainerInterface            $container
     * @param AdminRouteHelper              $adminRouteHelper
     * @param array                         $providerKeys
     */
    public function __construct(
        \Twig_Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        DataCollector $dataCollector,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        $enabled,
        ContainerInterface $container,
        AdminRouteHelper $adminRouteHelper,
        array $providerKeys
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
        if (!$this->isEnabled()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();
        $url = $event->getRequest()->getRequestUri();
        $token = $this->tokenStorage->getToken();

        // Only enable toolbar when the firewall name equals the provided config value kunstmaan_admin.provider_key.
        if (null !== $token && method_exists($token, 'getProviderKey')) {
            $key = $token->getProviderKey();
        } else {
            $key = 'main';
        }

        // Do not capture redirects or modify XML HTTP Requests
        if (!$token || !\in_array($key, $this->providerKeys, false) || !$event->isMasterRequest() || $request->isXmlHttpRequest(
            ) || $this->adminRouteHelper->isAdminRoute(
                $url
            ) || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
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
                        '@KunstmaanAdmin/Toolbar/toolbar.html.twig'
                        ,
                        ['collectors' => $this->dataCollector->getDataCollectors()]
                    )
                )."\n";
            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }
}
