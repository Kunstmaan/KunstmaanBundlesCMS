<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder
{
    private $translator;
    private $extra;
    private $request;
    private $adaptors = array();
    private $topmenuitems = null;

    private $currentCache = null;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(Translator $translator, ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->container = $container;
    }

    public function addAdaptMenu(MenuAdaptorInterface $adaptor)
    {
        $this->adaptors[] = $adaptor;
    }

    public function getCurrent()
    {
        if ($this->currentCache !== null) {
            return $this->currentCache;
        }
        $active = null;
        do {
            $children = $this->getChildren($active);
            $foundActiveChild = false;
            foreach($children as $child){
                if($child->getActive()){
                    $foundActiveChild = true;
                    $active = $child;
                    break;
                }
            }
        } while($foundActiveChild);

        $this->currentCache = $active;

        return $active;
    }
    
    public function getBreadCrumb()
    {
        $result = array();
        $current = $this->getCurrent();
        while(!is_null($current)){
            array_unshift($result, $current);
            $current = $current->getParent();
        }
        return $result;
    }
    
    public function getLowestTopChild(){
        $current = $this->getCurrent();
        while(!is_null($current)){
            if($current instanceof TopMenuItem){
                return $current;
            }
            $current = $current->getParent();
        }
        return null;
    }
    
    public function getTopChildren(){
        $request = $this->container->get('request');
        if(is_null($this->topmenuitems)){
            $this->topmenuitems = array();
            foreach($this->adaptors as $menuadaptor){
                $menuadaptor->adaptChildren($this, $this->topmenuitems, null, $request);
            }
        }
        return $this->topmenuitems;
    }
    
    public function getChildren(MenuItem $parent = null){
        if ($parent == null) {
            return $this->getTopChildren();
        }
        $request = $this->container->get('request');
        $result = array();
        foreach($this->adaptors as $menuadaptor){
            $menuadaptor->adaptChildren($this, $result, $parent, $request);
        }
        return $result;
    }

}