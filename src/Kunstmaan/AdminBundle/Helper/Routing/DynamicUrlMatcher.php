<?php
namespace Kunstmaan\AdminBundle\Helper\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class DynamicUrlMatcher extends UrlMatcher
{
    private $routesCopy;
    
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        parent::__construct($routes, $context);
        $this->routesCopy = $routes;
    }
    
    /**
     * Check if url exists
     *
     * @param string $pathinfo
     */
    public function match($pathinfo)
    {
        if ($ret = $this->matchCollection($pathinfo, $this->routesCopy)) {
            return $ret;
        }
        return false;
    }
}
