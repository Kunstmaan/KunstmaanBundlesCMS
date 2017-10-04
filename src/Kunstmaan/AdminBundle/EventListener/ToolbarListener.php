<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\Toolbar\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

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
     * @var bool
     */
    protected $debug;

    /**
     * @var AdminRouteHelper
     */
    protected $adminRouteHelper;

    /**
     * ToolbarListener constructor.
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param DataCollector         $dataCollector
     * @param AuthorizationChecker  $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param bool                  $enabled
     * @param bool                  $debug
     * @param AdminRouteHelper      $adminRouteHelper
     */
    public function __construct(\Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, DataCollector $dataCollector, AuthorizationChecker $authorizationChecker, TokenStorageInterface $tokenStorage, $enabled, $debug, AdminRouteHelper $adminRouteHelper)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->dataCollector = $dataCollector;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->enabled = $enabled;
        $this->debug = $debug;
        $this->adminRouteHelper = $adminRouteHelper;
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
        return !$this->debug && $this->enabled;
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

        // Do not capture redirects or modify XML HTTP Requests
        if (!$event->isMasterRequest() || $request->isXmlHttpRequest() || $this->adminRouteHelper->isAdminRoute($url) || !$token || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        if ($response->isRedirection() || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
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
