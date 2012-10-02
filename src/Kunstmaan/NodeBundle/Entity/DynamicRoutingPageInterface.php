<?php

namespace Kunstmaan\NodeBundle\Entity;

use Symfony\Component\Routing\RouteCollection;

/**
 * DynamicRoutingPageInterface
 */
interface DynamicRoutingPageInterface extends PageInterface
{
    /**
     * initialize routing configuration
     */
    public function initRoutes();

    /**
     * @return RouteCollection
     */
    public function getRoutes();

    /**
     * @param RouteCollection $routes
     */
    public function setRoutes(RouteCollection $routes);

    /**
     * @param string $slug
     */
    public function match($slug);

    /**
     * @param string $name       The name
     * @param array  $parameters The parameters
     * @param bool   $absolute   Absolute or not
     */
    public function generate($name, array $parameters = array(), $absolute = false);

    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale);
}
