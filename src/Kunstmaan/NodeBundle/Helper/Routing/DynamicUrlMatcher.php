<?php

namespace Kunstmaan\NodeBundle\Helper\Routing;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Custom UrlMatcher which holds a copy of all the routes, this is needed for the DynamicRouting
 */
class DynamicUrlMatcher extends UrlMatcher
{
    /**
     * @var RouteCollection
     */
    private $routesCopy;

    /**
     * @param RouteCollection $routes  The route collection
     * @param RequestContext  $context The request context
     */
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        parent::__construct($routes, $context);
        $this->routesCopy = $routes;
    }

    /**
     * Check if url exists
     */
    public function match($pathinfo): array
    {
        if ($ret = $this->matchCollection($pathinfo, $this->routesCopy)) {
            return $ret;
        }

        throw new ResourceNotFoundException(sprintf('No routes found for "%s".', $pathinfo));
    }
}
