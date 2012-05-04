<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\RouteCollection;

use Kunstmaan\AdminNodeBundle\Entity\HasNode;

interface DynamicRoutingPageInterface extends PageIFace
{
    public function getRoutes();
    public function setRoutes(RouteCollection $routes);
    public function hasRoutes();
    public function match($slug);
}