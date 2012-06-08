<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Menu;

use Symfony\Component\Translation\Translator;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem; 

class MenuItemWithWeight extends MenuItem
{
    protected $weight = -50;

    public function getWeight(){
        return $this->weight; 
    }
    
    public function setWeight($weight){
        $this->weight = $weight; 
    }
}
