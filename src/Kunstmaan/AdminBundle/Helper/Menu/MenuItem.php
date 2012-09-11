<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

class MenuItem
{
    private $menu;
    private $internalname;
    private $role;
    private $parent;
    private $route;
    private $routeparams = array();
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
    public function getInternalname()
    {
        return $this->internalname;
    }

    /**
     * @param string $internalname
     */
    public function setInternalname($internalname)
    {
        $this->internalname = $internalname;
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
     */
    public function setRole($role)
    {
        $this->role = $role;
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
     */
    public function setParent(MenuItem $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @param array  $params
     */
    public function setRoute($route, $params = array())
    {
        $this->route       = $route;
        $this->routeparams = $params;
    }

    /**
     * @return array
     */
    public function getRouteparams()
    {
        return $this->routeparams;
    }

    /**
     * @param array $routeparams
     */
    public function setRouteparams($routeparams)
    {
        $this->routeparams = $routeparams;
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
     */
    public function addAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
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
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     */
    public function setAppearInNavigation($appearInNavigation)
    {
        $this->appearInNavigation = $appearInNavigation;
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
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

}
