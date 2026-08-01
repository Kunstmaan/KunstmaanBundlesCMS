<?php

namespace Kunstmaan\RedirectBundle\Router;

use Doctrine\Persistence\ObjectRepository;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Repository\RedirectRepository;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RedirectRouter implements RouterInterface
{
    /** @var RequestContext */
    private $context;

    /** @var RouteCollection */
    private $routeCollection;

    /** @var RedirectRepository */
    private $redirectRepository;

    /** @var DomainConfigurationInterface */
    private $domainConfiguration;

    public function __construct(ObjectRepository $redirectRepository, DomainConfigurationInterface $domainConfiguration)
    {
        $this->redirectRepository = $redirectRepository;
        $this->domainConfiguration = $domainConfiguration;
        $this->context = new RequestContext();
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException('You cannot generate a url from a redirect');
    }

    public function match($pathinfo): array
    {
        $this->initRouteCollection($pathinfo);

        $urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->getContext());

        return $urlMatcher->match($pathinfo);
    }

    public function getRouteCollection(): RouteCollection
    {
        // The route collection will always be populated with matched redirects based on the current request. If this
        // method is called before the url matching has been executed, the collection will be empty.
        return $this->routeCollection ?? new RouteCollection();
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    private function initRouteCollection(string $pathInfo): void
    {
        if (null !== $this->routeCollection) {
            return;
        }

        $this->routeCollection = new RouteCollection();

        $domain = $this->domainConfiguration->getHost();
        $redirect = $this->redirectRepository->findByRequestPathAndDomain($pathInfo, $domain);
        if (null === $redirect) {
            return;
        }

        $origin = $redirect->getOrigin();
        $routePath = str_contains($origin, '/*') ? $pathInfo : $origin;
        $targetPath = $redirect->getTarget();
        if (str_contains($origin, '/*') && str_contains($redirect->getTarget(), '/*')) {
            if ($origin === '/*' && $routePath === '/') { // exclude root path
                return;
            }

            $origin = rtrim($origin, '/*');
            $target = rtrim($redirect->getTarget(), '/*');
            if ($origin === '') {
                $targetPath = $target . $pathInfo;
            } else {
                $targetPath = str_replace($origin, $target, $pathInfo);
            }
        }

        $queryString = $this->context->getQueryString();
        if ($queryString) {
            $targetPath .= '?' . $queryString;
        }

        $needsUtf8 = false;
        foreach ([$routePath, $targetPath] as $item) {
            if (preg_match('/[\x80-\xFF]/', $item)) {
                $needsUtf8 = true;

                break;
            }
        }

        $route = new Route(urldecode($routePath), [
            '_controller' => 'Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction',
            'path' => $targetPath,
            'permanent' => $redirect->isPermanent(),
        ], [], ['utf8' => $needsUtf8]);

        $this->routeCollection->add('_redirect_route_' . $redirect->getId(), $route);
    }
}
