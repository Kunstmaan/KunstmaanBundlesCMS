<?php

namespace Kunstmaan\NodeBundle\Entity;

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
