<?php

namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;
use Kunstmaan\AdminBundle\Helper\Routing\DynamicUrlMatcher;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class DynamicRoutingPage extends AbstractPage implements DynamicRoutingPageInterface
{
    private $routes = NULL;
    private $context;
    private $matcher;
    private $generator;
    protected $locale;
    
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
        if (!$this->routes) {
            $this->initRoutes();
        }
        return $this->routes;
    }

    /**
     * Match slug against route collection
     *
     * @param string $slug
     * @return array|false Matching controller info
     */
    public function match($slug)
    {
        return $this->getMatcher()->match($slug);
    }

    public function generate($name, $parameters = array(), $absolute = FALSE)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    /**
     * @return RequestContext
     */
    public function getContext()
    {
        if (!$this->context) {
            $this->context = new RequestContext();
        }
        return $this->context;
    }

    /**
     * @return DynamicUrlMatcher
     */
    public function getMatcher()
    {
        if (!$this->matcher) {
            $this->matcher = new DynamicUrlMatcher($this->getRoutes(), $this->getContext());
        }
        return $this->matcher;
    }

    /**
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if (!$this->generator) {
            $this->generator = new UrlGenerator($this->getRoutes(), $this->getContext());
        }
        return $this->generator;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
