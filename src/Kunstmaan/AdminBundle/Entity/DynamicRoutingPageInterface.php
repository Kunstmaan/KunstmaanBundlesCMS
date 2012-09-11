<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Routing\RouteCollection;

interface DynamicRoutingPageInterface extends PageInterface
{
    public function initRoutes();
    public function getRoutes();
    public function setRoutes(RouteCollection $routes);
    public function match($slug);
    public function generate($name, $parameters = array(), $absolute = false);
}
