<?php

namespace Kunstmaan\NodeBundle\Entity;

/**
 * When browsing to an entity implementing this interface a forward will be done to the configured controller action.
 */
interface ControllerActionInterface extends HasNodeInterface
{

    /**
     * The controller action name (a string like BlogBundle:Post:index)
     *
     * @return string
     */
    public function getControllerAction();

    /**
     * @return array
     */
    public function getPathParams();

    /**
     * @return array
     */
    public function getQueryParams();

}
