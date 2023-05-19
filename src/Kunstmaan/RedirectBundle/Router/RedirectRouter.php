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

    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * Parameters that reference placeholders in the route pattern will substitute them in the
     * path or host. Extra params are added as query string to the URL.
     *
     * When the passed reference type cannot be generated for the route because it requires a different
     * host or scheme than the current one, the method will return a more comprehensive reference
     * that includes the required params. For example, when you call this method with $referenceType = ABSOLUTE_PATH
     * but the route requires the https scheme whereas the current scheme is http, it will instead return an
     * ABSOLUTE_URL with the https scheme and the current host. This makes sure the generated URL matches
     * the route in any case.
     *
     * If there is no route with the given name, the generator must throw the RouteNotFoundException.
     *
     * @param string      $name          The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException              If the named route doesn't exist
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                                                                  it does not match the requirement
     *
     * @api
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException('You cannot generate a url from a redirect');
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param string $pathinfo The path info to be parsed (raw format, i.e. not urldecoded)
     *
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException If the resource could not be found
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException If the resource was found but the request method is not allowed
     *
     * @api
     */
    public function match($pathinfo): array
    {
        $this->initRouteCollection($pathinfo);

        $urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->getContext());

        return $urlMatcher->match($pathinfo);
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection()
    {
        // The route collection will always be populated with matched redirects based on the current request. If this
        // method is called before the url matching has been executed, the collection will be empty.
        return $this->routeCollection ?? new RouteCollection();
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * @return void
     */
    public function setContext(RequestContext $context)
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

        $routePath = str_contains($redirect->getOrigin(), '/*') ? $pathInfo : $redirect->getOrigin();
        $targetPath = $redirect->getTarget();
        if (str_contains($redirect->getOrigin(), '/*') && str_contains($redirect->getTarget(), '/*')) {
            $origin = rtrim($redirect->getOrigin(), '/*');
            $target = rtrim($redirect->getTarget(), '/*');
            $targetPath = str_replace($origin, $target, $pathInfo);
        }

        $needsUtf8 = false;
        foreach ([$routePath, $targetPath] as $item) {
            if (preg_match('/[\x80-\xFF]/', $item)) {
                $needsUtf8 = true;

                break;
            }
        }

        $route = new Route($routePath, [
            '_controller' => 'Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction',
            'path' => $targetPath,
            'permanent' => $redirect->isPermanent(),
        ], [], ['utf8' => $needsUtf8]);

        $this->routeCollection->add('_redirect_route_' . $redirect->getId(), $route);
    }
}
