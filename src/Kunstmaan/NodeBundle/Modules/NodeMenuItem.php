<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminNodeBundle\Modules;

use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class NodeMenuItem
{
    private $em;
    private $node;
    private $lang;
    private $lazyChildren = null;
    private $parent;
    private $menu;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct($em, Node $node, $lang, $parent, $menu)
    {
        $this->em = $em;
        $this->node = $node;
        $this->lang = $lang;
        $this->parent = $parent;
        $this->menu = $menu;
    }

    public function getId(){
    	return $this->node->getId();
    }

    public function getNode(){
    	return $this->node;
    }

    public function getNodeTranslation(){
    	return $this->node->getNodeTranslation($this->getLang());
    }

    public function getLang(){
    	return $this->lang;
    }

    public function getTitle(){
    	$nodeTranslation = $this->node->getNodeTranslation($this->lang);
    	if($nodeTranslation){
    		return $nodeTranslation->getTitle();
    	}
    	return "Untranslated";
    }

    public function getSlugPart(){
    	$nodeTranslation = $this->node->getNodeTranslation($this->lang);
    	if($nodeTranslation){
    		return $nodeTranslation->getSlug();
    	}
    	return null;
    }

    public function getSlug(){
    	$result = $this->getSlugPart();
    	return $result;
    }

    public function getParent(){
    	return $this->parent;
    }

    public function getChildren(){
    	if(is_null($this->lazyChildren)){
    		$this->lazyChildren = array();
    		$children = $this->node->getChildren();
    		foreach($children as $child){
    			$this->lazyChildren[] = new NodeMenuItem($this->em, $child, $this->lang, $this, $this->menu);
    		}
    	}
    	return $this->lazyChildren;
    }

    public function getPage(){
    	return $this->node->getNodeTranslation($this->lang)->getRef($this->em);
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
