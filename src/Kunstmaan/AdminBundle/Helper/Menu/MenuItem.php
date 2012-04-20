<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Helper\Menu;
use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

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

    public function __construct(MenuBuilder $menu)
    {
        $this->menu = $menu;
        $this->appearInNavigation = true;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function getInternalname()
    {
        return $this->internalname;
    }

    public function setInternalname($internalname)
    {
        $this->internalname = $internalname;
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route, $params = array())
    {
        $this->route = $route;
        $this->routeparams = $params;
    }

    public function getRouteparams()
    {
        return $this->routeparams;
    }

    public function setRouteparams($routeparams)
    {
        $this->routeparams = $routeparams;
    }

    public function getChildren()
    {
        if (is_null($this->children)) {
            $children = $this->menu->getChildren($this);
        }
        return $children;
    }
    
    public function getNavigationChildren()
    {
        $result = array();
        $children = $this->getChildren();
        foreach($children as $child){
            if($child->getAppearInNavigation()){
                $result[] = $child;
            }
        }
        return $result;
    }

    public function getTopChildren()
    {
        $result = array();
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child instanceof TopMenuItem) {
                $result[] = $child;
            }
        }
        return $result;
    }
    
    public function addAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getAppearInNavigation()
    {
        return $this->appearInNavigation;
    }

    public function setAppearInNavigation($appearInNavigation)
    {
        $this->appearInNavigation = $appearInNavigation;
    }

}
