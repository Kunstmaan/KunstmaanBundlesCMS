<?php

namespace Kunstmaan\AdminBundle\Helper\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class DynamicUrlMatcher extends UrlMatcher
{
    private $routesCopy;

    /**
     * @param RouteCollection $routes
     * @param RequestContext  $context
     */
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        parent::__construct($routes, $context);
        $this->routesCopy = $routes;
    }

    /**
     * Check if url exists
     *
     * @todo Check if there's a better solution then this hack...
     *
     * @param string $pathinfo
     *
     * @return boolean
     */
    public function match($pathinfo)
    {
        if ($ret = $this->matchCollection($pathinfo, $this->routesCopy)) {
            return $ret;
        }

        return false;
    }

}
