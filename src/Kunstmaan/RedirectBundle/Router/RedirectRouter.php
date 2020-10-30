<?php

namespace Kunstmaan\RedirectBundle\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
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
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
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
        if (\is_null($this->routeCollection)) {
            $this->routeCollection = new RouteCollection();
            $this->initRoutes();
        }

        return $this->routeCollection;
    }

    private function initRoutes()
    {
        $redirects = $this->redirectRepository->findAll();
        $domain = $this->domainConfiguration->getHost();

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            // Check for wildcard routing and adjust as required
            if ($this->isWildcardRedirect($redirect)) {
                $route = $this->createWildcardRoute($redirect);
            } else {
                $route = $this->createRoute($redirect);
            }

            // Only add the route when the domain matches or the domain is empty
            if ($redirect->getDomain() == $domain || !$redirect->getDomain()) {
                $this->routeCollection->add(
                    '_redirect_route_' . $redirect->getId(),
                    $route
                );
            }
        }
    }

    /**
     * @param Redirect $redirect
     *
     * @return bool
     */
    private function isWildcardRedirect(Redirect $redirect)
    {
        $origin = $redirect->getOrigin();
        $matchSegment = substr($origin, 0, -1);
        if (substr($origin, -2) == '/*') {
            return $this->isPathInfoWildcardMatch($matchSegment);
        }

        return false;
    }

    private function isPathInfoWildcardMatch($matchSegment)
    {
        $path = $this->context->getPathInfo();

        return strstr($path, $matchSegment);
    }

    /**
     * @param Redirect $redirect
     *
     * @return Route
     */
    private function createRoute(Redirect $redirect)
    {
        $needsUtf8 = false;
        foreach ([$redirect->getOrigin(), $redirect->getTarget()] as $item) {
            if (preg_match('/[\x80-\xFF]/', $item)) {
                $needsUtf8 = true;

                break;
            }
        }

        return new Route(
            $redirect->getOrigin(), [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => $redirect->getTarget(),
                'permanent' => $redirect->isPermanent(),
            ], [], ['utf8' => $needsUtf8]);
    }

    /**
     * @param Redirect $redirect
     *
     * @return Route
     */
    private function createWildcardRoute(Redirect $redirect)
    {
        $origin = $redirect->getOrigin();
        $target = $redirect->getTarget();
        $url = $this->context->getPathInfo();
        $needsUtf8 = preg_match('/[\x80-\xFF]/', $redirect->getTarget());

        $origin = substr($origin, 0, -1);
        $target = substr($target, 0, -1);
        $pathInfo = str_replace($origin, $target, $url);

        $this->context->setPathInfo($pathInfo);

        return new Route($url, [
            '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
            'path' => $url,
            'permanent' => $redirect->isPermanent(),
        ], [], ['utf8' => $needsUtf8]);
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
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }
}
