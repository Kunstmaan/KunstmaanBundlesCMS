<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

/**
 * MenuItem
 */
class MenuItem
{
    private $menu;
    private $internalName;
    private $role;
    private $parent;
    private $route;
    private $routeParams = array();
    private $active = false;
    private $children = null;
    private $attributes = array();
    private $appearInNavigation;
    private $weight = -50;

    /**
     * @param MenuBuilder $menu
     */
    public function __construct(MenuBuilder $menu)
    {
        $this->menu               = $menu;
        $this->appearInNavigation = true;
    }

    /**
     * @return MenuBuilder
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param string $internalName
     *
     * @return MenuItem
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return MenuItem
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return MenuItem|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent(MenuItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route  The route
     * @param array  $params The route parameters
     *
     * @return MenuItem
     */
    public function setRoute($route, $params = array())
    {
        $this->route       = $route;
        $this->routeParams = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     *
     * @return MenuItem
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $children = $this->menu->getChildren($this);
        }

        return $children;
    }

    /**
     * @return MenuItem[]
     */
    public function getNavigationChildren()
    {
        $result   = array();
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child->getAppearInNavigation()) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @return TopMenuItem[]
     */
    public function getTopChildren()
    {
        $result   = array();
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child instanceof TopMenuItem) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @param array $attributes
     *
     * @return MenuItem
     */
    public function addAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return MenuItem
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAppearInNavigation()
    {
        return $this->appearInNavigation;
    }

    /**
     * @param bool $appearInNavigation
     *
     * @return MenuItem
     */
    public function setAppearInNavigation($appearInNavigation)
    {
        $this->appearInNavigation = $appearInNavigation;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return MenuItem
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

}
