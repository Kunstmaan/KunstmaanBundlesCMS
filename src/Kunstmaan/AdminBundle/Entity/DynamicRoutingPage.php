<?php
namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Symfony\Component\Routing\Matcher\UrlMatcher;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\RouteCollection;

abstract class DynamicRoutingPage implements DynamicRoutingPageInterface
{
    private $routes = null;
    
    public function __construct()
    {
        $this->routes = new RouteCollection();
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
     * Returns true when the routing collection contains at least 1 route
     *
     * @return boolean
     */
    public function hasRoutes()
    {
        $all = $this->routes->all();
        return (count($all) > 0);
    }
 
    /**
     * Match slug against route collection
     *
     * @param string $slug
     * @param string $prefix Optional prefix for routes
     * @return array Matching controller info
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
    public function match($slug, $prefix = '')
    {
        if (!$this->hasRoutes()) {
            throw new ResourceNotFoundException();
        }
        if (!empty($prefix)) {
            $routes->addPrefix($prefix);
        }
        $context = new RequestContext();
        $matcher = new UrlMatcher($this->routes, $context);
        return $matcher->match($slug);
    }
}
