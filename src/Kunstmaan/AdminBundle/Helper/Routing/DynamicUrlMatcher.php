<?php

namespace Kunstmaan\AdminBundle\Helper\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * DynamicUrlMatcher
 */
class DynamicUrlMatcher extends UrlMatcher
{
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
     *
     * @param string $pathInfo
     *
     * @todo Check if there's a better solution then this ugly hack...
     *
     * @return bool
     */
    public function match($pathInfo)
    {
        if ($ret = $this->matchCollection($pathInfo, $this->routesCopy)) {
            return $ret;
        }

        return false;
    }

}
