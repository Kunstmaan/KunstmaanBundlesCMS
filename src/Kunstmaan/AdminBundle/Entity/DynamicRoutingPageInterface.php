<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Kunstmaan\AdminNodeBundle\Entity\HasNode;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

interface DynamicRoutingPageInterface extends PageInterface
{
    public function initRoutes();
    public function getRoutes();
    public function setRoutes(RouteCollection $routes);
    public function match($slug);
    public function generate($name, $parameters = array(), $absolute = false);
}