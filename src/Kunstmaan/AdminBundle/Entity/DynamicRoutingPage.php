<?php
namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Helper\Routing\DynamicUrlMatcher;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;

abstract class DynamicRoutingPage implements DynamicRoutingPageInterface
{
    private $routes = null;
    
    /**
     * Routes should be defined here
     */
    public function initRoutes()
    {
        if (!$this->routes) {
            $this->routes = new RouteCollection();
        }
    }
    
    /**
     * Set routing collection
     *
     * @param RouteCollection $routes
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Return routing collection
     *
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    
    /**
     * Match slug against route collection
     *
     * @param string $slug
     * @param string $prefix Optional prefix for routes
     * @return array|false Matching controller info
     */
    public function match($slug, $prefix = '')
    {
        $this->initRoutes();
        if (!empty($prefix)) {
            $this->routes->addPrefix('/' . $prefix);
        }
        $context = new RequestContext();
        $matcher = new DynamicUrlMatcher($this->routes, $context);
        $result = $matcher->match('/' . $slug);

        return $result;
    }
}
