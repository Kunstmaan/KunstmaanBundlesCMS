<?php

namespace Kunstmaan\RedirectBundle\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RedirectRouter implements RouterInterface
{
    // Wildcard path placeholder
    private const WILDCARD_PATH = 'wildcard';

    // Character used as wildcard in Redirect.origin
    private const WILDCARD_CHAR = '*';

    /** @var RequestContext */
    private $context;

    /** @var RouteCollection */
    private $routeCollection = null;

    /** @var ObjectRepository */
    private $redirectRepository;

    /** @var DomainConfigurationInterface */
    private $domainConfiguration;

    /**
     * @param ObjectRepository             $redirectRepository
     * @param DomainConfigurationInterface $domainConfiguration
     */
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
     * @return string The generated URL
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException              If the named route doesn't exist
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                                                                  it does not match the requirement
     *
     * @api
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
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
     * @return array An array of parameters
     *
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException If the resource could not be found
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException If the resource was found but the request method is not allowed
     *
     * @api
     */
    public function match($pathinfo)
    {
        $urlMatcher = new RedirectableUrlMatcher($this->getRouteCollection(), $this->getContext());

        return $urlMatcher->match($pathinfo);
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection()
    {
        if (null === $this->routeCollection) {
            $this->routeCollection = new RouteCollection();
            $this->initRoutes();
        }

        return $this->routeCollection;
    }

    /**
     * Gets the request context.
     *
     * @return \Symfony\Component\Routing\RequestContext The context
     *
     * @api
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @api
     */
    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    private function initRoutes(): void
    {
        $redirects = $this->redirectRepository->findBy([], ['origin' => 'DESC']);

        $domain = $this->domainConfiguration->getHost();

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            if ($redirect->getDomain() !== $domain && '' !== (string) $redirect->getDomain()) {
                continue;
            }

            $this->routeCollection->add(
                '_redirect_route_' . $redirect->getId(),
                $this->createRedirectRoute($redirect)
            );
        }
    }

    /**
     * @param Redirect $redirect
     *
     * @return Route
     */
    private function createRedirectRoute(Redirect $redirect): Route
    {
        $origin = $redirect->getOrigin();
        $hasWildcard = mb_substr_count($origin, self::WILDCARD_CHAR) === 1;

        if ($hasWildcard) {
            $origin = str_replace(self::WILDCARD_CHAR, $this->generatePath(self::WILDCARD_PATH), $origin);
        }

        $route = new Route($origin, [
            '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
            'path' => $redirect->getTarget(),
            'permanent' => $redirect->isPermanent(),
        ]);

        if ('' !== (string) $redirect->getDomain()) {
            $route->setHost($redirect->getDomain());
        }

        if ($hasWildcard) {
            $route->setRequirement(self::WILDCARD_PATH, '.*');
        }

        $route->setOption('utf8', preg_match('/[\x80-\xFF]/', $redirect->getTarget()));

        return $route;
    }

    /**
     * @param string $wildcard
     *
     * @return string
     */
    private function generatePath($wildcard): string
    {
        return sprintf('{%s}', $wildcard);
    }
}
