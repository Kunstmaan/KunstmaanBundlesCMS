<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminNodeBundle\Modules;

use Kunstmaan\AdminNodeBundle\Entity\Node;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class NodeMenuItem
{
    private $em;
    private $node;
    private $lazyChildren = null;
    private $parent;
    private $menu;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct($em, $node, $parent, $menu)
    {
        $this->em = $em;
        $this->node = $node;
        $this->parent = $parent;
        $this->menu = $menu;
    }
    
    public function getId(){
    	return $this->node->getId();
    }
    
    public function getNode(){
    	return $this->node;
    }
    
    public function getTitle(){
    	return $this->node->getTitle();
    }
    
    public function getSlugPart(){
    	return $this->node->getSlug();
    }
    
    public function getSlug(){
    	$result = $this->getSlugPart();
    	$p = $this->getParent();
    	while(!is_null($p)){
    		$result = $p->getSlug() . "/" . $result;
    		$p = $p->getParent();
    	}
    	return $result;
    }

    public function getParent(){
    	return $this->parent;
    }
    
    public function getChildren(){
    	if(is_null($this->lazyChildren)){
    		$this->lazyChildren = array();
    		$children = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getChildren($this->node);
    		foreach($children as $child){
    			$this->lazyChildren[] = new NodeMenuItem($this->em, $child, $this, $this->menu);
    		}
    	}
    	return $this->lazyChildren;
    }
    
    public function getPage(){
    	return $this->em->getRepository($node->getRefEntityname())->find($this->node->getRefId());
    }
    
    public function getActive(){
    	//TODO: change to something like in_array() but that didn't work
    	$bc = $this->menu->getBreadCrumb();
    	foreach($bc as $bcItem){
    		if($bcItem->getSlug() == $this->getSlug()){
    			return true;
    		}
    	}
    	return false;
    }
}